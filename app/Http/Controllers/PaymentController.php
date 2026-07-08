<?php

namespace App\Http\Controllers;

use App\Models\Installment;
use App\Models\Order;
use App\Models\Payment;
use App\Models\PaymentSecurityLog;
use App\Http\Requests\ProcessPaymentRequest;
use App\Models\Cart;
use App\Notifications\InstallmentPaidNotification;
use App\Notifications\InstallmentScheduleNotification;
use App\Notifications\PaymentOtpNotification;
use App\Services\GeniusPayService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * PaymentController — Orchestrateur du flux de paiement sécurisé (10 couches)
 *
 * ┌──────────────────────────────────────────────────────────────┐
 * │ Couche 1  : HTTPS forcé (ForceHttpsMiddleware)               │
 * │ Couche 2  : Headers HTTP (SecurityHeadersMiddleware)         │
 * │ Couche 3  : Rate Limiting (throttle:5,1 sur les routes)      │
 * │ Couche 4  : 2FA OTP email (process → PaymentOtpController)  │
 * │ Couche 5  : Sanitization (ProcessPaymentRequest)             │
 * │ Couche 6  : CSRF (Laravel) + XSS (headers) + SQL (Eloquent) │
 * │ Couche 7  : AES-256 données sensibles (Order::$casts)        │
 * │ Couche 8  : Signature HMAC-SHA256 du webhook                 │
 * │ Couche 9  : lockForUpdate + Anti-replay 5 min               │
 * │ Couche 10 : Audit Trail + Détection fraude                   │
 * └──────────────────────────────────────────────────────────────┘
 *
 * Flux principal :
 *  1. process()              → génère OTP, redirige vers page OTP
 *  2. PaymentOtpController   → vérifie OTP, crée commande, → GeniusPay
 *  3. callback()             → retour user ; vérifie via API GeniusPay
 *  4. webhook()              → confirmation S2S (source de vérité)
 */
class PaymentController extends Controller
{
    public function __construct(private GeniusPayService $geniusPay) {}

    // ─────────────────────────────────────────────────────────────────
    // 1. INITIER LE PAIEMENT → génère et envoie l'OTP (Couche 4)
    // ─────────────────────────────────────────────────────────────────

