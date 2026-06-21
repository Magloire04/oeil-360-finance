<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Les catégories et comptes par défaut sont créés via UserBootstrapService
        // à la première connexion de chaque utilisateur — pas de seed global.
    }
}
