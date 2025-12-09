<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\GamoObjective>
 */
class GamoObjectiveFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'code' => 'GAMO-' . fake()->unique()->bothify('??##'),
            'name' => fake()->sentence(4),
            'name_id' => fake()->sentence(4),
            'description' => fake()->paragraph(),
            'description_id' => fake()->paragraph(),
            'category' => fake()->randomElement(['EDM', 'APO', 'BAI', 'DSS', 'MEA']),
            'objective_order' => fake()->numberBetween(1, 10),
            'is_active' => true,
        ];
    }
}
