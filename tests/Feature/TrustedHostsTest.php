<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Tests\TestCase;

/**
 * Hôtes de confiance (audit des dépendances du 07/11/2026, GHSA-5mx8-mwrp-5693) :
 * seul l'hôte d'APP_URL est accepté, pour que les URL générées (canonical, og:url, liens)
 * ne puissent pas pointer vers un hôte choisi par le client.
 */
class TrustedHostsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config(['app.url' => 'https://oeil360finance.bytechnum.com']);

        // TrustHosts est inactif en environnement local et pendant les tests.
        $this->app['env'] = 'production';
    }

    protected function tearDown(): void
    {
        // La liste des hôtes de confiance est statique côté Symfony : ne pas la laisser fuir.
        Request::setTrustedHosts([]);

        parent::tearDown();
    }

    public function test_l_hote_officiel_est_accepte(): void
    {
        $this->get('https://oeil360finance.bytechnum.com/')->assertOk();
    }

    public function test_un_hote_inconnu_est_refuse(): void
    {
        $this->get('https://attaquant.example/')->assertBadRequest();
    }

    public function test_un_hote_contenant_l_hote_officiel_est_refuse(): void
    {
        $this->get('https://oeil360finance.bytechnum.com.attaquant.example/')->assertBadRequest();
        $this->get('https://oeil360financeXbytechnum.com/')->assertBadRequest();
    }

    public function test_un_sous_domaine_est_refuse(): void
    {
        $this->get('https://admin.oeil360finance.bytechnum.com/')->assertBadRequest();
    }
}
