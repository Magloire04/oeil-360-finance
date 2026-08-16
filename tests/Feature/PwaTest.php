<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PwaTest extends TestCase
{
    use RefreshDatabase;

    public function test_la_landing_expose_les_metadonnees_pwa(): void
    {
        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee('manifest.webmanifest', false);
        $response->assertSee('name="theme-color"', false);
        $response->assertSee("navigator.serviceWorker.register('/sw.js')", false);
        $response->assertSee('apple-touch-icon', false);
    }

    public function test_la_politique_expose_le_manifest(): void
    {
        $this->get('/politique-confidentialite')
            ->assertOk()
            ->assertSee('manifest.webmanifest', false);
    }
}
