<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'first_name',
        'last_name',
        'email',
        'user_type',
        'password',
        'program_id',
        'failed_login_attempts',
        'locked_until',
        'password_change_required',
        'last_login_at',
        'last_login_ip',
        'profile_photo_path',
        'lms_user_id',
        'email_verified_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_login_at' => 'datetime',
        'locked_until' => 'datetime',
        'password_change_required' => 'boolean',
    ];

    public function getProfilePhotoUrlAttribute()
    {
        if ($this->profile_photo_path) {
            return asset('storage/' . $this->profile_photo_path);
        }

        return $this->profile_photo_path;
    }

    /**
     * Get the program for student users.
     */
    public function program()
    {
        return $this->belongsTo(Program::class);
    }

    /**
     * Get the student record for student users.
     */
    public function student()
    {
        return $this->hasOne(Student::class);
    }

    /**
     * Get the user's notification settings.
     */
    public function notificationSettings()
    {
        return $this->hasOne(NotificationSetting::class);
    }

    /**
     * Get announcements created by this user.
     */
    public function announcements()
    {
        return $this->hasMany(Announcement::class, 'user_id');
    }

    /**
     * Check if user wants email for a specific notification type.
     *
     * @param string $type
     * @return bool
     */
    public function wantsNotificationEmail(string $type): bool
    {
        $settings = $this->notificationSettings()->first();

        if (!$settings) {
            // Create default settings if they don't exist
            $settings = NotificationSetting::getOrCreateForUser($this->id);
        }

        return $settings->shouldSendEmail($type);
    }

    /**
     * Check if user has LMS account linked
     */
    public function hasLmsAccount(): bool
    {
        return ! empty($this->lms_user_id);
    }

    /**
     * Check if user is a student
     */
    public function isStudent(): bool
    {
        return $this->user_type === 'student';
    }

    /**
     * Check if user is an instructor
     */
    public function isInstructor(): bool
    {
        return $this->user_type === 'instructor';
    }

    /**
     * Check if user is an admin
     */
    public function isAdmin(): bool
    {
        return $this->user_type === 'admin';
    }

    /**
     * Check if student's application is fully approved.
     * Returns false for non-students.
     */
    public function isApplicationApproved(): bool
    {
        if (! $this->isStudent()) {
            return false;
        }

        return $this->student?->studentApplication?->isApproved() ?? false;
    }

    /**
     * Check if student has pending application (not yet approved).
     */
    public function hasApplicationPending(): bool
    {
        if (! $this->isStudent()) {
            return false;
        }

        $application = $this->student?->studentApplication;

        if (! $application) {
            return false;
        }

        return ! $application->isApproved();
    }

    /**
     * Get the student's linked application through the student record.
     */
    public function getStudentApplicationAttribute(): ?StudentApplication
    {
        return $this->student?->studentApplication;
    }
}
