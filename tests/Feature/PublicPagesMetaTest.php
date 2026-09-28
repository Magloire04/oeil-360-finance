<?php

namespace Tests\Feature;

use App\Http\Controllers\ConsentController;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class PublicPagesMetaTest extends TestCase
{
    use RefreshDatabase;

    public function test_la_politique_a_une_meta_description_de_longueur_raisonnable(): void
    {
        $html = $this->get('/politique-confidentialite')->assertOk()->getContent();

        $this->assertSame(1, preg_match('#<meta name="description" content="([^"]+)">#', $html, $match));
        $length = mb_strlen(html_entity_decode($match[1]));
        $this->assertGreaterThanOrEqual(50, $length);
        $this->assertLessThanOrEqual(160, $length);
    }

    public function test_la_politique_declare_son_url_canonique(): void
    {
        $this->get('/politique-confidentialite')
            ->assertOk()
            ->assertSee('<link rel="canonical" href="'.url('/politique-confidentialite').'">', false);
    }

    public function test_la_politique_indique_ou_sont_hebergees_les_donnees(): void
    {
        $this->get('/politique-confidentialite')
            ->assertOk()
            ->assertSee('Hébergement et transfert des données hors du Bénin')
            ->assertSee('Spaceship')
            ->assertSee('Amsterdam (Pays-Bas')
            ->assertSee('Auth0')
            ->assertSee('États-Unis');
    }

    public function test_la_date_de_mise_a_jour_de_la_politique_ne_suit_pas_la_date_du_jour(): void
    {
        Carbon::setTestNow('2030-01-15');

        $this->get('/politique-confidentialite')
            ->assertOk()
            ->assertSee('dernière mise à jour : '.Carbon::parse(ConsentController::POLICY_UPDATED_AT)->format('d/m/Y'))
            ->assertDontSee('15/01/2030');
    }

    public function test_la_page_compte_supprime_est_exclue_des_moteurs(): void
    {
        $this->get('/compte-supprime')
            ->assertOk()
            ->assertSee('<meta name="robots" content="noindex">', false);
    }
}
