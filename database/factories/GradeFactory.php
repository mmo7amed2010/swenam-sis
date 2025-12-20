<?php

namespace Database\Factories;

use App\Models\Grade;
use App\Models\Submission;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Grade>
 */
class GradeFactory extends Factory
{
    protected $model = Grade::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $maxPoints = fake()->randomElement([10, 20, 25, 50, 100]);
        $pointsAwarded = fake()->randomFloat(1, $maxPoints * 0.4, $maxPoints);
        $isPublished = fake()->boolean(80);

        return [
            'submission_id' => Submission::factory(),
            'points_awarded' => $pointsAwarded,
            'max_points' => $maxPoints,
            'feedback' => fake()->optional(0.7)->paragraphs(2, true),
            'rubric_scores' => $this->generateRubricScores($maxPoints),
            'annotated_file_path' => null,
            'graded_by_user_id' => User::factory()->instructor(),
            'graded_at' => fake()->dateTimeBetween('-30 days', 'now'),
            'is_published' => $isPublished,
            'published_at' => $isPublished ? fake()->dateTimeBetween('-30 days', 'now') : null,
            'late_penalty_override' => null,
            'version' => 1,
        ];
    }

    /**
     * Generate rubric scores.
     */
    protected function generateRubricScores(int $maxPoints): array
    {
        $criteriaCount = fake()->numberBetween(3, 5);
        $pointsPerCriteria = intval($maxPoints / $criteriaCount);
        $scores = [];

        for ($i = 0; $i < $criteriaCount; $i++) {
            $scores[] = [
                'criteria_index' => $i,
                'points' => fake()->randomFloat(1, $pointsPerCriteria * 0.5, $pointsPerCriteria),
                'comment' => fake()->optional(0.5)->sentence(),
            ];
        }

        return $scores;
    }

    /**
     * Create a published grade.
     */
    public function published(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_published' => true,
            'published_at' => now(),
        ]);
    }

    /**
     * Create a draft (unpublished) grade.
     */
    public function draft(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_published' => false,
            'published_at' => null,
        ]);
    }

    /**
     * Create a high grade (A range).
     */
    public function highGrade(): static
    {
        return $this->state(function (array $attributes) {
            $maxPoints = $attributes['max_points'] ?? 100;
            return [
                'points_awarded' => fake()->randomFloat(1, $maxPoints * 0.9, $maxPoints),
            ];
        });
    }

    /**
     * Create a passing grade.
     */
    public function passing(int $passingPercentage = 70): static
    {
        return $this->state(function (array $attributes) use ($passingPercentage) {
            $maxPoints = $attributes['max_points'] ?? 100;
            $minPoints = $maxPoints * ($passingPercentage / 100);
            return [
                'points_awarded' => fake()->randomFloat(1, $minPoints, $maxPoints),
            ];
        });
    }

    /**
     * Create a failing grade.
     */
    public function failing(int $passingPercentage = 70): static
    {
        return $this->state(function (array $attributes) use ($passingPercentage) {
            $maxPoints = $attributes['max_points'] ?? 100;
            $maxFailingPoints = $maxPoints * (($passingPercentage - 1) / 100);
            return [
                'points_awarded' => fake()->randomFloat(1, 0, $maxFailingPoints),
            ];
        });
    }

    /**
     * Create a grade with late penalty.
     */
    public function withLatePenalty(float $penalty = 10): static
    {
        return $this->state(fn (array $attributes) => [
            'late_penalty_override' => $penalty,
        ]);
    }
}
