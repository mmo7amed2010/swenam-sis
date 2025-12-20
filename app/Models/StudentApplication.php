<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class StudentApplication extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'reference_number',
        'status',

        // Program Information
        'program_id',
        'intake_id',
        'preferred_intake', // Legacy field - keeping for backward compatibility
        'has_referral',
        'referral_agency_name',

        // Personal Information
        'first_name',
        'last_name',
        'email',
        'phone',
        'date_of_birth',
        'country_of_citizenship',
        'residency_status',
        'primary_language',
        'address_line1',
        'address_line2',
        'city',
        'state_province',
        'postal_code',
        'country',

        // Education History
        'highest_education_level',
        'education_field',
        'institution_name',
        'education_completed',
        'education_country',
        'has_disciplinary_action',

        // Work History
        'has_work_experience',
        'position_level',
        'position_title',
        'organization_name',
        'work_start_date',
        'work_end_date',
        'years_of_experience',

        // Supporting Documents
        'degree_certificate_path',
        'transcripts_path',
        'cv_path',
        'english_test_path',

        // Review/Approval
        'reviewed_by',
        'reviewed_at',
        'rejection_reason',
        'admin_notes',
        'created_user_id',
        'initial_approved_at',
        'initial_approved_by',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'date_of_birth' => 'date',
        'work_start_date' => 'date',
        'work_end_date' => 'date',
        'has_disciplinary_action' => 'boolean',
        'has_work_experience' => 'boolean',
        'has_referral' => 'boolean',
        'reviewed_at' => 'datetime',
        'initial_approved_at' => 'datetime',
    ];

    /**
     * Get the program data from LMS API.
     *
     * Programs are managed in LMS (master system). This method fetches
     * program details from the cached API data.
     */
    public function getProgramAttribute(): ?array
    {
        if (! $this->program_id) {
            return null;
        }

        $lmsApiService = app(\App\Services\LmsApiService::class);
        $programs = \Illuminate\Support\Facades\Cache::remember('lms_programs', 300, fn () => collect($lmsApiService->getPrograms()));

        return $programs->firstWhere('id', $this->program_id);
    }

    /**
     * Get the intake data from LMS API.
     *
     * Intakes are managed in LMS (master system). This method fetches
     * intake details from the cached API data.
     */
    public function getIntakeAttribute(): ?array
    {
        if (! $this->intake_id) {
            return null;
        }

        $lmsApiService = app(\App\Services\LmsApiService::class);
        $intakes = \Illuminate\Support\Facades\Cache::remember('lms_intakes', 300, fn () => collect($lmsApiService->getIntakes()));

        return $intakes->firstWhere('id', $this->intake_id);
    }

    /**
     * Get the program name (convenience accessor).
     */
    public function getProgramNameAttribute(): ?string
    {
        return $this->program['name'] ?? null;
    }

    /**
     * Get the intake name (convenience accessor).
     */
    public function getIntakeNameAttribute(): ?string
    {
        return $this->intake['name'] ?? null;
    }

    /**
     * Get the admin who reviewed this application.
     */
    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    /**
     * Get the student user created from this application.
     */
    public function createdUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_user_id');
    }

    /**
     * Get the admin who initially approved this application.
     */
    public function initialApprover(): BelongsTo
    {
        return $this->belongsTo(User::class, 'initial_approved_by');
    }

    /**
     * Scope a query to only include pending applications.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope a query to only include approved applications.
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    /**
     * Scope a query to only include rejected applications.
     */
    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    /**
     * Scope a query to only include initially approved applications.
     */
    public function scopeInitialApproved($query)
    {
        return $query->where('status', 'initial_approved');
    }

    /**
     * Scope a query to filter by status.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByStatus($query, ?string $status)
    {
        if ($status && $status !== 'all') {
            return $query->where('status', $status);
        }

        return $query;
    }

    /**
     * Scope a query to filter by date range.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByDateRange($query, ?string $from, ?string $to)
    {
        if ($from && $to) {
            return $query->whereBetween('created_at', [$from, $to]);
        }

        return $query;
    }

    /**
     * Generate a unique reference number for this application.
     */
    public static function generateReferenceNumber(): string
    {
        do {
            // Format: APP-YYYYMMDD-XXXX (e.g., APP-20251115-A1B2)
            $reference = 'APP-'.date('Ymd').'-'.strtoupper(Str::random(4));
        } while (self::where('reference_number', $reference)->exists());

        return $reference;
    }

    /**
     * Get the applicant's full name.
     */
    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    /**
     * Check if application is pending.
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Check if application is approved.
     */
    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    /**
     * Check if application is rejected.
     */
    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }

    /**
     * Check if application is initially approved.
     */
    public function isInitialApproved(): bool
    {
        return $this->status === 'initial_approved';
    }

    /**
     * Check if application can be initially approved.
     */
    public function canBeInitiallyApproved(): bool
    {
        return $this->isPending();
    }

    /**
     * Check if application can be finally approved (account created).
     */
    public function canBeFinallyApproved(): bool
    {
        return $this->isInitialApproved();
    }

    /**
     * Check if application can be rejected.
     */
    public function canBeRejected(): bool
    {
        return $this->isPending() || $this->isInitialApproved();
    }
}
