<?php

namespace Database\Factories;

use App\Models\Assignment;
use App\Models\Course;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Assignment>
 */
class AssignmentFactory extends Factory
{
    protected $model = Assignment::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Valid enum values for assignment_type: file_upload, text_submission, quiz, external_link
        $assignmentType = fake()->randomElement(['file_upload', 'text_submission', 'file_upload']);
        // Valid enum values for submission_type: file_upload, text_entry, url_submission, multiple
        $submissionType = fake()->randomElement(['file_upload', 'text_entry', 'file_upload', 'file_upload']);
        $maxPoints = fake()->randomElement([10, 20, 25, 50, 100]);
        $isFileType = $submissionType === 'file_upload';

        return [
            'course_id' => Course::factory(),
            'assignmentable_type' => null,
            'assignmentable_id' => null,
            'title' => fake()->randomElement([
                'Assignment: '.fake()->words(3, true),
                'Project: '.fake()->words(2, true),
                'Homework: '.fake()->words(2, true),
                fake()->sentence(4),
            ]),
            'description' => fake()->paragraphs(2, true),
            'instructions' => fake()->paragraphs(3, true),
            'assignment_type' => $assignmentType,
            'submission_type' => $submissionType,
            'max_file_size_mb' => $isFileType ? fake()->randomElement([5, 10, 25, 50]) : null,
            'max_points' => $maxPoints,
            'total_points' => $maxPoints,
            'weight' => fake()->randomFloat(2, 0.05, 0.30),
            'passing_score' => fake()->randomElement([50, 60, 70, 80]),
            'rubric' => $this->generateRubric($maxPoints),
            'is_published' => fake()->boolean(85),
            'attempts_allowed' => fake()->randomElement([1, 2, 3]),
            'allow_resubmission' => fake()->boolean(60),
            'created_by_user_id' => User::factory()->instructor(),
        ];
    }

    /**
     * Generate a rubric for grading.
     */
    protected function generateRubric(int $maxPoints): array
    {
        $criteria = fake()->numberBetween(3, 5);
        $pointsPerCriteria = intval($maxPoints / $criteria);
        $rubric = [];

        $criteriaNames = [
            'Content Quality',
            'Organization',
            'Research & Analysis',
            'Writing Style',
            'Grammar & Mechanics',
            'Creativity',
            'Critical Thinking',
            'Presentation',
        ];

        $selectedCriteria = fake()->randomElements($criteriaNames, $criteria);

        foreach ($selectedCriteria as $name) {
            $rubric[] = [
                'name' => $name,
                'points' => $pointsPerCriteria,
                'description' => fake()->sentence(),
            ];
        }

        return $rubric;
    }

    /**
     * Create a published assignment.
     */
    public function published(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_published' => true,
        ]);
    }

    /**
     * Create a draft assignment.
     */
    public function draft(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_published' => false,
        ]);
    }

    /**
     * Create a file upload assignment.
     */
    public function fileUpload(): static
    {
        return $this->state(fn (array $attributes) => [
            'assignment_type' => 'file_upload',
            'submission_type' => 'file_upload',
            'max_file_size_mb' => 25,
        ]);
    }

    /**
     * Create a text submission assignment.
     */
    public function textSubmission(): static
    {
        return $this->state(fn (array $attributes) => [
            'assignment_type' => 'text_submission',
            'submission_type' => 'text_entry',
            'max_file_size_mb' => null,
        ]);
    }
}
