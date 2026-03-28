<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Http\Requests\ProcessPaymentRequest;
use Illuminate\Support\Facades\DB;

/**
 * PaymentController — Simulation sécurisée du paiement
 * 
 * IMPORTANT : Ce contrôleur simule un paiement.
 * Dans un vrai projet, remplacer processPayment() par l'API Stripe/PayPal.
 * 
 * SÉCURITÉ :
 * - On ne stocke JAMAIS le numéro de carte complet
 * - On ne stocke JAMAIS le CVV
 * - Seuls les 4 derniers chiffres sont enregistrés
 */
class PaymentController extends Controller
{
    /**
     * Traite le paiement et crée la commande.
     * Utilise une transaction DB pour garantir l'intégrité des données.
     */
    public function process(ProcessPaymentRequest $request)
    {
        $data = $request->validated();

        // Récupérer le panier actif
        $cart = Cart::getOrCreateActive(auth()->id());
        $cart->load('items.product');

        // Vérifications finales
        if ($cart->items->isEmpty()) {
            return redirect()->route('cart.index')
                ->with('error', 'Votre panier est vide.');
        }

        // Transaction DB : si une étape échoue, tout est annulé
        try {
            $order = DB::transaction(function () use ($cart, $data) {

                // 1. CRÉER LA COMMANDE
                $order = Order::create([
                    'user_id'              => auth()->id(),
                    'order_number'         => Order::generateOrderNumber(),
                    'total_amount'         => $cart->total_amount,
                    'status'               => 'pending',
                    'shipping_address'     => $data['shipping_address'],
                    'shipping_city'        => $data['shipping_city'],
                    'shipping_postal_code' => $data['shipping_postal_code'],
                    'notes'                => $data['notes'] ?? null,
                ]);

                // 2. CRÉER LES ARTICLES DE COMMANDE (snapshot)
                foreach ($cart->items as $item) {
                    OrderItem::create([
                        'order_id'     => $order->id,
                        'product_id'   => $item->product_id,
                        'product_name' => $item->product->name,     // Snapshot du nom
                        'quantity'     => $item->quantity,
                        'unit_price'   => $item->price,
                        'subtotal'     => $item->price * $item->quantity,
                    ]);

                    // 3. DÉCRÉMENTER LE STOCK
                    $item->product->decrement('stock', $item->quantity);
                }

                // 4. SIMULER LE TRAITEMENT DU PAIEMENT
                //    Dans la réalité : appel API Stripe ici
                $paymentSuccess = Payment::processPayment($data);

                // 5. CRÉER L'ENREGISTREMENT DE PAIEMENT
                //    SÉCURITÉ : on extrait uniquement les 4 derniers chiffres
                $cardLastFour = substr(preg_replace('/\s+/', '', $data['card_number']), -4);

                $payment = Payment::create([
                    'order_id'              => $order->id,
                    'transaction_reference' => Payment::generateTransactionReference(),
                    'amount'                => $cart->total_amount,
                    'status'                => $paymentSuccess ? 'success' : 'failed',
                    'payment_method'        => 'card',
                    'card_last_four'        => $cardLastFour,       // Seulement 4 chiffres !
                    'card_holder_name'      => $data['card_holder_name'],
                    'paid_at'               => $paymentSuccess ? now() : null,
                    'failure_reason'        => $paymentSuccess ? null : 'Paiement refusé par la banque.',
                ]);

                // 6. METTRE À JOUR LE STATUT DE LA COMMANDE
                if ($paymentSuccess) {
                    $order->update(['status' => 'paid']);
                    // Marquer le panier comme converti
                    $cart->markAsConverted();
                } else {
                    // Paiement échoué : remettre le stock
                    foreach ($cart->items as $item) {
                        $item->product->increment('stock', $item->quantity);
                    }
                    $order->update(['status' => 'cancelled']);
                }

                return $order;
            });

            // Redirection selon le résultat
            if ($order->payment->status === 'success') {
                return redirect()->route('payment.success', $order)
                    ->with('success', 'Paiement effectué avec succès !');
            } else {
                return redirect()->route('payment.failed', $order)
                    ->with('error', 'Le paiement a échoué. Veuillez réessayer.');
            }

        } catch (\Exception $e) {
            return redirect()->route('cart.index')
                ->with('error', 'Une erreur est survenue. Veuillez réessayer.');
        }
    }

    /** Page de confirmation paiement réussi */
    public function success(Order $order)
    {
        if ($order->user_id !== auth()->id()) {
            abort(403);
        }

        $order->load('items', 'payment');
        return view('payment.success', compact('order'));
    }

    /** Page d'échec du paiement */
    public function failed(Order $order)
    {
        if ($order->user_id !== auth()->id()) {
            abort(403);
        }

        $order->load('payment');
        return view('payment.failed', compact('order'));
    }
}