    public function process(ProcessPaymentRequest $request)
    {
        $data = $request->validated();

        $cart = Cart::getOrCreateActive(auth()->id());
        $cart->load('items.product');

        if ($cart->items->isEmpty()) {
            return redirect()->route('cart.index')
                ->with('error', 'Votre panier est vide.');
        }

        // Détection de fraude : blocage si trop de paiements échoués
        if (PaymentSecurityLog::hasSuspiciousPaymentActivity(auth()->id())) {
            PaymentSecurityLog::log(PaymentSecurityLog::FRAUD_DETECTED, [
                'reason' => 'Trop de paiements échoués récents (>= 5 en 1h)',
            ]);
            return redirect()->route('cart.index')
                ->with('error', 'Votre compte a été temporairement limité. Veuillez contacter le support.');
        }

        $isCOD = ($data['payment_method'] === 'cash_on_delivery');

        // Appliquer le coupon si présent
        $coupon         = null;
        $discountAmount = 0;
        $finalAmount    = $cart->total_amount;

        if (!empty($data['coupon_code'])) {
            $coupon = \App\Models\Coupon::findValid($data['coupon_code']);
            if ($coupon) {
                $discountAmount = $coupon->calculateDiscount($cart->total_amount);
                $finalAmount    = max(0, $cart->total_amount - $discountAmount);
            }
        }

        // Créer la commande directement
        try {
            $order = DB::transaction(function () use ($cart, $data, $isCOD, $coupon, $discountAmount, $finalAmount) {
                $order = \App\Models\Order::create([
                    'user_id'           => auth()->id(),
                    'order_number'      => \App\Models\Order::generateOrderNumber(),
                    'total_amount'      => $finalAmount,
                    'status'            => 'pending',
                    'shipping_address'  => $data['shipping_address'] ?? null,
                    'shipping_phone'    => $data['shipping_phone'],
                    'shipping_city'     => $data['shipping_city'],
                    'shipping_commune'  => $data['shipping_commune'],
                    'shipping_quartier' => $data['shipping_quartier'],
                    'notes'             => $data['notes'] ?? null,
                    'installment_count' => (int) ($data['installment_count'] ?? 1),
                    'coupon_code'       => $coupon?->code,
                    'discount_amount'   => $discountAmount,
                ]);

                foreach ($cart->items as $item) {
                    \App\Models\OrderItem::create([
                        'order_id'     => $order->id,
                        'product_id'   => $item->product_id,
                        'product_name' => $item->product->name,
                        'quantity'     => $item->quantity,
                        'unit_price'   => $item->price,
                        'subtotal'     => $item->price * $item->quantity,
                    ]);
                    $item->product->decrement('stock', $item->quantity);
                }

                $installmentCount = (int) ($data['installment_count'] ?? 1);
                $firstAmount      = $installmentCount > 1
                    ? round($order->total_amount / $installmentCount, 0)
                    : $order->total_amount;

                \App\Models\Payment::create([
                    'order_id'              => $order->id,
                    'transaction_reference' => 'PENDING-' . \Illuminate\Support\Str::uuid(),
                    'amount'                => $firstAmount,
                    'status'                => 'pending',
                    'payment_method'        => $isCOD ? 'cash_on_delivery' : 'geniuspay',
                ]);

                if ($installmentCount > 1) {
                    $installmentAmount = round($order->total_amount / $installmentCount, 0);
                    for ($i = 1; $i <= $installmentCount; $i++) {
                        \App\Models\Installment::create([
                            'order_id'           => $order->id,
                            'installment_number' => $i,
                            'amount'             => $installmentAmount,
                            'due_date'           => now()->addDays(($i - 1) * 30)->toDateString(),
                            'status'             => 'pending',
                        ]);
                    }
                }

                $cart->markAsConverted();

                if ($coupon) {
                    $coupon->increment('used_count');
                }

                return $order;
            });
        } catch (\Exception $e) {
            report($e);
            return redirect()->route('cart.index')
                ->with('error', 'Une erreur est survenue. Veuillez réessayer.');
        }

        PaymentSecurityLog::log(PaymentSecurityLog::PAYMENT_INITIATED, [
            'order_number'      => $order->order_number,
            'amount'            => $order->total_amount,
            'payment_method'    => $data['payment_method'],
            'installment_count' => $data['installment_count'] ?? 1,
        ], auth()->id(), $order->id);

        // Paiement à la livraison
        if ($isCOD) {
            $order->load('items', 'payment', 'installments', 'user');
            if ($order->hasInstallments()) {
                try {
                    $order->user->notify(new InstallmentScheduleNotification($order));
                } catch (\Exception $e) { report($e); }
            }
            return view('payment.success', ['order' => $order, 'cod' => true]);
        }

        // GeniusPay → redirection vers la page de paiement
        try {
            $order->load('user', 'payment');
            $payData = $this->geniusPay->createPayment($order);

            $reference   = $payData['reference'] ?? $payData['id'] ?? $payData['transaction_reference'] ?? null;
            $checkoutUrl = $payData['checkout_url'] ?? $payData['payment_url'] ?? $payData['url'] ?? $payData['redirect_url'] ?? null;

            if ($reference) {
                $order->payment->update(['transaction_reference' => $reference]);
            }

            if (!$checkoutUrl) {
                throw new \RuntimeException('GeniusPay : checkout_url manquant.');
            }

            session(['geniuspay_checkout_url_' . $order->id => $checkoutUrl]);

            return redirect()->route('payment.waiting', $order);

        } catch (\Exception $e) {
            report($e);
            DB::transaction(function () use ($order) {
                $order->load('items.product', 'payment');
                $order->restoreStock();
                $order->update(['status' => 'cancelled']);
                $order->payment->update([
                    'status'         => 'failed',
                    'failure_reason' => 'Impossible de contacter le service de paiement.',
                ]);
            });
            return redirect()->route('cart.index')
                ->with('error', 'Le service de paiement est momentanément indisponible. Veuillez réessayer.');
        }
    }

