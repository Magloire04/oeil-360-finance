<?php

namespace App\Listeners;

use App\Models\User;
use App\Services\UserBootstrapService;
use Auth0\Laravel\Events\AuthenticationSucceeded;

class ProvisionAuth0User
{
    public function __construct(private UserBootstrapService $bootstrap) {}

    public function handle(AuthenticationSucceeded $event): void
    {
        $auth0User = $event->user;

        // Récupère le sub Auth0 (identifiant unique de l'utilisateur)
        $sub   = $auth0User->getAttribute('sub') ?? $auth0User->getAuthIdentifier();
        $email = $auth0User->getAttribute('email');
        $name  = $auth0User->getAttribute('name') ?? $auth0User->getAttribute('nickname') ?? $email;

        if (! $sub) {
            return;
        }

        $isNew = ! User::where('auth0_id', $sub)->exists();

        $user = User::updateOrCreate(
            ['auth0_id' => $sub],
            ['email' => $email, 'name' => $name],
        );

        if ($isNew) {
            $this->bootstrap->bootstrapNewUser($user);
        }
    }
}
