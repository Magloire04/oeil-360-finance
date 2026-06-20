<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\Category;
use App\Models\RecurringTransaction;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GenerateRecurringTransactionsTest extends TestCase
{
    use RefreshDatabase;

    private function makeRecurring(array $overrides = []): RecurringTransaction
    {
        $category = Category::factory()->create();
        $account  = Account::factory()->create();

        return RecurringTransaction::factory()->create(array_merge([
            'category_id'          => $category->id,
            'account_id'           => $account->id,
            'is_active'            => true,
            'frequency'            => 'monthly',
            'next_occurrence_date' => Carbon::today()->toDateString(),
        ], $overrides));
    }

    public function test_genere_une_transaction_pour_une_recurrente_due(): void
    {
        $recurring = $this->makeRecurring([
            'amount'               => 25000,
            'sense'                => 'expense',
            'next_occurrence_date' => Carbon::today()->toDateString(),
        ]);

        $this->artisan('transactions:generate-recurring')->assertExitCode(0);

        $this->assertDatabaseHas('transactions', [
            'amount'                   => 25000,
            'sense'                    => 'expense',
            'recurring_transaction_id' => $recurring->id,
            'transaction_date'         => Carbon::today()->toDateString(),
        ]);
        $this->assertSame(1, Transaction::count());
    }

    public function test_avance_next_occurrence_date_daily(): void
    {
        $today = Carbon::today();
        $recurring = $this->makeRecurring([
            'frequency'            => 'daily',
            'next_occurrence_date' => $today->toDateString(),
        ]);

        $this->artisan('transactions:generate-recurring')->assertExitCode(0);

        $recurring->refresh();
        $this->assertSame($today->copy()->addDay()->toDateString(), $recurring->next_occurrence_date->toDateString());
    }

    public function test_avance_next_occurrence_date_weekly(): void
    {
        $today = Carbon::today();
        $recurring = $this->makeRecurring([
            'frequency'            => 'weekly',
            'next_occurrence_date' => $today->toDateString(),
        ]);

        $this->artisan('transactions:generate-recurring')->assertExitCode(0);

        $recurring->refresh();
        $this->assertSame($today->copy()->addWeek()->toDateString(), $recurring->next_occurrence_date->toDateString());
    }

    public function test_avance_next_occurrence_date_monthly(): void
    {
        $today = Carbon::today();
        $recurring = $this->makeRecurring([
            'frequency'            => 'monthly',
            'next_occurrence_date' => $today->toDateString(),
        ]);

        $this->artisan('transactions:generate-recurring')->assertExitCode(0);

        $recurring->refresh();
        $this->assertSame($today->copy()->addMonth()->toDateString(), $recurring->next_occurrence_date->toDateString());
    }

    public function test_avance_next_occurrence_date_yearly(): void
    {
        $today = Carbon::today();
        $recurring = $this->makeRecurring([
            'frequency'            => 'yearly',
            'next_occurrence_date' => $today->toDateString(),
        ]);

        $this->artisan('transactions:generate-recurring')->assertExitCode(0);

        $recurring->refresh();
        $this->assertSame($today->copy()->addYear()->toDateString(), $recurring->next_occurrence_date->toDateString());
    }

    public function test_ignore_les_recurrentes_inactives(): void
    {
        $this->makeRecurring([
            'is_active'            => false,
            'next_occurrence_date' => Carbon::today()->toDateString(),
        ]);

        $this->artisan('transactions:generate-recurring')->assertExitCode(0);

        $this->assertSame(0, Transaction::count());
    }

    public function test_ignore_les_recurrentes_futures(): void
    {
        $this->makeRecurring([
            'next_occurrence_date' => Carbon::tomorrow()->toDateString(),
        ]);

        $this->artisan('transactions:generate-recurring')->assertExitCode(0);

        $this->assertSame(0, Transaction::count());
    }

    public function test_pas_de_duplication_si_relance_le_meme_jour(): void
    {
        $this->makeRecurring([
            'frequency'            => 'monthly',
            'next_occurrence_date' => Carbon::today()->toDateString(),
        ]);

        // Première exécution
        $this->artisan('transactions:generate-recurring')->assertExitCode(0);
        $this->assertSame(1, Transaction::count());

        // Deuxième exécution le même jour → next_occurrence_date > today, donc ignoré
        $this->artisan('transactions:generate-recurring')->assertExitCode(0);
        $this->assertSame(1, Transaction::count()); // Toujours 1, pas de doublon
    }
}
