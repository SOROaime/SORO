<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Table order_items : détail des articles d'une commande.
     * Snapshot des produits au moment de la commande (prix figé).
     */
    public function up(): void
    {
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_id')->constrained()->onDelete('restrict');
            $table->string('product_name');         // Copie du nom (en cas de modification)
            $table->integer('quantity');
            $table->decimal('unit_price', 10, 2);   // Prix unitaire au moment de l'achat
            $table->decimal('subtotal', 10, 2);     // quantity * unit_price
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
