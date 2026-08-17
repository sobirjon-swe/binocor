<?php

namespace Database\Factories;

use App\Models\Contract;
use Illuminate\Database\Eloquent\Factories\Factory;

class PaymentFactory extends Factory
{
    public function definition(): array
    {
        return [
            'contract_id' => Contract::factory(),
            'amount' => fake()->numberBetween(5, 50) * 1_000_000,
            'due_date' => fake()->dateTimeBetween('-2 months', '+2 months')->format('Y-m-d'),
            'paid_date' => null,
            'status' => 'pending',
        ];
    }

    public function overdue(): static
    {
        return $this->state(fn () => [
            'due_date' => now()->subDays(5)->format('Y-m-d'),
            'status' => 'pending',
        ]);
    }

    public function paid(): static
    {
        return $this->state(fn () => [
            'paid_date' => now()->format('Y-m-d'),
            'status' => 'paid',
        ]);
    }
}
