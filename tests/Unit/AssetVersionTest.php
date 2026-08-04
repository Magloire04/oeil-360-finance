<?php

namespace Tests\Unit;

use Tests\TestCase;

class AssetVersionTest extends TestCase
{
    public function test_ajoute_une_version_pour_un_fichier_existant(): void
    {
        // public/css/app.css est versionné dans le dépôt.
        $url = assetVersion('css/app.css');

        $this->assertMatchesRegularExpression('#^/css/app\.css\?v=\d+$#', $url);
    }

    public function test_normalise_le_slash_initial(): void
    {
        $this->assertStringStartsWith('/js/utils.js?v=', assetVersion('/js/utils.js'));
    }

    public function test_sans_version_si_fichier_absent(): void
    {
        $url = assetVersion('js/fichier-inexistant-xyz.js');

        $this->assertSame('/js/fichier-inexistant-xyz.js', $url);
    }
}
