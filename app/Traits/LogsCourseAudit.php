<?php

namespace App\Traits;

use App\Models\Course;
use App\Models\CourseAuditLog;
use Illuminate\Support\Facades\Log;

/**
 * Logs Course Audit Trait
 *
 * Provides centralized course audit logging functionality.
 * Safely handles audit logging in both HTTP and queue contexts.
 *
 * Features:
 * - Safe user detection (works in queue context)
 * - Automatic IP and user agent capture
 * - Error handling with fallback logging
 * - Consistent audit trail across services
 */
trait LogsCourseAudit
{
    /**
     * Log a course audit event safely
     *
     * Works in both HTTP and queue context by safely detecting the authenticated user.
     * If audit logging fails, the error is logged but doesn't interrupt the operation.
     *
     * @param  Course  $course  The course being audited
     * @param  string  $action  Action performed (e.g., 'created', 'updated', 'published')
     * @param  array|null  $oldValues  Previous values before change
     * @param  array|null  $newValues  New values after change
     * @param  string|null  $description  Human-readable description of the action
     *
     * @example
     * $this->logCourseEvent($course, 'published', null, null, 'Course published for enrollment');
     * $this->logCourseEvent($course, 'updated', $oldValues, $newValues);
     */
    protected function logCourseEvent(
        Course $course,
        string $action,
        ?array $oldValues = null,
        ?array $newValues = null,
        ?string $description = null
    ): void {
        try {
            $user = $this->getAuthUserSafely();

            CourseAuditLog::create([
                'course_id' => $course->id,
                'user_id' => $user?->id,
                'action' => $action,
                'old_values' => $oldValues,
                'new_values' => $newValues,
                'description' => $description ?? $this->buildDescription($action, $user),
                'ip_address' => $this->getRequestIpSafely(),
                'user_agent' => $this->getRequestUserAgentSafely(),
            ]);
        } catch (\Exception $e) {
            // Log the error but don't interrupt the operation
            Log::error('Failed to log course audit event', [
                'course_id' => $course->id,
                'action' => $action,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        }
    }

    /**
     * Get authenticated user safely (works in queue context where auth() may not exist)
     *
     * @return \App\Models\User|null
     */
    private function getAuthUserSafely()
    {
        try {
            return auth()->check() ? auth()->user() : null;
        } catch (\Exception $e) {
            // Auth facade may not be available in queue context
            return null;
        }
    }

    /**
     * Get request IP address safely (works when no HTTP request exists)
     */
    private function getRequestIpSafely(): ?string
    {
        try {
            return request()?->ip();
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Get request user agent safely (works when no HTTP request exists)
     */
    private function getRequestUserAgentSafely(): ?string
    {
        try {
            return request()?->userAgent();
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Build description from action and user
     *
     * Creates a default human-readable description if none is provided.
     *
     * @param  string  $action  The action performed
     * @param  \App\Models\User|null  $user  The user who performed the action
     */
    private function buildDescription(string $action, $user): string
    {
        $userName = $user?->name ?? 'System';
        $actionLabel = ucfirst($action);

        return "{$actionLabel} by {$userName}";
    }
}
