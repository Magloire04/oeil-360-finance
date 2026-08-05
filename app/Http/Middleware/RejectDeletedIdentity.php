<?php

namespace App\Http\Middleware;

use App\Models\DeletedIdentity;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Coupe l'accès à une identité dont le compte a été supprimé volontairement :
 * même si le SSO Auth0 la ré-authentifie, on ne recrée pas de compte
 * (cf. Auth0UserRepository) et on redirige vers un écran « compte supprimé ».
 *
 * On lit le `sub` directement dans les credentials Auth0 (sans passer par le
 * repository, donc sans recréation), puis on termine la session Auth0.
 */
class RejectDeletedIdentity
{
    public function handle(Request $request, Closure $next): Response
    {
        $sub = $this->auth0Sub();

        if ($sub !== null && DeletedIdentity::isBlocked($sub)) {
            Auth::guard('web')->logout();

            return redirect()->route('account.deleted');
        }

        return $next($request);
    }

    /** Identifiant Auth0 (sub) de la session en cours, sans déclencher de recréation. */
    private function auth0Sub(): ?string
    {
        try {
            $credentials = Auth::guard('web')->sdk()->getCredentials();
        } catch (\Throwable) {
            return null;
        }

        $sub = $credentials?->user['sub'] ?? null;

        return is_string($sub) ? $sub : null;
    }
}
