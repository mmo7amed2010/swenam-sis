<?php

namespace Database\Factories;

use App\Models\Course;
use App\Models\CourseModule;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CourseModule>
 */
class CourseModuleFactory extends Factory
{
    protected $model = CourseModule::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'course_id' => Course::factory(),
            'title' => $this->faker->sentence(3),
            'description' => $this->faker->paragraph(),
            'order_index' => $this->faker->numberBetween(1, 10),
            'status' => 'published',
            'release_date' => null,
            'estimated_hours' => $this->faker->randomFloat(1, 2, 20),
        ];
    }

    /**
     * Indicate that the module is in draft status.
     */
    public function draft(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'draft',
        ]);
    }

    /**
     * Indicate that the module has a future release date.
     */
    public function scheduled(): static
    {
        return $this->state(fn (array $attributes) => [
            'release_date' => $this->faker->dateTimeBetween('+1 day', '+30 days'),
        ]);
    }

    /**
     * Indicate that the module has been released.
     */
    public function released(): static
    {
        return $this->state(fn (array $attributes) => [
            'release_date' => $this->faker->dateTimeBetween('-30 days', 'now'),
        ]);
    }
}
