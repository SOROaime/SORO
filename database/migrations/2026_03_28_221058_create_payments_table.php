<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Table payments : enregistrement des transactions de paiement.
     * IMPORTANT : Ne jamais stocker de données bancaires complètes ici !
     * Seule la référence de transaction et le statut sont conservés.
     */
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->string('transaction_reference')->unique(); // Référence unique générée
            $table->decimal('amount', 10, 2);                  // Montant payé
            $table->enum('status', ['pending', 'success', 'failed', 'refunded'])->default('pending');
            $table->string('payment_method')->default('card'); // card, paypal, etc.
            // Sécurité : on ne stocke QUE les 4 derniers chiffres (jamais le numéro complet)
            $table->string('card_last_four')->nullable();
            $table->string('card_holder_name')->nullable();
            $table->timestamp('paid_at')->nullable();          // Date du paiement réussi
            $table->text('failure_reason')->nullable();        // Raison en cas d'échec
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
