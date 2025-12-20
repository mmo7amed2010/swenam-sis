<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ApplicationAuditLog extends Model
{
    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'application_id',
        'user_id',
        'action',
        'old_status',
        'new_status',
        'reason',
        'ip_address',
        'user_agent',
    ];

    /**
     * Get the application that this audit log belongs to.
     */
    public function application(): BelongsTo
    {
        return $this->belongsTo(StudentApplication::class, 'application_id');
    }

    /**
     * Get the user who performed this action.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Log a decision for an application.
     */
    public static function logDecision(
        StudentApplication $application,
        string $action,
        ?string $reason = null,
        ?string $oldStatus = null
    ): self {
        return self::create([
            'application_id' => $application->id,
            'user_id' => auth()->id(),
            'action' => $action,
            'old_status' => $oldStatus ?? $application->getOriginal('status') ?? $application->status,
            'new_status' => $application->status,
            'reason' => $reason,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }
}
