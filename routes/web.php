<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\WishlistController;
use Illuminate\Support\Facades\Route;

// ============================================================
// ROUTES PUBLIQUES (accessibles sans authentification)
// ============================================================

// Page d'accueil
Route::get('/', [ProductController::class, 'home'])->name('home');

// Catalogue produits
Route::get('/produits', [ProductController::class, 'index'])->name('products.index');
Route::get('/produits/{product}', [ProductController::class, 'show'])->name('products.show');

// ============================================================
// ROUTES D'AUTHENTIFICATION (invités seulement)
// ============================================================
Route::middleware('guest')->group(function () {
    Route::get('/inscription', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/inscription', [AuthController::class, 'register']);

    Route::get('/connexion', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/connexion', [AuthController::class, 'login']);
});

// Déconnexion (utilisateurs connectés)
Route::post('/deconnexion', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

// ============================================================
// ROUTES PROTÉGÉES (utilisateurs connectés)
// ============================================================
Route::middleware('auth')->group(function () {

    // --- PANIER ---
    Route::get('/panier', [CartController::class, 'index'])->name('cart.index');
    Route::post('/panier/ajouter', [CartController::class, 'add'])->name('cart.add');
    Route::patch('/panier/article/{cartItem}', [CartController::class, 'update'])->name('cart.update');
    Route::delete('/panier/article/{cartItem}', [CartController::class, 'remove'])->name('cart.remove');
    Route::delete('/panier/vider', [CartController::class, 'clear'])->name('cart.clear');

    // --- COMMANDES ---
    Route::get('/mes-commandes', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/commande/{order}', [OrderController::class, 'show'])->name('orders.show');
    Route::get('/checkout', [OrderController::class, 'checkout'])->name('orders.checkout');
    Route::post('/commande/{order}/annuler', [OrderController::class, 'cancel'])->name('orders.cancel');

    // --- FAVORIS ---
    Route::get('/favoris', [WishlistController::class, 'index'])->name('wishlist.index');
    Route::post('/favoris/{product}/toggle', [WishlistController::class, 'toggle'])->name('wishlist.toggle');

    // --- PAIEMENT ---
    Route::post('/paiement/traiter', [PaymentController::class, 'process'])->name('payment.process');
    Route::get('/paiement/succes/{order}', [PaymentController::class, 'success'])->name('payment.success');
    Route::get('/paiement/echec/{order}', [PaymentController::class, 'failed'])->name('payment.failed');
});

// ============================================================
// ROUTES ADMIN (admin seulement, middleware 'admin')
// ============================================================
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {

    // Tableau de bord
    Route::get('/', [AdminController::class, 'dashboard'])->name('dashboard');

    // Gestion des produits
    Route::get('/produits', [AdminController::class, 'products'])->name('products.index');
    Route::get('/produits/creer', [ProductController::class, 'create'])->name('products.create');
    Route::post('/produits', [ProductController::class, 'store'])->name('products.store');
    Route::get('/produits/{product}/modifier', [ProductController::class, 'edit'])->name('products.edit');
    Route::put('/produits/{product}', [ProductController::class, 'update'])->name('products.update');
    Route::delete('/produits/{product}', [ProductController::class, 'destroy'])->name('products.destroy');

    // Gestion des commandes
    Route::get('/commandes', [AdminController::class, 'orders'])->name('orders.index');
    Route::get('/commandes/{order}', [AdminController::class, 'showOrder'])->name('orders.show');
    Route::patch('/commandes/{order}/statut', [AdminController::class, 'updateOrderStatus'])->name('orders.status');

    // Gestion des paiements
    Route::get('/paiements', [AdminController::class, 'payments'])->name('payments.index');

    // Gestion des utilisateurs
    Route::get('/utilisateurs', [AdminController::class, 'users'])->name('users.index');
});
