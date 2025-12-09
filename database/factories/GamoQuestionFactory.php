<?php

namespace Database\Factories;

use App\Models\GamoObjective;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\GamoQuestion>
 */
class GamoQuestionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'code' => 'Q-' . fake()->unique()->numerify('####'),
            'gamo_objective_id' => GamoObjective::factory(),
            'question_text' => fake()->sentence() . '?',
            'guidance' => fake()->paragraph(),
            'evidence_requirement' => fake()->sentence(),
            'question_type' => fake()->randomElement(['text', 'rating', 'yes_no', 'evidence']),
            'maturity_level' => fake()->numberBetween(1, 5),
            'required' => true,
            'question_order' => fake()->numberBetween(1, 10),
            'is_active' => true,
        ];
    }
}
