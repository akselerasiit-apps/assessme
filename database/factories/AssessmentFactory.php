<?php

namespace Database\Factories;

use App\Models\Company;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Assessment>
 */
class AssessmentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'code' => 'ASM-' . fake()->unique()->numerify('######'),
            'title' => fake()->sentence(5),
            'description' => fake()->paragraph(),
            'company_id' => Company::factory(),
            'assessment_type' => fake()->randomElement(['initial', 'periodic', 'specific']),
            'scope_type' => fake()->randomElement(['full', 'tailored']),
            'status' => fake()->randomElement(['draft', 'in_progress', 'completed', 'reviewed']),
            'assessment_period_start' => fake()->dateTimeBetween('-1 month', 'now'),
            'assessment_period_end' => fake()->dateTimeBetween('now', '+2 months'),
            'created_by' => User::factory(),
            'progress_percentage' => fake()->numberBetween(0, 100),
        ];
    }

    /**
     * Indicate that the assessment is in draft status.
     */
    public function draft(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'draft',
            'progress_percentage' => 0,
        ]);
    }

    /**
     * Indicate that the assessment is completed.
     */
    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'completed',
            'progress_percentage' => 100,
        ]);
    }
}
