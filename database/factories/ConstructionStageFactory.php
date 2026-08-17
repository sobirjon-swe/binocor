<?php

namespace Database\Factories;

use App\Models\Project;
use Illuminate\Database\Eloquent\Factories\Factory;

class ConstructionStageFactory extends Factory
{
    public function definition(): array
    {
        return [
            'project_id' => Project::factory(),
            'name' => fake()->randomElement(['Poydevor', 'Karkas', 'Devor', 'Tomlash', 'Pardozlash']),
            'progress_percent' => fake()->numberBetween(0, 100),
            'planned_date' => fake()->dateTimeBetween('now', '+3 months')->format('Y-m-d'),
            'actual_date' => null,
        ];
    }
}
