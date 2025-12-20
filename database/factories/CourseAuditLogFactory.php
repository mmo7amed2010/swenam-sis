<?php

namespace Database\Factories;

use App\Models\Course;
use App\Models\CourseAuditLog;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CourseAuditLog>
 */
class CourseAuditLogFactory extends Factory
{
    protected $model = CourseAuditLog::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $eventType = fake()->randomElement(['created', 'updated', 'published', 'archived', 'restored']);

        return [
            'auditable_type' => Course::class,
            'auditable_id' => Course::factory(),
            'event_type' => $eventType,
            'old_values' => $this->generateOldValues($eventType),
            'new_values' => $this->generateNewValues($eventType),
            'user_id' => User::factory()->admin(),
            'user_type' => fake()->randomElement(['admin', 'instructor']),
            'ip_address' => fake()->ipv4(),
            'user_agent' => fake()->userAgent(),
            'description' => $this->generateDescription($eventType),
            'created_at' => fake()->dateTimeBetween('-90 days', 'now'),
        ];
    }

    /**
     * Generate old values based on event type.
     */
    protected function generateOldValues(string $eventType): ?array
    {
        if ($eventType === 'created') {
            return null;
        }

        return match ($eventType) {
            'updated' => [
                'name' => fake()->sentence(3),
                'description' => fake()->paragraph(),
            ],
            'published' => ['status' => 'draft'],
            'archived' => ['status' => 'active'],
            'restored' => ['status' => 'archived'],
            default => null,
        };
    }

    /**
     * Generate new values based on event type.
     */
    protected function generateNewValues(string $eventType): ?array
    {
        return match ($eventType) {
            'created' => [
                'name' => fake()->sentence(3),
                'description' => fake()->paragraph(),
                'status' => 'draft',
            ],
            'updated' => [
                'name' => fake()->sentence(3),
                'description' => fake()->paragraph(),
            ],
            'published' => ['status' => 'published'],
            'archived' => ['status' => 'archived'],
            'restored' => ['status' => 'active'],
            default => null,
        };
    }

    /**
     * Generate description based on event type.
     */
    protected function generateDescription(string $eventType): string
    {
        return match ($eventType) {
            'created' => 'Course was created',
            'updated' => 'Course details were updated',
            'published' => 'Course was published',
            'archived' => 'Course was archived',
            'restored' => 'Course was restored from archive',
            'deleted' => 'Course was deleted',
            default => 'Course was modified',
        };
    }

    /**
     * Create a course creation log.
     */
    public function created(): static
    {
        return $this->state(fn (array $attributes) => [
            'event_type' => 'created',
            'old_values' => null,
            'new_values' => [
                'name' => fake()->sentence(3),
                'description' => fake()->paragraph(),
                'status' => 'draft',
            ],
            'description' => 'Course was created',
        ]);
    }

    /**
     * Create a course update log.
     */
    public function updated(): static
    {
        return $this->state(fn (array $attributes) => [
            'event_type' => 'updated',
            'old_values' => ['name' => fake()->sentence(3)],
            'new_values' => ['name' => fake()->sentence(3)],
            'description' => 'Course details were updated',
        ]);
    }

    /**
     * Create a course published log.
     */
    public function published(): static
    {
        return $this->state(fn (array $attributes) => [
            'event_type' => 'published',
            'old_values' => ['status' => 'draft'],
            'new_values' => ['status' => 'published'],
            'description' => 'Course was published',
        ]);
    }

    /**
     * Create an admin-initiated log.
     */
    public function byAdmin(): static
    {
        return $this->state(fn (array $attributes) => [
            'user_type' => 'admin',
            'user_id' => User::factory()->admin(),
        ]);
    }

    /**
     * Create an instructor-initiated log.
     */
    public function byInstructor(): static
    {
        return $this->state(fn (array $attributes) => [
            'user_type' => 'instructor',
            'user_id' => User::factory()->instructor(),
        ]);
    }
}
