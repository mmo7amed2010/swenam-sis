<?php

namespace App\Support;

/**
 * Centralized cache key generation for consistent naming across the application.
 *
 * All cache keys MUST include user context to prevent data leakage between users.
 * Format: {resource}_{userId}_{resourceId}
 */
class CacheKey
{
    /**
     * Generate cache key for course progress.
     *
     * @param  int  $userId  Student ID
     * @param  int  $courseId  Course ID
     * @return string Cache key
     */
    public static function courseProgress(int $userId, int $courseId): string
    {
        return "course_progress_{$userId}_{$courseId}";
    }

    /**
     * Generate cache key for module progress.
     *
     * @param  int  $userId  Student ID
     * @param  int  $moduleId  Module ID
     * @return string Cache key
     */
    public static function moduleProgress(int $userId, int $moduleId): string
    {
        return "module_progress_{$userId}_{$moduleId}";
    }

    /**
     * Generate cache key for program overview.
     *
     * @param  int  $userId  Student user ID
     * @param  int  $programId  Program ID
     * @return string Cache key
     */
    public static function programOverview(int $userId, int $programId): string
    {
        return "program_overview_{$userId}_{$programId}";
    }

    /**
     * Generate cache key for program courses.
     *
     * @param  int  $userId  Student user ID
     * @param  int  $programId  Program ID
     * @return string Cache key
     */
    public static function programCourses(int $userId, int $programId): string
    {
        return "program_courses_{$userId}_{$programId}";
    }

    /**
     * Generate cache key for student dashboard data.
     *
     * @param  int  $userId  Student user ID
     * @param  string  $dataType  Type of dashboard data (deadlines, courses, activity, stats, today)
     * @param  int|null  $programId  Optional program ID
     * @return string Cache key
     */
    public static function studentDashboard(int $userId, string $dataType, ?int $programId = null): string
    {
        $key = "student_dashboard_{$dataType}_{$userId}";

        if ($programId !== null) {
            $key .= "_{$programId}";
        }

        return $key;
    }

    /**
     * Generate cache key for course grades.
     *
     * @param  int  $userId  Student user ID
     * @param  int  $courseId  Course ID
     * @return string Cache key
     */
    public static function courseGrades(int $userId, int $courseId): string
    {
        return "course_grades_{$userId}_{$courseId}";
    }

    /**
     * Invalidate all progress-related cache for a user.
     *
     * @param  int  $userId  User ID
     * @param  int  $courseId  Course ID
     */
    public static function invalidateUserProgress(int $userId, int $courseId): void
    {
        \Illuminate\Support\Facades\Cache::forget(self::courseProgress($userId, $courseId));
    }

    /**
     * Invalidate all program-related cache for a user.
     *
     * @param  int  $userId  User ID
     * @param  int  $programId  Program ID
     */
    public static function invalidateUserProgram(int $userId, int $programId): void
    {
        \Illuminate\Support\Facades\Cache::forget(self::programOverview($userId, $programId));
        \Illuminate\Support\Facades\Cache::forget(self::programCourses($userId, $programId));
    }

    /**
     * Invalidate all dashboard cache for a user.
     *
     * @param  int  $userId  User ID
     * @param  int|null  $programId  Optional program ID
     */
    public static function invalidateUserDashboard(int $userId, ?int $programId = null): void
    {
        \Illuminate\Support\Facades\Cache::forget(self::studentDashboard($userId, 'deadlines'));
        \Illuminate\Support\Facades\Cache::forget(self::studentDashboard($userId, 'courses', $programId));
        \Illuminate\Support\Facades\Cache::forget(self::studentDashboard($userId, 'activity'));
        \Illuminate\Support\Facades\Cache::forget(self::studentDashboard($userId, 'stats', $programId));
        \Illuminate\Support\Facades\Cache::forget(self::studentDashboard($userId, 'today'));
    }
}
