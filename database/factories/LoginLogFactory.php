<?php

namespace Database\Factories;

use App\Models\LoginLog;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\LoginLog>
 */
class LoginLogFactory extends Factory
{
    protected $model = LoginLog::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $status = fake()->randomElement(['success', 'success', 'success', 'success', 'failed']);

        return [
            'user_id' => User::factory(),
            'email' => fake()->safeEmail(),
            'ip_address' => fake()->ipv4(),
            'user_agent' => fake()->userAgent(),
            'status' => $status,
            'failure_reason' => $status === 'failed' ? fake()->randomElement(['Invalid password', 'Account locked', null]) : null,
            'created_at' => fake()->dateTimeBetween('-90 days', 'now'),
        ];
    }

    /**
     * Create a successful login log.
     */
    public function successful(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'success',
            'failure_reason' => null,
        ]);
    }

    /**
     * Create a failed login log.
     */
    public function failed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'failed',
            'failure_reason' => fake()->randomElement(['Invalid password', 'Account not found', 'Session expired']),
        ]);
    }

    /**
     * Create a locked account login log.
     */
    public function locked(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'locked',
            'failure_reason' => 'Account locked due to multiple failed attempts',
        ]);
    }

    /**
     * Create a recent login log.
     */
    public function recent(): static
    {
        return $this->state(fn (array $attributes) => [
            'created_at' => fake()->dateTimeBetween('-24 hours', 'now'),
        ]);
    }

    /**
     * Create a login log from a specific IP.
     */
    public function fromIp(string $ip): static
    {
        return $this->state(fn (array $attributes) => [
            'ip_address' => $ip,
        ]);
    }
}
