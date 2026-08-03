<?php

namespace App\Http\Middleware;

use App\Http\Responses\ApiResponse;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // Contrôle d'accès côté serveur (OWASP A01) : l'appartenance admin est vérifiée
        // à chaque requête, jamais côté interface uniquement.
        if ($user === null || $user->is_admin !== true) {
            if ($request->is('api/*')) {
                return ApiResponse::error('Accès refusé', 'FORBIDDEN', null, 403);
            }

            abort(403);
        }

        return $next($request);
    }
}
