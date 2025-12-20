<?php

namespace Database\Factories;

use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\QuizAttempt>
 */
class QuizAttemptFactory extends Factory
{
    protected $model = QuizAttempt::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $startTime = fake()->dateTimeBetween('-60 days', 'now');
        $endTime = (clone $startTime)->modify('+' . fake()->numberBetween(10, 90) . ' minutes');
        $score = fake()->numberBetween(0, 100);
        $status = fake()->randomElement(['submitted', 'graded', 'graded', 'graded']);

        return [
            'quiz_id' => Quiz::factory(),
            'student_id' => User::factory()->student(),
            'attempt_number' => 1,
            'start_time' => $startTime,
            'end_time' => $endTime,
            'status' => $status,
            'score' => $status !== 'in_progress' ? $score : null,
            'percentage' => $status !== 'in_progress' ? $score : null,
            'answers_json' => [],
            'questions_order' => [],
        ];
    }

    /**
     * Create an in-progress attempt.
     */
    public function inProgress(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'in_progress',
            'end_time' => null,
            'score' => null,
            'percentage' => null,
        ]);
    }

    /**
     * Create a submitted attempt.
     */
    public function submitted(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'submitted',
            'score' => null,
            'percentage' => null,
        ]);
    }

    /**
     * Create a graded attempt.
     */
    public function graded(): static
    {
        $score = fake()->numberBetween(0, 100);

        return $this->state(fn (array $attributes) => [
            'status' => 'graded',
            'score' => $score,
            'percentage' => $score,
        ]);
    }

    /**
     * Create a passed attempt.
     */
    public function passed(int $passingScore = 70): static
    {
        $score = fake()->numberBetween($passingScore, 100);

        return $this->state(fn (array $attributes) => [
            'status' => 'graded',
            'score' => $score,
            'percentage' => $score,
        ]);
    }

    /**
     * Create a failed attempt.
     */
    public function failed(int $passingScore = 70): static
    {
        $score = fake()->numberBetween(0, $passingScore - 1);

        return $this->state(fn (array $attributes) => [
            'status' => 'graded',
            'score' => $score,
            'percentage' => $score,
        ]);
    }

    /**
     * Set a specific attempt number.
     */
    public function attemptNumber(int $number): static
    {
        return $this->state(fn (array $attributes) => [
            'attempt_number' => $number,
        ]);
    }
}
