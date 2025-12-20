<?php

namespace Database\Factories;

use App\Models\CourseModule;
use App\Models\ModuleProgress;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ModuleProgress>
 */
class ModuleProgressFactory extends Factory
{
    protected $model = ModuleProgress::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $status = fake()->randomElement(['not_started', 'in_progress', 'completed', 'completed', 'completed']);

        return [
            'module_id' => CourseModule::factory(),
            'student_id' => User::factory()->student(),
            'status' => $status,
            'exam_passed_at' => $status === 'completed' ? fake()->dateTimeBetween('-60 days', 'now') : null,
            'exam_attempts_used' => $status === 'completed' ? fake()->numberBetween(1, 2) : 0,
            'exam_first_score' => $status === 'completed' ? fake()->numberBetween(50, 100) : null,
            'exam_best_score' => $status === 'completed' ? fake()->numberBetween(70, 100) : null,
            'primary_exam_failed' => false,
            'retake_exam_failed' => false,
            'retake_unlocked_at' => null,
            'retake_passed_at' => null,
        ];
    }

    /**
     * Create a not started progress.
     */
    public function notStarted(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'not_started',
            'exam_passed_at' => null,
            'exam_attempts_used' => 0,
            'exam_first_score' => null,
            'exam_best_score' => null,
        ]);
    }

    /**
     * Create an in-progress state.
     */
    public function inProgress(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'in_progress',
            'exam_passed_at' => null,
            'exam_attempts_used' => 0,
            'exam_first_score' => null,
            'exam_best_score' => null,
        ]);
    }

    /**
     * Create a completed progress.
     */
    public function completed(): static
    {
        $score = fake()->numberBetween(70, 100);

        return $this->state(fn (array $attributes) => [
            'status' => 'completed',
            'exam_passed_at' => fake()->dateTimeBetween('-60 days', 'now'),
            'exam_attempts_used' => fake()->numberBetween(1, 2),
            'exam_first_score' => $score - fake()->numberBetween(0, 15),
            'exam_best_score' => $score,
        ]);
    }

    /**
     * Create an exam failed progress.
     */
    public function examFailed(): static
    {
        $score = fake()->numberBetween(30, 69);

        return $this->state(fn (array $attributes) => [
            'status' => 'exam_failed',
            'exam_passed_at' => null,
            'exam_attempts_used' => 2,
            'exam_first_score' => $score,
            'exam_best_score' => $score + fake()->numberBetween(0, 10),
            'primary_exam_failed' => true,
        ]);
    }

    /**
     * Create an exam locked progress.
     */
    public function examLocked(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'exam_locked',
            'exam_passed_at' => null,
            'exam_attempts_used' => 2,
            'primary_exam_failed' => true,
            'retake_exam_failed' => true,
        ]);
    }

    /**
     * Create a progress with retake unlocked.
     */
    public function retakeUnlocked(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'exam_failed',
            'primary_exam_failed' => true,
            'retake_exam_failed' => false,
            'retake_unlocked_at' => fake()->dateTimeBetween('-7 days', 'now'),
        ]);
    }
}
