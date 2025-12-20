<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Announcement extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'course_id',
        'user_id',
        'title',
        'content',
        'type',
        'priority',
        'target_audience',
        'program_id',
        'is_published',
        'send_email',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_published' => 'boolean',
        'send_email' => 'boolean',
    ];

    /**
     * Get the course this announcement belongs to (null for system announcements).
     */
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    /**
     * Get the user who created this announcement.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Scope to get only published announcements.
     */
    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    /**
     * Scope to get announcements for a specific course.
     */
    public function scopeForCourse($query, $courseId)
    {
        return $query->where('course_id', $courseId)->where('type', 'course');
    }

    /**
     * Scope to get system-wide announcements.
     */
    public function scopeSystemWide($query)
    {
        return $query->where('type', 'system');
    }

    /**
     * Check if email should be sent for this announcement.
     */
    public function shouldSendEmail(): bool
    {
        return $this->send_email && $this->is_published;
    }

    /**
     * Get target users for this announcement.
     */
    public function getTargetUsers()
    {
        if ($this->type === 'course' && $this->course_id) {
            // Get all students enrolled in the course
            return User::whereHas('programCourses', function ($query) {
                $query->where('courses.id', $this->course_id);
            })->get();
        }

        // System-wide announcement - return all users or specific role
        return User::all();
    }

    /**
     * Get priority badge color for UI.
     */
    public function getPriorityColorAttribute(): string
    {
        return match ($this->priority) {
            'high' => 'danger',
            'medium' => 'warning',
            'low' => 'info',
            default => 'secondary',
        };
    }
}
