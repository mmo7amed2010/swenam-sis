<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProgramChangeLog extends Model
{
    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'program_id',
        'user_id',
        'action',
        'field_changed',
        'old_value',
        'new_value',
        'reason',
    ];

    /**
     * Get the program that this log belongs to.
     */
    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class);
    }

    /**
     * Get the user who made this change.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
