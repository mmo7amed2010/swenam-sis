<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NotificationSetting extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'course_announcements_email',
        'system_notifications_email',
        'assignment_reminders_email',
        'grade_notifications_email',
        'application_updates_email',
        'quiz_notifications_email',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'course_announcements_email' => 'boolean',
        'system_notifications_email' => 'boolean',
        'assignment_reminders_email' => 'boolean',
        'grade_notifications_email' => 'boolean',
        'application_updates_email' => 'boolean',
        'quiz_notifications_email' => 'boolean',
    ];

    /**
     * Get the user that owns the notification settings.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check if user wants email for a specific notification type.
     *
     * @param string $type - notification type (e.g., 'course_announcements', 'grade_notifications')
     * @return bool
     */
    public function shouldSendEmail(string $type): bool
    {
        $field = $type . '_email';

        return $this->{$field} ?? true; // Default to true if field doesn't exist
    }

    /**
     * Update a specific notification preference.
     *
     * @param string $type - notification type
     * @param bool $value - enable/disable
     * @return bool
     */
    public function updatePreference(string $type, bool $value): bool
    {
        $field = $type . '_email';

        if (!in_array($field, $this->fillable)) {
            return false;
        }

        $this->{$field} = $value;
        return $this->save();
    }

    /**
     * Get or create notification settings for a user.
     *
     * @param int $userId
     * @return NotificationSetting
     */
    public static function getOrCreateForUser(int $userId): NotificationSetting
    {
        return static::firstOrCreate(
            ['user_id' => $userId],
            [
                'course_announcements_email' => true,
                'system_notifications_email' => true,
                'assignment_reminders_email' => true,
                'grade_notifications_email' => true,
                'application_updates_email' => true,
                'quiz_notifications_email' => true,
            ]
        );
    }

    /**
     * Disable all email notifications for the user.
     */
    public function disableAllEmails(): bool
    {
        $this->course_announcements_email = false;
        $this->system_notifications_email = false;
        $this->assignment_reminders_email = false;
        $this->grade_notifications_email = false;
        $this->application_updates_email = false;
        $this->quiz_notifications_email = false;

        return $this->save();
    }

    /**
     * Enable all email notifications for the user.
     */
    public function enableAllEmails(): bool
    {
        $this->course_announcements_email = true;
        $this->system_notifications_email = true;
        $this->assignment_reminders_email = true;
        $this->grade_notifications_email = true;
        $this->application_updates_email = true;
        $this->quiz_notifications_email = true;

        return $this->save();
    }
}
