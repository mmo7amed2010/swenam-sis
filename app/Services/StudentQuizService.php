<?php

namespace App\Services;

use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\QuizQuestion;
use App\Repositories\QuizRepository;
use Illuminate\Support\Facades\Log;

class StudentQuizService
{
    public function __construct(
        private QuizRepository $quizRepository,
        private QuizGradingService $gradingService
    ) {}

    /**
     * Start a quiz attempt.
     *
     * @param  Quiz  $quiz  Quiz to start
     * @param  int  $studentId  Student user ID
     * @return QuizAttempt Created attempt
     *
     * @throws \Exception If cannot start quiz
     */
    public function startAttempt(Quiz $quiz, int $studentId): QuizAttempt
    {
        $attemptsUsed = $this->quizRepository->getAttemptCount($quiz->id, $studentId);
        $attemptsRemaining = $quiz->max_attempts === -1
            ? PHP_INT_MAX
            : max(0, $quiz->max_attempts - $attemptsUsed);

        // Check if can start
        $canStart = $this->canTakeQuiz($quiz, $studentId);
        if (! $canStart['can_start']) {
            throw new \Exception($canStart['reason']);
        }

        // Check for in-progress attempt
        $inProgress = $this->quizRepository->getInProgressAttempt($quiz->id, $studentId);
        if ($inProgress) {
            throw new \Exception('You have an attempt in progress.');
        }

        // Create new attempt
        $attempt = QuizAttempt::create([
            'quiz_id' => $quiz->id,
            'student_id' => $studentId,
            'attempt_number' => $attemptsUsed + 1,
            'start_time' => now(),
            'status' => 'in_progress',
        ]);

        // Load questions (shuffle if needed)
        $questions = $quiz->questions()->get();

        if ($quiz->shuffle_questions) {
            $questions = $questions->shuffle();
        }

        // Store question order for this attempt
        $attempt->update([
            'questions_order' => $questions->pluck('id')->toArray(),
        ]);

        Log::info('Quiz attempt started', [
            'attempt_id' => $attempt->id,
            'quiz_id' => $quiz->id,
            'student_id' => $studentId,
            'attempt_number' => $attempt->attempt_number,
        ]);

        return $attempt;
    }

    /**
     * Submit a quiz attempt.
     *
     * @param  Quiz  $quiz  Quiz being submitted
     * @param  QuizAttempt  $attempt  Attempt to submit
     * @return QuizAttempt Submitted and graded attempt
     */
    public function submitAttempt(Quiz $quiz, QuizAttempt $attempt): QuizAttempt
    {
        // Update attempt
        $attempt->update([
            'end_time' => now(),
            'status' => 'submitted',
        ]);

        // Auto-grade
        $result = $this->gradingService->gradeAttempt($attempt);

        $percentage = $quiz->total_points > 0
            ? ($result['total_score'] / $quiz->total_points) * 100
            : 0;

        $attempt->update([
            'score' => $result['total_score'],
            'percentage' => $percentage,
            'status' => 'graded', // Since we only have MCQ and True/False, all are auto-graded
        ]);

        Log::info('Quiz attempt submitted', [
            'attempt_id' => $attempt->id,
            'quiz_id' => $quiz->id,
            'student_id' => $attempt->student_id,
            'score' => $result['total_score'],
            'percentage' => $percentage,
        ]);

        return $attempt->fresh();
    }

    /**
     * Save an answer for an attempt.
     *
     * @param  QuizAttempt  $attempt  Attempt to save answer for
     * @param  int  $questionId  Question ID
     * @param  mixed  $answer  Student answer
     * @return QuizAttempt Updated attempt
     */
    public function saveAnswer(QuizAttempt $attempt, int $questionId, $answer): QuizAttempt
    {
        $answers = $attempt->answers_json ?? [];

        // Update or add answer
        $found = false;
        foreach ($answers as &$a) {
            if ($a['question_id'] == $questionId) {
                $a['answer'] = $answer;
                $a['answered_at'] = now()->toISOString();
                $found = true;
                break;
            }
        }

        if (! $found) {
            $answers[] = [
                'question_id' => $questionId,
                'answer' => $answer,
                'answered_at' => now()->toISOString(),
            ];
        }

        $attempt->update(['answers_json' => $answers]);

        return $attempt->fresh();
    }

