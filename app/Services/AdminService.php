<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

/**
 * Admin Service
 *
 * Handles all business logic for admin users including CRUD, validation,
 * and statistics with caching.
 */
class AdminService
{
    /**
     * Create a new admin user.
     *
     * @param  array  $data  Admin data
     * @return User Newly created admin user
     *
     * @throws \Exception
     */
    public function createAdmin(array $data): User
    {
        DB::beginTransaction();
        try {
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'user_type' => 'admin',
            ]);

            // Handle avatar upload
            if (isset($data['avatar']) && $data['avatar']) {
                $avatarPath = $data['avatar']->store('avatars', 'public');
                $user->update(['profile_photo_path' => $avatarPath]);
            }

            DB::commit();

            // Clear cached counts
            $this->clearCountCache();

            Log::info('Admin created', [
                'admin_id' => $user->id,
                'created_by' => auth()->id(),
                'email' => $user->email,
            ]);

            return $user;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Update an existing admin user.
     *
     * @param  User  $user  Admin to update
     * @param  array  $data  Update data
     * @return User Updated admin
     */
    public function updateAdmin(User $user, array $data): User
    {
        $updateData = [
            'name' => $data['name'],
            'email' => $data['email'],
        ];

        // Update password only if provided
        if (! empty($data['password'])) {
            $updateData['password'] = Hash::make($data['password']);
        }

        // Handle avatar upload
        if (isset($data['avatar']) && $data['avatar']) {
            // Delete old avatar if exists
            if ($user->profile_photo_path) {
                Storage::disk('public')->delete($user->profile_photo_path);
            }
            $avatarPath = $data['avatar']->store('avatars', 'public');
            $updateData['profile_photo_path'] = $avatarPath;
        }

        // Handle avatar removal
        if (! empty($data['avatar_remove']) && $user->profile_photo_path) {
            Storage::disk('public')->delete($user->profile_photo_path);
            $updateData['profile_photo_path'] = null;
        }

        $user->update($updateData);

        Log::info('Admin updated', [
            'admin_id' => $user->id,
            'updated_by' => auth()->id(),
            'changes' => $user->getChanges(),
        ]);

        return $user->fresh();
    }

    /**
     * Delete an admin user.
     *
     * @param  User  $user  Admin to delete
     * @return bool Success status
     */
    public function deleteAdmin(User $user): bool
    {
        // Clear cached counts
        $this->clearCountCache();

        // Delete avatar if exists
        if ($user->profile_photo_path) {
            Storage::disk('public')->delete($user->profile_photo_path);
        }

        Log::warning('Admin deleted', [
            'admin_id' => $user->id,
            'deleted_by' => auth()->id(),
            'email' => $user->email,
        ]);

        return $user->delete();
    }

    /**
     * Check if an admin can be deleted.
     * Admins cannot delete themselves.
     *
     * @param  User  $user  Admin to check
     * @return bool True if can be deleted
     */
    public function canDelete(User $user): bool
    {
        return $user->id !== auth()->id();
    }

    /**
     * Get total admin count (cached).
     *
     * @param  int  $cacheDuration  Cache duration in seconds (default: 5 minutes)
     */
    public function getTotalCount(int $cacheDuration = 300): int
    {
        return User::where('user_type', 'admin')->count();
    }

    /**
     * Get active admins count (logged in within last 30 days).
     *
     * @param  int  $cacheDuration  Cache duration in seconds (default: 5 minutes)
     */
    public function getActiveCount(int $cacheDuration = 300): int
    {
            return User::where('user_type', 'admin')
            ->where('last_login_at', '>=', now()->subDays(30))
            ->count();
    }

    /**
     * Get new admins this month count (cached).
     *
     * @param  int  $cacheDuration  Cache duration in seconds (default: 5 minutes)
     */
    public function getNewThisMonthCount(int $cacheDuration = 300): int
    {
            return User::where('user_type', 'admin')
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count();
    }

    /**
     * Clear cached admin counts.
     */
    public function clearCountCache(): void
    {
        Cache::forget('admins.total.count');
        Cache::forget('admins.active.count');
        Cache::forget('admins.new_this_month.count');
    }

    /**
     * Get admin statistics.
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
     * Get admin counts for AJAX response (no cache).
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
