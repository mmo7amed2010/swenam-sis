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
}
