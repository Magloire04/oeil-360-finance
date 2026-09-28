<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LandingTest extends TestCase
{
    use RefreshDatabase;

    public function test_landing_is_accessible_without_authentication(): void
    {
        $this->get('/')->assertStatus(200);
    }

    public function test_landing_offers_a_login_call_to_action(): void
    {
        $this->get('/')
            ->assertStatus(200)
            ->assertSee('Se connecter')
            ->assertSee(route('login'), false);
    }

    public function test_landing_exposes_the_presentation_video(): void
    {
        $this->get('/')
            ->assertStatus(200)
            ->assertSee('/videos/oeil360-promo.mp4', false);
    }

    public function test_landing_does_not_leak_protected_navigation(): void
    {
        $this->get('/')
            ->assertStatus(200)
            ->assertDontSee('/transactions', false);
    }

    public function test_authenticated_user_is_redirected_to_dashboard(): void
    {
        $user = User::factory()->create(); // factory par défaut = consenti

        $this->actingAs($user)->get('/')->assertRedirect('/dashboard');
    }

    public function test_privacy_policy_remains_public(): void
    {
        $this->get('/politique-confidentialite')->assertStatus(200);
    }

    public function test_landing_meta_description_fits_in_search_results(): void
    {
        $html = $this->get('/')->assertStatus(200)->getContent();

        $this->assertMatchesRegularExpression('#<meta name="description" content="([^"]+)">#', $html);
        preg_match('#<meta name="description" content="([^"]+)">#', $html, $match);
        $this->assertLessThanOrEqual(160, mb_strlen(html_entity_decode($match[1])));
    }

    public function test_landing_declares_its_canonical_url(): void
    {
        $this->get('/')
            ->assertStatus(200)
            ->assertSee('<link rel="canonical" href="'.url('/').'">', false);
    }

    public function test_landing_exposes_link_preview_tags(): void
    {
        $this->get('/')
            ->assertStatus(200)
            ->assertSee('<meta property="og:type" content="website">', false)
            ->assertSee('<meta property="og:title" content="', false)
            ->assertSee('<meta property="og:description" content="', false)
            ->assertSee('<meta property="og:url" content="'.url('/').'">', false)
            ->assertSee('<meta property="og:image" content="'.url('/images/oeil360-promo-poster.jpg').'">', false)
            ->assertSee('<meta name="twitter:card" content="summary_large_image">', false);
    }

    public function test_landing_exposes_website_and_organization_structured_data(): void
    {
        $html = $this->get('/')->assertStatus(200)->getContent();

        preg_match('#<script type="application/ld\+json">(.+?)</script>#s', $html, $match);
        $this->assertNotEmpty($match, 'bloc JSON-LD absent');

        $types = array_column(json_decode($match[1], true, flags: JSON_THROW_ON_ERROR)['@graph'], '@type');
        $this->assertSame(['WebSite', 'Organization'], $types);
    }
}
