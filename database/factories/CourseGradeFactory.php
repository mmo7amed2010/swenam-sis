<?php

namespace Database\Factories;

use App\Models\Course;
use App\Models\CourseGrade;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CourseGrade>
 */
class CourseGradeFactory extends Factory
{
    protected $model = CourseGrade::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $pointsTotal = fake()->randomElement([100, 200, 500, 1000]);
        $percentage = fake()->randomFloat(2, 40, 100);
        $pointsEarned = round($pointsTotal * ($percentage / 100), 2);

        return [
            'student_id' => User::factory()->student(),
            'course_id' => Course::factory(),
            'points_earned' => $pointsEarned,
            'points_total' => $pointsTotal,
            'percentage' => $percentage,
        ];
    }

    /**
     * Create an A grade (90-100%).
     */
    public function gradeA(): static
    {
        $percentage = fake()->randomFloat(2, 90, 100);

        return $this->state(function (array $attributes) use ($percentage) {
            $pointsTotal = $attributes['points_total'] ?? 100;
            return [
                'percentage' => $percentage,
                'points_earned' => round($pointsTotal * ($percentage / 100), 2),
            ];
        });
    }

    /**
     * Create a B grade (80-89%).
     */
    public function gradeB(): static
    {
        $percentage = fake()->randomFloat(2, 80, 89.99);

        return $this->state(function (array $attributes) use ($percentage) {
            $pointsTotal = $attributes['points_total'] ?? 100;
            return [
                'percentage' => $percentage,
                'points_earned' => round($pointsTotal * ($percentage / 100), 2),
            ];
        });
    }

    /**
     * Create a C grade (70-79%).
     */
    public function gradeC(): static
    {
        $percentage = fake()->randomFloat(2, 70, 79.99);

        return $this->state(function (array $attributes) use ($percentage) {
            $pointsTotal = $attributes['points_total'] ?? 100;
            return [
                'percentage' => $percentage,
                'points_earned' => round($pointsTotal * ($percentage / 100), 2),
            ];
        });
    }

    /**
     * Create a D grade (60-69%).
     */
    public function gradeD(): static
    {
        $percentage = fake()->randomFloat(2, 60, 69.99);

        return $this->state(function (array $attributes) use ($percentage) {
            $pointsTotal = $attributes['points_total'] ?? 100;
            return [
                'percentage' => $percentage,
                'points_earned' => round($pointsTotal * ($percentage / 100), 2),
            ];
        });
    }

    /**
     * Create a failing grade (<60%).
     */
    public function failing(): static
    {
        $percentage = fake()->randomFloat(2, 0, 59.99);

        return $this->state(function (array $attributes) use ($percentage) {
            $pointsTotal = $attributes['points_total'] ?? 100;
            return [
                'percentage' => $percentage,
                'points_earned' => round($pointsTotal * ($percentage / 100), 2),
            ];
        });
    }

    /**
     * Create a passing grade (>=70%).
     */
    public function passing(): static
    {
        $percentage = fake()->randomFloat(2, 70, 100);

        return $this->state(function (array $attributes) use ($percentage) {
            $pointsTotal = $attributes['points_total'] ?? 100;
            return [
                'percentage' => $percentage,
                'points_earned' => round($pointsTotal * ($percentage / 100), 2),
            ];
        });
    }
}
