<?php

namespace Tests\Unit;

use Tests\TestCase;

class PublicSeoFilesTest extends TestCase
{
    private const SITE_URL = 'https://oeil360finance.bytechnum.com';

    public function test_robots_declare_le_sitemap(): void
    {
        $robots = file_get_contents(public_path('robots.txt'));

        $this->assertStringContainsString('Sitemap: '.self::SITE_URL.'/sitemap.xml', $robots);
    }

    public function test_sitemap_liste_les_pages_publiques(): void
    {
        $urls = $this->sitemapUrls();

        $this->assertSame([
            self::SITE_URL.'/',
            self::SITE_URL.'/politique-confidentialite',
        ], $urls);
    }

    public function test_sitemap_ne_liste_aucune_page_protegee(): void
    {
        foreach ($this->sitemapUrls() as $url) {
            $this->assertDoesNotMatchRegularExpression(
                '#/(dashboard|transactions|categories|accounts|transfers|recurring|help|mon-compte|admin|auth|consent|compte-supprime)#',
                $url
            );
        }
    }

    /** @return list<string> */
    private function sitemapUrls(): array
    {
        $xml = simplexml_load_file(public_path('sitemap.xml'));
        $this->assertNotFalse($xml, 'sitemap.xml doit être un XML valide');

        $xml->registerXPathNamespace('sm', 'http://www.sitemaps.org/schemas/sitemap/0.9');

        return array_map('strval', $xml->xpath('//sm:url/sm:loc'));
    }
}
