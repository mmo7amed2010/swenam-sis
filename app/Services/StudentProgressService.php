<?php

namespace App\Services;

use App\Models\Course;
use App\Models\ModuleItemProgress;
use App\Models\QuizAttempt;
use App\Models\Submission;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

/**
 * Student Progress Service
 *
 * Centralizes student progress logic for course views.
 * Provides DataTables query support and progress calculations.
 */
class StudentProgressService
{
    /**
     * Get base query for course students (for DataTables).
     *
     * Students are enrolled in programs, not courses directly.
     */
    public function getCourseStudentsQuery(Course $course): Builder
    {
        return User::where('program_id', $course->program_id)
            ->where('user_type', 'student');
    }

    /**
     * Get students stats for a course.
     *
     * Returns total, started, and not_started counts.
     */
    public function getCourseStudentsStats(Course $course): array
    {
        $totalStudents = User::where('program_id', $course->program_id)
            ->where('user_type', 'student')
            ->count();

        // Count students who have started (any progress)
        $moduleItemIds = $this->getCourseModuleItemIds($course);

        $studentsWithProgress = 0;
        if ($moduleItemIds->isNotEmpty()) {
            $studentsWithProgress = ModuleItemProgress::whereIn('module_item_id', $moduleItemIds)
                ->distinct('user_id')
                ->count('user_id');
        }

        return [
            'total' => $totalStudents,
            'started' => $studentsWithProgress,
            'not_started' => max(0, $totalStudents - $studentsWithProgress),
        ];
    }

    /**
     * Calculate progress data for a student in a course.
     *
     * Returns content progress, assignment progress, and quiz progress.
     */
    public function getStudentProgress(User $student, Course $course): array
    {
        // Total items in course
        $totalItems = $course->modules()
            ->withCount('items')
            ->get()
            ->sum('items_count');

        // Completed items by student
        $moduleItemIds = $this->getCourseModuleItemIds($course);
        $completedItems = 0;

        if ($moduleItemIds->isNotEmpty()) {
            $completedItems = ModuleItemProgress::where('user_id', $student->id)
                ->whereIn('module_item_id', $moduleItemIds)
                ->whereNotNull('completed_at')
                ->count();
        }

        // Assignment progress
        $assignmentIds = $course->assignments()->pluck('id');
        $totalAssignments = $assignmentIds->count();
        $submittedAssignments = 0;

        if ($totalAssignments > 0) {
            $submittedAssignments = Submission::where('user_id', $student->id)
                ->whereIn('assignment_id', $assignmentIds)
                ->distinct('assignment_id')
                ->count('assignment_id');
        }

        // Quiz progress
        $quizIds = $course->quizzes()->pluck('id');
        $totalQuizzes = $quizIds->count();
        $completedQuizzes = 0;

        if ($totalQuizzes > 0) {
            $completedQuizzes = QuizAttempt::where('student_id', $student->id)
                ->whereIn('quiz_id', $quizIds)
                ->whereIn('status', ['completed', 'graded'])
                ->distinct('quiz_id')
                ->count('quiz_id');
        }

        return [
            'content_progress' => $totalItems > 0 ? round(($completedItems / $totalItems) * 100) : 0,
            'completed_items' => $completedItems,
            'total_items' => $totalItems,
            'assignments_submitted' => $submittedAssignments,
            'total_assignments' => $totalAssignments,
            'quizzes_completed' => $completedQuizzes,
            'total_quizzes' => $totalQuizzes,
        ];
    }

    /**
     * Get detailed progress for a student.
     *
     * Returns module-by-module progress, submissions, and quiz attempts.
     */
    public function getDetailedProgress(User $student, Course $course): array
    {
        // Load course with modules and items
        $course->load([
            'modules' => function ($query) {
                $query->orderBy('order_index')
                    ->with(['items' => function ($q) {
                        $q->orderBy('order_position');
                    }]);
            },
        ]);

        // Get item progress for this student
        $moduleItemIds = $this->getCourseModuleItemIds($course);
        $completedItemIds = [];

        if ($moduleItemIds->isNotEmpty()) {
            $completedItemIds = ModuleItemProgress::where('user_id', $student->id)
                ->whereIn('module_item_id', $moduleItemIds)
                ->whereNotNull('completed_at')
                ->pluck('module_item_id')
                ->toArray();
        }

        // Get assignment submissions with grades
        $assignmentIds = $course->assignments()->pluck('id');
        $submissions = collect();

        if ($assignmentIds->isNotEmpty()) {
            $submissions = Submission::where('user_id', $student->id)
                ->whereIn('assignment_id', $assignmentIds)
                ->with(['assignment', 'grades' => function ($q) {
                    $q->where('is_published', true)->latest('version');
                }])
                ->orderBy('submitted_at', 'desc')
                ->get();
        }

        // Get quiz attempts with scores
        $quizIds = $course->quizzes()->pluck('id');
        $quizAttempts = collect();

        if ($quizIds->isNotEmpty()) {
            $quizAttempts = QuizAttempt::where('student_id', $student->id)
                ->whereIn('quiz_id', $quizIds)
                ->with('quiz')
                ->orderBy('start_time', 'desc')
                ->get();
        }

        // Calculate overall grade
        $grades = [];
        foreach ($submissions as $submission) {
            $publishedGrade = $submission->grades->first();
            if ($publishedGrade) {
                $grades[] = [
                    'type' => 'assignment',
                    'title' => $submission->assignment->title,
                    'score' => $publishedGrade->points_earned,
                    'max' => $submission->assignment->total_points ?? 100,
                    'percentage' => $submission->assignment->total_points
                        ? round(($publishedGrade->points_earned / $submission->assignment->total_points) * 100)
                        : 0,
                ];
            }
        }

        foreach ($quizAttempts as $attempt) {
            if ($attempt->score !== null) {
                $grades[] = [
                    'type' => 'quiz',
                    'title' => $attempt->quiz->title,
                    'score' => $attempt->score,
                    'max' => $attempt->quiz->total_points ?? 100,
                    'percentage' => $attempt->quiz->total_points
                        ? round(($attempt->score / $attempt->quiz->total_points) * 100)
                        : 0,
                ];
            }
        }

        $averageGrade = count($grades) > 0
            ? round(collect($grades)->avg('percentage'))
            : null;

        return [
            'completedItemIds' => $completedItemIds,
            'submissions' => $submissions,
            'quizAttempts' => $quizAttempts,
            'grades' => $grades,
            'averageGrade' => $averageGrade,
        ];
    }

    /**
     * Get all module item IDs for a course.
     *
     * Helper method to avoid repeating the subquery.
     */
    protected function getCourseModuleItemIds(Course $course)
    {
        $moduleIds = $course->modules()->pluck('id');

        if ($moduleIds->isEmpty()) {
            return collect();
        }

        return \App\Models\ModuleItem::whereIn('module_id', $moduleIds)->pluck('id');
    }
}
