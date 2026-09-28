<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
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

    public function test_la_page_compte_supprime_est_exclue_des_moteurs(): void
    {
        $this->get('/compte-supprime')
            ->assertOk()
            ->assertSee('<meta name="robots" content="noindex">', false);
    }
}
