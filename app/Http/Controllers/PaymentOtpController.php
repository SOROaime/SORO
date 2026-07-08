<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Coupon;
use App\Models\Installment;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\PaymentSecurityLog;
use App\Notifications\InstallmentScheduleNotification;
use App\Services\GeniusPayService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * Couche 4 — Authentification renforcée : vérification OTP avant paiement.
 *
 * Flux :
 *  showForm() → affiche la page de saisie du code OTP
 *  confirm()  → vérifie le code, crée la commande, redirige vers GeniusPay
 */
class PaymentOtpController extends Controller
{
    public function __construct(private GeniusPayService $geniusPay) {}

    // ─────────────────────────────────────────────────────────────────
    // Affiche le formulaire OTP
    // ─────────────────────────────────────────────────────────────────

    public function showForm(Request $request)
    {
        if (!$request->session()->has('payment_otp_hash')) {
            return redirect()->route('orders.checkout')
                ->with('error', 'Session expirée. Veuillez recommencer votre commande.');
        }

        $maskedEmail = $this->maskEmail(auth()->user()->email);
        $expiresAt   = $request->session()->get('payment_otp_expires', 0);
        $secondsLeft = max(0, $expiresAt - now()->timestamp);

        return view('payment.otp-confirm', compact('maskedEmail', 'secondsLeft'));
    }

    // ─────────────────────────────────────────────────────────────────
    // Vérifie le code OTP et crée la commande si valide
    // ─────────────────────────────────────────────────────────────────