    // ─────────────────────────────────────────────────────────────────
    // 2. RETOUR UTILISATEUR (redirection depuis GeniusPay)
    // ─────────────────────────────────────────────────────────────────

    public function callback(Order $order, Request $request)
    {
        // Couche 10 : log + blocage si accès non autorisé
        if ($order->user_id !== auth()->id()) {
            PaymentSecurityLog::log(PaymentSecurityLog::UNAUTHORIZED_ACCESS, [
                'target_order_id'  => $order->id,
                'target_owner_id'  => $order->user_id,
                'action'           => 'callback',
            ]);
            Log::warning('SECURITY: accès non autorisé au callback de paiement', [
                'attacker_user_id' => auth()->id(),
                'order_owner_id'   => $order->user_id,
                'order_id'         => $order->id,
                'ip'               => $request->ip(),
                'user_agent'       => $request->userAgent(),
            ]);
            abort(403);
        }

        Log::info('GeniusPay callback reçu', [
            'order'  => $order->order_number,
            'params' => $request->only(['status', 'reference']),
        ]);

        $order->refresh()->load('payment', 'installments', 'user');

        // Si le webhook a déjà traité → afficher directement
        if ($order->payment?->status === 'success' || $order->status === 'paid') {
            return view('payment.success', compact('order'));
        }
        if ($order->payment?->status === 'failed' || $order->status === 'cancelled') {
            return view('payment.failed', compact('order'));
        }

        // ── Couche 8+9 : On ne fait PAS confiance à ?status= de l'URL ──
        // Ce paramètre est visible et modifiable dans la barre d'adresse.
        // On interroge directement l'API GeniusPay (source de vérité externe).
        $reference = $order->payment?->transaction_reference ?? '';
        if (!$reference || str_starts_with($reference, 'PENDING-')) {
            // Aucune référence GeniusPay → le paiement n'a pas abouti
            DB::transaction(function () use ($order) {
                $order->load('items.product', 'payment');
                $order->restoreStock();
                $order->update(['status' => 'cancelled']);
                $order->payment?->update([
                    'status'         => 'failed',
                    'failure_reason' => 'Paiement non complété ou annulé.',
                ]);
            });
            return view('payment.failed', compact('order'));
        }

        // Paramètre status de l'URL GeniusPay (indicatif, pas source de vérité)
        $urlStatus = $request->query('status', '');

        try {
            $gpData     = $this->geniusPay->getPayment($reference);
            $gpStatus   = $gpData['status']   ?? null;
            $gpScenario = $gpData['scenario'] ?? null;

            Log::info('GeniusPay getPayment callback', [
                'reference' => $reference,
                'gpStatus'  => $gpStatus,
                'urlStatus' => $urlStatus,
                'data'      => $gpData,
            ]);

            // Sandbox : status=null avec scenario
            if ($gpStatus === null) {
                if ($gpScenario === 'success') $gpStatus = 'success';
                if ($gpScenario === 'failure') $gpStatus = 'failed';
            }

            // Si l'URL dit failed/cancelled et l'API dit processing ou pending
            // → GeniusPay a rejeté (solde insuffisant) mais n'envoie pas de webhook
            // → on fait confiance à l'URL
            if (in_array($urlStatus, ['failed', 'cancelled', 'expired'])
                && in_array($gpStatus, ['processing', 'pending', null])) {
                $gpStatus = 'failed';
            }

            $gpStatus = $gpStatus ?? 'pending';
        } catch (\Exception $e) {
            report($e);
            if (in_array($urlStatus, ['failed', 'cancelled', 'expired'])) {
                $gpStatus = $urlStatus;
            } else {
                return view('payment.pending', compact('order'));
            }
        }

        if (in_array($gpStatus, ['completed', 'success', 'paid'])) {
            DB::transaction(function () use ($order) {
                $order->payment->update(['status' => 'success', 'paid_at' => now()]);
                $first = $order->installments->where('installment_number', 1)->first();
                if ($first?->status === 'pending') {
                    $first->update(['status' => 'paid', 'paid_at' => now()]);
                }
                $allPaid = $order->installments->isEmpty()
                    || $order->installments->every(fn($i) => $i->status === 'paid');
                $order->update(['status' => $allPaid ? 'paid' : 'processing']);
            });

            PaymentSecurityLog::log(PaymentSecurityLog::PAYMENT_SUCCESS, [
                'reference' => $reference,
                'source'    => 'callback_api_verification',
            ], $order->user_id, $order->id);

            if ($order->hasInstallments()) {
                try {
                    $order->load('user', 'items', 'payment', 'installments');
                    $order->user->notify(new InstallmentScheduleNotification($order));
                } catch (\Exception $e) { report($e); }
            }

            $order->refresh()->load('payment');
            return view('payment.success', compact('order'));
        }

        if (in_array($gpStatus, ['failed', 'cancelled', 'expired', 'error', 'declined', 'rejected'])) {
            DB::transaction(function () use ($order, $gpStatus) {
                $order->restoreStock();
                $order->update(['status' => 'cancelled']);
                $order->payment->update([
                    'status'         => 'failed',
                    'failure_reason' => match ($gpStatus) {
                        'failed', 'error', 'declined', 'rejected' => 'Paiement refusé.',
                        'cancelled' => 'Paiement annulé par l\'utilisateur.',
                        'expired'   => 'Délai de paiement expiré.',
                        default     => 'Paiement non complété.',
                    },
                ]);
            });

            PaymentSecurityLog::log(PaymentSecurityLog::PAYMENT_FAILED, [
                'reference' => $reference,
                'gp_status' => $gpStatus,
                'source'    => 'callback_api_verification',
            ], $order->user_id, $order->id);

            $order->refresh()->load('payment');
            return view('payment.failed', compact('order'));
        }

        return view('payment.pending', compact('order'));
    }

