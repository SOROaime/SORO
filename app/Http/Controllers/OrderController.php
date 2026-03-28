<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;

/**
 * OrderController — Gestion des commandes
 * 
 * Transformation du panier en commande,
 * suivi des commandes utilisateur.
 */
class OrderController extends Controller
{
    /** Liste des commandes de l'utilisateur connecté */
    public function index()
    {
        $orders = Order::where('user_id', auth()->id())
                       ->with('items', 'payment')
                       ->latest()
                       ->paginate(10);

        return view('orders.index', compact('orders'));
    }

    /** Détail d'une commande */
    public function show(Order $order)
    {
        // Sécurité : seul le propriétaire ou un admin peut voir la commande
        if ($order->user_id !== auth()->id() && !auth()->user()->isAdmin()) {
            abort(403);
        }

        $order->load('items.product', 'payment', 'user');

        return view('orders.show', compact('order'));
    }

    /**
     * Crée une commande à partir du panier actif.
     * Appelé avant le paiement (checkout).
     */
    public function checkout()
    {
        $cart = Cart::getOrCreateActive(auth()->id());
        $cart->load('items.product');

        // Vérifications
        if ($cart->items->isEmpty()) {
            return redirect()->route('cart.index')
                ->with('error', 'Votre panier est vide.');
        }

        // Vérifier la disponibilité de tous les articles
        foreach ($cart->items as $item) {
            if (!$item->product->isAvailable()) {
                return redirect()->route('cart.index')
                    ->with('error', "Le produit \"{$item->product->name}\" n'est plus disponible.");
            }

            if ($item->product->stock < $item->quantity) {
                return redirect()->route('cart.index')
                    ->with('error', "Stock insuffisant pour \"{$item->product->name}\". Max : {$item->product->stock}.");
            }
        }

        return view('orders.checkout', compact('cart'));
    }

    /**
     * Annuler une commande
     * (seulement si statut 'pending' ou 'paid')
     */
    public function cancel(Order $order)
    {
        if ($order->user_id !== auth()->id()) {
            abort(403);
        }

        if (!$order->canBeCancelled()) {
            return back()->with('error', 'Cette commande ne peut plus être annulée.');
        }

        $order->update(['status' => 'cancelled']);

        return back()->with('success', "La commande #{$order->order_number} a été annulée.");
    }
}
