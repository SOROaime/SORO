<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware AdminMiddleware
 * 
 * Protège les routes réservées aux administrateurs.
 * Vérifie que l'utilisateur est connecté ET qu'il a le rôle 'admin'.
 */
class AdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        // Vérifier que l'utilisateur est connecté
        if (!auth()->check()) {
            return redirect()->route('login')
                ->with('error', 'Vous devez être connecté pour accéder à cette page.');
        }

        // Vérifier que l'utilisateur est admin
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Accès refusé. Vous n\'avez pas les droits administrateur.');
        }

        return $next($request);
    }
}
