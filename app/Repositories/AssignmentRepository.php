<?php

namespace App\Repositories;

use App\Models\Assignment;
use App\Models\Submission;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class AssignmentRepository
{
    /**
     * Get assignments for a student with filters.
     *
     * @param  array  $courseIds  Course IDs to filter by
     * @param  string  $filter  Filter type (all, upcoming, overdue, graded)
     * @param  int  $userId  Student user ID
     * @return Collection Filtered assignments
     */
    public function getStudentAssignments(array $courseIds, string $filter, int $userId): Collection
    {
        $query = Assignment::whereIn('course_id', $courseIds)
            ->available()
            ->with(['course', 'submissions' => function ($q) use ($userId) {
                $q->where('user_id', $userId);
            }])
            ->withCount(['submissions as student_submissions_count' => function ($q) use ($userId) {
                $q->where('user_id', $userId);
            }]);

        // Apply filters
        switch ($filter) {
            case 'upcoming':
                $query->upcoming();
                break;
            case 'overdue':
                $query->overdue()
                    ->whereDoesntHave('submissions', function ($q) use ($userId) {
                        $q->where('user_id', $userId)
                            ->where('status', 'submitted');
                    });
                break;
            case 'graded':
                $query->whereHas('submissions', function ($q) use ($userId) {
                    $q->where('user_id', $userId)
                        ->whereHas('grades', function ($gq) {
                            $gq->where('is_published', true);
                        });
                });
                break;
        }

        return $query->orderBy('due_date', 'asc')->get();
    }

    /**
     * Get assignment status for a student.
     *
     * @param  Assignment  $assignment  Assignment to check
     * @param  int  $userId  Student user ID
     * @return string Status (not_submitted, submitted, graded, overdue)
     */
    public function getAssignmentStatus(Assignment $assignment, int $userId): string
    {
        $submission = Submission::where('assignment_id', $assignment->id)
            ->where('user_id', $userId)
            ->where('status', 'submitted')
            ->first();

        if ($submission) {
            // Check if graded
            $publishedGrade = $submission->publishedGrade();
            if ($publishedGrade) {
                return 'graded';
            }

            return 'submitted';
        }

        return 'not_submitted';
    }

    /**
     * Get submissions for an assignment with filters and sorting.
     *
     * @param  int  $assignmentId  Assignment ID
     * @param  string  $filter  Filter type (all, ungraded, graded, late)
     * @param  string  $sortBy  Sort field
     * @param  string  $sortDir  Sort direction
     * @return Collection Filtered and sorted submissions
     */
    public function getSubmissionsForGrading(int $assignmentId, string $filter = 'all', string $sortBy = 'submitted_at', string $sortDir = 'desc'): Collection
    {
        $query = Submission::where('assignment_id', $assignmentId)
            ->whereIn('status', ['submitted', 'graded'])
            ->with(['student', 'grades']);

        // Apply filters
        switch ($filter) {
            case 'ungraded':
                $query->whereDoesntHave('grades', function ($q) {
                    $q->where('is_published', true);
                });
                break;
            case 'graded':
                $query->whereHas('grades', function ($q) {
                    $q->where('is_published', true);
                });
                break;
            case 'late':
                $query->where('is_late', true);
                break;
        }

        // Get submissions first, then sort in memory
        $submissions = $query->get();

        // Apply sorting
        switch ($sortBy) {
            case 'student_name':
                $submissions = $submissions->sortBy(function ($submission) {
                    return $submission->student ? $submission->student->name : '';
                }, SORT_REGULAR, $sortDir === 'desc');
                break;
            case 'grade':
                $submissions = $submissions->sortBy(function ($submission) {
                    $grade = $submission->publishedGrade();

                    return $grade ? $grade->points_awarded : 0;
                }, SORT_REGULAR, $sortDir === 'desc');
                break;
            default:
                $submissions = $submissions->sortBy('submitted_at', SORT_REGULAR, $sortDir === 'desc');
        }

        return $submissions->values();
    }

    /**
     * Get submissions query builder for DataTable server-side processing.
     *
     * Returns a query builder (not a collection) that can be used with
     * HandlesDataTableRequests trait for server-side pagination, sorting, and filtering.
     *
     * @param  int  $assignmentId  Assignment ID
     * @return Builder Query builder for submissions
     */
    public function getSubmissionsForGradingQuery(int $assignmentId): Builder
    {
        return Submission::query()
            ->where('submissions.assignment_id', $assignmentId)
            ->whereIn('submissions.status', ['submitted', 'graded'])
            ->leftJoin('users as students', 'submissions.user_id', '=', 'students.id')
            ->leftJoin('grades as published_grades', function ($join) {
                $join->on('submissions.id', '=', 'published_grades.submission_id')
                    ->where('published_grades.is_published', true);
            })
            ->select([
                'submissions.*',
                'students.name as student_name',
                'students.email as student_email',
                'students.profile_photo_path as student_photo',
                'published_grades.id as published_grade_id',
                'published_grades.points_awarded as published_grade_points',
                'published_grades.max_points as published_grade_max',
                DB::raw('CASE WHEN published_grades.max_points > 0 THEN ROUND((published_grades.points_awarded / published_grades.max_points) * 100, 2) ELSE 0 END as published_grade_percentage'),
            ])
            ->with(['student']);
    }

    /**
     * Get submission statistics for an assignment.
     *
     * @param  int  $assignmentId  Assignment ID
     * @param  int|null  $programId  Program ID for total student count
     * @return array Statistics array
     */
    public function getSubmissionStats(int $assignmentId, ?int $programId = null): array
    {
        $allSubmissions = Submission::where('assignment_id', $assignmentId)->get();

        $stats = [
            'total' => $allSubmissions->count(),
            'pending' => $allSubmissions->where('status', 'pending')->count(),
            'submitted' => $allSubmissions->where('status', 'submitted')->count(),
            'graded' => $allSubmissions->where('status', 'graded')->count(),
        ];

        // Get total students if program ID provided
        if ($programId) {
            $stats['total_students'] = \App\Models\User::where('program_id', $programId)
                ->where('user_type', 'student')
                ->count();
        }

        return $stats;
    }
}
