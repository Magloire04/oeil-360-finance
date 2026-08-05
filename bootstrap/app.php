<?php

use App\Http\Middleware\EnsureConsentGiven;
use App\Http\Middleware\EnsureUserIsAdmin;
use App\Http\Middleware\RecordActivityEvent;
use App\Http\Middleware\RejectDeletedIdentity;
use App\Http\Middleware\UpdateLastActivity;
use App\Http\Responses\ApiResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // API routes share the browser session so the Auth0 web guard can authenticate
        // fetch() calls from the same origin. Without these, the session cookie is never
        // decrypted/read and auth()->check() always returns false on /api/* routes.
        $middleware->api(prepend: [
            EncryptCookies::class,
            StartSession::class,
        ]);

        // Bloque l'accès aux identités dont le compte a été supprimé (avant auth/consent),
        // pour empêcher toute recréation silencieuse via le SSO.
        $middleware->web(append: [RejectDeletedIdentity::class]);

        // Journalisation d'usage pseudonyme sur toutes les requêtes web et api
        // (l'écriture réelle a lieu dans terminate() et applique une skip-list).
        $middleware->web(append: [RecordActivityEvent::class]);
        $middleware->api(append: [RecordActivityEvent::class]);

        // Aliases utilisés dans routes/web.php pour les routes protégées
        $middleware->alias([
            'consent' => EnsureConsentGiven::class,
            'activity' => UpdateLastActivity::class,
            'admin' => EnsureUserIsAdmin::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*'),
        );

        $exceptions->render(function (ValidationException $e, Request $request) {
            if ($request->is('api/*')) {
                return ApiResponse::error(
                    'Données invalides',
                    'VALIDATION_ERROR',
                    $e->errors(),
                    422
                );
            }
        });

        $exceptions->render(function (NotFoundHttpException $e, Request $request) {
            if ($request->is('api/*')) {
                return ApiResponse::error('Ressource introuvable', 'NOT_FOUND', null, 404);
            }
        });
    })->create();
