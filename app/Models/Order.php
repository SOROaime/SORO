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
        'shipping_city',
        'shipping_postal_code',
        'notes',
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
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
        return number_format($this->total_amount, 2, ',', ' ') . ' €';
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

    /** Vérifie si la commande peut être annulée */
    public function canBeCancelled(): bool
    {
        return in_array($this->status, ['pending', 'paid']);
    }
}
