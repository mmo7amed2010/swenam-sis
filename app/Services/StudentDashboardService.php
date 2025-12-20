<?php

namespace App\Services;

use App\Models\Assignment;
use App\Models\Course;
use App\Models\CourseGrade;
use App\Models\Grade;
use App\Models\ModuleItemProgress;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\Student;
use App\Models\Submission;
use App\Models\User;
use App\Support\CacheKey;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

/**
 * Service for retrieving student dashboard data.
 * Provides real-time data for dashboard widgets with caching for performance.
 */
class StudentDashboardService
{
    /**
     * Cache TTL in seconds (5 minutes).
     */
    private const CACHE_TTL = 300;

    /**
     * Get upcoming deadlines (assignments and quizzes) for a student.
     * Uses program-based access - students see deadlines for ALL active courses in their program.
     *
     * @param  int  $userId  Student user ID
     * @param  int  $limit  Maximum number of items to return
     * @return Collection Collection of deadline items
     */
    public function getUpcomingDeadlines(int $userId, int $limit = 5): Collection
    {
        $cacheKey = CacheKey::studentDashboard($userId, 'deadlines');

        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($userId, $limit) {
            try {
                // Get course IDs from student's program (program-based access)
                $user = User::find($userId);
                if (! $user || ! $user->program_id) {
                    return collect();
                }

                $programCourseIds = $user->programCourses()->pluck('id');

                if ($programCourseIds->isEmpty()) {
                    return collect();
                }

                $now = now();
                $twoWeeksFromNow = now()->addDays(14);

                // Get upcoming assignments
                $assignments = Assignment::whereIn('course_id', $programCourseIds)
                    ->where('is_published', true)
                    ->where('due_date', '>=', $now)
                    ->where('due_date', '<=', $twoWeeksFromNow)
                    ->whereNull('deleted_at')
                    ->with('course:id,name,course_code')
                    ->select('id', 'course_id', 'title', 'due_date', 'max_points')
                    ->get()
                    ->map(function ($assignment) use ($userId) {
                        $hasSubmission = Submission::where('assignment_id', $assignment->id)
                            ->where('user_id', $userId)
                            ->whereIn('status', ['submitted', 'graded'])
                            ->exists();

                        return [
                            'id' => $assignment->id,
                            'type' => 'assignment',
                            'title' => $assignment->title,
                            'course_name' => $assignment->course->name ?? 'Unknown Course',
                            'course_code' => $assignment->course->course_code ?? '',
                            'due_date' => $assignment->due_date,
                            'points' => $assignment->max_points,
                            'status' => $hasSubmission ? 'submitted' : 'pending',
                            'icon' => 'document',
                            'color' => 'primary',
                        ];
                    });

                // Get upcoming quizzes
                $quizzes = Quiz::whereIn('course_id', $programCourseIds)
                    ->where('published', true)
                    ->where('due_date', '>=', $now)
                    ->where('due_date', '<=', $twoWeeksFromNow)
                    ->whereNull('deleted_at')
                    ->with('course:id,name,course_code')
                    ->select('id', 'course_id', 'title', 'due_date', 'total_points', 'max_attempts')
                    ->get()
                    ->map(function ($quiz) use ($userId) {
                        $attemptCount = QuizAttempt::where('quiz_id', $quiz->id)
                            ->where('student_id', $userId)
                            ->count();

                        $hasCompleted = QuizAttempt::where('quiz_id', $quiz->id)
                            ->where('student_id', $userId)
                            ->whereIn('status', ['submitted', 'graded'])
                            ->exists();

                        return [
                            'id' => $quiz->id,
                            'type' => 'quiz',
                            'title' => $quiz->title,
                            'course_name' => $quiz->course->name ?? 'Unknown Course',
                            'course_code' => $quiz->course->course_code ?? '',
                            'due_date' => $quiz->due_date,
                            'points' => $quiz->total_points,
                            'status' => $hasCompleted ? 'completed' : 'pending',
                            'attempts_used' => $attemptCount,
                            'max_attempts' => $quiz->max_attempts,
                            'icon' => 'questionnaire-tablet',
                            'color' => 'info',
                        ];
                    });

                // Combine and sort by due date
                return $assignments->concat($quizzes)
                    ->filter(fn ($item) => $item['status'] !== 'completed' && $item['status'] !== 'submitted')
                    ->sortBy('due_date')
                    ->take($limit)
                    ->values();
            } catch (\Exception $e) {
                Log::error('Failed to get upcoming deadlines', [
                    'user_id' => $userId,
                    'error' => $e->getMessage(),
                ]);

                return collect();
            }
        });
    }

    /**
     * Get current courses with progress for a student.
     * Uses program-based access - students see ALL active courses in their program.
     * Progress is now weight-based using ModuleItemProgressService.
     *
     * @param  int  $userId  Student user ID
     * @param  int|null  $programId  Student's program ID
     * @return Collection Collection of course data with progress
     */
    public function getCurrentCourses(int $userId, ?int $programId): Collection
    {
        $cacheKey = CacheKey::studentDashboard($userId, 'courses', $programId);

        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($userId, $programId) {
            try {
                if (! $programId) {
                    return collect();
                }

                // Get all active courses in student's program (program-based access)
                $courses = Course::where('program_id', $programId)
                    ->where('status', 'active')
                    ->with([
                        'modules' => function ($q) {
                            $q->where('status', 'published')
                                ->orderBy('order_index');
                        },
                        'instructors' => function ($q) {
                            $q->whereNull('removed_at')
                                ->with('instructor:id,name');
                        },
                    ])
                    ->orderBy('course_code')
                    ->get();

                // Get grades for all courses at once
                $courseGrades = CourseGrade::where('student_id', $userId)
                    ->whereIn('course_id', $courses->pluck('id'))
                    ->get()
                    ->keyBy('course_id');

                // Get the progress service for weight-based calculations
                $progressService = app(ModuleItemProgressService::class);

                return $courses->map(function ($course) use ($userId, $courseGrades, $progressService) {
                    // Calculate progress using ModuleItemProgressService (weight-based)
                    $progress = $progressService->getCourseProgress($userId, $course->id);

                    // Get instructor name (simplified: one instructor per course)
                    $instructorAssignment = $course->instructors->where('removed_at', null)->first();
                    $instructorName = $instructorAssignment?->instructor?->name ?? 'TBA';

                    // Get course grade if exists
                    $courseGrade = $courseGrades->get($course->id);

                    // Get last accessed time
                    $lastAccessed = ModuleItemProgress::where('user_id', $userId)
                        ->where('course_id', $course->id)
                        ->whereNotNull('last_accessed_at')
                        ->orderBy('last_accessed_at', 'desc')
                        ->value('last_accessed_at');

                    return [
                        'id' => $course->id,
                        'course_code' => $course->course_code,
                        'name' => $course->name,
                        'credits' => $course->credits,
                        'instructor' => $instructorName,
                        'progress_percentage' => $progress['percentage'],
                        'completed_items' => $progress['completed_items'],
                        'total_items' => $progress['total_items'],
                        'current_grade' => $courseGrade?->percentage,
                        'letter_grade' => $courseGrade?->letter_grade,
                        'last_accessed' => $lastAccessed,
                    ];
                })->values();
            } catch (\Exception $e) {
                Log::error('Failed to get current courses', [
                    'user_id' => $userId,
                    'program_id' => $programId,
                    'error' => $e->getMessage(),
                ]);

                return collect();
            }
        });
    }

    /**
     * Get recent activity for a student.
     *
     * @param  int  $userId  Student user ID
     * @param  int  $limit  Maximum number of items to return
     * @return Collection Collection of activity items
     */
    public function getRecentActivity(int $userId, int $limit = 10): Collection
    {
        try {
            $activities = collect();

            // Recent grades received
            $grades = Grade::join('submissions', 'grades.submission_id', '=', 'submissions.id')
                ->join('assignments', 'submissions.assignment_id', '=', 'assignments.id')
                ->join('courses', 'assignments.course_id', '=', 'courses.id')
                ->where('submissions.user_id', $userId)
                ->where('grades.is_published', true)
                ->whereNull('assignments.deleted_at')
                ->select(
                    'grades.id',
                    'grades.points_awarded',
                    'grades.max_points',
                    'grades.published_at as activity_date',
                    'assignments.title as item_title',
                    'courses.name as course_name',
                    'courses.course_code'
                )
                ->orderBy('grades.published_at', 'desc')
                ->limit($limit)
                ->get()
                ->map(function ($grade) {
                    $percentage = $grade->max_points > 0
                        ? round(($grade->points_awarded / $grade->max_points) * 100)
                        : 0;

                    return [
                        'id' => $grade->id,
                        'type' => 'grade',
                        'title' => "Grade received: {$grade->item_title}",
                        'description' => "{$grade->points_awarded}/{$grade->max_points} ({$percentage}%)",
                        'course_name' => $grade->course_name,
                        'course_code' => $grade->course_code,
                        'date' => Carbon::parse($grade->activity_date),
                        'icon' => 'chart-line-up',
                        'color' => $this->getPerformanceColor($percentage),
                    ];
                });

            $activities = $activities->concat($grades);

            // Recent quiz completions
            $quizCompletions = QuizAttempt::join('quizzes', 'quiz_attempts.quiz_id', '=', 'quizzes.id')
                ->join('courses', 'quizzes.course_id', '=', 'courses.id')
                ->where('quiz_attempts.student_id', $userId)
                ->whereIn('quiz_attempts.status', ['submitted', 'graded'])
                ->whereNull('quizzes.deleted_at')
                ->select(
                    'quiz_attempts.id',
                    'quiz_attempts.score',
                    'quiz_attempts.percentage',
                    'quiz_attempts.updated_at as activity_date',
                    'quizzes.title as item_title',
                    'quizzes.total_points',
                    'courses.name as course_name',
                    'courses.course_code'
                )
                ->orderBy('quiz_attempts.updated_at', 'desc')
                ->limit($limit)
                ->get()
                ->map(function ($attempt) {
                    $percentage = round($attempt->percentage ?? 0);

                    return [
                        'id' => $attempt->id,
                        'type' => 'quiz_completed',
                        'title' => "Quiz completed: {$attempt->item_title}",
                        'description' => "{$attempt->score}/{$attempt->total_points} ({$percentage}%)",
                        'course_name' => $attempt->course_name,
                        'course_code' => $attempt->course_code,
                        'date' => Carbon::parse($attempt->activity_date),
                        'icon' => 'questionnaire-tablet',
                        'color' => $this->getPerformanceColor($percentage),
                    ];
                });

            $activities = $activities->concat($quizCompletions);

            // Recent module item completions (lessons, quizzes, assignments)
            // Use Eloquent with eager loading instead of raw join to get polymorphic title
            $itemCompletions = ModuleItemProgress::with([
                'moduleItem.itemable',
                'moduleItem.module.course:id,name,course_code',
            ])
                ->where('user_id', $userId)
                ->whereNotNull('completed_at')
                ->orderBy('completed_at', 'desc')
                ->limit($limit)
                ->get()
                ->filter(fn ($progress) => $progress->moduleItem !== null)
                ->map(function ($progress) {
                    $moduleItem = $progress->moduleItem;
                    $itemable = $moduleItem->itemable;
                    $course = $moduleItem->module?->course;

                    // Get title from the polymorphic relation
                    $itemTitle = $itemable?->title ?? $itemable?->name ?? 'Unknown Item';
                    $itemableType = $moduleItem->itemable_type;

                    // Determine type label based on itemable_type
                    $typeLabel = match ($itemableType) {
                        'App\\Models\\ModuleLesson' => 'Lesson',
                        'App\\Models\\Quiz' => 'Quiz',
                        'App\\Models\\Assignment' => 'Assignment',
                        default => 'Item',
                    };

                    $icon = match ($itemableType) {
                        'App\\Models\\ModuleLesson' => 'book-open',
                        'App\\Models\\Quiz' => 'questionnaire-tablet',
                        'App\\Models\\Assignment' => 'document',
                        default => 'abstract-26',
                    };

                    return [
                        'id' => $progress->id,
                        'type' => 'item_completed',
                        'title' => "{$typeLabel} completed: {$itemTitle}",
                        'description' => 'Marked as complete',
                        'course_name' => $course?->name ?? 'Unknown Course',
                        'course_code' => $course?->course_code ?? '',
                        'date' => Carbon::parse($progress->completed_at),
                        'icon' => $icon,
                        'color' => 'success',
                    ];
                });

            $activities = $activities->concat($itemCompletions);

            // Sort all activities by date and take limit
            return $activities
                ->sortByDesc('date')
                ->take($limit)
                ->values();
        } catch (\Exception $e) {
            Log::error('Failed to get recent activity', [
                'user_id' => $userId,
                'error' => $e->getMessage(),
            ]);

            return collect();
        }
    }

    /**
     * Get overall progress statistics for a student.
     * Uses program-based access - statistics are calculated from all active courses in program.
     * Progress is now weight-based using ModuleItemProgressService.
     *
     * @param  int  $userId  Student user ID
     * @param  int|null  $programId  Student's program ID
     * @return array Statistics array
     */
    public function getProgressStats(int $userId, ?int $programId): array
    {
        $cacheKey = CacheKey::studentDashboard($userId, 'stats', $programId);

        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($userId, $programId) {
            try {
                if (! $programId) {
                    return $this->getEmptyStats();
                }

                // Get all active courses in student's program (program-based access)
                $programCourses = Course::where('program_id', $programId)
                    ->where('status', 'active')
                    ->get();

                $programCourseIds = $programCourses->pluck('id');

                // Get the progress service for weight-based calculations
                $progressService = app(ModuleItemProgressService::class);

                // Calculate progress for each course to determine status
                $coursesCompleted = 0;
                $coursesInProgress = 0;
                $totalProgress = 0;
                $creditsEarned = 0;

                foreach ($programCourses as $course) {
                    // Use ModuleItemProgressService for weight-based progress
                    // IMPORTANT: ModuleItemProgress uses user_id (not students.id)
                    $progress = $progressService->getCourseProgress($userId, $course->id);

                    $progressPercentage = $progress['percentage'];
                    $totalProgress += $progressPercentage;

                    if ($progressPercentage >= 100) {
                        $coursesCompleted++;
                        $creditsEarned += $course->credits ?? 0;
                    } elseif ($progressPercentage > 0) {
                        $coursesInProgress++;
                    }
                }

                // Calculate average progress
                $overallProgress = $programCourses->count() > 0
                    ? round($totalProgress / $programCourses->count())
                    : 0;

                // Calculate GPA from course grades
                $courseGrades = CourseGrade::where('student_id', $userId)
                    ->whereIn('course_id', $programCourseIds)
                    ->get();
                $gpa = 0;

                if ($courseGrades->count() > 0) {
                    $avgPercentage = $courseGrades->avg('percentage');
                    $gpa = round($avgPercentage / 25, 2); // Convert to 4.0 scale
                }

                // Calculate total credits required
                $creditsRequired = $programCourses->sum('credits');

                // Count pending assignments
                $pendingAssignments = Assignment::whereIn('course_id', $programCourseIds)
                    ->where('is_published', true)
                    ->where('due_date', '>=', now())
                    ->whereNull('deleted_at')
                    ->whereNotIn('id', function ($query) use ($userId) {
                        $query->select('assignment_id')
                            ->from('submissions')
                            ->where('user_id', $userId)
                            ->whereIn('status', ['submitted', 'graded']);
                    })
                    ->count();

                return [
                    'courses_completed' => $coursesCompleted,
                    'courses_in_progress' => $coursesInProgress,
                    'overall_progress' => $overallProgress,
                    'gpa' => $gpa,
                    'credits_earned' => (float) $creditsEarned,
                    'credits_required' => (float) $creditsRequired,
                    'pending_assignments' => $pendingAssignments,
                ];
            } catch (\Exception $e) {
                Log::error('Failed to get progress stats', [
                    'user_id' => $userId,
                    'program_id' => $programId,
                    'error' => $e->getMessage(),
                ]);

                return $this->getEmptyStats();
            }
        });
    }

    /**
     * Get tasks due today or overdue.
     * Uses program-based access - students see tasks for ALL active courses in their program.
     *
     * @param  int  $userId  Student user ID
     * @return Collection Collection of task items
     */
    public function getTodaysTasks(int $userId): Collection
    {
        $cacheKey = CacheKey::studentDashboard($userId, 'today');

        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($userId) {
            try {
                // Get course IDs from student's program (program-based access)
                $user = User::find($userId);
                if (! $user || ! $user->program_id) {
                    return collect();
                }

                $programCourseIds = $user->programCourses()->pluck('id');

                if ($programCourseIds->isEmpty()) {
                    return collect();
                }

                $today = now()->startOfDay();
                $endOfToday = now()->endOfDay();

                $tasks = collect();

                // Assignments due today or overdue (not yet submitted)
                $assignments = Assignment::whereIn('course_id', $programCourseIds)
                    ->where('is_published', true)
                    ->where('due_date', '<=', $endOfToday)
                    ->whereNull('deleted_at')
                    ->whereNotIn('id', function ($query) use ($userId) {
                        $query->select('assignment_id')
                            ->from('submissions')
                            ->where('user_id', $userId)
                            ->whereIn('status', ['submitted', 'graded']);
                    })
                    ->with('course:id,name,course_code')
                    ->select('id', 'course_id', 'title', 'due_date', 'max_points')
                    ->get()
                    ->map(function ($assignment) use ($today) {
                        $isOverdue = $assignment->due_date < $today;
                        $isDueToday = $assignment->due_date->isToday();

                        return [
                            'id' => $assignment->id,
                            'type' => 'assignment',
                            'title' => $assignment->title,
                            'course_name' => $assignment->course->name ?? 'Unknown Course',
                            'course_code' => $assignment->course->course_code ?? '',
                            'due_date' => $assignment->due_date,
                            'due_time' => $assignment->due_date->format('g:i A'),
                            'points' => $assignment->max_points,
                            'is_overdue' => $isOverdue,
                            'is_due_today' => $isDueToday,
                            'status' => $isOverdue ? 'overdue' : 'due_today',
                            'status_color' => $isOverdue ? 'danger' : 'warning',
                            'icon' => 'document',
                        ];
                    });

                $tasks = $tasks->concat($assignments);

                // Quizzes due today or overdue (not yet completed)
                $quizzes = Quiz::whereIn('course_id', $programCourseIds)
                    ->where('published', true)
                    ->where('due_date', '<=', $endOfToday)
                    ->whereNull('deleted_at')
                    ->whereNotIn('id', function ($query) use ($userId) {
                        $query->select('quiz_id')
                            ->from('quiz_attempts')
                            ->where('student_id', $userId)
                            ->whereIn('status', ['submitted', 'graded']);
                    })
                    ->with('course:id,name,course_code')
                    ->select('id', 'course_id', 'title', 'due_date', 'total_points')
                    ->get()
                    ->map(function ($quiz) use ($today) {
                        $isOverdue = $quiz->due_date < $today;
                        $isDueToday = $quiz->due_date->isToday();

                        return [
                            'id' => $quiz->id,
                            'type' => 'quiz',
                            'title' => $quiz->title,
                            'course_name' => $quiz->course->name ?? 'Unknown Course',
                            'course_code' => $quiz->course->course_code ?? '',
                            'due_date' => $quiz->due_date,
                            'due_time' => $quiz->due_date->format('g:i A'),
                            'points' => $quiz->total_points,
                            'is_overdue' => $isOverdue,
                            'is_due_today' => $isDueToday,
                            'status' => $isOverdue ? 'overdue' : 'due_today',
                            'status_color' => $isOverdue ? 'danger' : 'warning',
                            'icon' => 'questionnaire-tablet',
                        ];
                    });

                $tasks = $tasks->concat($quizzes);

                // Sort: overdue first, then by due date
                return $tasks
                    ->sortBy([
                        ['is_overdue', 'desc'],
                        ['due_date', 'asc'],
                    ])
                    ->values();
            } catch (\Exception $e) {
                Log::error('Failed to get today\'s tasks', [
                    'user_id' => $userId,
                    'error' => $e->getMessage(),
                ]);

                return collect();
            }
        });
    }

    /**
     * Clear all dashboard cache for a student.
     *
     * @param  int  $userId  Student user ID
     * @param  int|null  $programId  Student's program ID
     */
    public function clearCache(int $userId, ?int $programId = null): void
    {
        CacheKey::invalidateUserDashboard($userId, $programId);
    }

    /**
     * Get performance color based on percentage.
     *
     * @return string Bootstrap color class
     */
    private function getPerformanceColor(float $percentage): string
    {
        return match (true) {
            $percentage >= 90 => 'success',
            $percentage >= 80 => 'primary',
            $percentage >= 70 => 'warning',
            $percentage >= 60 => 'info',
            default => 'danger',
        };
    }

    /**
     * Get empty statistics array for error cases or students without a program.
     */
    private function getEmptyStats(): array
    {
        return [
            'courses_completed' => 0,
            'courses_in_progress' => 0,
            'overall_progress' => 0,
            'gpa' => 0,
            'credits_earned' => 0,
            'credits_required' => 0,
            'pending_assignments' => 0,
        ];
    }
}