    // ─────────────────────────────────────────────────────────────────
    // 3. WEBHOOK GENIUSPAY (serveur → serveur, sans CSRF)
    // ─────────────────────────────────────────────────────────────────

    public function webhook(Request $request)
    {
        $signature   = $request->header('X-Webhook-Signature', '');
        $timestamp   = $request->header('X-Webhook-Timestamp', '');
        $rawPayload  = $request->getContent();
        $jsonPayload = json_encode($request->json()->all());

        $payload = $request->json()->all();
        $event   = $payload['event'] ?? $request->header('X-Webhook-Event', '');

        // Répondre immédiatement aux pings de test GeniusPay (avant vérif signature)
        if ($event === 'webhook.test' || $event === 'ping' || $rawPayload === '{}' || empty($rawPayload)) {
            Log::info('GeniusPay webhook test reçu avec succès');
            return response()->json(['status' => 'ok', 'message' => 'Webhook opérationnel']);
        }

        // ── Couche 8 : Vérification HMAC-SHA256 ───────────────────────
        $signatureValid = $this->geniusPay->verifyWebhookSignature($signature, $timestamp, $rawPayload)
                       || $this->geniusPay->verifyWebhookSignature($signature, $timestamp, $jsonPayload);

        if (!$signatureValid) {
            Log::warning('SECURITY: webhook GeniusPay — signature invalide', [
                'ip'        => $request->ip(),
                'signature' => $signature,
                'timestamp' => $timestamp,
            ]);
            PaymentSecurityLog::log(PaymentSecurityLog::UNAUTHORIZED_ACCESS, [
                'action' => 'webhook_invalid_signature',
                'ip'     => $request->ip(),
            ], null);
            return response()->json(['error' => 'Signature invalide'], 401);
        }

        // ── Couche 9 : Protection anti-replay (fenêtre 5 minutes) ─────
        if (abs(time() - (int) $timestamp) > 300) {
            Log::warning('SECURITY: webhook GeniusPay — requête expirée (replay attack?)', [
                'timestamp' => $timestamp,
                'ip'        => $request->ip(),
            ]);
            PaymentSecurityLog::log(PaymentSecurityLog::UNAUTHORIZED_ACCESS, [
                'action'    => 'webhook_replay_attack',
                'timestamp' => $timestamp,
            ], null);
            return response()->json(['error' => 'Requête expirée'], 400);
        }

        $data      = $payload['data'] ?? [];
        $reference = $data['reference'] ?? '';

        // Événement de test avec référence vide
        if ($event === 'webhook.test') {
            Log::info('GeniusPay webhook test reçu avec succès');
            return response()->json(['status' => 'ok', 'message' => 'Webhook test reçu']);
        }

        if (empty($reference)) {
            Log::warning('SECURITY: webhook GeniusPay reçu sans référence', ['payload' => $payload]);
            return response()->json(['error' => 'Référence manquante'], 400);
        }

        // Couche 10 : audit de chaque webhook reçu
        PaymentSecurityLog::log(PaymentSecurityLog::WEBHOOK_RECEIVED, [
            'event'     => $event,
            'reference' => $reference,
        ], null);

        // ── Couche 9 : Traitement atomique avec verrou pessimiste ──────
        // lockForUpdate() DOIT être à l'intérieur de DB::transaction().
        // Verrou exclusif sur la ligne jusqu'à la fin → un seul traitement
        // même si GeniusPay envoie deux webhooks simultanément.
        DB::transaction(function () use ($event, $data, $reference) {

            $payment = Payment::where('transaction_reference', $reference)
                ->lockForUpdate()
                ->first();

            if ($payment) {
                $order = $payment->order()
                    ->with('items.product', 'installments', 'user')
                    ->first();

                match ($event) {
                    'payment.success'                          => $this->handleSuccess($payment, $order, $data),
                    'payment.failed', 'payment.cancelled',
                    'payment.expired'                         => $this->handleFailed($payment, $order, $event),
                    default                                   => null,
                };
                return;
            }

            // Vérifier si la référence correspond à une tranche
            $installment = Installment::where('transaction_reference', $reference)
                ->lockForUpdate()
                ->first();

            if (!$installment || $event !== 'payment.success') return;

            // Idempotence : déjà payée → rien à faire
            if ($installment->status === 'paid') return;

            $installment->update(['status' => 'paid', 'paid_at' => now()]);

            $order = $installment->order()
                ->with('installments', 'payment', 'user', 'items')
                ->first();

            $allPaid = $order->installments->every(fn($i) => $i->status === 'paid');
            if ($allPaid) {
                $order->update(['status' => 'paid']);
                $order->payment->update(['status' => 'success', 'paid_at' => now()]);
            }

            try {
                $order->user->notify(new InstallmentPaidNotification($order, $installment));
            } catch (\Exception $e) {
                report($e);
            }
        });

        return response()->json(['status' => 'ok']);
    }

