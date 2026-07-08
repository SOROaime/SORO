<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Couche 1 — Force HTTPS/TLS
 *
 * En production : redirige toute requête HTTP vers HTTPS (301 permanent).
 * En développement : laisse passer pour ne pas bloquer les tests locaux.
 *
 * Démonstration jury : en prod, tenter d'accéder en http:// renvoie
 * automatiquement vers https:// avec code 301.
 */
class ForceHttpsMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->secure() && app()->environment('production')) {
            return redirect()->secure($request->getRequestUri(), 301);
        }

        return $next($request);
    }
}
