<?php

namespace Database\Factories;

use App\Models\Course;
use App\Models\CourseInstructor;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CourseInstructor>
 */
class CourseInstructorFactory extends Factory
{
    protected $model = CourseInstructor::class;

    /**
     * Define the model's default state.
     * Simplified model: one instructor per course with full access.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'course_id' => Course::factory(),
            'user_id' => User::factory()->create(['user_type' => 'instructor']),
            'assigned_by_admin_id' => User::factory()->create(['user_type' => 'admin']),
            'assigned_at' => now(),
            'removed_at' => null,
        ];
    }

    /**
     * Indicate that the instructor has been removed.
     */
    public function removed(): static
    {
        return $this->state(fn (array $attributes) => [
            'removed_at' => now(),
        ]);
    }
}
