<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\Transaction;
use App\Models\Transfer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AccountTest extends TestCase
{
    use RefreshDatabase;

    public function test_liste_les_comptes_avec_balance(): void
    {
        Account::factory()->create(['name' => 'Espèces', 'initial_balance' => 10000]);
        $response = $this->getJson('/api/accounts');
        $response->assertStatus(200)
            ->assertJsonStructure(['data', 'meta', 'error'])
            ->assertJsonPath('error', null);
        $this->assertArrayHasKey('balance', $response->json('data.0'));
    }

    public function test_cree_un_compte_valide(): void
    {
        $response = $this->postJson('/api/accounts', [
            'name' => 'MTN Mobile Money',
            'type' => 'mobile_money',
            'initial_balance' => 50000,
        ]);
        $response->assertStatus(201)
            ->assertJsonPath('data.name', 'MTN Mobile Money')
            ->assertJsonPath('data.type', 'mobile_money')
            ->assertJsonPath('data.balance', 50000);
        $this->assertDatabaseHas('accounts', ['name' => 'MTN Mobile Money']);
    }

    public function test_refuse_type_invalide(): void
    {
        $this->postJson('/api/accounts', ['name' => 'Test', 'type' => 'crypto'])
            ->assertStatus(422)
            ->assertJsonPath('error.code', 'VALIDATION_ERROR');
    }

    public function test_balance_calcule_correctement(): void
    {
        $account = Account::factory()->create(['initial_balance' => 100000]);
        // Revenu
        Transaction::factory()->create(['account_id' => $account->id, 'sense' => 'income', 'amount' => 50000]);
        // Dépense
        Transaction::factory()->create(['account_id' => $account->id, 'sense' => 'expense', 'amount' => 20000]);
        // Transfert entrant
        $otherAccount = Account::factory()->create(['initial_balance' => 0]);
        Transfer::factory()->create(['to_account_id' => $account->id, 'from_account_id' => $otherAccount->id, 'amount' => 10000]);
        // Transfert sortant
        Transfer::factory()->create(['from_account_id' => $account->id, 'to_account_id' => $otherAccount->id, 'amount' => 5000]);

        // Balance attendue = 100000 + 50000 - 20000 + 10000 - 5000 = 135000
        $response = $this->getJson("/api/accounts/{$account->id}");
        $response->assertStatus(200)
            ->assertJsonPath('data.balance', 135000);
    }

    public function test_archive_un_compte_utilise(): void
    {
        $account = Account::factory()->create();
        Transaction::factory()->create(['account_id' => $account->id]);

        $this->deleteJson("/api/accounts/{$account->id}")
            ->assertStatus(200)
            ->assertJsonPath('data.archived', true);
        $this->assertDatabaseHas('accounts', ['id' => $account->id, 'is_archived' => true]);
    }

    public function test_supprime_un_compte_vide(): void
    {
        $account = Account::factory()->create();

        $this->deleteJson("/api/accounts/{$account->id}")
            ->assertStatus(204);
        $this->assertDatabaseMissing('accounts', ['id' => $account->id]);
    }

    public function test_restaure_un_compte_archive(): void
    {
        $account = Account::factory()->create(['is_archived' => true]);

        $this->postJson("/api/accounts/{$account->id}/restore")
            ->assertStatus(200)
            ->assertJsonPath('data.is_archived', false);
    }
}
