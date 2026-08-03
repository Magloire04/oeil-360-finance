<?php

namespace Tests\Feature;

use App\Models\ActivityEvent;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class ActivityTrackingTest extends TestCase
{
    use RefreshDatabase;

    public function test_une_requete_applicative_est_journalisee(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get('/dashboard')->assertOk();

        $this->assertDatabaseHas('activity_events', [
            'user_id' => $user->id,
            'feature' => 'home',
            'method' => 'GET',
            'path' => 'dashboard',
            'status' => 200,
        ]);
    }

    public function test_le_health_check_n_est_pas_journalise(): void
    {
        $this->get('/up')->assertOk();

        $this->assertDatabaseCount('activity_events', 0);
    }

    public function test_les_endpoints_admin_ne_sont_pas_journalises(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->getJson('/api/admin/metrics/overview')->assertOk();

        $this->assertDatabaseCount('activity_events', 0);
    }

    public function test_la_table_ne_contient_aucune_donnee_personnelle(): void
    {
        $this->assertFalse(Schema::hasColumn('activity_events', 'ip'));
        $this->assertFalse(Schema::hasColumn('activity_events', 'ip_address'));
        $this->assertFalse(Schema::hasColumn('activity_events', 'user_agent'));
    }

    public function test_la_purge_supprime_les_evenements_anciens(): void
    {
        $old = ActivityEvent::factory()->create();
        $old->created_at = now()->subDays(100);
        $old->saveQuietly();

        $recent = ActivityEvent::factory()->create();

        $this->artisan('oeil360:prune-activity-events')->assertSuccessful();

        $this->assertDatabaseMissing('activity_events', ['id' => $old->id]);
        $this->assertDatabaseHas('activity_events', ['id' => $recent->id]);
    }
}
