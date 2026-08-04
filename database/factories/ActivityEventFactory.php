<?php

namespace Database\Factories;

use App\Models\ActivityEvent;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ActivityEvent>
 */
class ActivityEventFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $feature = fake()->randomElement([
            'dashboard', 'transactions.index', 'transactions.store',
            'categories.index', 'accounts.index', 'transfers.store',
        ]);

        return [
            'user_id' => User::factory(),
            'feature' => $feature,
            'method' => fake()->randomElement(['GET', 'POST', 'PUT', 'DELETE']),
            'path' => str_replace('.', '/', $feature),
            'status' => 200,
            'duration_ms' => fake()->numberBetween(5, 400),
            'created_at' => now(),
        ];
    }

    public function feature(string $feature): static
    {
        return $this->state(fn (array $attributes) => [
            'feature' => $feature,
        ]);
    }

    public function failed(int $status = 500): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => $status,
        ]);
    }
}
