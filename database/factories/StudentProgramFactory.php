<?php

namespace Database\Factories;

use App\Models\Program;
use App\Models\StudentProgram;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\StudentProgram>
 */
class StudentProgramFactory extends Factory
{
    protected $model = StudentProgram::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $enrollmentDate = fake()->dateTimeBetween('-2 years', 'now');
        $expectedGraduation = (clone $enrollmentDate)->modify('+' . fake()->numberBetween(1, 4) . ' years');

        return [
            'student_id' => User::factory()->student(),
            'program_id' => Program::factory(),
            'enrollment_date' => $enrollmentDate,
            'expected_graduation' => $expectedGraduation,
            'status' => fake()->randomElement(['enrolled', 'enrolled', 'enrolled', 'completed', 'withdrawn']),
        ];
    }

    /**
     * Create an enrolled student program.
     */
    public function enrolled(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'enrolled',
        ]);
    }

    /**
     * Create a completed student program.
     */
    public function completed(): static
    {
        $enrollmentDate = fake()->dateTimeBetween('-4 years', '-1 year');

        return $this->state(fn (array $attributes) => [
            'status' => 'completed',
            'enrollment_date' => $enrollmentDate,
            'expected_graduation' => (clone $enrollmentDate)->modify('+2 years'),
        ]);
    }

    /**
     * Create a withdrawn student program.
     */
    public function withdrawn(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'withdrawn',
        ]);
    }

    /**
     * Create a recent enrollment.
     */
    public function recent(): static
    {
        $enrollmentDate = fake()->dateTimeBetween('-3 months', 'now');

        return $this->state(fn (array $attributes) => [
            'enrollment_date' => $enrollmentDate,
            'expected_graduation' => (clone $enrollmentDate)->modify('+2 years'),
            'status' => 'enrolled',
        ]);
    }
}
