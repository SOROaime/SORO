<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'price',
        'stock',
        'image',
        'category',
        'is_active',
    ];

    protected $casts = [
        'price'     => 'decimal:2',
        'is_active' => 'boolean',
    ];

    // ========================
    // SCOPES
    // ========================

    /** Retourne uniquement les produits actifs */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /** Retourne les produits en stock */
    public function scopeInStock($query)
    {
        return $query->where('stock', '>', 0);
    }

    /** Filtre par catégorie */
    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    // ========================
    // ACCESSORS
    // ========================

    /** Prix formaté en FCFA */
    public function getFormattedPriceAttribute(): string
    {
        return number_format($this->price, 0, ',', ' ') . ' FCFA';
    }

    /** URL de l'image (image par défaut si non définie) */
    public function getImageUrlAttribute(): string
    {
        if ($this->image && file_exists(public_path('storage/' . $this->image))) {
            return asset('storage/' . $this->image);
        }
        // Placeholder externe en HTTPS (pas de mixed content)
        return 'https://placehold.co/400x220/e2e8f0/94a3b8?text=' . urlencode($this->name ?? 'Produit');
    }

    /** Vérifie si le produit est disponible */
    public function isAvailable(): bool
    {
        return $this->is_active && $this->stock > 0;
    }

    // ========================
    // RELATIONS
    // ========================

    /** Articles du panier liés à ce produit */
    public function cartItems()
    {
        return $this->hasMany(CartItem::class);
    }

    /** Articles de commande liés à ce produit */
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }
}
