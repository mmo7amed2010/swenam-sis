<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
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
        'funding_type',

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
        'government_id_path',

        // Review/Approval
        'reviewed_by',
        'reviewed_at',
        'rejection_reason',
        'admin_notes',
        'created_user_id',
        'initial_approved_at',
        'initial_approved_by',

        // Contract & Payment tracking
        'contract_sent_at',
        'contract_sent_by',
        'contract_uploaded_at',
        'signed_contract_path',
        'contract_approved_at',
        'contract_approved_by',
        'contract_rejection_reason',
        'payment_uploaded_at',
        'payment_receipt_path',
        'payment_approved_at',
        'payment_approved_by',
        'payment_rejection_reason',

        // NOA (Notice of Assessment) tracking
        'noa_status',
        'noa_requested_at',
        'noa_requested_by',
        'noa_uploaded_at',
        'noa_document_path',
        'noa_approved_at',
        'noa_approved_by',
        'noa_rejection_reason',
        'noa_skipped',

        // MSFAA (Master Student Financial Assistance Agreement) tracking
        'msfaa_status',
        'msfaa_requested_at',
        'msfaa_requested_by',
        'msfaa_confirmed_at',
        'msfaa_approved_at',
        'msfaa_approved_by',
        'msfaa_rejection_reason',
        'msfaa_rejected_by',
        'msfaa_admin_notes',
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
        'contract_sent_at' => 'datetime',
        'contract_uploaded_at' => 'datetime',
        'contract_approved_at' => 'datetime',
        'payment_uploaded_at' => 'datetime',
        'payment_approved_at' => 'datetime',
        'noa_requested_at' => 'datetime',
        'noa_uploaded_at' => 'datetime',
        'noa_approved_at' => 'datetime',
        'noa_skipped' => 'boolean',
        'msfaa_requested_at' => 'datetime',
        'msfaa_confirmed_at' => 'datetime',
        'msfaa_approved_at' => 'datetime',
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
        $programs = collect($lmsApiService->getPrograms());

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
        $intakes = collect($lmsApiService->getIntakes());

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
     * Get the admin who sent the contract.
     */
    public function contractSender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'contract_sent_by');
    }

    /**
     * Get the admin who approved the contract.
     */
    public function contractApprover(): BelongsTo
    {
        return $this->belongsTo(User::class, 'contract_approved_by');
    }

    /**
     * Get the admin who approved the payment.
     */
    public function paymentApprover(): BelongsTo
    {
        return $this->belongsTo(User::class, 'payment_approved_by');
    }

    /**
     * Get the admin who requested the NOA.
     */
    public function noaRequester(): BelongsTo
    {
        return $this->belongsTo(User::class, 'noa_requested_by');
    }

    /**
     * Get the admin who approved the NOA.
     */
    public function noaApprover(): BelongsTo
    {
        return $this->belongsTo(User::class, 'noa_approved_by');
    }

    /**
     * Get the admin who requested the MSFAA.
     */
    public function msfaaRequester(): BelongsTo
    {
        return $this->belongsTo(User::class, 'msfaa_requested_by');
    }

    /**
     * Get the admin who approved the MSFAA.
     */
    public function msfaaApprover(): BelongsTo
    {
        return $this->belongsTo(User::class, 'msfaa_approved_by');
    }

    /**
     * Get the admin who rejected the MSFAA.
     */
    public function msfaaRejecter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'msfaa_rejected_by');
    }

    /**
     * Get all contracts for this application.
     */
    public function studentContracts(): HasMany
    {
        return $this->hasMany(StudentContract::class, 'application_id');
    }

    /**
     * Get the latest contract for this application.
     */
    public function latestContract(): HasOne
    {
        return $this->hasOne(StudentContract::class, 'application_id')->latestOfMany();
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
     * Check if application status is contract_sent.
     */
    public function isContractSent(): bool
    {
        return $this->status === 'contract_sent';
    }

    /**
     * Check if application status is contract_uploaded.
     */
    public function isContractUploaded(): bool
    {
        return $this->status === 'contract_uploaded';
    }

    /**
     * Check if application status is contract_approved.
     */
    public function isContractApproved(): bool
    {
        return $this->status === 'contract_approved';
    }

    /**
     * Check if application status is payment_pending.
     */
    public function isPaymentPending(): bool
    {
        return $this->status === 'payment_pending';
    }

    /**
     * Check if application status is payment_uploaded.
     */
    public function isPaymentUploaded(): bool
    {
        return $this->status === 'payment_uploaded';
    }

    /**
     * Check if application status is payment_approved.
     */
    public function isPaymentApproved(): bool
    {
        return $this->status === 'payment_approved';
    }

    /**
     * Check if student is self-funded.
     */
    public function isSelfFunded(): bool
    {
        return $this->funding_type === 'self_funded';
    }

    /**
     * Check if student is government-funded.
     */
    public function isGovernmentFunded(): bool
    {
        return $this->funding_type === 'government_funded';
    }

    /**
     * Check if application can be finally approved (account created).
     * Government-funded: after contract_approved
     * Self-funded: after payment_approved
     */
    public function canBeFinallyApproved(): bool
    {
        if ($this->isGovernmentFunded()) {
            return $this->isContractApproved();
        }

        return $this->isPaymentApproved();
    }

    /**
     * Check if application can be rejected.
     */
    public function canBeRejected(): bool
    {
        return in_array($this->status, [
            'pending',
            'initial_approved',
            'contract_sent',
            'contract_uploaded',
            'contract_approved',
            'payment_pending',
            'payment_uploaded',
            'payment_approved',
        ]);
    }

    /**
     * Check if a contract can be sent for this application.
     */
    public function canSendContract(): bool
    {
        return $this->isInitialApproved();
    }

    /**
     * Check if a signed contract can be uploaded.
     */
    public function canUploadSignedContract(): bool
    {
        return $this->isContractSent();
    }

    /**
     * Check if the contract can be approved.
     */
    public function canApproveContract(): bool
    {
        return $this->isContractUploaded();
    }

    /**
     * Check if a payment receipt can be uploaded.
     */
    public function canUploadPayment(): bool
    {
        return $this->isPaymentPending();
    }

    /**
     * Check if the payment can be approved.
     */
    public function canApprovePayment(): bool
    {
        return $this->isPaymentUploaded();
    }

    /**
     * Check if NOA can be requested for this application.
     */
    public function canRequestNoa(): bool
    {
        return $this->isApproved() && is_null($this->noa_status);
    }

    /**
     * Check if student can upload a NOA document.
     */
    public function canUploadNoa(): bool
    {
        return in_array($this->noa_status, ['requested', 'rejected']);
    }

    /**
     * Check if the NOA can be reviewed (approved/rejected).
     */
    public function canReviewNoa(): bool
    {
        return $this->noa_status === 'uploaded';
    }

    /**
     * Check if NOA has been requested.
     */
    public function isNoaRequested(): bool
    {
        return $this->noa_status === 'requested';
    }

    /**
     * Check if NOA has been uploaded.
     */
    public function isNoaUploaded(): bool
    {
        return $this->noa_status === 'uploaded';
    }

    /**
     * Check if NOA has been approved.
     */
    public function isNoaApproved(): bool
    {
        return $this->noa_status === 'approved';
    }

    /**
     * Check if the student has skipped NOA upload.
     */
    public function isNoaSkipped(): bool
    {
        return (bool) $this->noa_skipped;
    }

    /**
     * Get the number of days elapsed since NOA was requested.
     */
    public function getNoaElapsedDaysAttribute(): ?int
    {
        if (! $this->noa_requested_at) {
            return null;
        }

        return (int) $this->noa_requested_at->diffInDays(now());
    }

    /**
     * Check if MSFAA can be requested for this application.
     */
    public function canRequestMsfaa(): bool
    {
        return $this->isNoaApproved() && is_null($this->msfaa_status);
    }

    /**
     * Check if student can confirm MSFAA.
     */
    public function canConfirmMsfaa(): bool
    {
        return in_array($this->msfaa_status, ['requested', 'rejected']);
    }

    /**
     * Check if the MSFAA can be reviewed (approved/rejected).
     */
    public function canReviewMsfaa(): bool
    {
        return $this->msfaa_status === 'confirmed';
    }

    /**
     * Check if MSFAA has been requested.
     */
    public function isMsfaaRequested(): bool
    {
        return $this->msfaa_status === 'requested';
    }

    /**
     * Check if MSFAA has been confirmed by student.
     */
    public function isMsfaaConfirmed(): bool
    {
        return $this->msfaa_status === 'confirmed';
    }

    /**
     * Check if MSFAA has been approved.
     */
    public function isMsfaaApproved(): bool
    {
        return $this->msfaa_status === 'approved';
    }

    /**
     * Check if MSFAA has been rejected.
     */
    public function isMsfaaRejected(): bool
    {
        return $this->msfaa_status === 'rejected';
    }

    /**
     * Get the number of days elapsed since MSFAA was requested.
     */
    public function getMsfaaElapsedDaysAttribute(): ?int
    {
        if (! $this->msfaa_requested_at) {
            return null;
        }

        return (int) $this->msfaa_requested_at->diffInDays(now());
    }
}
