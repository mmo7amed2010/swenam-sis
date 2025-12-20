<?php

namespace Database\Factories;

use App\Models\Student;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Student>
 */
class StudentFactory extends Factory
{
    protected $model = Student::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $firstName = fake()->firstName();
        $lastName = fake()->lastName();

        return [
            'user_id' => User::factory()->student(),
            'student_number' => 'STU-' . date('Y') . '-' . fake()->unique()->numerify('#####'),
            'first_name' => $firstName,
            'last_name' => $lastName,
            'email' => fake()->unique()->safeEmail(),
            'phone' => fake()->phoneNumber(),
            'date_of_birth' => fake()->dateTimeBetween('-35 years', '-18 years'),
            'address' => [
                'street' => fake()->streetAddress(),
                'city' => fake()->city(),
                'state' => fake()->state(),
                'postal_code' => fake()->postcode(),
                'country' => fake()->country(),
            ],
            'enrollment_status' => fake()->randomElement(['active', 'active', 'active', 'graduated', 'withdrawn']),
        ];
    }

    /**
     * Create an active student.
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'enrollment_status' => 'active',
        ]);
    }

    /**
     * Create a graduated student.
     */
    public function graduated(): static
    {
        return $this->state(fn (array $attributes) => [
            'enrollment_status' => 'graduated',
        ]);
    }

    /**
     * Create a withdrawn student.
     */
    public function withdrawn(): static
    {
        return $this->state(fn (array $attributes) => [
            'enrollment_status' => 'withdrawn',
        ]);
    }
}
