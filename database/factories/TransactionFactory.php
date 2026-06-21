<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Account;
use App\Models\Transaction;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Transaction>
 */
class TransactionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id'                  => null,
            'amount'                   => fake()->randomFloat(2, 100, 500000),
            'sense'                    => fake()->randomElement(['income', 'expense']),
            'transaction_date'         => fake()->dateTimeBetween('2026-01-01', '2026-12-31')->format('Y-m-d'),
            'category_id'              => Category::factory(),
            'account_id'               => Account::factory(),
            'note'                     => null,
            'recurring_transaction_id' => null,
        ];
    }
}
