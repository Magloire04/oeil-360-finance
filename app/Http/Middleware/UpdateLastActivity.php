<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class UpdateLastActivity
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user !== null) {
            $lastActivity = $user->last_activity_at;

            // Une seule écriture par jour pour ne pas surcharger la BDD
            if ($lastActivity === null || $lastActivity->lt(now()->startOfDay())) {
                $user->updateQuietly(['last_activity_at' => now()]);
            }
        }

        return $next($request);
    }
}
