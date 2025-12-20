<?php

namespace App\Repositories;

use App\Models\Quiz;
use App\Models\QuizAttempt;
use Illuminate\Database\Eloquent\Collection;

class QuizRepository
{
    /**
     * Get published quizzes for a course with student attempts.
     *
     * @param  int  $courseId  Course ID
     * @param  int  $studentId  Student user ID
     * @return Collection Quizzes with attempts
     */
    public function getStudentQuizzes(int $courseId, int $studentId): Collection
    {
        return Quiz::where('course_id', $courseId)
            ->where('published', true)
            ->with(['attempts' => function ($q) use ($studentId) {
                $q->where('student_id', $studentId);
            }])
            ->orderBy('due_date')
            ->get();
    }

    /**
     * Get quiz attempts for a student.
     *
     * @param  int  $quizId  Quiz ID
     * @param  int  $studentId  Student user ID
     * @return Collection Student's attempts
     */
    public function getStudentAttempts(int $quizId, int $studentId): Collection
    {
        return QuizAttempt::where('quiz_id', $quizId)
            ->where('student_id', $studentId)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Get in-progress attempt for a student.
     *
     * @param  int  $quizId  Quiz ID
     * @param  int  $studentId  Student user ID
     * @return QuizAttempt|null In-progress attempt or null
     */
    public function getInProgressAttempt(int $quizId, int $studentId): ?QuizAttempt
    {
        return QuizAttempt::where('quiz_id', $quizId)
            ->where('student_id', $studentId)
            ->where('status', 'in_progress')
            ->first();
    }

    /**
     * Get attempt count for a student.
     *
     * @param  int  $quizId  Quiz ID
     * @param  int  $studentId  Student user ID
     * @return int Attempt count
     */
    public function getAttemptCount(int $quizId, int $studentId): int
    {
        return QuizAttempt::where('quiz_id', $quizId)
            ->where('student_id', $studentId)
            ->count();
    }

    /**
     * Get best score for a student.
     *
     * @param  int  $quizId  Quiz ID
     * @param  int  $studentId  Student user ID
     * @return float|null Best score or null
     */
    public function getBestScore(int $quizId, int $studentId): ?float
    {
        return QuizAttempt::where('quiz_id', $quizId)
            ->where('student_id', $studentId)
            ->max('score');
    }
}
