<?php

namespace App\Services;

use App\Models\Course;
use App\Models\User;
use Illuminate\Support\Facades\Cache;

/**
 * Course Statistics Service
 *
 * Centralizes course statistics logic shared by both admin and instructor contexts.
 * Provides cached methods for student count and pending grading calculations.
 */
class CourseStatisticsService
{
    protected int $cacheTtl = 300; // 5 minutes

    /**
     * Get student count for a course's program.
     *
     * Students are enrolled in programs, not courses directly.
     * We count students by getting students in the course's program.
     */
    public function getStudentCount(Course $course): int
    {
        return Cache::remember(
            "course_{$course->id}_student_count",
            $this->cacheTtl,
            fn () => User::where('program_id', $course->program_id)
                ->where('user_type', 'student')
                ->count()
        );
    }

    /**
     * Get pending grading count (assignments + quizzes).
     *
     * Pending assignments = submissions with status 'submitted'
     * Pending quizzes = attempts with status 'completed' (needs grading)
     */
    public function getPendingGradingCount(Course $course): int
    {
        return Cache::remember(
            "course_{$course->id}_pending_grading",
            $this->cacheTtl,
            fn () => $this->calculatePendingGrading($course)
        );
    }

    /**
     * Calculate pending grading count without caching.
     */
    protected function calculatePendingGrading(Course $course): int
    {
        $pendingAssignments = $course->assignments()
            ->withCount(['submissions as pending_count' => fn ($q) => $q->where('status', 'submitted')])
            ->get()
            ->sum('pending_count');

        $pendingQuizzes = $course->quizzes()
            ->withCount(['attempts as pending_count' => fn ($q) => $q->where('status', 'completed')])
            ->get()
            ->sum('pending_count');

        return $pendingAssignments + $pendingQuizzes;
    }

    /**
     * Clear cache for a course.
     *
     * Call this when grading status changes (submission graded, quiz attempt graded, etc.)
     */
    public function clearCache(Course $course): void
    {
        Cache::forget("course_{$course->id}_student_count");
        Cache::forget("course_{$course->id}_pending_grading");
    }

    /**
     * Clear only pending grading cache for a course.
     *
     * Call this when a submission is graded or a quiz attempt is graded.
     */
    public function clearPendingGradingCache(Course $course): void
    {
        Cache::forget("course_{$course->id}_pending_grading");
    }

    /**
     * Clear only student count cache for a course.
     *
     * Call this when a student is added/removed from the program.
     */
    public function clearStudentCountCache(Course $course): void
    {
        Cache::forget("course_{$course->id}_student_count");
    }
}