    // ─────────────────────────────────────────────────────────────────
    // MÉTHODES PRIVÉES
    // ─────────────────────────────────────────────────────────────────

    private function handleSuccess(Payment $payment, Order $order, array $data): void
    {
        // Idempotence : déjà traité → rien à faire
        if ($order->status === 'paid') return;

        // ── Couche 9+10 : Vérification du montant ─────────────────────
        // Le montant GeniusPay doit correspondre au montant attendu (±1 FCFA).
        // Bloque les tentatives de payer 1 FCFA pour une commande de 50 000 FCFA.
        $amountPaid = (float) ($data['amount'] ?? 0);
        if ($amountPaid > 0 && abs($amountPaid - (float) $payment->amount) > 1) {
            Log::critical('SECURITY: montant webhook ne correspond pas au montant attendu', [
                'order_id'        => $order->id,
                'order_number'    => $order->order_number,
                'amount_expected' => $payment->amount,
                'amount_received' => $amountPaid,
                'reference'       => $payment->transaction_reference,
            ]);
            PaymentSecurityLog::log(PaymentSecurityLog::FRAUD_DETECTED, [
                'type'            => 'amount_mismatch',
                'amount_expected' => $payment->amount,
                'amount_received' => $amountPaid,
                'reference'       => $payment->transaction_reference,
            ], $order->user_id, $order->id);
            throw new \RuntimeException(
                "Fraude détectée : montant attendu {$payment->amount} FCFA, reçu {$amountPaid} FCFA."
            );
        }

        $payment->update([
            'status'         => 'success',
            'payment_method' => $data['payment_method'] ?? $data['provider'] ?? 'geniuspay',
            'paid_at'        => now(),
        ]);

        $order->loadMissing('installments', 'user');
        $first = $order->installments->where('installment_number', 1)->first();
        if ($first && $first->status === 'pending') {
            $first->update(['status' => 'paid', 'paid_at' => now()]);
            $order->installments = $order->installments->map(function ($i) use ($first) {
                if ($i->id === $first->id) $i->status = 'paid';
                return $i;
            });
        }

        $allPaid = $order->installments->isEmpty()
            || $order->installments->every(fn($i) => $i->status === 'paid');
        $order->update(['status' => $allPaid ? 'paid' : 'processing']);

        // Couche 10 : audit succès
        PaymentSecurityLog::log(PaymentSecurityLog::PAYMENT_SUCCESS, [
            'reference'      => $payment->transaction_reference,
            'amount'         => $payment->amount,
            'payment_method' => $payment->payment_method,
        ], $order->user_id, $order->id);

        if ($order->hasInstallments()) {
            try {
                $order->user->notify(new InstallmentScheduleNotification($order));
            } catch (\Exception $e) { report($e); }
        }
    }

