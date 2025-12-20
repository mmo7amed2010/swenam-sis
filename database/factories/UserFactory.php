<?php

namespace Database\Factories;

use App\Models\Program;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    protected static ?string $password;

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
            'name' => $firstName . ' ' . $lastName,
            'first_name' => $firstName,
            'last_name' => $lastName,
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
            'user_type' => 'student',
            'last_login_at' => fake()->optional(0.7)->dateTimeBetween('-30 days', 'now'),
            'last_login_ip' => fake()->optional(0.7)->ipv4(),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    /**
     * Create a student user.
     */
    public function student(): static
    {
        return $this->state(fn (array $attributes) => [
            'user_type' => 'student',
            'program_id' => Program::inRandomOrder()->first()?->id ?? Program::factory(),
        ]);
    }

    /**
     * Create an instructor user.
     */
    public function instructor(): static
    {
        return $this->state(fn (array $attributes) => [
            'user_type' => 'instructor',
            'program_id' => null,
        ]);
    }

    /**
     * Create an admin user.
     */
    public function admin(): static
    {
        return $this->state(fn (array $attributes) => [
            'user_type' => 'admin',
            'program_id' => null,
        ]);
    }

    /**
     * Create a user with failed login attempts.
     */
    public function withFailedLogins(int $attempts = 3): static
    {
        return $this->state(fn (array $attributes) => [
            'failed_login_attempts' => $attempts,
            'locked_until' => $attempts >= 3 ? now()->addMinutes(15) : null,
        ]);
    }

    /**
     * Create a user requiring password reset.
     */
    public function requiresPasswordReset(): static
    {
        return $this->state(fn (array $attributes) => [
            'password_change_required' => true,
        ]);
    }
}
