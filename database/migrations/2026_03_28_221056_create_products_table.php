<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Table products : catalogue des produits de la boutique.
     * Contient : nom, description, prix, stock, image, catégorie.
     */
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');                          // Nom du produit
            $table->text('description');                     // Description détaillée
            $table->decimal('price', 10, 2);                 // Prix HT (ex: 29.99)
            $table->integer('stock')->default(0);            // Quantité en stock
            $table->string('image')->nullable();             // Chemin de l'image
            $table->string('category')->nullable();          // Catégorie du produit
            $table->boolean('is_active')->default(true);     // Produit visible ou non
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
