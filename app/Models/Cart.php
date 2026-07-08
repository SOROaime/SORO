<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'status',
    ];

    // ========================
    // RELATIONS
    // ========================

    /** Le panier appartient à un utilisateur */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /** Un panier contient plusieurs articles */
    public function items()
    {
        return $this->hasMany(CartItem::class);
    }

    /** Articles avec les produits chargés (eager loading) */
    public function itemsWithProducts()
    {
        return $this->hasMany(CartItem::class)->with('product');
    }

    // ========================
    // ACCESSORS / CALCULÉS
    // ========================

    /** Nombre total d'articles dans le panier */
    public function getTotalItemsAttribute(): int
    {
        return $this->items->sum('quantity');
    }

    /** Montant total du panier */
    public function getTotalAmountAttribute(): float
    {
        return $this->items->sum(function ($item) {
            return $item->price * $item->quantity;
        });
    }

    /** Montant total formaté */
    public function getFormattedTotalAttribute(): string
    {
        return number_format($this->total_amount, 0, ',', ' ') . ' FCFA';
    }

    // ========================
    // MÉTHODES MÉTIER
    // ========================

    /** Récupère ou crée le panier actif pour un utilisateur */
    public static function getOrCreateActive(int $userId): self
    {
        return self::firstOrCreate(
            ['user_id' => $userId, 'status' => 'active']
        );
    }

    /** Vide le panier */
    public function clear(): void
    {
        $this->items()->delete();
    }

    /** Marque le panier comme converti en commande */
    public function markAsConverted(): void
    {
        // Supprimer les anciens paniers de cet utilisateur (converted ou abandoned)
        // pour éviter la violation de contrainte unique (user_id, status).
        self::where('user_id', $this->user_id)
            ->where('id', '!=', $this->id)
            ->whereIn('status', ['converted', 'abandoned'])
            ->delete();

        $this->update(['status' => 'converted']);
    }
}
