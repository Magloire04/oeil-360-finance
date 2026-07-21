<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LandingTest extends TestCase
{
    use RefreshDatabase;

    public function test_landing_is_accessible_without_authentication(): void
    {
        $this->get('/')->assertStatus(200);
    }

    public function test_landing_offers_a_login_call_to_action(): void
    {
        $this->get('/')
            ->assertStatus(200)
            ->assertSee('Se connecter')
            ->assertSee(route('login'), false);
    }

    public function test_landing_exposes_the_presentation_video(): void
    {
        $this->get('/')
            ->assertStatus(200)
            ->assertSee('/videos/oeil360-promo.mp4', false);
    }

    public function test_landing_does_not_leak_protected_navigation(): void
    {
        $this->get('/')
            ->assertStatus(200)
            ->assertDontSee('/transactions', false);
    }

    public function test_authenticated_user_is_redirected_to_dashboard(): void
    {
        $user = User::factory()->create(); // factory par défaut = consenti

        $this->actingAs($user)->get('/')->assertRedirect('/dashboard');
    }

    public function test_privacy_policy_remains_public(): void
    {
        $this->get('/politique-confidentialite')->assertStatus(200);
    }
}
