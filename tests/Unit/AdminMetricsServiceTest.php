<?php

namespace Tests\Unit;

use App\Models\Account;
use App\Models\ActivityEvent;
use App\Models\Category;
use App\Models\Transaction;
use App\Models\Transfer;
use App\Models\User;
use App\Services\AdminMetricsService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminMetricsServiceTest extends TestCase
{
    use RefreshDatabase;

    private AdminMetricsService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(AdminMetricsService::class);
    }

    private function today(): string
    {
        return now()->toDateString();
    }

    /** Force la date de création d'un événement (created_at n'est pas fillable). */
    private function ageEvent(ActivityEvent $event, string $date): void
    {
        $event->created_at = Carbon::parse($date);
        $event->saveQuietly();
    }

    public function test_overview_compte_inscrits_operations_et_consentement(): void
    {
        User::factory()->count(2)->create();
        User::factory()->unconsented()->create();

        Transaction::factory()->count(2)->create();
        Transfer::factory()->create();

        $overview = $this->service->getOverview($this->today(), $this->today());

        $this->assertSame(3, $overview['total_users']);
        $this->assertSame(3, $overview['new_users']);
        $this->assertSame(3, $overview['operations']); // 2 transactions + 1 transfert
        $this->assertSame(2, $overview['consent']['count']);
        $this->assertSame(0.6667, $overview['consent']['rate']);
        $this->assertSame(3, $overview['active_users']['month']);
    }

    public function test_overview_taux_consentement_zero_sans_utilisateur(): void
    {
        $overview = $this->service->getOverview($this->today(), $this->today());

        $this->assertSame(0, $overview['total_users']);
        $this->assertSame(0.0, $overview['consent']['rate']);
    }

    public function test_user_growth_retourne_douze_mois(): void
    {
        User::factory()->create();

        $growth = $this->service->getUserGrowth();

        $this->assertCount(12, $growth);
        $last = $growth[11];
        $this->assertSame(now()->format('Y-m'), $last['month']);
        $this->assertGreaterThanOrEqual(1, $last['count']);
    }

    public function test_operations_breakdown_compte_par_type(): void
    {
        Transaction::factory()->count(3)->create();
        Transfer::factory()->count(2)->create();
        Category::factory()->create();
        Account::factory()->create();

        $rows = collect($this->service->getOperationsBreakdown($this->today(), $this->today()))
            ->keyBy('type');

        $this->assertSame(3, $rows['transactions']['count']);
        $this->assertSame(2, $rows['transfers']['count']);
    }

    public function test_traffic_compte_les_visites_du_jour_et_ignore_hors_periode(): void
    {
        ActivityEvent::factory()->count(3)->create();
        $this->ageEvent(ActivityEvent::factory()->create(), now()->subDays(10)->toDateString());

        $traffic = $this->service->getTraffic($this->today(), $this->today());

        $this->assertCount(1, $traffic);
        $this->assertSame($this->today(), $traffic[0]['date']);
        $this->assertSame(3, $traffic[0]['visits']);
    }

    public function test_active_users_compte_les_utilisateurs_distincts(): void
    {
        $a = User::factory()->create();
        $b = User::factory()->create();

        ActivityEvent::factory()->count(2)->create(['user_id' => $a->id]);
        ActivityEvent::factory()->create(['user_id' => $b->id]);

        $active = $this->service->getActiveUsers($this->today(), $this->today());

        $this->assertSame(2, $active[0]['active_users']);
    }

    public function test_top_features_trie_par_usage(): void
    {
        ActivityEvent::factory()->count(3)->feature('dashboard')->create();
        ActivityEvent::factory()->feature('transactions.index')->create();

        $top = $this->service->getTopFeatures($this->today(), $this->today());

        $this->assertSame('dashboard', $top[0]['feature']);
        $this->assertSame(3, $top[0]['count']);
        $this->assertSame('transactions.index', $top[1]['feature']);
    }

    public function test_performance_calcule_taux_erreur_et_p95(): void
    {
        ActivityEvent::factory()->create(['status' => 200, 'duration_ms' => 10]);
        ActivityEvent::factory()->create(['status' => 200, 'duration_ms' => 20]);
        ActivityEvent::factory()->create(['status' => 200, 'duration_ms' => 30]);
        ActivityEvent::factory()->create(['status' => 200, 'duration_ms' => 40]);
        ActivityEvent::factory()->create(['status' => 500, 'duration_ms' => 50]);

        $perf = $this->service->getPerformance($this->today(), $this->today());

        $this->assertSame(5, $perf['total_requests']);
        $this->assertSame(0.2, $perf['error_rate']);
        $this->assertSame(50, $perf['p95_ms']);
        $this->assertSame(500, $perf['errors_by_status'][0]['status']);
        $this->assertSame(1, $perf['errors_by_status'][0]['count']);
    }
}
