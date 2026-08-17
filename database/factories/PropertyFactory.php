<?php

namespace Database\Factories;

use App\Models\Project;
use Illuminate\Database\Eloquent\Factories\Factory;

class PropertyFactory extends Factory
{
    public function definition(): array
    {
        return [
            'project_id' => Project::factory(),
            'type' => fake()->randomElement(['apartment', 'office', 'land']),
            'area' => fake()->randomFloat(2, 30, 150),
            'floor' => fake()->numberBetween(1, 16),
            'rooms_count' => fake()->numberBetween(1, 4),
            'price' => fake()->numberBetween(200, 900) * 1_000_000,
            'status' => 'available',
        ];
    }
}
