<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'transaction_reference',
        'amount',
        'status',
        'payment_method',
        'card_last_four',
        'card_holder_name',
        'paid_at',
        'failure_reason',
    ];

    protected $casts = [
        'amount'  => 'decimal:2',
        'paid_at' => 'datetime',
    ];

    const STATUS_LABELS = [
        'pending'  => 'En attente',
        'success'  => 'Réussi',
        'failed'   => 'Échoué',
        'refunded' => 'Remboursé',
    ];

    const STATUS_COLORS = [
        'pending'  => 'warning',
        'success'  => 'success',
        'failed'   => 'danger',
        'refunded' => 'secondary',
    ];

    // ========================
    // RELATIONS
    // ========================

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    // ========================
    // ACCESSORS
    // ========================

    public function getStatusLabelAttribute(): string
    {
        return self::STATUS_LABELS[$this->status] ?? $this->status;
    }

    public function getStatusColorAttribute(): string
    {
        return self::STATUS_COLORS[$this->status] ?? 'secondary';
    }

    public function getFormattedAmountAttribute(): string
    {
        return number_format($this->amount, 2, ',', ' ') . ' €';
    }

    // ========================
    // MÉTHODES MÉTIER
    // ========================

    /**
     * Génère une référence de transaction unique.
     * Format : TXN-YYYYMMDD-XXXXXXXX (ex: TXN-20240328-A3F9D2B1)
     */
    public static function generateTransactionReference(): string
    {
        return 'TXN-' . now()->format('Ymd') . '-' . strtoupper(Str::random(8));
    }

    /**
     * Simule le traitement du paiement.
     * Dans un vrai projet, ici on appellerait l'API Stripe/PayPal.
     * 
     * @param  array  $cardData  Données de la carte (non stockées en entier)
     * @return bool   true si paiement réussi, false sinon
     */
    public static function processPayment(array $cardData): bool
    {
        // Simulation : 90% de succès, 10% d'échec
        // Dans la réalité : appel API Stripe, PayPal, etc.
        return rand(1, 10) > 1;
    }
}
