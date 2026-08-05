<?php

namespace Tests\Feature;

use App\Models\DeletedIdentity;
use App\Models\User;
use App\Repositories\Auth0UserRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DeletedIdentityTest extends TestCase
{
    use RefreshDatabase;

    private function repo(): Auth0UserRepository
    {
        return app(Auth0UserRepository::class);
    }

    public function test_une_nouvelle_identite_cree_bien_un_utilisateur(): void
    {
        $user = $this->repo()->fromSession(['sub' => 'auth0|new1', 'email' => 'a@b.c', 'name' => 'A']);

        $this->assertNotNull($user);
        $this->assertDatabaseHas('users', ['auth0_id' => 'auth0|new1']);
    }

    public function test_une_identite_supprimee_n_est_pas_recreee(): void
    {
        // Première connexion → création
        $user = $this->repo()->fromSession(['sub' => 'auth0|blk', 'email' => 'a@b.c']);
        $this->assertNotNull($user);

        // Suppression : blocage + effacement
        DeletedIdentity::block('auth0|blk');
        User::where('auth0_id', 'auth0|blk')->delete();

        // Reconnexion via SSO : bloquée, aucune recréation
        $again = $this->repo()->fromSession(['sub' => 'auth0|blk', 'email' => 'a@b.c']);
        $this->assertNull($again);
        $this->assertDatabaseMissing('users', ['auth0_id' => 'auth0|blk']);
    }

    public function test_la_suppression_enregistre_l_identite_bloquee(): void
    {
        $user = User::factory()->create(['auth0_id' => 'auth0|del1']);

        $this->actingAs($user, 'web')
            ->deleteJson('/api/profile')
            ->assertStatus(200);

        $this->assertTrue(DeletedIdentity::isBlocked('auth0|del1'));
    }

    public function test_la_page_compte_supprime_est_publique(): void
    {
        $this->get('/compte-supprime')
            ->assertStatus(200)
            ->assertSee('Compte supprimé');
    }
}
