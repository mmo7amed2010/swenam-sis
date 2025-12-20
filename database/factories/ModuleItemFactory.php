<?php

namespace Database\Factories;

use App\Models\Assignment;
use App\Models\CourseModule;
use App\Models\ModuleItem;
use App\Models\ModuleLesson;
use App\Models\Quiz;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ModuleItem>
 */
class ModuleItemFactory extends Factory
{
    protected $model = ModuleItem::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'module_id' => CourseModule::factory(),
            'itemable_type' => ModuleLesson::class,
            'itemable_id' => ModuleLesson::factory(),
            'order_position' => fake()->numberBetween(1, 20),
            'is_required' => fake()->boolean(80),
            'release_date' => fake()->optional(0.3)->dateTimeBetween('-7 days', '+30 days'),
        ];
    }

    /**
     * Create a lesson item.
     */
    public function lesson(): static
    {
        return $this->state(fn (array $attributes) => [
            'itemable_type' => ModuleLesson::class,
            'itemable_id' => ModuleLesson::factory(),
        ]);
    }

    /**
     * Create a quiz item.
     */
    public function quiz(): static
    {
        return $this->state(fn (array $attributes) => [
            'itemable_type' => Quiz::class,
            'itemable_id' => Quiz::factory(),
        ]);
    }

    /**
     * Create an assignment item.
     */
    public function assignment(): static
    {
        return $this->state(fn (array $attributes) => [
            'itemable_type' => Assignment::class,
            'itemable_id' => Assignment::factory(),
        ]);
    }

    /**
     * Create a required item.
     */
    public function required(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_required' => true,
        ]);
    }

    /**
     * Create an optional item.
     */
    public function optional(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_required' => false,
        ]);
    }

    /**
     * Create an item with a scheduled release.
     */
    public function scheduled(): static
    {
        return $this->state(fn (array $attributes) => [
            'release_date' => fake()->dateTimeBetween('+1 day', '+30 days'),
        ]);
    }

    /**
     * Create an item that's already released.
     */
    public function released(): static
    {
        return $this->state(fn (array $attributes) => [
            'release_date' => fake()->dateTimeBetween('-30 days', '-1 day'),
        ]);
    }

    /**
     * Set a specific order position.
     */
    public function position(int $position): static
    {
        return $this->state(fn (array $attributes) => [
            'order_position' => $position,
        ]);
    }
}
