<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Account;
use App\Models\RecurringTransaction;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<RecurringTransaction>
 */
class RecurringTransactionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $startDate = fake()->dateTimeBetween('2026-01-01', '2026-12-31')->format('Y-m-d');
        return [
            'amount' => fake()->randomFloat(2, 100, 500000),
            'sense' => fake()->randomElement(['income', 'expense']),
            'frequency' => fake()->randomElement(['daily', 'weekly', 'monthly', 'yearly']),
            'start_date' => $startDate,
            'next_occurrence_date' => $startDate,
            'category_id' => Category::factory(),
            'account_id' => Account::factory(),
            'note' => null,
            'is_active' => true,
        ];
    }
}
