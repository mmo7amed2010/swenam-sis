<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoginLog extends Model
{
    /**
     * Disable updated_at timestamp (only need created_at)
     */
    const UPDATED_AT = null;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'email',
        'ip_address',
        'user_agent',
        'status',
        'failure_reason',
    ];

    /**
     * Get the user that owns the login log.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
