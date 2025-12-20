<?php

namespace Database\Factories;

use App\Models\Course;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Course>
 */
class CourseFactory extends Factory
{
    protected $model = Course::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'course_code' => strtoupper($this->faker->unique()->bothify('??###')),
            'name' => $this->faker->sentence(3),
            'description' => $this->faker->paragraph(),
            'version' => 1,
            'credits' => $this->faker->randomFloat(1, 1, 5),
            'department' => $this->faker->randomElement(['Computer Science', 'Mathematics', 'Physics', 'Chemistry', 'Biology']),
            'program' => $this->faker->randomElement(['Undergraduate', 'Graduate', 'Certificate', 'Diploma']),
            'status' => 'draft',
            'created_by_admin_id' => User::factory()->create(['user_type' => 'admin']),
        ];
    }

    /**
     * Indicate that the course is published.
     */
    public function published(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'published',
            'published_at' => now(),
        ]);
    }

    /**
     * Indicate that the course is active.
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'active',
            'published_at' => now()->subDays(30),
        ]);
    }

    /**
     * Indicate that the course is archived.
     */
    public function archived(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'archived',
            'published_at' => now()->subMonths(6),
            'archived_at' => now(),
        ]);
    }

    /**
     * Indicate that the course has a specific department.
     */
    public function department(string $department): static
    {
        return $this->state(fn (array $attributes) => [
            'department' => $department,
        ]);
    }

    /**
     * Indicate that the course has a specific program.
     */
    public function program(string $program): static
    {
        return $this->state(fn (array $attributes) => [
            'program' => $program,
        ]);
    }
}
