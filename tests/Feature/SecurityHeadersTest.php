<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SecurityHeadersTest extends TestCase
{
    use RefreshDatabase;

    public function test_une_page_web_impose_https_pendant_un_an(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertHeader('Strict-Transport-Security', 'max-age=31536000');
    }

    public function test_une_page_web_interdit_la_detection_du_type_mime(): void
    {
        $this->get('/')->assertHeader('X-Content-Type-Options', 'nosniff');
    }

    public function test_une_page_web_refuse_l_integration_par_un_autre_site(): void
    {
        $this->get('/')->assertHeader('X-Frame-Options', 'SAMEORIGIN');
    }

    public function test_une_page_web_limite_le_referent_transmis(): void
    {
        $this->get('/')->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin');
    }

    public function test_une_reponse_api_porte_aussi_les_en_tetes(): void
    {
        $this->getJson('/api/categories')
            ->assertUnauthorized()
            ->assertHeader('X-Content-Type-Options', 'nosniff')
            ->assertHeader('Strict-Transport-Security', 'max-age=31536000');
    }
}
