<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Transaction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_liste_toutes_les_categories_triees_par_nom(): void
    {
        Category::factory()->create(['name' => 'Zèbre']);
        Category::factory()->create(['name' => 'Alimentation']);

        $response = $this->getJson('/api/categories');

        $response->assertStatus(200)
            ->assertJsonStructure(['data', 'meta', 'error'])
            ->assertJsonPath('error', null);

        $names = collect($response->json('data'))->pluck('name')->values()->toArray();
        $this->assertSame(['Alimentation', 'Zèbre'], $names);
    }

    public function test_cree_une_categorie_valide(): void
    {
        $response = $this->postJson('/api/categories', [
            'name' => 'Transport',
            'type' => 'expense',
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('data.name', 'Transport')
            ->assertJsonPath('data.type', 'expense')
            ->assertJsonPath('data.is_archived', false);

        $this->assertDatabaseHas('categories', ['name' => 'Transport']);
    }

    public function test_refuse_une_categorie_sans_nom(): void
    {
        $response = $this->postJson('/api/categories', ['type' => 'expense']);

        $response->assertStatus(422)
            ->assertJsonPath('error.code', 'VALIDATION_ERROR');
    }

    public function test_refuse_un_type_invalide(): void
    {
        $response = $this->postJson('/api/categories', [
            'name' => 'Test',
            'type' => 'invalid_type',
        ]);

        $response->assertStatus(422)
            ->assertJsonPath('error.code', 'VALIDATION_ERROR');
    }

    public function test_affiche_une_categorie_par_id(): void
    {
        $category = Category::factory()->create(['name' => 'Santé', 'type' => 'expense']);

        $this->getJson("/api/categories/{$category->id}")
            ->assertStatus(200)
            ->assertJsonPath('data.name', 'Santé');
    }

    public function test_retourne_404_pour_une_categorie_inexistante(): void
    {
        $this->getJson('/api/categories/9999')
            ->assertStatus(404)
            ->assertJsonPath('error.code', 'NOT_FOUND');
    }

    public function test_met_a_jour_une_categorie(): void
    {
        $category = Category::factory()->create(['name' => 'Loisirs']);

        $this->putJson("/api/categories/{$category->id}", ['name' => 'Loisirs & Sport'])
            ->assertStatus(200)
            ->assertJsonPath('data.name', 'Loisirs & Sport');

        $this->assertDatabaseHas('categories', ['id' => $category->id, 'name' => 'Loisirs & Sport']);
    }

    public function test_supprime_une_categorie_non_utilisee_204(): void
    {
        $category = Category::factory()->create();

        $this->deleteJson("/api/categories/{$category->id}")
            ->assertStatus(204);

        $this->assertDatabaseMissing('categories', ['id' => $category->id]);
    }

    public function test_archive_une_categorie_utilisee_au_lieu_de_la_supprimer(): void
    {
        $category = Category::factory()->create();
        // Créer une transaction liée à cette catégorie
        Transaction::factory()->create(['category_id' => $category->id]);

        $this->deleteJson("/api/categories/{$category->id}")
            ->assertStatus(200)
            ->assertJsonPath('data.archived', true);

        $this->assertDatabaseHas('categories', ['id' => $category->id, 'is_archived' => true]);
    }

    public function test_restaure_une_categorie_archivee(): void
    {
        $category = Category::factory()->create(['is_archived' => true]);

        $this->postJson("/api/categories/{$category->id}/restore")
            ->assertStatus(200)
            ->assertJsonPath('data.is_archived', false);

        $this->assertDatabaseHas('categories', ['id' => $category->id, 'is_archived' => false]);
    }
}
