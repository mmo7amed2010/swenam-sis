<?php

namespace Database\Factories;

use App\Models\CourseModule;
use App\Models\ModuleLesson;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ModuleLesson>
 */
class ModuleLessonFactory extends Factory
{
    protected $model = ModuleLesson::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $contentType = fake()->randomElement(['video', 'text_html', 'pdf', 'external_link', 'video_upload']);

        return [
            'module_id' => CourseModule::factory(),
            'title' => fake()->sentence(fake()->numberBetween(3, 8)),
            'content_type' => $contentType,
            'content' => $this->generateContent($contentType),
            'content_url' => $this->generateContentUrl($contentType),
            'file_path' => $contentType === 'pdf' ? 'lessons/'.fake()->uuid().'.pdf' : null,
            'order_number' => fake()->numberBetween(1, 20),
            'status' => fake()->randomElement(['published', 'published', 'published', 'draft']),
            'estimated_duration' => fake()->numberBetween(5, 60),
            'open_new_tab' => $contentType === 'external_link',
        ];
    }

    /**
     * Generate content based on type.
     */
    protected function generateContent(string $type): ?string
    {
        return match ($type) {
            'text_html' => fake()->paragraphs(fake()->numberBetween(3, 8), true),
            default => null,
        };
    }

    /**
     * Generate content URL based on type.
     */
    protected function generateContentUrl(string $type): ?string
    {
        return match ($type) {
            'video' => fake()->randomElement([
                'https://www.youtube.com/watch?v='.fake()->regexify('[A-Za-z0-9]{11}'),
                'https://vimeo.com/'.fake()->numberBetween(100000000, 999999999),
            ]),
            'external_link' => fake()->url(),
            default => null,
        };
    }

    /**
     * Create a video lesson.
     */
    public function video(): static
    {
        return $this->state(fn (array $attributes) => [
            'content_type' => 'video',
            'content' => null,
            'content_url' => 'https://www.youtube.com/watch?v='.fake()->regexify('[A-Za-z0-9]{11}'),
            'file_path' => null,
            'estimated_duration' => fake()->numberBetween(10, 45),
        ]);
    }

    /**
     * Create a text/HTML lesson.
     */
    public function text(): static
    {
        return $this->state(fn (array $attributes) => [
            'content_type' => 'text_html',
            'content' => fake()->paragraphs(5, true),
            'content_url' => null,
            'file_path' => null,
            'estimated_duration' => fake()->numberBetween(10, 30),
        ]);
    }

    /**
     * Create an HTML lesson (alias for text).
     */
    public function html(): static
    {
        return $this->state(fn (array $attributes) => [
            'content_type' => 'text_html',
            'content' => '<div>'.fake()->paragraphs(5, true).'</div>',
            'content_url' => null,
            'file_path' => null,
        ]);
    }

    /**
     * Create a PDF lesson.
     */
    public function pdf(): static
    {
        return $this->state(fn (array $attributes) => [
            'content_type' => 'pdf',
            'content' => null,
            'content_url' => null,
            'file_path' => 'lessons/'.fake()->uuid().'.pdf',
            'estimated_duration' => fake()->numberBetween(15, 45),
        ]);
    }

    /**
     * Create an external link lesson.
     */
    public function externalLink(): static
    {
        return $this->state(fn (array $attributes) => [
            'content_type' => 'external_link',
            'content' => null,
            'content_url' => fake()->url(),
            'file_path' => null,
            'open_new_tab' => true,
        ]);
    }

    /**
     * Create a published lesson.
     */
    public function published(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'published',
        ]);
    }

    /**
     * Create a draft lesson.
     */
    public function draft(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'draft',
        ]);
    }
}