    /**
     * Check if a student can take a quiz.
     *
     * @param  Quiz  $quiz  Quiz to check
     * @param  int  $studentId  Student user ID
     * @return array Result with can_start flag and reason
     */
    public function canTakeQuiz(Quiz $quiz, int $studentId): array
    {
        $attemptsUsed = $this->quizRepository->getAttemptCount($quiz->id, $studentId);
        $attemptsRemaining = $quiz->max_attempts === -1
            ? PHP_INT_MAX
            : max(0, $quiz->max_attempts - $attemptsUsed);

        // Check attempts
        if ($attemptsRemaining <= 0 && $quiz->max_attempts !== -1) {
            return ['can_start' => false, 'reason' => 'No attempts remaining'];
        }

        // Check due date
        if ($quiz->due_date && now()->isAfter($quiz->due_date)) {
            return ['can_start' => false, 'reason' => 'Quiz is past due date'];
        }

        // Check if there's an in-progress attempt
        $inProgress = $this->quizRepository->getInProgressAttempt($quiz->id, $studentId);
        if ($inProgress) {
            return [
                'can_start' => false,
                'reason' => 'You have an attempt in progress',
                'resume_url' => route('student.quizzes.take', [$quiz->course, $quiz, $inProgress]),
            ];
        }

        return ['can_start' => true];
    }

    /**
     * Get attempt summary for results page.
     *
     * @param  Quiz  $quiz  Quiz
     * @param  QuizAttempt  $attempt  Attempt
     * @param  int  $studentId  Student user ID
     * @return array Summary data
     */
    public function getAttemptSummary(Quiz $quiz, QuizAttempt $attempt, int $studentId): array
    {
        $percentage = $attempt->percentage ?? 0;
        $passed = $percentage >= $quiz->passing_score;
        $timeTaken = $attempt->time_taken;

        $attemptsUsed = $this->quizRepository->getAttemptCount($quiz->id, $studentId);
        $attemptsRemaining = $quiz->max_attempts === -1
            ? PHP_INT_MAX
            : max(0, $quiz->max_attempts - $attemptsUsed);

        // Check if can show correct answers
        $showCorrectAnswers = match ($quiz->show_correct_answers) {
            'never' => false,
            'after_each_attempt' => true,
            'after_all_attempts' => $attemptsRemaining == 0,
            'after_due_date' => $quiz->due_date && now()->isAfter($quiz->due_date),
        };

        // Load questions in attempt order
        $questionIds = $attempt->questions_order ?? $quiz->questions()->pluck('id')->toArray();
        $questions = QuizQuestion::whereIn('id', $questionIds)
            ->get()
            ->sortBy(function ($q) use ($questionIds) {
                return array_search($q->id, $questionIds);
            })
            ->values();

        $studentAnswers = collect($attempt->answers_json ?? [])
            ->keyBy('question_id')
            ->map(fn ($a) => $a['answer']);

        return [
            'percentage' => $percentage,
            'passed' => $passed,
            'timeTaken' => $timeTaken,
            'attemptsRemaining' => $attemptsRemaining,
            'showCorrectAnswers' => $showCorrectAnswers,
            'questions' => $questions,
            'studentAnswers' => $studentAnswers,
        ];
    }

    /**
     * Determine quiz status for listing.
     *
     * @param  Quiz  $quiz  Quiz
     * @param  \Illuminate\Database\Eloquent\Collection  $attempts  Student's attempts
     * @param  float|null  $bestScore  Best score
     * @param  int  $attemptsRemaining  Remaining attempts
     * @return string Status string
     */
    public function determineStatus(Quiz $quiz, $attempts, ?float $bestScore, int $attemptsRemaining): string
    {
        if ($attempts->isEmpty()) {
            return 'not_started';
        }

        $lastAttempt = $attempts->sortByDesc('created_at')->first();

        if ($lastAttempt->status === 'in_progress') {
            return 'in_progress';
        }

        if ($bestScore === null) {
            return 'not_started';
        }

        $percentage = $quiz->total_points > 0
            ? ($bestScore / $quiz->total_points) * 100
            : 0;

        if ($percentage >= $quiz->passing_score) {
            return 'passed';
        }

        // Failed
        if ($attemptsRemaining <= 0 && $quiz->max_attempts !== -1) {
            return 'failed_no_attempts';
        }

        return 'failed_can_retry';
    }
}
