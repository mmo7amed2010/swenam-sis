<?php

namespace App\Repositories;

use App\Models\LessonProgress;
use App\Models\ModuleLesson;
use App\Support\CacheKey;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

/**
 * @deprecated Use ModuleItemProgressService instead. This repository will be removed in a future version.
 */
class LessonProgressRepository
{
    public function __construct()
    {
        trigger_error(
            'LessonProgressRepository is deprecated. Use ModuleItemProgressService instead.',
            E_USER_DEPRECATED
        );
    }

    /**
     * Mark lesson as complete
     *
     * @deprecated Use ModuleItemProgressService::markComplete() instead
     */
    public function markComplete(int $userId, int $lessonId, int $courseId): LessonProgress
    {
        $progress = LessonProgress::firstOrCreate(
            [
                'user_id' => $userId,
                'lesson_id' => $lessonId,
            ],
            [
                'course_id' => $courseId,
                'completed_at' => now(),
            ]
        );

        // Clear cached progress data
        $this->clearProgressCache($userId, $courseId);

        return $progress;
    }

    /**
     * Get course progress data
     *
     * @deprecated Use ModuleItemProgressService::getCourseProgress() instead
     */
    public function getCourseProgress(int $userId, int $courseId): array
    {
        $cacheKey = CacheKey::courseProgress($userId, $courseId);

            $totalLessons = DB::table('module_lessons')
                ->join('course_modules', 'module_lessons.module_id', '=', 'course_modules.id')
                ->where('course_modules.course_id', $courseId)
                ->where('module_lessons.status', 'published')
                ->where('course_modules.status', 'published')
                ->whereNull('module_lessons.deleted_at')
                ->whereNull('course_modules.deleted_at')
                ->count();

            $completedLessons = LessonProgress::where('user_id', $userId)
                ->where('course_id', $courseId)
                ->count();

            $percentage = $totalLessons > 0
                ? round(($completedLessons / $totalLessons) * 100)
                : 0;

            return [
                'total_lessons' => $totalLessons,
            'completed_lessons' => $completedLessons,
            'percentage' => $percentage,
        ];
    }

    /**
     * Get module progress data
     *
     * @deprecated Use ModuleItemProgressService::getModuleProgress() instead
     */
    public function getModuleProgress(int $userId, int $moduleId): array
    {
        $totalLessons = DB::table('module_lessons')
            ->where('module_id', $moduleId)
            ->where('status', 'published')
            ->whereNull('deleted_at')
            ->count();

        $completedLessons = LessonProgress::whereIn('lesson_id', function ($query) use ($moduleId) {
            $query->select('id')
                ->from('module_lessons')
                ->where('module_id', $moduleId);
        })
            ->where('user_id', $userId)
            ->count();

        $percentage = $totalLessons > 0
            ? round(($completedLessons / $totalLessons) * 100)
            : 0;

        return [
            'total_lessons' => $totalLessons,
            'completed_lessons' => $completedLessons,
            'percentage' => $percentage,
        ];
    }

    /**
     * Check if lesson is completed
     *
     * @deprecated Use ModuleItemProgressService::isItemCompleted() instead
     */
    public function isLessonCompleted(int $userId, int $lessonId): bool
    {
        return LessonProgress::where('user_id', $userId)
            ->where('lesson_id', $lessonId)
            ->exists();
    }

    /**
     * Clear progress cache
     */
    protected function clearProgressCache(int $userId, int $courseId): void
    {
        CacheKey::invalidateUserProgress($userId, $courseId);
    }

    /**
     * Update last accessed timestamp for a lesson.
     * Used for "Continue Learning" feature to track where student left off.
     *
     * @deprecated Use ModuleItemProgressService::markAccessed() instead
     */
    public function updateLastAccessed(int $userId, int $lessonId, int $courseId): void
    {
        LessonProgress::updateOrCreate(
            [
                'user_id' => $userId,
                'lesson_id' => $lessonId,
            ],
            [
                'course_id' => $courseId,
                'last_accessed_at' => now(),
            ]
        );
    }

    /**
     * Get the last accessed lesson for a student.
     * Returns the lesson the student most recently viewed.
     *
     * @deprecated Use ModuleItemProgressService::getLastAccessedItem() instead
     */
    public function getLastAccessedLesson(int $userId): ?ModuleLesson
    {
        $progress = LessonProgress::where('user_id', $userId)
            ->whereNotNull('last_accessed_at')
            ->orderBy('last_accessed_at', 'desc')
            ->first();

        if (! $progress) {
            return null;
        }

        return ModuleLesson::with(['module.course'])
            ->where('id', $progress->lesson_id)
            ->where('status', 'published')
            ->first();
    }

    /**
     * Get the next incomplete lesson in a course for a student.
     * Useful for "Continue Learning" when no last_accessed record exists.
     *
     * @deprecated Will be replaced with ModuleItemProgressService functionality
     */
    public function getNextIncompleteLesson(int $userId, int $courseId): ?ModuleLesson
    {
        // Get all completed lesson IDs for this student in this course
        $completedLessonIds = LessonProgress::where('user_id', $userId)
            ->where('course_id', $courseId)
            ->whereNotNull('completed_at')
            ->pluck('lesson_id');

        // Find first published lesson not in completed list
        return ModuleLesson::join('course_modules', 'module_lessons.module_id', '=', 'course_modules.id')
            ->where('course_modules.course_id', $courseId)
            ->where('course_modules.status', 'published')
            ->where('module_lessons.status', 'published')
            ->whereNotIn('module_lessons.id', $completedLessonIds)
            ->orderBy('course_modules.order_index')
            ->orderBy('module_lessons.order_number')
            ->select('module_lessons.*')
            ->first();
    }

    /**
     * Get the first lesson in a course.
     * Fallback for new students who haven't accessed any lessons yet.
     *
     * @deprecated Will be replaced with ModuleItemProgressService functionality
     */
    public function getFirstLesson(int $courseId): ?ModuleLesson
    {
        return ModuleLesson::join('course_modules', 'module_lessons.module_id', '=', 'course_modules.id')
            ->where('course_modules.course_id', $courseId)
            ->where('course_modules.status', 'published')
            ->where('module_lessons.status', 'published')
            ->orderBy('course_modules.order_index')
            ->orderBy('module_lessons.order_number')
            ->select('module_lessons.*')
            ->first();
    }
}
