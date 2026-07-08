<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Faire confiance aux proxies connus (ngrok, Cloudflare)
        $middleware->trustProxies(at: '*', headers: \Illuminate\Http\Request::HEADER_X_FORWARDED_FOR |
            \Illuminate\Http\Request::HEADER_X_FORWARDED_HOST |
            \Illuminate\Http\Request::HEADER_X_FORWARDED_PORT |
            \Illuminate\Http\Request::HEADER_X_FORWARDED_PROTO);

        // Exclure le webhook GeniusPay de la vérification CSRF (appel serveur→serveur)
        $middleware->validateCsrfTokens(except: [
            'webhook/geniuspay',
            '/webhook/geniuspay',
            'webhook/*',
        ]);

        // Couche 1 : Force HTTPS en production (redirection 301 HTTP → HTTPS)
        $middleware->web(prepend: [
            \App\Http\Middleware\ForceHttpsMiddleware::class,
        ]);

        // Couche 2 : Headers HTTP de sécurité appliqués à toutes les réponses
        $middleware->web(append: [
            \App\Http\Middleware\SecurityHeadersMiddleware::class,
        ]);

        // Enregistrement du middleware admin avec alias 'admin'
        $middleware->alias([
            'admin' => \App\Http\Middleware\AdminMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
