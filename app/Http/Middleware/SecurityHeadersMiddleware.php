<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Injecte les headers HTTP de sécurité sur chaque réponse.
 *
 * Démonstration jury :
 *  - X-Frame-Options          → empêche le clickjacking (iframe malveillante)
 *  - X-Content-Type-Options   → bloque le MIME-sniffing (injection de scripts)
 *  - X-XSS-Protection         → protection XSS native du navigateur
 *  - Referrer-Policy          → ne transmet pas l'URL complète aux tiers
 *  - Permissions-Policy       → désactive caméra / micro / géolocalisation
 *  - Strict-Transport-Security → force HTTPS (uniquement si connexion sécurisée)
 */
class SecurityHeadersMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-XSS-Protection', '1; mode=block');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set('Permissions-Policy', 'geolocation=(), camera=()'); // microphone autorisé pour Sara
        $response->headers->set('Content-Security-Policy',
            "default-src 'self'; " .
            "script-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net; " .
            "style-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net https://fonts.googleapis.com; " .
            "font-src 'self' https://fonts.gstatic.com https://cdn.jsdelivr.net; " .
            "img-src 'self' https: data:; " .
            "connect-src 'self' https://api.groq.com; " .
            "frame-ancestors 'none'"
        );

        // HSTS uniquement en HTTPS (production)
        if ($request->secure()) {
            $response->headers->set(
                'Strict-Transport-Security',
                'max-age=31536000; includeSubDomains; preload'
            );
        }

        // Bypass la page d'avertissement ngrok (utile en développement/démo)
        if (str_contains($request->getHost(), 'ngrok')) {
            $response->headers->set('ngrok-skip-browser-warning', '1');
        }

        return $response;
    }
}
