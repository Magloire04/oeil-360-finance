<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_mon_compte_page_returns_200(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get('/mon-compte')->assertStatus(200);
    }

    public function test_export_returns_json_file(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/mon-compte/export');

        $response->assertStatus(200);
        $this->assertStringContainsString('application/json', $response->headers->get('Content-Type') ?? '');
        $this->assertStringContainsString('attachment', $response->headers->get('Content-Disposition') ?? '');
    }

    public function test_export_contains_profile_data(): void
    {
        $user = User::factory()->create(['name' => 'Élisée Test', 'email' => 'test@oeil360.test']);

        $response = $this->actingAs($user)->get('/mon-compte/export');
        $content = json_decode($response->streamedContent(), true);

        $this->assertArrayHasKey('profile', $content);
        $this->assertEquals('Élisée Test', $content['profile']['name']);
        $this->assertEquals('test@oeil360.test', $content['profile']['email']);
        $this->assertArrayHasKey('accounts', $content);
        $this->assertArrayHasKey('transactions', $content);
        $this->assertArrayHasKey('categories', $content);
    }

    public function test_unauthenticated_cannot_access_mon_compte(): void
    {
        $this->get('/mon-compte')->assertRedirect('/auth/login');
    }

    public function test_delete_account_removes_user_and_returns_success(): void
    {
        $user = User::factory()->create();
        $userId = $user->id;

        $response = $this->actingAs($user)
            ->deleteJson('/api/profile');

        $response->assertStatus(200)
            ->assertJsonPath('data.message', 'Compte supprimé avec succès.');

        $this->assertDatabaseMissing('users', ['id' => $userId]);
        // La session doit être vidée : sinon un ré-login SSO recréerait le compte.
        $this->assertGuest('web');
    }

    public function test_unauthenticated_cannot_delete_account(): void
    {
        $this->deleteJson('/api/profile')->assertStatus(401);
    }
}
