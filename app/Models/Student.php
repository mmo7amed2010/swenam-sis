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
        'emergency_first_name',
        'emergency_last_name',
        'emergency_phone',
        'emergency_address',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'date_of_birth' => 'date',
        'address' => 'array',
        'emergency_address' => 'array',
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
     * Gets the last number from database and increments.
     */
    public static function generateStudentNumber(): string
    {
        $year = date('Y');
        $prefix = "STU-{$year}-";

        // Get the last student number for this year
        $lastStudent = self::where('student_number', 'like', $prefix.'%')
            ->orderByRaw('CAST(SUBSTRING(student_number, -5) AS UNSIGNED) DESC')
            ->first();

        if ($lastStudent) {
            $lastNumber = (int) substr($lastStudent->student_number, -5);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return sprintf('STU-%s-%05d', $year, $newNumber);
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