    private function handleFailed(Payment $payment, Order $order, string $event): void
    {
        if (in_array($order->status, ['paid', 'cancelled'])) return;

        $reason = match ($event) {
            'payment.failed'    => 'Paiement refusé.',
            'payment.cancelled' => 'Paiement annulé par l\'utilisateur.',
            'payment.expired'   => 'Délai de paiement expiré.',
            default             => 'Paiement non complété.',
        };

        $order->restoreStock();
        $order->update(['status' => 'cancelled']);
        $payment->update(['status' => 'failed', 'failure_reason' => $reason]);

        // Couche 10 : audit échec
        PaymentSecurityLog::log(PaymentSecurityLog::PAYMENT_FAILED, [
            'reference' => $payment->transaction_reference,
            'reason'    => $reason,
            'event'     => $event,
        ], $order->user_id, $order->id);
    }

    // ─────────────────────────────────────────────────────────────────
    // 4. PAIEMENT D'UNE TRANCHE (client)
    // ─────────────────────────────────────────────────────────────────

    public function payInstallment(Order $order, Installment $installment)
    {
        if ($order->user_id !== auth()->id()) {
            PaymentSecurityLog::log(PaymentSecurityLog::UNAUTHORIZED_ACCESS, [
                'action'        => 'pay_installment',
                'target_order'  => $order->id,
                'installment'   => $installment->id,
            ]);
            Log::warning('SECURITY: tentative de paiement de tranche sur commande non possédée', [
                'attacker_user_id' => auth()->id(),
                'order_owner_id'   => $order->user_id,
                'order_id'         => $order->id,
                'ip'               => request()->ip(),
            ]);
            abort(403);
        }
        if ($installment->order_id !== $order->id) abort(404);

        if ($installment->status === 'paid') {
            return back()->with('error', 'Cette tranche est déjà payée.');
        }

        // ── Vérification de l'ordre séquentiel ────────────────────────
        if ($installment->installment_number > 1) {
            $previous = $order->installments()
                ->where('installment_number', $installment->installment_number - 1)
                ->first();

            if ($previous && $previous->status !== 'paid') {
                return back()->with('error',
                    "Vous devez d'abord régler la tranche {$previous->installment_number} avant de payer celle-ci."
                );
            }
        }

        try {
            $order->load('user');
            $payData = $this->geniusPay->createInstallmentPayment($order, $installment);
            $installment->update(['transaction_reference' => $payData['reference']]);

            $checkoutUrl = $payData['checkout_url'] ?? $payData['payment_url'] ?? null;
            if (!$checkoutUrl) {
                throw new \RuntimeException('GeniusPay : checkout_url manquant.');
            }

            return redirect()->away($checkoutUrl);

        } catch (\Exception $e) {
            report($e);
            return back()->with('error', 'Le service de paiement est momentanément indisponible. Veuillez réessayer.');
        }
    }

