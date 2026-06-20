<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\Category;
use App\Models\RecurringTransaction;
use App\Models\Transaction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RecurringTransactionTest extends TestCase
{
    use RefreshDatabase;

    private function makeRecurring(array $overrides = []): array
    {
        $category = Category::factory()->create();
        $account  = Account::factory()->create();
        return array_merge([
            'amount'      => 50000,
            'sense'       => 'expense',
            'frequency'   => 'monthly',
            'start_date'  => '2026-06-01',
            'category_id' => $category->id,
            'account_id'  => $account->id,
            'note'        => null,
        ], $overrides);
    }

    public function test_cree_une_recurrente_mensuelle(): void
    {
        $data = $this->makeRecurring();
        $response = $this->postJson('/api/recurring-transactions', $data);

        $response->assertStatus(201)
            ->assertJsonPath('data.frequency', 'monthly')
            ->assertJsonPath('data.amount', '50000.00');

        $this->assertDatabaseHas('recurring_transactions', [
            'frequency' => 'monthly',
            'amount'    => 50000,
        ]);
    }

    public function test_next_occurrence_date_initialise_a_start_date(): void
    {
        $data = $this->makeRecurring(['start_date' => '2026-07-01']);
        $response = $this->postJson('/api/recurring-transactions', $data);

        $response->assertStatus(201)
            ->assertJsonPath('data.next_occurrence_date', '2026-07-01');

        $this->assertDatabaseHas('recurring_transactions', [
            'start_date'           => '2026-07-01',
            'next_occurrence_date' => '2026-07-01',
        ]);
    }

    public function test_refuse_frequence_invalide(): void
    {
        $data = $this->makeRecurring(['frequency' => 'biweekly']);
        $this->postJson('/api/recurring-transactions', $data)
            ->assertStatus(422)
            ->assertJsonPath('error.code', 'VALIDATION_ERROR');
    }

    public function test_refuse_montant_zero(): void
    {
        $data = $this->makeRecurring(['amount' => 0]);
        $this->postJson('/api/recurring-transactions', $data)
            ->assertStatus(422)
            ->assertJsonPath('error.code', 'VALIDATION_ERROR');
    }

    public function test_liste_les_recurrentes(): void
    {
        RecurringTransaction::factory()->count(3)->create();
        $response = $this->getJson('/api/recurring-transactions');

        $response->assertStatus(200)
            ->assertJsonStructure(['data', 'meta', 'error'])
            ->assertJsonPath('error', null);

        $this->assertCount(3, $response->json('data'));
    }

    public function test_met_a_jour_une_recurrente(): void
    {
        $recurring = RecurringTransaction::factory()->create(['frequency' => 'monthly']);

        $this->putJson("/api/recurring-transactions/{$recurring->id}", [
            'frequency' => 'yearly',
            'is_active' => false,
        ])->assertStatus(200)
          ->assertJsonPath('data.frequency', 'yearly')
          ->assertJsonPath('data.is_active', false);
    }

    public function test_supprime_une_recurrente_et_detache_les_transactions(): void
    {
        $recurring = RecurringTransaction::factory()->create();

        // Créer une transaction liée à cette récurrente
        $transaction = Transaction::factory()->create([
            'account_id'               => $recurring->account_id,
            'category_id'              => $recurring->category_id,
            'recurring_transaction_id' => $recurring->id,
        ]);

        $this->deleteJson("/api/recurring-transactions/{$recurring->id}")
            ->assertStatus(204);

        // La récurrente est supprimée
        $this->assertDatabaseMissing('recurring_transactions', ['id' => $recurring->id]);

        // La transaction existe toujours, mais recurring_transaction_id est NULL (nullOnDelete)
        $this->assertDatabaseHas('transactions', [
            'id'                       => $transaction->id,
            'recurring_transaction_id' => null,
        ]);
    }
}
