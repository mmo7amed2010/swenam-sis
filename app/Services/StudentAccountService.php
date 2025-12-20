<?php

namespace App\Services;

use App\Models\Student;
use App\Models\StudentApplication;
use Illuminate\Support\Facades\Hash;

class StudentAccountService
{
    /**
     * Create a student user account from an approved application
     * Returns array with student and plain password
     */
    public function createFromApplication(StudentApplication $application): array
    {
        // Generate secure random password
        $password = $this->generateSecurePassword();

        // Create student account using Student model
        $student = Student::create([
            'first_name' => $application->first_name,
            'last_name' => $application->last_name,
            'name' => $application->full_name,
            'email' => $application->email,
            'password' => Hash::make($password),
            'user_type' => 'student',
            'email_verified_at' => now(), // Auto-verify since application was approved
        ]);

        // Return both student and plain password for email notification
        return [
            'user' => $student,
            'password' => $password,
        ];
    }

    /**
     * Generate a secure random password
     */
    protected function generateSecurePassword(): string
    {
        // Generate 12-character password with mixed case, numbers, and symbols
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

        // Fill remaining characters randomly
        $allChars = $uppercase.$lowercase.$numbers.$symbols;
        for ($i = 0; $i < 4; $i++) {
            $password .= $allChars[random_int(0, strlen($allChars) - 1)];
        }

        // Shuffle the password characters
        return str_shuffle($password);
    }
}
