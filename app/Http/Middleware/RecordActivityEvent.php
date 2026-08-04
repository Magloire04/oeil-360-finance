<?php

namespace App\Http\Middleware;

use App\Models\ActivityEvent;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

/**
 * Enregistre un événement d'usage pseudonyme par requête applicative (visite/action)
 * pour alimenter les statistiques admin. Minimisé APDP : ni IP, ni user-agent, ni contenu.
 * L'écriture a lieu dans terminate() (après envoi de la réponse) pour ne pas ajouter de
 * latence perçue — important en hébergement mutualisé.
 */
class RecordActivityEvent
{
    /** Chemins à ne pas journaliser (bruit, boucles, ou risque de jeton en URL). */
    private const SKIP_PATTERNS = [
        'up',            // health check
        'api/admin/*',   // évite le bruit du tableau de bord admin lui-même
        'auth/*',        // flux Auth0 (login/logout/callback)
    ];

    public function handle(Request $request, Closure $next): Response
    {
        $request->attributes->set('activity_started_at', microtime(true));

        return $next($request);
    }

    public function terminate(Request $request, Response $response): void
    {
        if ($this->shouldSkip($request)) {
            return;
        }

        $startedAt = $request->attributes->get('activity_started_at');
        $durationMs = is_float($startedAt)
            ? (int) round((microtime(true) - $startedAt) * 1000)
            : 0;

        $route = $request->route();
        $pathTemplate = $route?->uri() ?? $request->path();

        try {
            ActivityEvent::create([
                'user_id' => $request->user()?->id,
                'feature' => $route?->getName()
                    ?: strtolower($request->method()).' '.$pathTemplate,
                'method' => $request->method(),
                'path' => $pathTemplate,
                'status' => $response->getStatusCode(),
                'duration_ms' => $durationMs,
            ]);
        } catch (\Throwable $e) {
            // La télémétrie ne doit jamais casser la requête utilisateur — mais l'échec
            // est journalisé (pas d'échec silencieux), sans aucune donnée personnelle.
            Log::warning('Enregistrement activity_event échoué', ['reason' => $e->getMessage()]);
        }
    }

    private function shouldSkip(Request $request): bool
    {
        foreach (self::SKIP_PATTERNS as $pattern) {
            if ($request->is($pattern)) {
                return true;
            }
        }

        // Ignore les fichiers statiques (assets) servis via PHP en local.
        return (bool) preg_match('/\.(css|js|png|jpe?g|gif|svg|ico|woff2?|ttf|map)$/i', $request->path());
    }
}
