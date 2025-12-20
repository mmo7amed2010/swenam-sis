<?php

namespace App\Services;

use App\Models\Assignment;
use App\Models\Grade;
use App\Models\ModuleItem;
use App\Models\ModuleItemProgress;
use App\Models\ModuleLesson;
use App\Models\Quiz;
use App\Support\CacheKey;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

/**
 * Unified service for calculating and managing module item progress.
 * Uses weight-based calculation: Quiz/Assignment use total_points, Lessons default to 1.
 */
class ModuleItemProgressService
{
    /**
     * Cache TTL in seconds (15 minutes).
     */
    private const CACHE_TTL = 900;

    /**
     * Apply polymorphic published filter to a ModuleItem query.
     *
     * Different content types use different fields for published status:
     * - ModuleLesson: status = 'published'
     * - Quiz: published = true
     * - Assignment: is_published = true
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query  The ModuleItem query builder
     * @return \Illuminate\Database\Eloquent\Builder The query with published filter applied
     */
    private function scopePublishedItemable($query)
    {
        return $query->where(function ($q) {
            // Lessons: check status field using explicit whereExists
            $q->where(function ($sub) {
                $sub->where('itemable_type', ModuleLesson::class)
                    ->whereExists(function ($exists) {
                        $exists->selectRaw(1)
                            ->from('module_lessons')
                            ->whereColumn('module_lessons.id', 'module_items.itemable_id')
                            ->where('module_lessons.status', 'published');
                    });
            })
            // Quizzes: check published boolean
                ->orWhere(function ($sub) {
                    $sub->where('itemable_type', Quiz::class)
                        ->whereExists(function ($exists) {
                            $exists->selectRaw(1)
                                ->from('quizzes')
                                ->whereColumn('quizzes.id', 'module_items.itemable_id')
                                ->where('quizzes.published', true);
                        });
                })
            // Assignments: check is_published boolean
                ->orWhere(function ($sub) {
                    $sub->where('itemable_type', Assignment::class)
                        ->whereExists(function ($exists) {
                            $exists->selectRaw(1)
                                ->from('assignments')
                                ->whereColumn('assignments.id', 'module_items.itemable_id')
                                ->where('assignments.is_published', true)
                                ->whereNull('assignments.deleted_at');
                        });
                });
        });
    }

    /**
     * Calculate progress for a course using ModuleItems (weight-based).
     * Progress = Sum(completed_item_weights) / Sum(total_item_weights) * 100
     *
     * @param  int  $userId  User ID
     * @param  int  $courseId  Course ID
     * @return array Progress data including percentage, counts, and weights
     */
    public function getCourseProgress(int $userId, int $courseId): array
    {

            try {
                // Get all published module items with their weights
                $query = ModuleItem::with('itemable')
                    ->whereHas('module', fn ($q) => $q->where('course_id', $courseId)->where('status', 'published'));

                // Apply polymorphic published filter
                $moduleItems = $this->scopePublishedItemable($query)->get();

                // Get completed item IDs for this student
                $completedItemIds = ModuleItemProgress::where('user_id', $userId)
                    ->where('course_id', $courseId)
                    ->whereNotNull('completed_at')
                    ->pluck('module_item_id')
                    ->toArray();

                $totalWeight = 0;
                $completedWeight = 0;
                $totalItems = $moduleItems->count();
                $completedItems = 0;

                foreach ($moduleItems as $item) {
                    // Get weight: Quiz/Assignment use total_points, Lesson defaults to 1
                    $weight = $this->getItemWeight($item);
                    $totalWeight += $weight;

                    if (in_array($item->id, $completedItemIds)) {
                        $completedWeight += $weight;
                        $completedItems++;
                    }
                }

                $percentage = $totalWeight > 0
                    ? round(($completedWeight / $totalWeight) * 100)
                    : 0;

                return [
                    'total_items' => $totalItems,
                    'completed_items' => $completedItems,
                    'total_weight' => $totalWeight,
                    'completed_weight' => $completedWeight,
                    'percentage' => $percentage,
                ];
            } catch (\Exception $e) {
                Log::error('Failed to calculate course progress', [
                    'user_id' => $userId,
                    'course_id' => $courseId,
                    'error' => $e->getMessage(),
                ]);

                return [
                    'total_items' => 0,
                    'completed_items' => 0,
                    'total_weight' => 0,
                    'completed_weight' => 0,
                    'percentage' => 0,
            ];
        }
    }

