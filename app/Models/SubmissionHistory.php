<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SubmissionHistory extends Model
{
    use HasFactory;

    protected $table = 'assignment_submission_history';

    protected $fillable = [
        'submission_id',
        'assignment_id',
        'user_id',
        'submission_type',
        'text_content',
        'file_path',
        'file_name',
        'file_size',
        'external_url',
        'quiz_answers',
        'attempt_number',
        'submitted_at',
        'is_late',
        'late_days',
        'status',
        'archived_at',
        'archived_by_user_id',
    ];

    protected $casts = [
        'quiz_answers' => 'array',
        'submitted_at' => 'datetime',
        'archived_at' => 'datetime',
        'is_late' => 'boolean',
    ];

    public function submission(): BelongsTo
    {
        return $this->belongsTo(Submission::class);
    }

    public function assignment(): BelongsTo
    {
        return $this->belongsTo(Assignment::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function archivedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'archived_by_user_id');
    }
}
