<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Table orders : commandes validées.
     * Transformées depuis un panier, avec statut de traitement.
     */
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('order_number')->unique();        // Ex: ORD-20240328-00001
            $table->decimal('total_amount', 10, 2);          // Total de la commande
            $table->enum('status', [
                'pending',      // En attente de paiement
                'paid',         // Payée
                'processing',   // En cours de préparation
                'shipped',      // Expédiée
                'delivered',    // Livrée
                'cancelled',    // Annulée
                'refunded'      // Remboursée
            ])->default('pending');
            $table->text('shipping_address')->nullable();    // Adresse de livraison
            $table->string('shipping_city')->nullable();
            $table->string('shipping_postal_code')->nullable();
            $table->text('notes')->nullable();               // Notes client
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
