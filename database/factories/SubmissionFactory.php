<?php

namespace Database\Factories;

use App\Models\Assignment;
use App\Models\Submission;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Submission>
 */
class SubmissionFactory extends Factory
{
    protected $model = Submission::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $submittedAt = fake()->dateTimeBetween('-60 days', 'now');
        $isLate = fake()->boolean(15);

        return [
            'assignment_id' => Assignment::factory(),
            'user_id' => User::factory()->student(),
            'submission_type' => fake()->randomElement(['file', 'text']),
            'text_content' => fake()->optional(0.4)->paragraphs(3, true),
            'file_path' => fake()->optional(0.6)->filePath(),
            'file_name' => fake()->optional(0.6)->word() . '.pdf',
            'file_size' => fake()->optional(0.6)->numberBetween(10000, 5000000),
            'external_url' => null,
            'quiz_answers' => null,
            'attempt_number' => 1,
            'submitted_at' => $submittedAt,
            'is_late' => $isLate,
            'late_days' => $isLate ? fake()->numberBetween(1, 7) : 0,
            'status' => fake()->randomElement(['submitted', 'graded', 'graded', 'graded']),
        ];
    }

    /**
     * Create a draft submission.
     */
    public function draft(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'draft',
            'submitted_at' => null,
        ]);
    }

    /**
     * Create a submitted (pending grading) submission.
     */
    public function submitted(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'submitted',
        ]);
    }

    /**
     * Create a graded submission.
     */
    public function graded(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'graded',
        ]);
    }

    /**
     * Create a late submission.
     */
    public function late(int $days = null): static
    {
        $lateDays = $days ?? fake()->numberBetween(1, 7);

        return $this->state(fn (array $attributes) => [
            'is_late' => true,
            'late_days' => $lateDays,
        ]);
    }

    /**
     * Create a text submission.
     */
    public function text(): static
    {
        return $this->state(fn (array $attributes) => [
            'submission_type' => 'text',
            'text_content' => fake()->paragraphs(5, true),
            'file_path' => null,
            'file_name' => null,
            'file_size' => null,
        ]);
    }

    /**
     * Create a file submission.
     */
    public function file(): static
    {
        $fileName = fake()->word() . '.' . fake()->randomElement(['pdf', 'doc', 'docx']);

        return $this->state(fn (array $attributes) => [
            'submission_type' => 'file',
            'text_content' => null,
            'file_path' => 'submissions/' . fake()->uuid() . '/' . $fileName,
            'file_name' => $fileName,
            'file_size' => fake()->numberBetween(50000, 10000000),
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
