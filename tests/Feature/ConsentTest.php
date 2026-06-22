<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ConsentTest extends TestCase
{
    use RefreshDatabase;

    public function test_unauthenticated_user_is_redirected_to_login(): void
    {
        $this->get('/consent')->assertRedirect('/auth/login');
    }

    public function test_politique_page_is_accessible_without_auth(): void
    {
        $this->get('/politique-confidentialite')->assertStatus(200);
    }

    public function test_authenticated_user_without_consent_is_redirected_to_consent(): void
    {
        $user = User::factory()->unconsented()->create();

        $this->actingAs($user)->get('/')->assertRedirect('/consent');
    }

    public function test_consent_page_returns_200_for_unconsented_user(): void
    {
        $user = User::factory()->unconsented()->create();

        $this->actingAs($user)->get('/consent')->assertStatus(200);
    }

    public function test_consent_requires_checkbox(): void
    {
        $user = User::factory()->unconsented()->create();

        $this->actingAs($user)
            ->post('/consent', [])
            ->assertSessionHasErrors('agree');
    }

    public function test_consent_stores_and_redirects_to_home(): void
    {
        $user = User::factory()->unconsented()->create();

        $this->actingAs($user)
            ->post('/consent', ['agree' => '1'])
            ->assertRedirect('/');

        $this->assertNotNull($user->fresh()->consent_given_at);
        $this->assertEquals('1.0', $user->fresh()->consent_version);
    }

    public function test_consented_user_can_access_dashboard(): void
    {
        $user = User::factory()->create(); // factory defaults = consented

        $this->actingAs($user)->get('/')->assertStatus(200);
    }

    public function test_consented_user_cannot_be_forced_to_consent_page(): void
    {
        $user = User::factory()->create();

        // Le consentement est déjà donné : /consent redirige ou retourne 200
        // selon l'implémentation ; on vérifie surtout qu'on n'est pas bloqué
        $response = $this->actingAs($user)->get('/consent');
        // Le controller montre juste la vue même si déjà consenti
        $response->assertStatus(200);
    }
}
