<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Couche 10 — Audit Trail + Détection de fraude
 *
 * Enregistre chaque événement significatif du système de paiement.
 * Sert aussi de base pour la détection de comportements suspects.
 */
class PaymentSecurityLog extends Model
{
    // ── Constantes des types d'événements ────────────────────────────
    const PAYMENT_INITIATED   = 'payment_initiated';
    const OTP_SENT            = 'otp_sent';
    const OTP_VERIFIED        = 'otp_verified';
    const OTP_FAILED          = 'otp_failed';
    const OTP_BLOCKED         = 'otp_blocked';
    const PAYMENT_SUCCESS     = 'payment_success';
    const PAYMENT_FAILED      = 'payment_failed';
    const WEBHOOK_RECEIVED    = 'webhook_received';
    const FRAUD_DETECTED      = 'fraud_detected';
    const UNAUTHORIZED_ACCESS = 'unauthorized_access';

    protected $fillable = [
        'event_type',
        'user_id',
        'order_id',
        'ip_address',
        'user_agent',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
    ];

    // ── Relations ─────────────────────────────────────────────────────

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    // ── Méthode centrale de logging ───────────────────────────────────

    /**
     * Enregistre un événement de sécurité.
     *
     * @param  string    $event    Constante de type (OTP_SENT, FRAUD_DETECTED…)
     * @param  array     $metadata Données contextuelles (montant, raison, etc.)
     * @param  int|null  $userId   Optionnel si différent de l'utilisateur connecté
     * @param  int|null  $orderId  Identifiant de la commande concernée
     */
    public static function log(
        string $event,
        array  $metadata = [],
        ?int   $userId   = null,
        ?int   $orderId  = null
    ): self {
        return self::create([
            'event_type' => $event,
            'user_id'    => $userId ?? auth()->id(),
            'order_id'   => $orderId,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'metadata'   => $metadata ?: null,
        ]);
    }

    // ── Détection de fraude ───────────────────────────────────────────

    /**
     * Couche 10 — Détection : l'utilisateur est-il bloqué suite à trop d'OTP échoués ?
     * Règle : >= 3 échecs OTP dans les 10 dernières minutes.
     */
    public static function isOtpBlocked(int $userId): bool
    {
        return self::where('user_id', $userId)
            ->where('event_type', self::OTP_FAILED)
            ->where('created_at', '>=', now()->subMinutes(10))
            ->count() >= 3;
    }

    /**
     * Couche 10 — Détection : activité de paiement suspecte ?
     * Règle : >= 5 paiements échoués dans la dernière heure.
     */
    public static function hasSuspiciousPaymentActivity(int $userId): bool
    {
        return self::where('user_id', $userId)
            ->where('event_type', self::PAYMENT_FAILED)
            ->where('created_at', '>=', now()->subHour())
            ->count() >= 5;
    }
}
