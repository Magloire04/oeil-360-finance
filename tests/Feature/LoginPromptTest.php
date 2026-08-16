<?php

namespace Tests\Feature;

use Auth0\Laravel\Events\LoginAttempting;
use Tests\TestCase;

class LoginPromptTest extends TestCase
{
    public function test_le_login_force_le_choix_de_compte(): void
    {
        $event = new LoginAttempting;

        event($event);

        // Le listener (AppServiceProvider) doit imposer le sélecteur de compte,
        // pour qu'un utilisateur puisse toujours choisir/saisir un autre compte.
        $this->assertSame('select_account', $event->parameters['prompt'] ?? null);
    }
}
