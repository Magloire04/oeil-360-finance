<?php

namespace Tests\Unit;

use App\Models\Account;
use App\Models\Category;
use App\Models\Transaction;
use App\Models\User;
use App\Services\StatementService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StatementServiceTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    private Account $account;

    private Category $category;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->account = Account::factory()->create(['user_id' => $this->user->id, 'initial_balance' => 0]);
        $this->category = Category::factory()->create(['user_id' => $this->user->id]);

        // Deux opérations en mars, une en janvier (hors d'une période mars).
        $this->makeTransaction('income', 100000, '2026-03-10');
        $this->makeTransaction('expense', 30000, '2026-03-20');
        $this->makeTransaction('income', 50000, '2026-01-05');
    }

    private function makeTransaction(string $sense, float $amount, string $date): void
    {
        Transaction::factory()->create([
            'user_id' => $this->user->id,
            'account_id' => $this->account->id,
            'category_id' => $this->category->id,
            'sense' => $sense,
            'amount' => $amount,
            'transaction_date' => $date,
        ]);
    }

    private function service(): StatementService
    {
        return app(StatementService::class);
    }

    public function test_all_data_includes_every_transaction_and_correct_total_balance(): void
    {
        $report = $this->service()->buildStatement($this->user, null, null);

        $this->assertTrue($report['period']['is_all']);
        $this->assertCount(3, $report['transactions']);
        // Solde = 0 initial + (100000 + 50000) entrées - 30000 sorties = 120000.
        $this->assertSame(120000.0, $report['summary']['total_balance']);
    }

    public function test_period_filter_excludes_out_of_range_transactions(): void
    {
        $report = $this->service()->buildStatement($this->user, '2026-03-01', '2026-03-31');

        $this->assertFalse($report['period']['is_all']);
        $this->assertCount(2, $report['transactions']);
        $this->assertSame(100000.0, $report['summary']['income']);
        $this->assertSame(30000.0, $report['summary']['expense']);
        $this->assertSame(70000.0, $report['summary']['net']);
        $this->assertStringContainsString('Du 01/03/2026 au 31/03/2026', $report['period']['label']);
    }

    public function test_statement_is_scoped_to_the_owner(): void
    {
        $other = User::factory()->create();
        $otherAccount = Account::factory()->create(['user_id' => $other->id]);
        $otherCategory = Category::factory()->create(['user_id' => $other->id]);
        Transaction::factory()->create([
            'user_id' => $other->id,
            'account_id' => $otherAccount->id,
            'category_id' => $otherCategory->id,
            'sense' => 'income',
            'amount' => 999999,
            'transaction_date' => '2026-03-15',
        ]);

        $report = $this->service()->buildStatement($this->user, null, null);

        // Toujours 3 (les données de l'autre utilisateur sont absentes).
        $this->assertCount(3, $report['transactions']);
        $this->assertSame(120000.0, $report['summary']['total_balance']);
    }
}
