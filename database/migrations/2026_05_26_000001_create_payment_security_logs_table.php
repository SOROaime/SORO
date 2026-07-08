<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Couche 10 — Audit Trail
 *
 * Table payment_security_logs : enregistre chaque événement de sécurité
 * lié aux paiements (OTP, fraude, webhooks, accès non autorisés…).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payment_security_logs', function (Blueprint $table) {
            $table->id();

            // Type d'événement (ex: otp_sent, payment_success, fraud_detected)
            $table->string('event_type', 60)->index();

            // Utilisateur concerné (nullable car webhooks sont sans user connecté)
            $table->foreignId('user_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            // Commande concernée (nullable car OTP envoyé avant création commande)
            $table->foreignId('order_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            // Contexte réseau
            $table->string('ip_address', 45);
            $table->text('user_agent')->nullable();

            // Données arbitraires en JSON (référence, montant, raison, etc.)
            $table->json('metadata')->nullable();

            $table->timestamps();

            // Index composite pour les requêtes de détection de fraude
            $table->index(['user_id', 'event_type', 'created_at'], 'idx_user_event_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_security_logs');
    }
};
