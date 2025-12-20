<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentProgram extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'student_id',
        'program_id',
        'enrollment_date',
        'expected_graduation',
        'status',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'enrollment_date' => 'date',
        'expected_graduation' => 'date',
    ];

    /**
     * Get the student.
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Get the program.
     */
    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class);
    }
}
