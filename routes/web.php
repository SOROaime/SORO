<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\CouponController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\WishlistController;
use App\Http\Controllers\ReviewController;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Illuminate\Support\Facades\Route;

// ============================================================
// ROUTES PUBLIQUES (accessibles sans authentification)
// ============================================================

// ── Chatbot (accessible à tous, connecté ou non) ─────────────
Route::post('/chat/send', [ChatController::class, 'send'])->name('chat.send')->middleware('throttle:chat');
Route::get('/chat/history', [ChatController::class, 'history'])->name('chat.history');

// Page d'accueil
Route::get('/', [ProductController::class, 'home'])->name('home');

// Pages statiques
Route::get('/contact', [ContactController::class, 'show'])->name('contact');
Route::post('/contact', [ContactController::class, 'send'])->name('contact.send')->middleware('throttle:contact');
Route::view('/cgv', 'cgv')->name('cgv');

// Sitemap XML pour les moteurs de recherche
Route::get('/sitemap.xml', function () {
    $path = public_path('sitemap.xml');
    if (!file_exists($path)) {
        \Artisan::call('sitemap:generate');
    }
    return response()->file($path, ['Content-Type' => 'application/xml']);
})->name('sitemap');

// Catalogue produits
Route::get('/produits', [ProductController::class, 'index'])->name('products.index');
Route::get('/produits/{product}', [ProductController::class, 'show'])->name('products.show');

// ============================================================
// ROUTES D'AUTHENTIFICATION (invités seulement)
// ============================================================
Route::middleware('guest')->group(function () {
    Route::get('/inscription', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/inscription', [AuthController::class, 'register'])->middleware('throttle:register');

    Route::get('/connexion', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/connexion', [AuthController::class, 'login'])->middleware('throttle:login');
});

// Déconnexion (utilisateurs connectés)
Route::post('/deconnexion', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

// ============================================================
// VÉRIFICATION EMAIL
// ============================================================
Route::middleware('auth')->group(function () {
    Route::get('/email/verifier', function () {
        return view('auth.verify-email');
    })->name('verification.notice');

    Route::get('/email/verifier/{id}/{hash}', function (\Illuminate\Foundation\Auth\EmailVerificationRequest $request) {
        $request->fulfill();
        return redirect()->route('home')->with('success', 'Email vérifié avec succès ! Bienvenue sur ShopCI.');
    })->middleware('signed')->name('verification.verify');

    Route::post('/email/renvoyer', function (\Illuminate\Http\Request $request) {
        $request->user()->sendEmailVerificationNotification();
        return back()->with('success', 'Un nouvel email de vérification vous a été envoyé.');
    })->middleware('throttle:3,1')->name('verification.send');
});

// ============================================================
// RÉINITIALISATION DU MOT DE PASSE (invités)
// ============================================================
Route::get('/mot-de-passe-oublie', [PasswordResetController::class, 'showForgot'])->name('password.request');
Route::post('/mot-de-passe-oublie', [PasswordResetController::class, 'sendResetLink'])->name('password.email');
Route::get('/reinitialiser-mot-de-passe/{token}', [PasswordResetController::class, 'showReset'])->name('password.reset');
Route::post('/reinitialiser-mot-de-passe', [PasswordResetController::class, 'reset'])->name('password.update');

// ============================================================
// ROUTES PROTÉGÉES (utilisateurs connectés)
// ============================================================
Route::middleware('auth')->group(function () {

    // --- PANIER (email vérifié requis pour acheter) ---
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

    // --- AVIS PRODUITS ---
    Route::post('/produits/{product}/avis', [ReviewController::class, 'store'])->name('reviews.store');
    Route::delete('/avis/{review}', [ReviewController::class, 'destroy'])->name('reviews.destroy');

    // --- COUPON ---
    Route::post('/coupon/verifier', [CouponController::class, 'check'])->name('coupon.check');

    // --- PAIEMENT ---
    Route::middleware('throttle:5,1')->group(function () {
        Route::post('/paiement/traiter', [PaymentController::class, 'process'])->name('payment.process');
        Route::post('/commande/{order}/tranche/{installment}/payer', [PaymentController::class, 'payInstallment'])->name('installment.pay');
    });

    Route::get('/paiement/attente/{order}', [PaymentController::class, 'waiting'])->name('payment.waiting');
    Route::get('/paiement/statut/{order}', [PaymentController::class, 'status'])->name('payment.status');
    Route::get('/paiement/retour/{order}', [PaymentController::class, 'callback'])->name('payment.callback');
    Route::get('/paiement/succes/{order}', [PaymentController::class, 'success'])->name('payment.success');
    Route::get('/paiement/echec/{order}', [PaymentController::class, 'failed'])->name('payment.failed');

    // --- TRANCHES (paiement client) ---
    Route::get('/paiement/retour/{order}/tranche/{installment}', [PaymentController::class, 'installmentCallback'])->name('installment.callback');

    // --- PROFIL UTILISATEUR ---
    Route::get('/profil', [ProfileController::class, 'show'])->name('profile.show');
    Route::post('/profil', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profil/mot-de-passe', [ProfileController::class, 'updatePassword'])->name('profile.password');
});

// ============================================================
// WEBHOOK GENIUSPAY (sans CSRF, sans auth — appel serveur→serveur)
// throttle:120,1 → max 120 requêtes/min par IP (protection DDoS sur le webhook)
// ============================================================
Route::post('/webhook/geniuspay', [PaymentController::class, 'webhook'])
    ->withoutMiddleware(ValidateCsrfToken::class)
    ->middleware('throttle:120,1')
    ->name('webhook.geniuspay');

// GET pour la vérification d'URL par GeniusPay lors de la configuration
Route::get('/webhook/geniuspay', function () {
    return response()->json(['status' => 'ok', 'service' => 'ShopCI Webhook']);
})->withoutMiddleware(ValidateCsrfToken::class);

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
    Route::get('/tranches', [AdminController::class, 'installments'])->name('installments.index');
    Route::patch('/commandes/{order}/tranches/{installment}/payer', [AdminController::class, 'markInstallmentPaid'])->name('installments.pay');

    // Gestion des paiements
    Route::get('/paiements', [AdminController::class, 'payments'])->name('payments.index');

    // Gestion des utilisateurs
    Route::get('/utilisateurs', [AdminController::class, 'users'])->name('users.index');

    // Gestion des coupons
    Route::resource('coupons', CouponController::class)->except(['show']);

    // Rapports & statistiques
    Route::get('/rapports', [AdminController::class, 'reports'])->name('reports');
    Route::get('/rapports/export', [AdminController::class, 'exportSales'])->name('reports.export');

    // Conversations chatbot
    Route::get('/conversations', [ChatController::class, 'adminIndex'])->name('conversations.index');
    Route::get('/conversations/{conversation}', [ChatController::class, 'adminShow'])->name('conversations.show');

    // Gestion des avis
    Route::get('/avis', [AdminController::class, 'reviews'])->name('reviews.index');
    Route::delete('/avis/{review}', [AdminController::class, 'destroyReview'])->name('reviews.destroy');
});
