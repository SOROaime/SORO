<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'order_number',
        'total_amount',
        'status',
        'shipping_address',
        'shipping_phone',
        'shipping_city',
        'shipping_commune',
        'shipping_quartier',
        'notes',
        'installment_count',
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        // Couche 7 — Chiffrement AES-256 des données personnelles sensibles
        // Laravel utilise AES-256-CBC avec la clé APP_KEY (.env).
        // Stockées chiffrées en base, déchiffrées automatiquement à la lecture.
        'shipping_address'  => 'encrypted',
        'shipping_phone'    => 'encrypted',
        'shipping_commune'  => 'encrypted',
        'shipping_quartier' => 'encrypted',
        'notes'             => 'encrypted',
    ];

    // Libellés lisibles des statuts
    const STATUS_LABELS = [
        'pending'    => 'En attente',
        'paid'       => 'Payée',
        'processing' => 'En préparation',
        'shipped'    => 'Expédiée',
        'delivered'  => 'Livrée',
        'cancelled'  => 'Annulée',
        'refunded'   => 'Remboursée',
    ];

    // Classes CSS Bootstrap pour les badges de statut
    const STATUS_COLORS = [
        'pending'    => 'warning',
        'paid'       => 'success',
        'processing' => 'info',
        'shipped'    => 'primary',
        'delivered'  => 'success',
        'cancelled'  => 'danger',
        'refunded'   => 'secondary',
    ];

    // ========================
    // RELATIONS
    // ========================

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function payment()
    {
        return $this->hasOne(Payment::class);
    }

    public function installments()
    {
        return $this->hasMany(Installment::class)->orderBy('installment_number');
    }

    // ========================
    // ACCESSORS
    // ========================

    /** Libellé humain du statut */
    public function getStatusLabelAttribute(): string
    {
        return self::STATUS_LABELS[$this->status] ?? $this->status;
    }

    /** Couleur Bootstrap du badge de statut */
    public function getStatusColorAttribute(): string
    {
        return self::STATUS_COLORS[$this->status] ?? 'secondary';
    }

    /** Total formaté */
    public function getFormattedTotalAttribute(): string
    {
        return number_format($this->total_amount, 0, ',', ' ') . ' FCFA';
    }

    // ========================
    // MÉTHODES MÉTIER
    // ========================

    /** Génère un numéro de commande unique */
    public static function generateOrderNumber(): string
    {
        $date = now()->format('Ymd');
        $last = self::where('order_number', 'like', "ORD-{$date}-%")->count();
        return sprintf('ORD-%s-%05d', $date, $last + 1);
    }

    public function hasInstallments(): bool
    {
        return ($this->installment_count ?? 1) > 1;
    }

    public function getPaidInstallmentsCountAttribute(): int
    {
        return $this->installments->where('status', 'paid')->count();
    }

    /** Vérifie si la commande peut être annulée */
    public function canBeCancelled(): bool
    {
        return in_array($this->status, ['pending', 'paid']);
    }

    /**
     * Remet en stock les quantités de chaque article de la commande.
     * À appeler juste avant de passer le statut à 'cancelled'.
     * Ignore les articles dont le produit a été supprimé.
     */
    public function restoreStock(): void
    {
        // Guard : ne restaurer qu'une seule fois (évite le doublement du stock)
        if ($this->status === 'cancelled') return;

        $this->loadMissing('items.product');

        foreach ($this->items as $item) {
            if ($item->product) {
                $item->product->increment('stock', $item->quantity);
            }
        }
    }
}
