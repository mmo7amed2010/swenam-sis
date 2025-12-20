<?php

namespace Database\Factories;

use App\Models\Announcement;
use App\Models\Course;
use App\Models\Program;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Announcement>
 */
class AnnouncementFactory extends Factory
{
    protected $model = Announcement::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $type = fake()->randomElement(['course', 'system']);
        $isPublished = fake()->boolean(80);

        return [
            'course_id' => $type === 'course' ? Course::factory() : null,
            'user_id' => User::factory()->instructor(),
            'title' => fake()->sentence(fake()->numberBetween(4, 10)),
            'content' => fake()->paragraphs(fake()->numberBetween(2, 5), true),
            'type' => $type,
            'priority' => fake()->randomElement(['low', 'medium', 'high']),
            'target_audience' => fake()->randomElement(['all', 'students', 'instructors']),
            'program_id' => $type === 'system' ? (fake()->boolean(50) ? Program::factory() : null) : null,
            'is_published' => $isPublished,
            'publish_at' => $isPublished ? fake()->dateTimeBetween('-30 days', 'now') : fake()->dateTimeBetween('now', '+30 days'),
            'expires_at' => fake()->optional(0.3)->dateTimeBetween('+7 days', '+90 days'),
            'send_email' => fake()->boolean(30),
        ];
    }

    /**
     * Create a course announcement.
     */
    public function courseAnnouncement(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'course',
            'course_id' => Course::factory(),
            'program_id' => null,
        ]);
    }

    /**
     * Create a system announcement.
     */
    public function systemAnnouncement(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'system',
            'course_id' => null,
        ]);
    }

    /**
     * Create a published announcement.
     */
    public function published(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_published' => true,
            'publish_at' => fake()->dateTimeBetween('-30 days', 'now'),
        ]);
    }

    /**
     * Create a scheduled announcement.
     */
    public function scheduled(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_published' => false,
            'publish_at' => fake()->dateTimeBetween('+1 day', '+30 days'),
        ]);
    }

    /**
     * Create a high priority announcement.
     */
    public function highPriority(): static
    {
        return $this->state(fn (array $attributes) => [
            'priority' => 'high',
        ]);
    }

    /**
     * Create an announcement with email notification.
     */
    public function withEmail(): static
    {
        return $this->state(fn (array $attributes) => [
            'send_email' => true,
        ]);
    }

    /**
     * Create an expired announcement.
     */
    public function expired(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_published' => true,
            'publish_at' => fake()->dateTimeBetween('-60 days', '-30 days'),
            'expires_at' => fake()->dateTimeBetween('-7 days', '-1 day'),
        ]);
    }
}
