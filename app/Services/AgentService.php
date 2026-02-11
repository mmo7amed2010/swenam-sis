<?php

namespace App\Services;

use App\Mail\AgentWelcomeMail;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

/**
 * Agent Service
 *
 * Handles all business logic for agent users including CRUD, validation,
 * and statistics with caching.
 */
class AgentService
{
    /**
     * Create a new agent user.
     *
     * @param  array  $data  Agent data
     * @return User Newly created agent user
     *
     * @throws \Exception
     */
    public function createAgent(array $data): User
    {
        DB::beginTransaction();
        try {
            $tempPassword = $this->generateTempPassword();

            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($tempPassword),
                'user_type' => 'agent',
                'password_change_required' => true,
                'email_verified_at' => now(),
            ]);

            // Handle avatar upload
            if (isset($data['avatar']) && $data['avatar']) {
                $avatarPath = $data['avatar']->store('avatars', 'public');
                $user->update(['profile_photo_path' => $avatarPath]);
            }

            DB::commit();

            // Clear cached counts
            $this->clearCountCache();

            // Queue welcome email with temporary password
            try {
                Mail::to($user->email)->queue(new AgentWelcomeMail($user, $tempPassword));
            } catch (\Exception $e) {
                Log::warning('Failed to queue agent welcome email', [
                    'agent_id' => $user->id,
                    'email' => $user->email,
                    'error' => $e->getMessage(),
                ]);
            }

            Log::info('Agent created', [
                'agent_id' => $user->id,
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
     * Generate a secure temporary password (12 chars with uppercase, lowercase, numbers, symbols).
     */
    private function generateTempPassword(): string
    {
        $uppercase = 'ABCDEFGHJKLMNPQRSTUVWXYZ';
        $lowercase = 'abcdefghjkmnpqrstuvwxyz';
        $numbers = '23456789';
        $symbols = '!@#$%^&*';

        $password = '';
        $password .= $uppercase[random_int(0, strlen($uppercase) - 1)];
        $password .= $uppercase[random_int(0, strlen($uppercase) - 1)];
        $password .= $lowercase[random_int(0, strlen($lowercase) - 1)];
        $password .= $lowercase[random_int(0, strlen($lowercase) - 1)];
        $password .= $numbers[random_int(0, strlen($numbers) - 1)];
        $password .= $numbers[random_int(0, strlen($numbers) - 1)];
        $password .= $symbols[random_int(0, strlen($symbols) - 1)];
        $password .= $symbols[random_int(0, strlen($symbols) - 1)];

        // Add 4 more random chars
        $allChars = $uppercase.$lowercase.$numbers.$symbols;
        for ($i = 0; $i < 4; $i++) {
            $password .= $allChars[random_int(0, strlen($allChars) - 1)];
        }

        return str_shuffle($password);
    }

    /**
     * Update an existing agent user.
     *
     * @param  User  $user  Agent to update
     * @param  array  $data  Update data
     * @return User Updated agent
     */
    public function updateAgent(User $user, array $data): User
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

        Log::info('Agent updated', [
            'agent_id' => $user->id,
            'updated_by' => auth()->id(),
            'changes' => $user->getChanges(),
        ]);

        return $user->fresh();
    }

    /**
     * Delete an agent user.
     *
     * @param  User  $user  Agent to delete
     * @return bool Success status
     */
    public function deleteAgent(User $user): bool
    {
        // Clear cached counts
        $this->clearCountCache();

        // Delete avatar if exists
        if ($user->profile_photo_path) {
            Storage::disk('public')->delete($user->profile_photo_path);
        }

        Log::warning('Agent deleted', [
            'agent_id' => $user->id,
            'deleted_by' => auth()->id(),
            'email' => $user->email,
        ]);

        return $user->delete();
    }

    /**
     * Check if an agent can be deleted.
     * Admins cannot delete themselves (not applicable here, but kept for consistency).
     *
     * @param  User  $user  Agent to check
     * @return bool True if can be deleted
     */
    public function canDelete(User $user): bool
    {
        return $user->id !== auth()->id();
    }

    /**
     * Get total agent count.
     *
     * @param  int  $cacheDuration  Cache duration in seconds (default: 5 minutes)
     */
    public function getTotalCount(int $cacheDuration = 300): int
    {
        return User::where('user_type', 'agent')->count();
    }

    /**
     * Get active agents count (logged in within last 30 days).
     *
     * @param  int  $cacheDuration  Cache duration in seconds (default: 5 minutes)
     */
    public function getActiveCount(int $cacheDuration = 300): int
    {
        return User::where('user_type', 'agent')
            ->where('last_login_at', '>=', now()->subDays(30))
            ->count();
    }

    /**
     * Get new agents this month count.
     *
     * @param  int  $cacheDuration  Cache duration in seconds (default: 5 minutes)
     */
    public function getNewThisMonthCount(int $cacheDuration = 300): int
    {
        return User::where('user_type', 'agent')
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();
    }

    /**
     * Clear cached agent counts.
     */
    public function clearCountCache(): void
    {
        Cache::forget('agents.total.count');
        Cache::forget('agents.active.count');
        Cache::forget('agents.new_this_month.count');
    }

    /**
     * Get agent statistics.
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
     * Get agent counts for AJAX response (no cache).
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
