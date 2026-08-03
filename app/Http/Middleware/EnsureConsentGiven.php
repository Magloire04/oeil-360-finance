<?php

namespace App\Http\Middleware;

use App\Http\Controllers\ConsentController;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureConsentGiven
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // Redirige si aucun consentement, ou si le consentement porte sur une
        // version antérieure de la politique (re-consentement requis).
        if ($user !== null
            && ($user->consent_given_at === null
                || $user->consent_version !== ConsentController::POLICY_VERSION)) {
            return redirect()->route('consent.show');
        }

        return $next($request);
    }
}
