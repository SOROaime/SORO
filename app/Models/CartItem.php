<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CartItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'cart_id',
        'product_id',
        'quantity',
        'price',
    ];

    protected $casts = [
        'price' => 'decimal:2',
    ];

    // ========================
    // RELATIONS
    // ========================

    /** Un article appartient à un panier */
    public function cart()
    {
        return $this->belongsTo(Cart::class);
    }

    /** Un article est lié à un produit */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // ========================
    // ACCESSORS
    // ========================

    /** Sous-total de la ligne (prix × quantité) */
    public function getSubtotalAttribute(): float
    {
        return $this->price * $this->quantity;
    }

    /** Sous-total formaté */
    public function getFormattedSubtotalAttribute(): string
    {
        return number_format($this->subtotal, 0, ',', ' ') . ' FCFA';
    }
}
