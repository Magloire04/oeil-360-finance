<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    /**
     * En-têtes posés sur toutes les réponses (pages web et API).
     * HSTS sans includeSubDomains : les autres sous-domaines de bytechnum.com ne sont pas gérés ici.
     * Les navigateurs ignorent HSTS reçu en http, donc sans effet en local.
     */
    private const HEADERS = [
        'Strict-Transport-Security' => 'max-age=31536000',
        'X-Content-Type-Options' => 'nosniff',
        'X-Frame-Options' => 'SAMEORIGIN',
        'Referrer-Policy' => 'strict-origin-when-cross-origin',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        foreach (self::HEADERS as $name => $value) {
            $response->headers->set($name, $value);
        }

        // Ne pas annoncer la version de PHP (en-tête ajouté par PHP, hors objet Response).
        header_remove('X-Powered-By');

        return $response;
    }
}
