<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Http\Request;

/**
 * CartController — Gestion du panier d'achat
 * 
 * Le panier est persistant en base de données (table carts + cart_items).
 * Chaque utilisateur authentifié a un panier actif.
 */
class CartController extends Controller
{
    /** Afficher le panier */
    public function index()
    {
        $cart = Cart::getOrCreateActive(auth()->id());
        $cart->load('items.product'); // Chargement eager des relations

        return view('cart.index', compact('cart'));
    }

    /** Ajouter un produit au panier */
    public function add(Request $request)
    {
        $request->validate([
            'product_id' => ['required', 'integer', 'exists:products,id'],
            'quantity'   => ['required', 'integer', 'min:1', 'max:99'],
        ]);

        $product = Product::findOrFail($request->product_id);

        // Vérifier disponibilité
        if (!$product->isAvailable()) {
            return back()->with('error', 'Ce produit n\'est pas disponible.');
        }

        // Vérifier le stock
        if ($product->stock < $request->quantity) {
            return back()->with('error', "Stock insuffisant. Seulement {$product->stock} disponible(s).");
        }

        // Récupérer ou créer le panier actif
        $cart = Cart::getOrCreateActive(auth()->id());

        // Chercher si le produit est déjà dans le panier
        $cartItem = CartItem::where('cart_id', $cart->id)
                            ->where('product_id', $product->id)
                            ->first();

        if ($cartItem) {
            // Mettre à jour la quantité
            $newQuantity = $cartItem->quantity + $request->quantity;

            if ($newQuantity > $product->stock) {
                return back()->with('error', "Vous ne pouvez pas ajouter plus de {$product->stock} unité(s).");
            }

            $cartItem->update(['quantity' => $newQuantity]);
        } else {
            // Ajouter un nouvel article (snapshot du prix actuel)
            CartItem::create([
                'cart_id'    => $cart->id,
                'product_id' => $product->id,
                'quantity'   => $request->quantity,
                'price'      => $product->price, // Prix figé au moment de l'ajout
            ]);
        }

        return redirect()->route('cart.index')
            ->with('success', "\"{$product->name}\" ajouté au panier !");
    }

    /** Mettre à jour la quantité d'un article */
    public function update(Request $request, CartItem $cartItem)
    {
        $this->authorizeCartItem($cartItem);

        $request->validate([
            'quantity' => ['required', 'integer', 'min:1', 'max:99'],
        ]);

        if ($cartItem->product->stock < $request->quantity) {
            if ($request->wantsJson()) {
                return response()->json([
                    'ok'      => false,
                    'message' => "Stock insuffisant. Maximum : {$cartItem->product->stock}.",
                    'max'     => $cartItem->product->stock,
                ], 422);
            }
            return back()->with('error', "Stock insuffisant. Maximum : {$cartItem->product->stock}.");
        }

        $cartItem->update(['quantity' => $request->quantity]);

        if ($request->wantsJson()) {
            // Recharger le panier pour calculer le nouveau total
            $cart = $cartItem->cart()->with('items.product')->first();
            return response()->json([
                'ok'       => true,
                'subtotal' => $cartItem->fresh()->formatted_subtotal,
                'total'    => $cart->formatted_total,
                'count'    => $cart->items->sum('quantity'),
            ]);
        }

        return back()->with('success', 'Quantité mise à jour.');
    }

    /** Supprimer un article du panier */
    public function remove(CartItem $cartItem)
    {
        $this->authorizeCartItem($cartItem);

        $productName = $cartItem->product->name;
        $cartItem->delete();

        return back()->with('success', "\"{$productName}\" retiré du panier.");
    }

    /** Vider entièrement le panier */
    public function clear()
    {
        $cart = Cart::getOrCreateActive(auth()->id());
        $cart->clear();

        return back()->with('success', 'Le panier a été vidé.');
    }

    // ========================
    // MÉTHODE PRIVÉE
    // ========================

    /** Vérifie que l'article du panier appartient bien à l'utilisateur connecté */
    private function authorizeCartItem(CartItem $cartItem): void
    {
        $cart = Cart::getOrCreateActive(auth()->id());

        if ($cartItem->cart_id !== $cart->id) {
            abort(403, 'Action non autorisée.');
        }
    }
}
