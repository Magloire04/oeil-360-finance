<?php
namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            // Dépenses
            ['name' => 'Alimentation', 'type' => 'expense'],
            ['name' => 'Transport',    'type' => 'expense'],
            ['name' => 'Logement',     'type' => 'expense'],
            ['name' => 'Santé',        'type' => 'expense'],
            ['name' => 'Loisirs',      'type' => 'expense'],
            ['name' => 'Imprévus',     'type' => 'expense'],
            // Revenus
            ['name' => 'Salaire',      'type' => 'income'],
            ['name' => 'Freelance',    'type' => 'income'],
            // Les deux
            ['name' => 'Autre',        'type' => 'both'],
        ];

        foreach ($categories as $data) {
            Category::firstOrCreate(['name' => $data['name']], $data);
        }
    }
}