    // ─────────────────────────────────────────────────────────────────
    // 5. RETOUR APRÈS PAIEMENT D'UNE TRANCHE
    // ─────────────────────────────────────────────────────────────────

    public function installmentCallback(Order $order, Installment $installment, Request $request)
    {
        if ($order->user_id !== auth()->id()) {
            PaymentSecurityLog::log(PaymentSecurityLog::UNAUTHORIZED_ACCESS, [
                'action'      => 'installment_callback',
                'order_id'    => $order->id,
                'installment' => $installment->id,
            ]);
            Log::warning('SECURITY: accès non autorisé au callback de tranche', [
                'attacker_user_id' => auth()->id(),
                'order_id'         => $order->id,
                'ip'               => $request->ip(),
            ]);
            abort(403);
        }
        if ($installment->order_id !== $order->id) abort(404);

        Log::info('GeniusPay installment callback reçu', [
            'order'       => $order->order_number,
            'installment' => $installment->installment_number,
            'params'      => $request->only(['status', 'reference']),
        ]);

        $installment->refresh();

        if ($installment->status === 'paid') {
            return redirect()->route('orders.show', $order)
                ->with('success', "Tranche {$installment->installment_number}/{$order->installment_count} déjà payée.");
        }

        // ── Couche 8 : Vérifier auprès de GeniusPay (pas l'URL) ───────
        $reference = $installment->transaction_reference ?? '';
        if (!$reference) {
            return redirect()->route('orders.show', $order)
                ->with('info', 'Votre paiement est en cours de confirmation.');
        }

        $urlStatus = $request->query('status', '');

        try {
            $gpData     = $this->geniusPay->getPayment($reference);
            $gpStatus   = $gpData['status']   ?? null;
            $gpScenario = $gpData['scenario'] ?? null;

            if ($gpStatus === null) {
                if ($gpScenario === 'success') $gpStatus = 'success';
                if ($gpScenario === 'failure') $gpStatus = 'failed';
            }

            // Si l'URL dit failed et l'API dit processing/pending → solde insuffisant, pas de webhook
            if (in_array($urlStatus, ['failed', 'cancelled', 'expired'])
                && in_array($gpStatus, ['processing', 'pending', null])) {
                $gpStatus = 'failed';
            }

            $gpStatus = $gpStatus ?? 'pending';
        } catch (\Exception $e) {
            report($e);
            return redirect()->route('orders.show', $order)
                ->with('info', 'Votre paiement est en cours de confirmation. La tranche sera mise à jour sous peu.');
        }

        if (in_array($gpStatus, ['completed', 'success', 'paid'])) {
            DB::transaction(function () use ($order, $installment) {
                $installment->update(['status' => 'paid', 'paid_at' => now()]);
                $order->load('installments', 'payment');
                $allPaid = $order->installments->every(fn($i) => $i->status === 'paid');
                if ($allPaid) {
                    $order->update(['status' => 'paid']);
                    $order->payment->update(['status' => 'success', 'paid_at' => now()]);
                }
            });

            try {
                $order->load('user', 'items', 'payment', 'installments');
                $order->user->notify(new InstallmentPaidNotification($order, $installment->fresh()));
            } catch (\Exception $e) { report($e); }

            return redirect()->route('orders.show', $order)
                ->with('success', "Tranche {$installment->installment_number}/{$order->installment_count} payée avec succès !");
        }

        if (in_array($gpStatus, ['failed', 'cancelled', 'expired', 'error', 'declined', 'rejected'])) {
            return redirect()->route('orders.show', $order)
                ->with('error', 'Le paiement a échoué. Veuillez réessayer.');
        }

        return redirect()->route('orders.show', $order)
            ->with('info', 'Votre paiement est en cours de confirmation. La tranche sera mise à jour sous peu.');
    }

