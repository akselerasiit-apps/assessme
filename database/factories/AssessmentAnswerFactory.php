<?php

namespace Database\Factories;

use App\Models\Assessment;
use App\Models\GamoObjective;
use App\Models\GamoQuestion;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\AssessmentAnswer>
 */
class AssessmentAnswerFactory extends Factory
{
    protected $model = \App\Models\AssessmentAnswer::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'assessment_id' => Assessment::factory(),
            'question_id' => GamoQuestion::factory(),
            'gamo_objective_id' => GamoObjective::factory(),
            'answer_text' => fake()->paragraph(),
            'maturity_level' => fake()->numberBetween(0, 5),
            'capability_score' => fake()->randomFloat(2, 0, 5),
            'notes' => fake()->sentence(),
            'answered_by' => User::factory(),
            'answered_at' => fake()->dateTimeBetween('-1 month', 'now'),
        ];
    }
}
