<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;

class Student extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'user_id',
        'student_number',
        'first_name',
        'last_name',
        'email',
        'phone',
        'date_of_birth',
        'address',
        'enrollment_status',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'date_of_birth' => 'date',
        'address' => 'array',
    ];

    /**
     * Get the user account associated with this student.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the student application that created this student account.
     */
    public function studentApplication(): HasOne
    {
        return $this->hasOne(StudentApplication::class, 'created_user_id', 'user_id');
    }

    /**
     * Generate a unique student number.
     */
    public static function generateStudentNumber(): string
    {
        $year = date('Y');
        $count = self::whereYear('created_at', $year)->count() + 1;

        return sprintf('STU-%s-%05d', $year, $count);
    }

    /**
     * Get the full name of the student.
     */
    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    /**
     * Get the program that this student belongs to (through user).
     */
    public function program(): HasOneThrough
    {
        return $this->hasOneThrough(
            Program::class,
            User::class,
            'id',           // Foreign key on users table
            'id',           // Foreign key on programs table
            'user_id',      // Local key on students table
            'program_id'    // Local key on users table
        );
    }

    /**
     * Get all courses in the student's program.
     * Uses hasManyThrough relationship for efficient querying.
     */
    public function programCourses(): HasManyThrough
    {
        return $this->hasManyThrough(
            Course::class,
            User::class,
            'id',          // Foreign key on users table
            'program_id',  // Foreign key on courses table
            'user_id',     // Local key on students table
            'program_id'   // Local key on users table (which references programs.id)
        );
    }

    /**
     * Get the lesson progress records for this student (Story 2.13).
     */
    public function lessonProgress(): HasMany
    {
        return $this->hasMany(LessonProgress::class);
    }
}
