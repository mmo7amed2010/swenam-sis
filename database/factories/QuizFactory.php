<?php

namespace Database\Factories;

use App\Models\Course;
use App\Models\CourseModule;
use App\Models\Quiz;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Quiz>
 */
class QuizFactory extends Factory
{
    protected $model = Quiz::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $totalPoints = fake()->randomElement([10, 20, 25, 50, 100]);

        return [
            'course_id' => Course::factory(),
            'module_id' => null,
            'created_by' => User::factory()->instructor(),
            'title' => fake()->randomElement([
                'Quiz: '.fake()->words(3, true),
                'Assessment: '.fake()->words(2, true),
                'Test: '.fake()->words(2, true),
                fake()->sentence(3),
            ]),
            'description' => fake()->paragraph(),
            'total_points' => $totalPoints,
            'due_date' => fake()->optional(0.7)->dateTimeBetween('now', '+60 days'),
            'time_limit' => fake()->randomElement([15, 30, 45, 60, 90, 120, null]),
            'max_attempts' => fake()->randomElement([1, 2, 3, -1]),
            'shuffle_questions' => fake()->boolean(70),
            'shuffle_answers' => fake()->boolean(50),
            'show_correct_answers' => fake()->randomElement(['never', 'after_all_attempts']),
            'passing_score' => fake()->randomElement([50, 60, 70, 80]),
            'published' => fake()->boolean(80),
            'assessment_type' => 'quiz',
            'scope' => 'module',
            'is_retake_exam' => false,
            'primary_exam_id' => null,
        ];
    }

    /**
     * Create a published quiz.
     */
    public function published(): static
    {
        return $this->state(fn (array $attributes) => [
            'published' => true,
        ]);
    }

    /**
     * Create a draft quiz.
     */
    public function draft(): static
    {
        return $this->state(fn (array $attributes) => [
            'published' => false,
        ]);
    }

    /**
     * Create an exam assessment.
     */
    public function exam(): static
    {
        return $this->state(fn (array $attributes) => [
            'assessment_type' => 'exam',
            'total_points' => 100,
            'time_limit' => fake()->randomElement([60, 90, 120, 180]),
            'max_attempts' => 1,
            'passing_score' => 70,
        ]);
    }

    /**
     * Create a module-scoped quiz.
     */
    public function moduleQuiz(): static
    {
        return $this->state(fn (array $attributes) => [
            'module_id' => CourseModule::factory(),
            'scope' => 'module',
        ]);
    }

    /**
     * Create a retake exam.
     */
    public function retake(Quiz $primaryExam): static
    {
        return $this->state(fn (array $attributes) => [
            'is_retake_exam' => true,
            'primary_exam_id' => $primaryExam->id,
            'course_id' => $primaryExam->course_id,
            'module_id' => $primaryExam->module_id,
            'total_points' => $primaryExam->total_points,
        ]);
    }

    /**
     * Create a quiz with a past due date.
     */
    public function overdue(): static
    {
        return $this->state(fn (array $attributes) => [
            'due_date' => fake()->dateTimeBetween('-30 days', '-1 day'),
        ]);
    }
}
