<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\Category;
use App\Models\Transaction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TransactionTest extends TestCase
{
    use RefreshDatabase;

    private function makeTransaction(array $overrides = []): array
    {
        $category = Category::factory()->create();
        $account = Account::factory()->create(['initial_balance' => 0]);
        return array_merge([
            'amount'           => 50000,
            'sense'            => 'expense',
            'transaction_date' => '2026-06-15',
            'category_id'      => $category->id,
            'account_id'       => $account->id,
            'note'             => null,
        ], $overrides);
    }

    public function test_liste_les_transactions_paginées(): void
    {
        Transaction::factory()->count(3)->create();
        $response = $this->getJson('/api/transactions');
        $response->assertStatus(200)
            ->assertJsonStructure(['data', 'meta', 'error'])
            ->assertJsonPath('meta.total', 3);
    }

    public function test_filtre_par_sens(): void
    {
        Transaction::factory()->create(['sense' => 'income']);
        Transaction::factory()->create(['sense' => 'expense']);
        $response = $this->getJson('/api/transactions?sense=income');
        $response->assertStatus(200);
        $this->assertCount(1, $response->json('data'));
        $this->assertSame('income', $response->json('data.0.sense'));
    }

    public function test_filtre_par_periode(): void
    {
        Transaction::factory()->create(['transaction_date' => '2026-01-10']);
        Transaction::factory()->create(['transaction_date' => '2026-06-15']);
        $response = $this->getJson('/api/transactions?start_date=2026-06-01&end_date=2026-06-30');
        $response->assertStatus(200);
        $this->assertCount(1, $response->json('data'));
    }

    public function test_filtre_par_note(): void
    {
        Transaction::factory()->create(['note' => 'Courses supermarché']);
        Transaction::factory()->create(['note' => 'Taxi']);
        $response = $this->getJson('/api/transactions?q=supermarché');
        $response->assertStatus(200);
        $this->assertCount(1, $response->json('data'));
    }

    public function test_cree_une_transaction_valide(): void
    {
        $data = $this->makeTransaction();
        $response = $this->postJson('/api/transactions', $data);
        $response->assertStatus(201)
            ->assertJsonPath('data.amount', '50000.00')
            ->assertJsonPath('data.sense', 'expense');
        $this->assertDatabaseHas('transactions', ['amount' => 50000]);
    }

    public function test_refuse_montant_zero(): void
    {
        $data = $this->makeTransaction(['amount' => 0]);
        $this->postJson('/api/transactions', $data)
            ->assertStatus(422)
            ->assertJsonPath('error.code', 'VALIDATION_ERROR');
    }

    public function test_refuse_montant_negatif(): void
    {
        $data = $this->makeTransaction(['amount' => -100]);
        $this->postJson('/api/transactions', $data)
            ->assertStatus(422)
            ->assertJsonPath('error.code', 'VALIDATION_ERROR');
    }

    public function test_refuse_category_inexistante(): void
    {
        $data = $this->makeTransaction(['category_id' => 9999]);
        $this->postJson('/api/transactions', $data)
            ->assertStatus(422)
            ->assertJsonPath('error.code', 'VALIDATION_ERROR');
    }

    public function test_met_a_jour_une_transaction(): void
    {
        $transaction = Transaction::factory()->create(['amount' => 10000]);
        $this->putJson("/api/transactions/{$transaction->id}", ['amount' => 20000])
            ->assertStatus(200)
            ->assertJsonPath('data.amount', '20000.00');
        $this->assertDatabaseHas('transactions', ['id' => $transaction->id, 'amount' => 20000]);
    }

    public function test_supprime_une_transaction(): void
    {
        $transaction = Transaction::factory()->create();
        $this->deleteJson("/api/transactions/{$transaction->id}")
            ->assertStatus(204);
        $this->assertDatabaseMissing('transactions', ['id' => $transaction->id]);
    }
}
