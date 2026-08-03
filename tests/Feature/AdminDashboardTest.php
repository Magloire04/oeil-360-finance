<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminDashboardTest extends TestCase
{
    use RefreshDatabase;

    private const METRICS_ENDPOINTS = [
        '/api/admin/metrics/overview',
        '/api/admin/metrics/user-growth',
        '/api/admin/metrics/active-users',
        '/api/admin/metrics/operations',
        '/api/admin/metrics/top-features',
        '/api/admin/metrics/traffic',
        '/api/admin/metrics/performance',
    ];

    public function test_admin_peut_afficher_la_page_admin(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->get('/admin')->assertOk();
    }

    public function test_utilisateur_non_admin_interdit_sur_la_page_admin(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get('/admin')->assertForbidden();
    }

    public function test_visiteur_non_authentifie_redirige_vers_login(): void
    {
        $this->get('/admin')->assertRedirect('/auth/login');
    }

    public function test_admin_recoit_l_enveloppe_api_sur_overview(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->getJson('/api/admin/metrics/overview')
            ->assertOk()
            ->assertJsonStructure([
                'data' => ['total_users', 'new_users', 'active_users', 'operations', 'consent'],
                'meta',
                'error',
            ]);
    }

    public function test_tous_les_endpoints_metrics_repondent_pour_un_admin(): void
    {
        $admin = User::factory()->admin()->create();

        foreach (self::METRICS_ENDPOINTS as $endpoint) {
            $this->actingAs($admin)->getJson($endpoint)->assertOk();
        }
    }

    public function test_non_admin_interdit_sur_l_api_admin(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->getJson('/api/admin/metrics/overview')
            ->assertForbidden()
            ->assertJsonPath('error.code', 'FORBIDDEN');
    }

    public function test_visiteur_non_authentifie_non_autorise_sur_l_api_admin(): void
    {
        $this->getJson('/api/admin/metrics/overview')->assertUnauthorized();
    }
}
