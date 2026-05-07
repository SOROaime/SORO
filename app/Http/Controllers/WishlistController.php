<?php

namespace App\Http\Controllers;

use App\Models\Wishlist;
use App\Models\Product;

class WishlistController extends Controller
{
    /** Page "Mes Favoris" */
    public function index()
    {
        $favorites = Wishlist::where('user_id', auth()->id())
            ->with('product')
            ->latest()
            ->get()
            ->filter(fn($w) => $w->product && $w->product->is_active);

        return view('wishlist.index', compact('favorites'));
    }

    /** Toggle favori (AJAX ou redirect) */
    public function toggle(Product $product)
    {
        $added = Wishlist::toggle(auth()->id(), $product->id);

        if (request()->expectsJson()) {
            return response()->json([
                'added'   => $added,
                'count'   => Wishlist::where('user_id', auth()->id())->count(),
                'message' => $added ? 'Ajouté aux favoris' : 'Retiré des favoris',
            ]);
        }

        return back()->with(
            $added ? 'success' : 'info',
            $added ? "« {$product->name} » ajouté aux favoris !" : "« {$product->name} » retiré des favoris."
        );
    }
}
