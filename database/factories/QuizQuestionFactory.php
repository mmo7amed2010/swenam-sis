<?php

namespace Database\Factories;

use App\Models\Quiz;
use App\Models\QuizQuestion;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\QuizQuestion>
 */
class QuizQuestionFactory extends Factory
{
    protected $model = QuizQuestion::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $questionType = fake()->randomElement(['mcq', 'mcq', 'mcq', 'true_false']);

        return [
            'quiz_id' => Quiz::factory(),
            'question_type' => $questionType,
            'question_text' => fake()->sentence() . '?',
            'points' => fake()->randomElement([1, 2, 5, 10]),
            'order_number' => fake()->numberBetween(1, 50),
            'answers_json' => $this->generateAnswers($questionType),
            'settings_json' => [
                'randomize_answers' => fake()->boolean(50),
            ],
        ];
    }

    /**
     * Generate answers based on question type.
     */
    protected function generateAnswers(string $type): array
    {
        if ($type === 'true_false') {
            $correctAnswer = fake()->boolean();
            return [
                ['text' => 'True', 'is_correct' => $correctAnswer],
                ['text' => 'False', 'is_correct' => !$correctAnswer],
            ];
        }

        // MCQ - generate 4 options with 1 correct answer
        $correctIndex = fake()->numberBetween(0, 3);
        $answers = [];

        for ($i = 0; $i < 4; $i++) {
            $answers[] = [
                'text' => fake()->sentence(fake()->numberBetween(2, 8)),
                'is_correct' => $i === $correctIndex,
            ];
        }

        return $answers;
    }

    /**
     * Create a multiple choice question.
     */
    public function mcq(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'question_type' => 'mcq',
                'answers_json' => $this->generateAnswers('mcq'),
            ];
        });
    }

    /**
     * Create a true/false question.
     */
    public function trueFalse(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'question_type' => 'true_false',
                'answers_json' => $this->generateAnswers('true_false'),
            ];
        });
    }

    /**
     * Set specific points value.
     */
    public function points(int $points): static
    {
        return $this->state(fn (array $attributes) => [
            'points' => $points,
        ]);
    }
}
