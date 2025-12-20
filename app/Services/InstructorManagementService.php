<?php

namespace App\Services;

use App\Models\Instructor;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

/**
 * Instructor Management Service
 *
 * Handles all business logic for instructors including CRUD, validation,
 * and statistics with caching.
 */
class InstructorManagementService
{
    /**
     * Create a new instructor.
     *
     * @param  array  $data  Instructor data
     * @return Instructor Newly created instructor
     */
    public function createInstructor(array $data): Instructor
    {
        // Hash password
        $data['password'] = Hash::make($data['password']);

        // Remove password_confirmation from data
        unset($data['password_confirmation']);

        // Set user_type to instructor
        $data['user_type'] = 'instructor';

        // Set name field from first_name and last_name
        $data['name'] = trim($data['first_name'].' '.$data['last_name']);

        // Email verification can be done later
        $data['email_verified_at'] = now();

        // Handle avatar upload
        if (isset($data['avatar']) && $data['avatar']) {
            $avatarPath = $data['avatar']->store('avatars', 'public');
            $data['profile_photo_path'] = $avatarPath;
            unset($data['avatar']);
        }

        $instructor = Instructor::create($data);

        // Clear cached counts
        $this->clearCountCache();

        Log::info('Instructor created', [
            'instructor_id' => $instructor->id,
            'created_by' => auth()->id(),
            'email' => $instructor->email,
        ]);

        return $instructor;
    }

    /**
     * Update an existing instructor.
     *
     * @param  Instructor  $instructor  Instructor to update
     * @param  array  $data  Update data
     * @return Instructor Updated instructor
     */
    public function updateInstructor(Instructor $instructor, array $data): Instructor
    {
        // Update password only if provided
        if (! empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        // Remove password_confirmation from data
        unset($data['password_confirmation']);

        // Ensure user_type remains 'instructor'
        $data['user_type'] = 'instructor';

        // Update name field from first_name and last_name
        $data['name'] = trim($data['first_name'].' '.$data['last_name']);

        // Handle avatar upload
        if (isset($data['avatar']) && $data['avatar']) {
            // Delete old avatar if exists
            if ($instructor->profile_photo_path) {
                Storage::disk('public')->delete($instructor->profile_photo_path);
            }
            $avatarPath = $data['avatar']->store('avatars', 'public');
            $data['profile_photo_path'] = $avatarPath;
            unset($data['avatar']);
        }

        // Handle avatar removal
        if (! empty($data['avatar_remove']) && $instructor->profile_photo_path) {
            Storage::disk('public')->delete($instructor->profile_photo_path);
            $data['profile_photo_path'] = null;
        }
        unset($data['avatar_remove']);

        $instructor->update($data);

        Log::info('Instructor updated', [
            'instructor_id' => $instructor->id,
            'updated_by' => auth()->id(),
            'changes' => $instructor->getChanges(),
        ]);

        return $instructor->fresh();
    }

    /**
     * Delete an instructor.
     *
     * @param  Instructor  $instructor  Instructor to delete
     * @return bool Success status
     */
    public function deleteInstructor(Instructor $instructor): bool
    {
        // Clear cached counts
        $this->clearCountCache();

        // Delete avatar if exists
        if ($instructor->profile_photo_path) {
            Storage::disk('public')->delete($instructor->profile_photo_path);
        }

        Log::warning('Instructor deleted', [
            'instructor_id' => $instructor->id,
            'deleted_by' => auth()->id(),
            'email' => $instructor->email,
        ]);

        return $instructor->delete();
    }

    /**
     * Check if an instructor can be deleted.
     * Instructors with active course assignments cannot be deleted.
     *
     * @param  Instructor  $instructor  Instructor to check
     * @return bool True if can be deleted
     */
    public function canDelete(Instructor $instructor): bool
    {
        return ! $instructor->courseInstructors()->whereNull('removed_at')->exists();
    }

    /**
     * Get total instructors count (cached).
     *
     * @param  int  $cacheDuration  Cache duration in seconds (default: 5 minutes)
     */
    public function getTotalCount(int $cacheDuration = 300): int
    {
        return Instructor::count();
    }

    /**
     * Get active instructors count (with course assignments).
     *
     * @param  int  $cacheDuration  Cache duration in seconds (default: 5 minutes)
     */
    public function getActiveCount(int $cacheDuration = 300): int
    {
        return Instructor::whereHas('courseInstructors', function ($q) {
            $q->whereNull('removed_at');
        })->count();
    }

    /**
     * Get new instructors this month count (cached).
     *
     * @param  int  $cacheDuration  Cache duration in seconds (default: 5 minutes)
     */
    public function getNewThisMonthCount(int $cacheDuration = 300): int
    {
        return Instructor::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();
    }

    /**
     * Clear cached instructor counts.
     */
    public function clearCountCache(): void
    {
        Cache::forget('instructors.total.count');
        Cache::forget('instructors.active.count');
        Cache::forget('instructors.new_this_month.count');
    }

    /**
     * Get instructor statistics.
     */
    public function getStatistics(): array
    {
        return [
            'total' => $this->getTotalCount(),
            'active' => $this->getActiveCount(),
            'new_this_month' => $this->getNewThisMonthCount(),
        ];
    }

    /**
     * Get instructor counts for AJAX response (no cache).
     */
    public function getCounts(): array
    {
        return [
            'total' => $this->getTotalCount(0),
            'active' => $this->getActiveCount(0),
            'new_this_month' => $this->getNewThisMonthCount(0),
        ];
    }
}
