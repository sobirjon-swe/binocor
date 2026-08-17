<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class CustomerFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'full_name' => fake()->name(),
            'phone' => '+998'.fake()->numerify('#########'),
            'passport_number' => strtoupper(fake()->bothify('??#######')),
            'address' => fake()->address(),
            'lead_status' => 'interested',
        ];
    }
}