    /**
     * Get the weight for a module item.
     * Quiz/Assignment: use total_points (default 1 if not set)
     * Lesson: default to 1
     *
     * @param  ModuleItem  $item  The module item
     * @return int The weight value
     */
    public function getItemWeight(ModuleItem $item): int
    {
        if (in_array($item->itemable_type, ['App\\Models\\Quiz', 'App\\Models\\Assignment'])) {
            return $item->itemable?->total_points ?? 1;
        }

        return 1; // Lessons default to 1 point
    }

    /**
     * Mark a module item as complete (explicit action).
     *
     * @param  int  $userId  User ID
     * @param  int  $moduleItemId  Module item ID
     * @param  int  $courseId  Course ID
     */
    public function markComplete(int $userId, int $moduleItemId, int $courseId): void
    {
        try {
            ModuleItemProgress::updateOrCreate(
                ['user_id' => $userId, 'module_item_id' => $moduleItemId],
                ['course_id' => $courseId, 'completed_at' => now()]
            );

            // Clear cache
            $this->clearProgressCache($userId, $courseId);
        } catch (\Exception $e) {
            Log::error('Failed to mark item complete', [
                'user_id' => $userId,
                'module_item_id' => $moduleItemId,
                'course_id' => $courseId,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Mark a module item as incomplete (remove completion).
     * Used when a student re-attempts and fails, or when grade changes to failing.
     *
     * @param  int  $userId  User ID
     * @param  int  $moduleItemId  Module item ID
     * @param  int  $courseId  Course ID
     */
    public function markIncomplete(int $userId, int $moduleItemId, int $courseId): void
    {
        try {
            ModuleItemProgress::where('user_id', $userId)
                ->where('module_item_id', $moduleItemId)
                ->update(['completed_at' => null]);

            // Clear cache
            $this->clearProgressCache($userId, $courseId);
        } catch (\Exception $e) {
            Log::error('Failed to mark item incomplete', [
                'user_id' => $userId,
                'module_item_id' => $moduleItemId,
                'course_id' => $courseId,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Mark a module item as accessed (for tracking last viewed).
     *
     * @param  int  $userId  User ID
     * @param  int  $moduleItemId  Module item ID
     * @param  int  $courseId  Course ID
     */
    public function markAccessed(int $userId, int $moduleItemId, int $courseId): void
    {
        try {
            ModuleItemProgress::updateOrCreate(
                ['user_id' => $userId, 'module_item_id' => $moduleItemId],
                ['course_id' => $courseId, 'last_accessed_at' => now()]
            );
        } catch (\Exception $e) {
            Log::error('Failed to mark item accessed', [
                'user_id' => $userId,
                'module_item_id' => $moduleItemId,
                'course_id' => $courseId,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Auto-complete a quiz module item when quiz is submitted with passing score.
     * Only marks complete if the student's percentage meets the passing threshold.
     *
     * @param  int  $userId  User ID
     * @param  Quiz  $quiz  The quiz that was submitted
     * @param  float  $percentage  The student's score percentage (0-100)
     */
    public function autoCompleteForQuiz(int $userId, Quiz $quiz, float $percentage): void
    {
        // Only mark complete if student passed
        if ($percentage < $quiz->getPassingPercentage()) {
            // Student failed - ensure item is marked incomplete
            $moduleItem = ModuleItem::where('itemable_type', Quiz::class)
                ->where('itemable_id', $quiz->id)
                ->first();

            if ($moduleItem) {
                $this->markIncomplete($userId, $moduleItem->id, $quiz->course_id);
            }

            return;
        }

        $moduleItem = ModuleItem::where('itemable_type', Quiz::class)
            ->where('itemable_id', $quiz->id)
            ->first();

        if ($moduleItem) {
            $this->markComplete($userId, $moduleItem->id, $quiz->course_id);
        }
    }

    /**
     * Auto-complete an assignment module item when assignment is submitted.
     *
     * NOTE: This method is now a no-op. Assignment completion is determined
     * when a passing grade is published, not on submission.
     * See updateProgressForGradedAssignment() instead.
     *
     * Kept for backward compatibility with existing code that calls this method.
     *
     * @param  int  $userId  User ID
     * @param  Assignment  $assignment  The assignment that was submitted
     *
     * @deprecated Use updateProgressForGradedAssignment() when grade is published
     */
    public function autoCompleteForAssignment(int $userId, Assignment $assignment): void
    {
        // No-op: completion now happens when passing grade is published
        // This method is kept for backward compatibility but does nothing
    }

    /**
     * Update assignment progress based on a published grade.
     * Marks the assignment module item as complete if the student passed,
     * or incomplete if the student failed.
     *
     * This should be called when a grade is published or re-graded.
     *
     * @param  int  $userId  User ID (student)
     * @param  Assignment  $assignment  The assignment that was graded
     * @param  Grade  $grade  The published grade
     */
    public function updateProgressForGradedAssignment(int $userId, Assignment $assignment, Grade $grade): void
    {
        $moduleItem = ModuleItem::where('itemable_type', Assignment::class)
            ->where('itemable_id', $assignment->id)
            ->first();

        if (! $moduleItem) {
            return;
        }

        $passed = $assignment->isPassingScore(
            (float) $grade->points_awarded,
            (float) $grade->max_points
        );

        if ($passed) {
            $this->markComplete($userId, $moduleItem->id, $assignment->course_id);
        } else {
            $this->markIncomplete($userId, $moduleItem->id, $assignment->course_id);
        }
    }

    /**
     * Mark a lesson as accessed (for tracking "Continue Learning" feature).
     *
     * @param  int  $userId  User ID
     * @param  ModuleLesson  $lesson  The lesson that was accessed
     */
    public function markAccessedForLesson(int $userId, ModuleLesson $lesson): void
    {
        $moduleItem = ModuleItem::where('itemable_type', ModuleLesson::class)
            ->where('itemable_id', $lesson->id)
            ->first();

        if ($moduleItem) {
            $courseId = $lesson->module?->course_id;
            if ($courseId) {
                $this->markAccessed($userId, $moduleItem->id, $courseId);
            }
        }
    }

    /**
     * Check if a specific module item is completed by a student.
     *
     * @param  int  $userId  User ID
     * @param  int  $moduleItemId  Module item ID
     * @return bool Whether the item is completed
     */
    public function isItemCompleted(int $userId, int $moduleItemId): bool
    {
        return ModuleItemProgress::where('user_id', $userId)
            ->where('module_item_id', $moduleItemId)
            ->whereNotNull('completed_at')
            ->exists();
    }

    /**
     * Get all completed module item IDs for a student in a course.
     *
     * @param  int  $userId  User ID
     * @param  int  $courseId  Course ID
     * @return array Array of completed module item IDs
     */
    public function getCompletedItemIds(int $userId, int $courseId): array
    {
        return ModuleItemProgress::where('user_id', $userId)
            ->where('course_id', $courseId)
            ->whereNotNull('completed_at')
            ->pluck('module_item_id')
            ->toArray();
    }

    /**
     * Calculate progress for a specific module (weight-based).
     * Progress = Sum(completed_item_weights) / Sum(total_item_weights) * 100
     *
     * @param  int  $userId  User ID
     * @param  int  $moduleId  Module ID
     * @return array Progress data including percentage, counts, and weights
     */
    public function getModuleProgress(int $userId, int $moduleId): array
    {
        try {
            // Get all published module items in this module
            $query = ModuleItem::with('itemable')
                ->where('module_id', $moduleId);

            // Apply polymorphic published filter
            $moduleItems = $this->scopePublishedItemable($query)->get();

            // Get completed item IDs for this student in this module
            $completedItemIds = ModuleItemProgress::where('user_id', $userId)
                ->whereIn('module_item_id', $moduleItems->pluck('id'))
                ->whereNotNull('completed_at')
                ->pluck('module_item_id')
                ->toArray();

            $totalWeight = 0;
            $completedWeight = 0;
            $totalItems = $moduleItems->count();
            $completedItems = 0;

            foreach ($moduleItems as $item) {
                $weight = $this->getItemWeight($item);
                $totalWeight += $weight;

                if (in_array($item->id, $completedItemIds)) {
                    $completedWeight += $weight;
                    $completedItems++;
                }
            }

            $percentage = $totalWeight > 0
                ? round(($completedWeight / $totalWeight) * 100)
                : 0;

            return [
                'total_items' => $totalItems,
                'completed_items' => $completedItems,
                'total_weight' => $totalWeight,
                'completed_weight' => $completedWeight,
                'percentage' => $percentage,
            ];
        } catch (\Exception $e) {
            Log::error('Failed to calculate module progress', [
                'user_id' => $userId,
                'module_id' => $moduleId,
                'error' => $e->getMessage(),
            ]);

            return [
                'total_items' => 0,
                'completed_items' => 0,
                'total_weight' => 0,
                'completed_weight' => 0,
                'percentage' => 0,
            ];
        }
    }

    /**
     * Clear progress cache for a student and course.
     *
     * @param  int  $userId  User ID
     * @param  int  $courseId  Course ID
     */
    public function clearProgressCache(int $userId, int $courseId): void
    {
        CacheKey::invalidateUserProgress($userId, $courseId);
    }

    /**
     * Clear all progress cache for a user.
     *
     * Note: This method is limited because we don't track all course IDs a user has progress in.
     * Consider using clearProgressCache($userId, $courseId) for specific courses, or let cache expire naturally.
     *
     * @param  int  $userId  User ID
     */
    public function clearAllProgressCacheForStudent(int $userId): void
    {
        // Clear dashboard cache which includes progress data
        CacheKey::invalidateUserDashboard($userId);
    }

    /**
     * Get progress for multiple modules in a single query.
     * Used to avoid N+1 queries when displaying course modules.
     *
     * @param  int  $userId  User/User ID
     * @param  \Illuminate\Support\Collection|array  $moduleIds  Collection or array of module IDs
     * @return array<int, array{percentage: float, completed: int, total: int}> Progress data keyed by module ID
     */
    public function getBatchModuleProgress(int $userId, $moduleIds): array
    {
        if ($moduleIds instanceof \Illuminate\Support\Collection) {
            $moduleIds = $moduleIds->toArray();
        }

        if (empty($moduleIds)) {
            return [];
        }

        try {
            // Get all published module items for these modules
            $query = ModuleItem::with('itemable')
                ->whereIn('module_id', $moduleIds);

            // Apply polymorphic published filter and group by module
            $moduleItems = $this->scopePublishedItemable($query)
                ->get()
                ->groupBy('module_id');

            // Get all completed items for this user
            $completedItems = ModuleItemProgress::where('user_id', $userId)
                ->whereIn('module_item_id', $moduleItems->flatten()->pluck('id'))
                ->whereNotNull('completed_at')
                ->pluck('module_item_id')
                ->toArray();

            $result = [];
            foreach ($moduleIds as $moduleId) {
                $items = $moduleItems->get($moduleId, collect());

                // Calculate using weights (consistent with getCourseProgress and getModuleProgress)
                $totalWeight = 0;
                $completedWeight = 0;
                $totalCount = $items->count();
                $completedCount = 0;

                foreach ($items as $item) {
                    $weight = $this->getItemWeight($item);
                    $totalWeight += $weight;

                    if (in_array($item->id, $completedItems)) {
                        $completedWeight += $weight;
                        $completedCount++;
                    }
                }

                $result[$moduleId] = [
                    'percentage' => $totalWeight > 0 ? round(($completedWeight / $totalWeight) * 100, 1) : 0,
                    'completed' => $completedCount,
                    'total' => $totalCount,
                ];
            }

            return $result;
        } catch (\Exception $e) {
            Log::error('Failed to get batch module progress', [
                'user_id' => $userId,
                'module_ids' => $moduleIds,
                'error' => $e->getMessage(),
            ]);

            // Return empty progress for all modules on error
            $result = [];
            foreach ($moduleIds as $moduleId) {
                $result[$moduleId] = [
                    'percentage' => 0,
                    'completed' => 0,
                    'total' => 0,
                ];
            }

            return $result;
        }
    }

    /**
     * Get the last accessed module item for "Continue Learning" feature.
     *
     * @param  int  $userId  User/User ID
     * @return ModuleItem|null The last accessed module item with loaded relationships
     */
    public function getLastAccessedItem(int $userId): ?ModuleItem
    {
        try {
            $progress = ModuleItemProgress::where('user_id', $userId)
                ->whereNotNull('last_accessed_at')
                ->orderBy('last_accessed_at', 'desc')
                ->first();

            if (! $progress) {
                return null;
            }

            return ModuleItem::with(['itemable', 'module.course'])
                ->find($progress->module_item_id);
        } catch (\Exception $e) {
            Log::error('Failed to get last accessed item', [
                'user_id' => $userId,
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }

    /**
     * Get batch item completion status for a module.
     * Returns array keyed by item_id with boolean completed status.
     *
     * @param  int  $userId  User/User ID
     * @param  int  $moduleId  Module ID
     * @return array<int, bool> Completion status keyed by module item ID
     */
    public function getBatchItemCompletion(int $userId, int $moduleId): array
    {
        try {
            $moduleItemIds = ModuleItem::where('module_id', $moduleId)->pluck('id');

            $completed = ModuleItemProgress::where('user_id', $userId)
                ->whereIn('module_item_id', $moduleItemIds)
                ->whereNotNull('completed_at')
                ->pluck('module_item_id')
                ->toArray();

            $result = [];
            foreach ($moduleItemIds as $itemId) {
                $result[$itemId] = in_array($itemId, $completed);
            }

            return $result;
        } catch (\Exception $e) {
            Log::error('Failed to get batch item completion', [
                'user_id' => $userId,
                'module_id' => $moduleId,
                'error' => $e->getMessage(),
            ]);

            return [];
        }
    }

    /**
     * Get the first module item in a course.
     * Fallback for new students who haven't accessed any items yet.
     *
     * @param  int  $courseId  Course ID
     * @return ModuleItem|null The first module item with loaded relationships
     */
    public function getFirstItem(int $courseId): ?ModuleItem
    {
        try {
            $query = ModuleItem::with(['itemable', 'module'])
                ->whereHas('module', fn ($q) => $q->where('course_id', $courseId)->where('status', 'published'));

            // Apply polymorphic published filter
            return $this->scopePublishedItemable($query)
                ->orderBy('order_position')
                ->first();
        } catch (\Exception $e) {
            Log::error('Failed to get first item', [
                'course_id' => $courseId,
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }

    /**
     * Get the next incomplete module item in a course for a student.
     * Useful for "Continue Learning" when no last_accessed record exists.
     *
     * @param  int  $userId  User/User ID
     * @param  int  $courseId  Course ID
     * @return ModuleItem|null The next incomplete module item
     */
    public function getNextIncompleteItem(int $userId, int $courseId): ?ModuleItem
    {
        try {
            // Get all completed item IDs for this student in this course
            $completedItemIds = $this->getCompletedItemIds($userId, $courseId);

            // Find first published item not in completed list
            $query = ModuleItem::with(['itemable', 'module'])
                ->whereHas('module', fn ($q) => $q->where('course_id', $courseId)->where('status', 'published'))
                ->whereNotIn('id', $completedItemIds);

            // Apply polymorphic published filter
            return $this->scopePublishedItemable($query)
                ->orderBy('order_position')
                ->first();
        } catch (\Exception $e) {
            Log::error('Failed to get next incomplete item', [
                'user_id' => $userId,
                'course_id' => $courseId,
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }
}
