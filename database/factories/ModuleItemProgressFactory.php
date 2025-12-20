<?php

namespace Database\Factories;

use App\Models\Course;
use App\Models\ModuleItem;
use App\Models\ModuleItemProgress;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ModuleItemProgress>
 */
class ModuleItemProgressFactory extends Factory
{
    protected $model = ModuleItemProgress::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $isCompleted = fake()->boolean(70);
        $lastAccessed = fake()->dateTimeBetween('-60 days', 'now');

        return [
            'user_id' => User::factory()->student(),
            'module_item_id' => ModuleItem::factory(),
            'course_id' => Course::factory(),
            'completed_at' => $isCompleted ? fake()->dateTimeBetween('-60 days', 'now') : null,
            'last_accessed_at' => $lastAccessed,
        ];
    }

    /**
     * Create a completed progress.
     */
    public function completed(): static
    {
        $completedAt = fake()->dateTimeBetween('-60 days', 'now');

        return $this->state(fn (array $attributes) => [
            'completed_at' => $completedAt,
            'last_accessed_at' => $completedAt,
        ]);
    }

    /**
     * Create an incomplete (in-progress) record.
     */
    public function incomplete(): static
    {
        return $this->state(fn (array $attributes) => [
            'completed_at' => null,
            'last_accessed_at' => fake()->dateTimeBetween('-7 days', 'now'),
        ]);
    }

    /**
     * Create a recently accessed progress.
     */
    public function recentlyAccessed(): static
    {
        return $this->state(fn (array $attributes) => [
            'last_accessed_at' => fake()->dateTimeBetween('-24 hours', 'now'),
        ]);
    }
}
