<?php

namespace App\Services;

use App\Models\Quiz;
use App\Models\QuizQuestion;
use Illuminate\Support\Facades\Log;

class QuizQuestionService
{
    /**
     * Create a new quiz question.
     *
     * @param  Quiz  $quiz  Quiz to add question to
     * @param  array  $data  Question data
     * @return QuizQuestion Created question
     */
    public function createQuestion(Quiz $quiz, array $data): QuizQuestion
    {
        // Get next order number
        $orderNumber = QuizQuestion::where('quiz_id', $quiz->id)
            ->max('order_number') ?? 0;
        $orderNumber++;

        // Format answers based on question type
        $answersJson = $this->formatAnswers($data);

        $question = QuizQuestion::create([
            'quiz_id' => $quiz->id,
            'question_type' => $data['question_type'],
            'question_text' => $data['question_text'],
            'points' => $data['points'],
            'order_number' => $orderNumber,
            'answers_json' => $answersJson,
            'settings_json' => $this->formatSettings($data),
        ]);

        // Update quiz total points
        $this->updateQuizTotalPoints($quiz);

        Log::info('Quiz question created', [
            'question_id' => $question->id,
            'quiz_id' => $quiz->id,
            'question_type' => $data['question_type'],
        ]);

        return $question;
    }

    /**
     * Update an existing quiz question.
     *
     * @param  Quiz  $quiz  Quiz the question belongs to
     * @param  QuizQuestion  $question  Question to update
     * @param  array  $data  Update data
     * @return QuizQuestion Updated question
     */
    public function updateQuestion(Quiz $quiz, QuizQuestion $question, array $data): QuizQuestion
    {
        // Format answers based on question type
        $answersJson = $this->formatAnswers($data);

        $question->update([
            'question_type' => $data['question_type'],
            'question_text' => $data['question_text'],
            'points' => $data['points'],
            'answers_json' => $answersJson,
            'settings_json' => $this->formatSettings($data),
        ]);

        // Update quiz total points
        $this->updateQuizTotalPoints($quiz);

        Log::info('Quiz question updated', [
            'question_id' => $question->id,
            'quiz_id' => $quiz->id,
            'changes' => $question->getChanges(),
        ]);

        return $question->fresh();
    }

    /**
     * Delete a quiz question.
     *
     * @param  Quiz  $quiz  Quiz the question belongs to
     * @param  QuizQuestion  $question  Question to delete
     * @return QuizQuestion Deleted question
     */
    public function deleteQuestion(Quiz $quiz, QuizQuestion $question): QuizQuestion
    {
        // Soft delete question
        $question->delete();

        // Update quiz total points
        $this->updateQuizTotalPoints($quiz);

        Log::info('Quiz question deleted', [
            'question_id' => $question->id,
            'quiz_id' => $quiz->id,
        ]);

        return $question;
    }

    /**
     * Reorder questions in a quiz.
     *
     * @param  Quiz  $quiz  Quiz to reorder questions for
     * @param  array  $order  Array of question IDs in new order
     * @return array Question IDs in new order
     *
     * @throws \Exception If invalid question IDs provided
     */
    public function reorderQuestions(Quiz $quiz, array $order): array
    {
        // Verify all questions belong to this quiz
        $questionIds = QuizQuestion::where('quiz_id', $quiz->id)
            ->whereIn('id', $order)
            ->pluck('id')
            ->toArray();

        if (count($questionIds) !== count($order)) {
            throw new \Exception('Invalid question IDs provided for reordering.');
        }

        foreach ($order as $index => $questionId) {
            QuizQuestion::where('id', $questionId)
                ->where('quiz_id', $quiz->id)
                ->update(['order_number' => $index + 1]);
        }

        Log::info('Quiz questions reordered', [
            'quiz_id' => $quiz->id,
            'question_count' => count($order),
        ]);

        return $order;
    }

    /**
     * Format answers based on question type.
     *
     * @param  array  $data  Question data
     * @return array Formatted answers
     */
    public function formatAnswers(array $data): array
    {
        if ($data['question_type'] === 'mcq') {
            $answers = $data['answers'] ?? [];

            // Convert is_correct to boolean (handles "0", "1", 0, 1, true, false)
            return array_map(function ($answer) {
                $answer['is_correct'] = filter_var($answer['is_correct'] ?? false, FILTER_VALIDATE_BOOLEAN);

                return $answer;
            }, $answers);
        } elseif ($data['question_type'] === 'true_false') {
            return ['correct' => filter_var($data['correct_answer'] ?? false, FILTER_VALIDATE_BOOLEAN)];
        }

        return [];
    }

    /**
     * Format settings for question.
     *
     * @param  array  $data  Question data
     * @return array Formatted settings
     */
    private function formatSettings(array $data): array
    {
        $settings = [];

        if ($data['question_type'] === 'mcq' && isset($data['randomize_answers'])) {
            $settings['randomize_answers'] = filter_var($data['randomize_answers'], FILTER_VALIDATE_BOOLEAN);
        }

        return $settings;
    }

    /**
     * Update quiz total points based on all questions.
     *
     * @param  Quiz  $quiz  Quiz to update
     */
    private function updateQuizTotalPoints(Quiz $quiz): void
    {
        $quiz->update([
            'total_points' => $quiz->questions()->sum('points'),
        ]);
    }
}