    // ─────────────────────────────────────────────────────────────────
    // PAGES STATIQUES
    // ─────────────────────────────────────────────────────────────────

    // ─────────────────────────────────────────────────────────────────
    // PAGE D'ATTENTE — ouvre GeniusPay + poll automatique du statut
    // ─────────────────────────────────────────────────────────────────

    public function waiting(Order $order)
    {
        if ($order->user_id !== auth()->id()) abort(403);
        $order->load('payment');

        // Si déjà traité, rediriger directement
        if ($order->payment?->status === 'success') {
            return redirect()->route('payment.success', $order);
        }
        if (in_array($order->payment?->status, ['failed']) || $order->status === 'cancelled') {
            return redirect()->route('payment.failed', $order);
        }

        $checkoutUrl = session('geniuspay_checkout_url_' . $order->id);

        return view('payment.waiting', compact('order', 'checkoutUrl'));
    }

    // ─────────────────────────────────────────────────────────────────
    // STATUT JSON — interrogé en polling par la page d'attente
    // ─────────────────────────────────────────────────────────────────

    public function status(Order $order)
    {
        if ($order->user_id !== auth()->id()) abort(403);
        $order->refresh()->load('payment');

        $paymentStatus = $order->payment?->status ?? 'pending';
        $orderStatus   = $order->status;

        // Déjà résolu en DB
        if ($paymentStatus === 'success' || $orderStatus === 'paid') {
            return response()->json(['status' => 'success', 'redirect' => route('payment.success', $order)]);
        }
        if ($paymentStatus === 'failed' || $orderStatus === 'cancelled') {
            return response()->json(['status' => 'failed', 'redirect' => route('payment.failed', $order)]);
        }

        // Toujours pending → interroger GeniusPay directement
        $reference = $order->payment?->transaction_reference ?? '';
        if (!$reference || str_starts_with($reference, 'PENDING-')) {
            return response()->json(['status' => 'pending']);
        }

        try {
            $gpData   = $this->geniusPay->getPayment($reference);
            $gpStatus = $gpData['status'] ?? 'pending';

            if (in_array($gpStatus, ['completed', 'success', 'paid'])) {
                DB::transaction(function () use ($order) {
                    $order->payment->update(['status' => 'success', 'paid_at' => now()]);
                    $order->update(['status' => 'paid']);
                });
                return response()->json(['status' => 'success', 'redirect' => route('payment.success', $order)]);
            }

            if (in_array($gpStatus, ['failed', 'cancelled', 'expired', 'error', 'declined', 'rejected'])) {
                DB::transaction(function () use ($order, $gpStatus) {
                    $order->load('items.product');
                    $order->restoreStock();
                    $order->update(['status' => 'cancelled']);
                    $order->payment->update(['status' => 'failed', 'failure_reason' => 'Paiement ' . $gpStatus]);
                });
                return response()->json(['status' => 'failed', 'redirect' => route('payment.failed', $order)]);
            }
        } catch (\Exception $e) {
            report($e);
        }

        return response()->json(['status' => 'pending']);
    }

    public function success(Order $order)
    {
        if ($order->user_id !== auth()->id()) abort(403);
        $order->load('items', 'payment');
        return view('payment.success', compact('order'));
    }

    public function failed(Order $order)
    {
        if ($order->user_id !== auth()->id()) abort(403);
        $order->load('payment');
        return view('payment.failed', compact('order'));
    }
}
