<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\Transaction;
use App\Models\Transfer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AccountTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->actingAs($this->user);
    }

    public function test_liste_les_comptes_avec_balance(): void
    {
        Account::factory()->create(['user_id' => $this->user->id, 'name' => 'EspÃ¨ces', 'initial_balance' => 10000]);

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
            ->assertJsonPath('data.balance', 50000.0);
        $this->assertDatabaseHas('accounts', ['name' => 'MTN Mobile Money', 'user_id' => $this->user->id]);
    }

    public function test_refuse_type_invalide(): void
    {
        $this->postJson('/api/accounts', ['name' => 'Test', 'type' => 'crypto'])
            ->assertStatus(422)
            ->assertJsonPath('error.code', 'VALIDATION_ERROR');
    }

    public function test_balance_calcule_correctement(): void
    {
        $account = Account::factory()->create(['user_id' => $this->user->id, 'initial_balance' => 100000]);
        Transaction::factory()->create(['user_id' => $this->user->id, 'account_id' => $account->id, 'sense' => 'income',  'amount' => 50000]);
        Transaction::factory()->create(['user_id' => $this->user->id, 'account_id' => $account->id, 'sense' => 'expense', 'amount' => 20000]);

        $otherAccount = Account::factory()->create(['user_id' => $this->user->id, 'initial_balance' => 0]);
        Transfer::factory()->create(['user_id' => $this->user->id, 'to_account_id' => $account->id,   'from_account_id' => $otherAccount->id, 'amount' => 10000]);
        Transfer::factory()->create(['user_id' => $this->user->id, 'from_account_id' => $account->id, 'to_account_id' => $otherAccount->id, 'amount' => 5000]);

        // Balance = 100000 + 50000 - 20000 + 10000 - 5000 = 135000
        $this->getJson("/api/accounts/{$account->id}")
            ->assertStatus(200)
            ->assertJsonPath('data.balance', 135000.0);
    }

    public function test_archive_un_compte_utilise(): void
    {
        $account = Account::factory()->create(['user_id' => $this->user->id]);
        Transaction::factory()->create(['user_id' => $this->user->id, 'account_id' => $account->id]);

        $this->deleteJson("/api/accounts/{$account->id}")
            ->assertStatus(200)
            ->assertJsonPath('data.archived', true);
        $this->assertDatabaseHas('accounts', ['id' => $account->id, 'is_archived' => true]);
    }

    public function test_supprime_un_compte_vide(): void
    {
        $account = Account::factory()->create(['user_id' => $this->user->id]);

        $this->deleteJson("/api/accounts/{$account->id}")
            ->assertStatus(204);
        $this->assertDatabaseMissing('accounts', ['id' => $account->id]);
    }

    public function test_restaure_un_compte_archive(): void
    {
        $account = Account::factory()->create(['user_id' => $this->user->id, 'is_archived' => true]);

        $this->postJson("/api/accounts/{$account->id}/restore")
            ->assertStatus(200)
            ->assertJsonPath('data.is_archived', false);
    }

    public function test_retourne_404_pour_un_compte_dun_autre_utilisateur(): void
    {
        $other = User::factory()->create();
        $account = Account::factory()->create(['user_id' => $other->id]);

        $this->getJson("/api/accounts/{$account->id}")
            ->assertStatus(404);
    }
}
