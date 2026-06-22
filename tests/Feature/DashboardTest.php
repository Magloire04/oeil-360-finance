<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\Category;
use App\Models\Transaction;
use App\Models\Transfer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->actingAs($this->user);
    }

    public function test_retourne_structure_correcte_sans_transactions(): void
    {
        $response = $this->getJson('/api/dashboard');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'balances' => ['total', 'accounts'],
                    'period' => ['income', 'expense', 'net'],
                    'expense_by_category' => [],
                    'balance_evolution' => [],
                    'recent_transactions' => [],
                ],
                'meta',
                'error',
            ])
            ->assertJsonPath('error', null)
            ->assertJsonPath('data.period.income', 0.0)
            ->assertJsonPath('data.period.expense', 0.0)
            ->assertJsonPath('data.period.net', 0.0)
            ->assertJsonPath('data.balances.total', 0.0);
    }

    public function test_calcule_les_totaux_income_expense_sur_la_periode(): void
    {
        $account = Account::factory()->create(['user_id' => $this->user->id, 'initial_balance' => 0]);
        $category = Category::factory()->create(['user_id' => $this->user->id]);

        Transaction::factory()->create([
            'user_id' => $this->user->id,
            'account_id' => $account->id,
            'category_id' => $category->id,
            'sense' => 'income',
            'amount' => 100000,
            'transaction_date' => '2026-06-10',
        ]);
        Transaction::factory()->create([
            'user_id' => $this->user->id,
            'account_id' => $account->id,
            'category_id' => $category->id,
            'sense' => 'expense',
            'amount' => 40000,
            'transaction_date' => '2026-06-15',
        ]);

        $response = $this->getJson('/api/dashboard?start_date=2026-06-01&end_date=2026-06-30');

        $response->assertStatus(200)
            ->assertJsonPath('data.period.income', 100000.0)
            ->assertJsonPath('data.period.expense', 40000.0)
            ->assertJsonPath('data.period.net', 60000.0);
    }

    public function test_les_transferts_ne_gonflent_pas_income_expense(): void
    {
        $from = Account::factory()->create(['user_id' => $this->user->id, 'initial_balance' => 100000]);
        $to = Account::factory()->create(['user_id' => $this->user->id, 'initial_balance' => 0]);

        Transfer::create([
            'user_id' => $this->user->id,
            'amount' => 50000,
            'transfer_date' => '2026-06-10',
            'from_account_id' => $from->id,
            'to_account_id' => $to->id,
        ]);

        $response = $this->getJson('/api/dashboard?start_date=2026-06-01&end_date=2026-06-30');

        $response->assertStatus(200)
            ->assertJsonPath('data.period.income', 0.0)
            ->assertJsonPath('data.period.expense', 0.0)
            ->assertJsonPath('data.period.net', 0.0);
    }

    public function test_filtre_par_periode(): void
    {
        $account = Account::factory()->create(['user_id' => $this->user->id, 'initial_balance' => 0]);
        $category = Category::factory()->create(['user_id' => $this->user->id]);

        Transaction::factory()->create([
            'user_id' => $this->user->id,
            'account_id' => $account->id,
            'category_id' => $category->id,
            'sense' => 'income',
            'amount' => 30000,
            'transaction_date' => '2026-06-15',
        ]);
        Transaction::factory()->create([
            'user_id' => $this->user->id,
            'account_id' => $account->id,
            'category_id' => $category->id,
            'sense' => 'income',
            'amount' => 50000,
            'transaction_date' => '2026-05-01',
        ]);

        $response = $this->getJson('/api/dashboard?start_date=2026-06-01&end_date=2026-06-30');

        $response->assertStatus(200)
            ->assertJsonPath('data.period.income', 30000.0);
    }

    public function test_expense_by_category_trie_par_montant_desc(): void
    {
        $account = Account::factory()->create(['user_id' => $this->user->id, 'initial_balance' => 0]);
        $cat1 = Category::factory()->create(['user_id' => $this->user->id, 'name' => 'Transport',    'type' => 'expense']);
        $cat2 = Category::factory()->create(['user_id' => $this->user->id, 'name' => 'Alimentation', 'type' => 'expense']);

        Transaction::factory()->create([
            'user_id' => $this->user->id,
            'account_id' => $account->id,
            'category_id' => $cat1->id,
            'sense' => 'expense',
            'amount' => 10000,
            'transaction_date' => '2026-06-10',
        ]);
        Transaction::factory()->create([
            'user_id' => $this->user->id,
            'account_id' => $account->id,
            'category_id' => $cat2->id,
            'sense' => 'expense',
            'amount' => 50000,
            'transaction_date' => '2026-06-12',
        ]);

        $response = $this->getJson('/api/dashboard?start_date=2026-06-01&end_date=2026-06-30');

        $response->assertStatus(200);
        $byCategory = $response->json('data.expense_by_category');
        $this->assertCount(2, $byCategory);
        $this->assertSame('Alimentation', $byCategory[0]['category_name']);
        $this->assertSame(50000.0, $byCategory[0]['amount']);
    }

    public function test_recent_transactions_retourne_au_plus_10(): void
    {
        $account = Account::factory()->create(['user_id' => $this->user->id, 'initial_balance' => 0]);
        $category = Category::factory()->create(['user_id' => $this->user->id]);

        Transaction::factory()->count(15)->create([
            'user_id' => $this->user->id,
            'account_id' => $account->id,
            'category_id' => $category->id,
        ]);

        $response = $this->getJson('/api/dashboard');

        $response->assertStatus(200);
        $this->assertCount(10, $response->json('data.recent_transactions'));
    }

    public function test_balance_evolution_contient_solde_ouverture(): void
    {
        Account::factory()->create(['user_id' => $this->user->id, 'initial_balance' => 75000]);

        $response = $this->getJson('/api/dashboard?start_date=2026-06-01&end_date=2026-06-30');

        $response->assertStatus(200);
        $evolution = $response->json('data.balance_evolution');
        $this->assertNotEmpty($evolution);
        $this->assertSame('2026-06-01', $evolution[0]['date']);
        $this->assertSame(75000.0, $evolution[0]['cumulative_balance']);
    }

    public function test_kpis_retourne_les_bons_comptes(): void
    {
        $account = Account::factory()->create(['user_id' => $this->user->id]);
        $category = Category::factory()->create(['user_id' => $this->user->id, 'type' => 'expense']);

        Transaction::factory()->create(['user_id' => $this->user->id, 'account_id' => $account->id, 'category_id' => $category->id, 'sense' => 'expense', 'amount' => 30000, 'transaction_date' => '2026-06-10']);
        Transaction::factory()->create(['user_id' => $this->user->id, 'account_id' => $account->id, 'category_id' => $category->id, 'sense' => 'income',  'amount' => 50000, 'transaction_date' => '2026-06-15']);

        $response = $this->getJson('/api/dashboard?start_date=2026-06-01&end_date=2026-06-30');

        $response->assertStatus(200)
            ->assertJsonPath('data.kpis.transactions_count', 2)
            ->assertJsonPath('data.kpis.top_expense_category', $category->name);

        $this->assertNotNull($response->json('data.kpis.daily_avg_expense'));
    }

    public function test_dashboard_monthly_retourne_12_mois(): void
    {
        $response = $this->getJson('/api/dashboard/monthly');

        $response->assertStatus(200)
            ->assertJsonStructure(['data', 'meta', 'error'])
            ->assertJsonPath('error', null);

        $this->assertCount(12, $response->json('data'));
        $this->assertArrayHasKey('month', $response->json('data.0'));
        $this->assertArrayHasKey('income', $response->json('data.0'));
        $this->assertArrayHasKey('expense', $response->json('data.0'));
    }

    public function test_dashboard_monthly_aggr_ã¨ge_par_mois(): void
    {
        $account = Account::factory()->create(['user_id' => $this->user->id]);
        $category = Category::factory()->create(['user_id' => $this->user->id]);

        $currentMonth = now()->format('Y-m');

        Transaction::factory()->create(['user_id' => $this->user->id, 'account_id' => $account->id, 'category_id' => $category->id, 'sense' => 'income',  'amount' => 80000, 'transaction_date' => now()->startOfMonth()->toDateString()]);
        Transaction::factory()->create(['user_id' => $this->user->id, 'account_id' => $account->id, 'category_id' => $category->id, 'sense' => 'expense', 'amount' => 25000, 'transaction_date' => now()->startOfMonth()->toDateString()]);

        $response = $this->getJson('/api/dashboard/monthly');

        $response->assertStatus(200);
        $data = collect($response->json('data'));
        $thisMonth = $data->firstWhere('month', $currentMonth);

        $this->assertNotNull($thisMonth);
        $this->assertSame(80000.0, $thisMonth['income']);
        $this->assertSame(25000.0, $thisMonth['expense']);
    }
}
