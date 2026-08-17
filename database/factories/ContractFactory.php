<?php

namespace Database\Factories;

use App\Models\Customer;
use App\Models\Property;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ContractFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'customer_id' => Customer::factory(),
            'property_id' => Property::factory(),
            'total_price' => fake()->numberBetween(200, 900) * 1_000_000,
            'payment_type' => 'cash',
            'down_payment' => null,
            'installment_months' => null,
            'signed_date' => fake()->dateTimeBetween('-6 months', 'now')->format('Y-m-d'),
            'status' => 'active',
        ];
    }

    public function installment(): static
    {
        return $this->state(fn () => [
            'payment_type' => 'installment',
            'down_payment' => 50_000_000,
            'installment_months' => 6,
        ]);
    }
}
