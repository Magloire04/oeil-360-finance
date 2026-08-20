<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\Category;
use App\Models\RecurringTransaction;
use App\Models\Transaction;
use App\Models\Transfer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StatementExportTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $account = Account::factory()->create(['user_id' => $this->user->id]);
        $other = Account::factory()->create(['user_id' => $this->user->id]);
        $category = Category::factory()->create(['user_id' => $this->user->id]);

        Transaction::factory()->count(3)->create([
            'user_id' => $this->user->id,
            'account_id' => $account->id,
            'category_id' => $category->id,
        ]);
        Transfer::factory()->create([
            'user_id' => $this->user->id,
            'from_account_id' => $account->id,
            'to_account_id' => $other->id,
        ]);
        RecurringTransaction::factory()->create([
            'user_id' => $this->user->id,
            'account_id' => $account->id,
            'category_id' => $category->id,
        ]);
    }

    public function test_pdf_download_returns_a_pdf_attachment(): void
    {
        $response = $this->actingAs($this->user)->get('/mon-compte/releve?format=pdf');

        $response->assertStatus(200);
        $this->assertStringContainsString('application/pdf', $response->headers->get('Content-Type') ?? '');
        $this->assertStringContainsString('attachment', $response->headers->get('Content-Disposition') ?? '');
        $this->assertStringContainsString('.pdf', $response->headers->get('Content-Disposition') ?? '');
        $this->assertNotEmpty($response->getContent());
    }

    public function test_export_defaults_to_pdf_when_no_format_given(): void
    {
        $response = $this->actingAs($this->user)->get('/mon-compte/releve');

        $response->assertStatus(200);
        $this->assertStringContainsString('application/pdf', $response->headers->get('Content-Type') ?? '');
    }

    public function test_excel_download_returns_a_spreadsheet_attachment(): void
    {
        $response = $this->actingAs($this->user)->get('/mon-compte/releve?format=excel');

        $response->assertStatus(200);
        $this->assertStringContainsString('spreadsheetml.sheet', $response->headers->get('Content-Type') ?? '');
        $this->assertStringContainsString('attachment', $response->headers->get('Content-Disposition') ?? '');
        $this->assertStringContainsString('.xlsx', $response->headers->get('Content-Disposition') ?? '');
    }

    public function test_period_can_be_passed_and_is_accepted(): void
    {
        $this->actingAs($this->user)
            ->get('/mon-compte/releve?format=pdf&start=2026-01-01&end=2026-12-31')
            ->assertStatus(200);
    }

    public function test_empty_date_strings_from_the_form_are_accepted(): void
    {
        // Le formulaire GET envoie start=&end= (vides) quand aucune période n'est choisie.
        $this->actingAs($this->user)
            ->get('/mon-compte/releve?format=pdf&start=&end=')
            ->assertStatus(200);
    }

    public function test_invalid_format_is_rejected(): void
    {
        $this->actingAs($this->user)
            ->get('/mon-compte/releve?format=csv')
            ->assertStatus(422);
    }

    public function test_end_date_before_start_date_is_rejected(): void
    {
        $this->actingAs($this->user)
            ->get('/mon-compte/releve?start=2026-03-31&end=2026-03-01')
            ->assertStatus(422);
    }

    public function test_unauthenticated_user_is_redirected_to_login(): void
    {
        $this->get('/mon-compte/releve')->assertRedirect('/auth/login');
    }
}
