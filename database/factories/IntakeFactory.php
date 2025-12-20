<?php

namespace Database\Factories;

use App\Models\Intake;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Intake>
 */
class IntakeFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Intake::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $month = fake()->randomElement(['January', 'April', 'September']);
        $year = fake()->numberBetween(2025, 2027);
        $name = "{$month} {$year}";

        return [
            'name' => $name,
            'slug' => Str::slug($name),
            'is_active' => true,
            'description' => fake()->optional(0.5)->sentence(10),
            'sort_order' => fake()->numberBetween(0, 10),
        ];
    }

    /**
     * Indicate that the intake is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }

    /**
     * Indicate that the intake is active.
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => true,
        ]);
    }
}
