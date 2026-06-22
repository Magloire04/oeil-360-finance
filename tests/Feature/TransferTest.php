<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\Transfer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TransferTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->actingAs($this->user);
    }

    public function test_cree_un_transfert_valide(): void
    {
        $from = Account::factory()->create(['user_id' => $this->user->id, 'initial_balance' => 100000]);
        $to = Account::factory()->create(['user_id' => $this->user->id, 'initial_balance' => 0]);

        $response = $this->postJson('/api/transfers', [
            'amount' => 30000,
            'transfer_date' => '2026-06-15',
            'from_account_id' => $from->id,
            'to_account_id' => $to->id,
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('data.amount', '30000.00');
        $this->assertDatabaseHas('transfers', ['amount' => 30000, 'user_id' => $this->user->id]);
    }

    public function test_refuse_transfert_vers_meme_compte(): void
    {
        $account = Account::factory()->create(['user_id' => $this->user->id]);

        $this->postJson('/api/transfers', [
            'amount' => 10000,
            'transfer_date' => '2026-06-15',
            'from_account_id' => $account->id,
            'to_account_id' => $account->id,
        ])->assertStatus(422)
            ->assertJsonPath('error.code', 'VALIDATION_ERROR');
    }

    public function test_refuse_montant_zero(): void
    {
        $from = Account::factory()->create(['user_id' => $this->user->id]);
        $to = Account::factory()->create(['user_id' => $this->user->id]);

        $this->postJson('/api/transfers', [
            'amount' => 0,
            'transfer_date' => '2026-06-15',
            'from_account_id' => $from->id,
            'to_account_id' => $to->id,
        ])->assertStatus(422)
            ->assertJsonPath('error.code', 'VALIDATION_ERROR');
    }

    public function test_le_transfert_modifie_les_soldes(): void
    {
        $from = Account::factory()->create(['user_id' => $this->user->id, 'initial_balance' => 100000]);
        $to = Account::factory()->create(['user_id' => $this->user->id, 'initial_balance' => 0]);

        $this->postJson('/api/transfers', [
            'amount' => 40000,
            'transfer_date' => '2026-06-15',
            'from_account_id' => $from->id,
            'to_account_id' => $to->id,
        ])->assertStatus(201);

        $this->assertSame(60000.0, $this->getJson("/api/accounts/{$from->id}")->json('data.balance'));
        $this->assertSame(40000.0, $this->getJson("/api/accounts/{$to->id}")->json('data.balance'));
    }

    public function test_le_transfert_naffecte_pas_income_expense_global(): void
    {
        $from = Account::factory()->create(['user_id' => $this->user->id, 'initial_balance' => 100000]);
        $to = Account::factory()->create(['user_id' => $this->user->id, 'initial_balance' => 0]);

        Transfer::create([
            'user_id' => $this->user->id,
            'amount' => 50000,
            'transfer_date' => '2026-06-15',
            'from_account_id' => $from->id,
            'to_account_id' => $to->id,
        ]);

        $response = $this->getJson('/api/accounts');
        $totalBalance = collect($response->json('data'))->sum('balance');
        $this->assertSame(100000.0, $totalBalance);
    }

    public function test_filtre_par_compte(): void
    {
        $a1 = Account::factory()->create(['user_id' => $this->user->id, 'initial_balance' => 100000]);
        $a2 = Account::factory()->create(['user_id' => $this->user->id, 'initial_balance' => 100000]);
        $a3 = Account::factory()->create(['user_id' => $this->user->id, 'initial_balance' => 100000]);

        Transfer::factory()->create(['user_id' => $this->user->id, 'from_account_id' => $a1->id, 'to_account_id' => $a2->id]);
        Transfer::factory()->create(['user_id' => $this->user->id, 'from_account_id' => $a2->id, 'to_account_id' => $a3->id]);

        $response = $this->getJson("/api/transfers?account_id={$a1->id}");
        $this->assertCount(1, $response->json('data'));
    }

    public function test_modifie_un_transfert(): void
    {
        $from = Account::factory()->create(['user_id' => $this->user->id, 'initial_balance' => 100000]);
        $to = Account::factory()->create(['user_id' => $this->user->id, 'initial_balance' => 0]);
        $transfer = Transfer::factory()->create([
            'user_id' => $this->user->id,
            'from_account_id' => $from->id,
            'to_account_id' => $to->id,
            'amount' => 20000,
        ]);

        $this->putJson("/api/transfers/{$transfer->id}", ['amount' => 35000])
            ->assertStatus(200)
            ->assertJsonPath('data.amount', '35000.00');
    }

    public function test_supprime_un_transfert(): void
    {
        $from = Account::factory()->create(['user_id' => $this->user->id, 'initial_balance' => 100000]);
        $to = Account::factory()->create(['user_id' => $this->user->id, 'initial_balance' => 0]);
        $transfer = Transfer::factory()->create([
            'user_id' => $this->user->id,
            'from_account_id' => $from->id,
            'to_account_id' => $to->id,
        ]);

        $this->deleteJson("/api/transfers/{$transfer->id}")
            ->assertStatus(204);
        $this->assertDatabaseMissing('transfers', ['id' => $transfer->id]);
    }
}
