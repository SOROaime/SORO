<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        // Force toujours l'URL complète (host + schéma) depuis APP_URL
        $appUrl = config('app.url');
        if ($appUrl) {
            URL::forceRootUrl($appUrl);
            if (str_starts_with($appUrl, 'https://')) {
                URL::forceScheme('https');
            }
        }

        // Exclure le webhook GeniusPay du CSRF (appel serveur→serveur)
        \Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::except([
            'webhook/geniuspay',
        ]);

        $this->configureRateLimiting();
    }

    private function configureRateLimiting(): void
    {
        // Connexion : 5 tentatives / minute par IP+email (anti brute-force)
        RateLimiter::for('login', function (Request $request) {
            return Limit::perMinute(5)
                ->by($request->input('email', '') . '|' . $request->ip())
                ->response(function () {
                    return response()->view('errors.429', [], 429);
                });
        });

        // Inscription : 3 comptes / heure par IP
        RateLimiter::for('register', function (Request $request) {
            return Limit::perHour(3)->by($request->ip());
        });

        // Réinitialisation mot de passe : 3 / heure par IP
        RateLimiter::for('password-reset', function (Request $request) {
            return Limit::perHour(3)->by($request->ip());
        });

        // Paiement : 5 / minute par utilisateur (déjà dans les routes, doublon sécurité)
        RateLimiter::for('payment', function (Request $request) {
            return Limit::perMinute(5)->by($request->user()?->id ?? $request->ip());
        });

        // Chatbot : 30 messages / minute par session
        RateLimiter::for('chat', function (Request $request) {
            return Limit::perMinute(30)->by($request->session()->getId());
        });

        // Contact : 5 / heure par IP (anti spam)
        RateLimiter::for('contact', function (Request $request) {
            return Limit::perHour(5)->by($request->ip());
        });
    }
}
