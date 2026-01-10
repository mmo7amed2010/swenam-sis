<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class LmsApiService
{
    protected string $apiUrl;

    protected string $apiKey;

    public function __construct()
    {
        $this->apiUrl = config('lms.api_url');
        $this->apiKey = config('lms.api_key');
    }

    /**
     * Get all active programs from LMS.
     */
    public function getPrograms(): array
    {
        try {
            $response = Http::withHeaders([
                'X-API-Key' => $this->apiKey,
            ])->get($this->apiUrl.'/api/v1/programs');

            if ($response->successful()) {
                return $response->json('data', []);
            }

            Log::error('Failed to fetch programs from LMS', [
                'status' => $response->status(),
                'error' => $response->json('error'),
            ]);

            return [];
        } catch (\Exception $e) {
            Log::error('LMS API error fetching programs', [
                'error' => $e->getMessage(),
            ]);

            return [];
        }
    }

    /**
     * Get a single program from LMS.
     */
    public function getProgram(int $id): ?array
    {
        try {
            $response = Http::withHeaders([
                'X-API-Key' => $this->apiKey,
            ])->get($this->apiUrl.'/api/v1/programs/'.$id);

            if ($response->successful()) {
                return $response->json('data');
            }

            return null;
        } catch (\Exception $e) {
            Log::error('LMS API error fetching program', [
                'id' => $id,
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }

    /**
     * Get all active intakes from LMS.
     */
    public function getIntakes(): array
    {
        try {
            $response = Http::withHeaders([
                'X-API-Key' => $this->apiKey,
            ])->get($this->apiUrl.'/api/v1/intakes');

            if ($response->successful()) {
                return $response->json('data', []);
            }

            Log::error('Failed to fetch intakes from LMS', [
                'status' => $response->status(),
                'error' => $response->json('error'),
            ]);

            return [];
        } catch (\Exception $e) {
            Log::error('LMS API error fetching intakes', [
                'error' => $e->getMessage(),
            ]);

            return [];
        }
    }

    /**
     * Get a single intake from LMS.
     */
    public function getIntake(int $id): ?array
    {
        try {
            $response = Http::withHeaders([
                'X-API-Key' => $this->apiKey,
            ])->get($this->apiUrl.'/api/v1/intakes/'.$id);

            if ($response->successful()) {
                return $response->json('data');
            }

            return null;
        } catch (\Exception $e) {
            Log::error('LMS API error fetching intake', [
                'id' => $id,
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }

    /**
     * Create a student account in LMS when application is approved.
     */
    public function createStudent(array $data): array
    {
        try {
            $response = Http::withHeaders([
                'X-API-Key' => $this->apiKey,
            ])->post($this->apiUrl.'/api/v1/students', $data);

            if ($response->successful()) {
                Log::info('Student created in LMS', [
                    'data' => $response->json(),
                    'email' => $data['email'] ?? null,
                    'lms_user_id' => $response->json('user_id'),
                ]);

                return [
                    'success' => true,
                    'user_id' => $response->json('user_id'),
                    'student_id' => $response->json('student_id'),
                    'student_number' => $response->json('student_number'),
                ];
            }

            Log::error('Failed to create student in LMS', [
                'status' => $response->status(),
                'error' => $response->json('error'),
                'data' => $data,
            ]);

            return [
                'success' => false,
                'error' => $response->json('error', 'Failed to create student account'),
            ];
        } catch (\Exception $e) {
            Log::error('LMS API error creating student', [
                'error' => $e->getMessage(),
                'data' => $data,
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Generate SSO token for student to access LMS.
     */
    public function generateSsoToken(User $user, string $redirectTo = '/dashboard'): ?array
    {
        // Block suspended users from SSO
        if ($user->isSuspended()) {
            Log::warning('Cannot generate SSO token: user account is suspended', [
                'user_id' => $user->id,
            ]);

            return null;
        }

        if (! $user->lms_user_id) {
            Log::warning('Cannot generate SSO token: user has no LMS account', [
                'user_id' => $user->id,
            ]);

            return null;
        }

        try {
            $response = Http::withHeaders([
                'X-API-Key' => $this->apiKey,
            ])->post($this->apiUrl.'/api/v1/sso/token', [
                'lms_user_id' => $user->lms_user_id,
                'email' => $user->email,
                'redirect_to' => $redirectTo,
            ]);

            if ($response->successful()) {
                Log::info('SSO token generated for student', [
                    'user_id' => $user->id,
                    'lms_user_id' => $user->lms_user_id,
                ]);

                return [
                    'access_token' => $response->json('access_token'),
                    'expires_at' => $response->json('expires_at'),
                    'redirect_to' => $response->json('redirect_to'),
                ];
            }

            Log::error('Failed to generate SSO token', [
                'status' => $response->status(),
                'error' => $response->json('error'),
                'user_id' => $user->id,
            ]);

            return null;
        } catch (\Exception $e) {
            Log::error('LMS API error generating SSO token', [
                'error' => $e->getMessage(),
                'user_id' => $user->id,
            ]);

            return null;
        }
    }

    /**
     * Build the SSO login URL for redirecting to LMS.
     */
    public function getSsoLoginUrl(string $token, string $redirectTo = '/dashboard'): string
    {
        return $this->apiUrl.'/auth/sso?'.http_build_query([
            'token' => $token,
            'redirect_to' => $redirectTo,
        ]);
    }

    /**
     * Check if an email exists in LMS.
     *
     * @param  string  $email  Email address to check
     * @return array Returns ['exists' => bool, 'user_id' => int|null]
     */
    public function checkEmailExists(string $email): array
    {
        try {
            $response = Http::withHeaders([
                'X-API-Key' => $this->apiKey,
            ])->get($this->apiUrl.'/api/v1/students/check-email', [
                'email' => $email,
            ]);

            if ($response->successful()) {
                $data = $response->json();

                return [
                    'exists' => $data['exists'] ?? false,
                    'user_id' => $data['user_id'] ?? null,
                ];
            }

            Log::warning('Failed to check email in LMS', [
                'status' => $response->status(),
                'error' => $response->json('error'),
                'email' => $email,
            ]);

            // Fail open - if API fails, allow the operation but log warning
            return [
                'exists' => false,
                'user_id' => null,
            ];
        } catch (\Exception $e) {
            Log::warning('LMS API error checking email', [
                'error' => $e->getMessage(),
                'email' => $email,
            ]);

            // Fail open - if API fails, allow the operation but log warning
            return [
                'exists' => false,
                'user_id' => null,
            ];
        }
    }

    /**
     * Update a student's name in LMS.
     *
     * @param  int  $lmsUserId  The LMS user ID to update
     * @param  array  $data  The data to update (first_name, last_name)
     * @return array Returns ['success' => bool, 'error' => string|null]
     */
    public function updateStudent(int $lmsUserId, array $data): array
    {
        try {
            $response = Http::withHeaders([
                'X-API-Key' => $this->apiKey,
            ])->put($this->apiUrl.'/api/v1/students/'.$lmsUserId, $data);

            if ($response->successful()) {
                Log::info('Student updated in LMS', [
                    'lms_user_id' => $lmsUserId,
                    'updated_fields' => array_keys($data),
                ]);

                return [
                    'success' => true,
                ];
            }

            Log::error('Failed to update student in LMS', [
                'status' => $response->status(),
                'error' => $response->json('error'),
                'lms_user_id' => $lmsUserId,
                'data' => $data,
            ]);

            return [
                'success' => false,
                'error' => $response->json('error', 'Failed to update student profile'),
            ];
        } catch (\Exception $e) {
            Log::error('LMS API error updating student', [
                'error' => $e->getMessage(),
                'lms_user_id' => $lmsUserId,
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Delete a student account from LMS.
     *
     * @param  int  $lmsUserId  The LMS user ID to delete
     * @return array Returns ['success' => bool, 'error' => string|null]
     */
    public function deleteStudent(int $lmsUserId): array
    {
        try {
            $response = Http::withHeaders([
                'X-API-Key' => $this->apiKey,
            ])->delete($this->apiUrl.'/api/v1/students/'.$lmsUserId);

            if ($response->successful()) {
                Log::info('Student deleted in LMS', [
                    'lms_user_id' => $lmsUserId,
                ]);

                return [
                    'success' => true,
                ];
            }

            Log::error('Failed to delete student in LMS', [
                'status' => $response->status(),
                'error' => $response->json('error'),
                'lms_user_id' => $lmsUserId,
            ]);

            return [
                'success' => false,
                'error' => $response->json('error', 'Failed to delete student account'),
            ];
        } catch (\Exception $e) {
            Log::error('LMS API error deleting student', [
                'error' => $e->getMessage(),
                'lms_user_id' => $lmsUserId,
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }
}