    public function confirm(Request $request)
    {
        $request->validate([
            'otp' => ['required', 'string', 'size:6', 'regex:/^[0-9]{6}$/'],
        ], [
            'otp.required' => 'Le code de confirmation est obligatoire.',
            'otp.size'     => 'Le code doit contenir exactement 6 chiffres.',
            'otp.regex'    => 'Le code ne doit contenir que des chiffres.',
        ]);

        $userId = auth()->id();

        // ── Protection : trop de tentatives échouées ──────────────────
        if (PaymentSecurityLog::isOtpBlocked($userId)) {
            PaymentSecurityLog::log(PaymentSecurityLog::OTP_BLOCKED, [
                'reason' => '3 tentatives OTP échouées en moins de 10 minutes',
            ]);
            $request->session()->forget([
                'payment_otp_hash', 'payment_otp_expires',
                'payment_pending_data', 'payment_otp_attempts',
            ]);
            return redirect()->route('orders.checkout')
                ->with('error', 'Trop de tentatives incorrectes. Veuillez recommencer dans 10 minutes.');
        }

        // ── Récupérer les données de session ─────────────────────────
        $otpHash    = $request->session()->get('payment_otp_hash');
        $otpExpiry  = $request->session()->get('payment_otp_expires');
        $pendingData = $request->session()->get('payment_pending_data');

        if (!$otpHash || !$otpExpiry || !$pendingData) {
            return redirect()->route('orders.checkout')
                ->with('error', 'Session expirée. Veuillez recommencer votre commande.');
        }

        // ── Vérifier l'expiration (5 minutes) ────────────────────────
        if (now()->timestamp > $otpExpiry) {
            $request->session()->forget([
                'payment_otp_hash', 'payment_otp_expires', 'payment_pending_data',
            ]);
            PaymentSecurityLog::log(PaymentSecurityLog::OTP_FAILED, [
                'reason' => 'Code OTP expiré',
            ]);
            return redirect()->route('orders.checkout')
                ->with('error', 'Votre code a expiré (5 minutes). Veuillez recommencer.');
        }

        // ── Vérifier le code OTP — hash_equals résiste aux timing attacks ──
        if (!hash_equals($otpHash, hash('sha256', $request->input('otp')))) {
            PaymentSecurityLog::log(PaymentSecurityLog::OTP_FAILED, [
                'reason' => 'Code OTP incorrect',
            ]);

            $attempts  = $request->session()->increment('payment_otp_attempts');
            $remaining = max(0, 3 - $attempts);

            if ($remaining === 0) {
                $request->session()->forget([
                    'payment_otp_hash', 'payment_otp_expires',
                    'payment_pending_data', 'payment_otp_attempts',
                ]);
                return redirect()->route('orders.checkout')
                    ->with('error', 'Trop de tentatives. Veuillez recommencer votre commande.');
            }

            return back()->withErrors([
                'otp' => "Code incorrect. Il vous reste {$remaining} tentative(s).",
            ]);
        }

        // ── OTP valide → nettoyer la session ─────────────────────────
        PaymentSecurityLog::log(PaymentSecurityLog::OTP_VERIFIED);
        $request->session()->forget([
            'payment_otp_hash', 'payment_otp_expires', 'payment_otp_attempts',
        ]);

        $data            = $pendingData;
        $isCOD           = ($data['payment_method'] === 'cash_on_delivery');
        $verifiedAmount  = $request->session()->get('payment_verified_amount');

        $request->session()->forget('payment_pending_data');
        $request->session()->forget('payment_verified_amount');

        $cart = Cart::getOrCreateActive(auth()->id());
        $cart->load('items.product');

        if ($cart->items->isEmpty()) {
            return redirect()->route('cart.index')
                ->with('error', 'Votre panier est vide.');
        }

        // Vérification que le montant du panier n'a pas changé depuis l'envoi de l'OTP
        if ($verifiedAmount !== null && abs($cart->total_amount - $verifiedAmount) > 1) {
            PaymentSecurityLog::log(PaymentSecurityLog::FRAUD_DETECTED, [
                'reason'           => 'Montant du panier modifié entre OTP et confirmation',
                'amount_verified'  => $verifiedAmount,
                'amount_current'   => $cart->total_amount,
            ]);
            return redirect()->route('cart.index')
                ->with('error', 'Le montant de votre panier a changé. Veuillez recommencer.');
        }

        // ── Appliquer le coupon si présent ──────────────────────────
        $coupon         = null;
        $discountAmount = 0;
        $finalAmount    = $cart->total_amount;

        if (!empty($data['coupon_code'])) {
            $coupon = Coupon::findValid($data['coupon_code']);
            if ($coupon) {
                $discountAmount = $coupon->calculateDiscount($cart->total_amount);
                $finalAmount    = max(0, $cart->total_amount - $discountAmount);
            }
        }

        // ── Créer la commande (logique identique à l'ancien process()) ──
        try {
            $order = DB::transaction(function () use ($cart, $data, $isCOD, $coupon, $discountAmount, $finalAmount) {
                $order = Order::create([
                    'user_id'           => auth()->id(),
                    'order_number'      => Order::generateOrderNumber(),
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
                    OrderItem::create([
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

                Payment::create([
                    'order_id'              => $order->id,
                    'transaction_reference' => 'PENDING-' . Str::uuid(),
                    'amount'                => $firstAmount,
                    'status'                => 'pending',
                    'payment_method'        => $isCOD ? 'cash_on_delivery' : 'geniuspay',
                ]);

                if ($installmentCount > 1) {
                    $installmentAmount = round($order->total_amount / $installmentCount, 0);
                    for ($i = 1; $i <= $installmentCount; $i++) {
                        Installment::create([
                            'order_id'           => $order->id,
                            'installment_number' => $i,
                            'amount'             => $installmentAmount,
                            'due_date'           => now()->addDays(($i - 1) * 30)->toDateString(),
                            'status'             => 'pending',
                        ]);
                    }
                }

                $cart->markAsConverted();

                // Incrémenter le compteur d'utilisation du coupon
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
            'installment_count' => $data['installment_count'],
        ], auth()->id(), $order->id);

        // ── Paiement à la livraison ───────────────────────────────────
        if ($isCOD) {
            $order->load('items', 'payment', 'installments', 'user');
            if ($order->hasInstallments()) {
                try {
                    $order->user->notify(new InstallmentScheduleNotification($order));
                } catch (\Exception $e) { report($e); }
            }
            return view('payment.success', ['order' => $order, 'cod' => true]);
        }

        // ── GeniusPay ─────────────────────────────────────────────────
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

            // Stocker l'URL GeniusPay en session puis rediriger vers la page d'attente
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
    // Renvoyer le code OTP (si expiré ou non reçu)
    // ─────────────────────────────────────────────────────────────────

    public function resend(Request $request)
    {
        if (!$request->session()->has('payment_pending_data')) {
            return redirect()->route('orders.checkout')
                ->with('error', 'Session expirée. Veuillez recommencer.');
        }

        $pendingData = $request->session()->get('payment_pending_data');
        $cart        = Cart::getOrCreateActive(auth()->id());

        $otp   = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $total = number_format($cart->total_amount, 0, ',', ' ') . ' FCFA';

        $request->session()->put([
            'payment_otp_hash'    => hash('sha256', $otp),
            'payment_otp_expires' => now()->addMinutes(5)->timestamp,
            'payment_otp_attempts' => 0,
        ]);

        try {
            auth()->user()->notify(new \App\Notifications\PaymentOtpNotification($otp, $total));
            PaymentSecurityLog::log(PaymentSecurityLog::OTP_SENT, ['resend' => true]);
        } catch (\Exception $e) {
            report($e);
        }

        return back()->with('success', 'Un nouveau code a été envoyé à votre adresse email.');
    }

    // ─────────────────────────────────────────────────────────────────

    private function maskEmail(string $email): string
    {
        [$local, $domain] = explode('@', $email, 2);
        $visible = min(2, strlen($local));
        return substr($local, 0, $visible) . str_repeat('*', max(strlen($local) - $visible, 3)) . '@' . $domain;
    }
}
