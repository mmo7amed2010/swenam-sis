<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ContractTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'program_id',
        'body',
        'is_active',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * SIS placeholders that are auto-populated from application data.
     */
    public static function getSisPlaceholders(): array
    {
        return [
            '{{student_name}}' => 'Full name of the student',
            '{{student_id}}' => 'Student reference number',
            '{{email}}' => 'Student email address',
            '{{phone}}' => 'Student phone number',
            '{{program_name}}' => 'Program name',
            '{{intake_name}}' => 'Intake name',
            '{{reference_number}}' => 'Application reference number',
            '{{date_of_birth}}' => 'Student date of birth',
            '{{funding_type}}' => 'Funding type (Self-Funded / Government-Funded)',
            '{{nationality}}' => 'Country of citizenship',
            '{{address}}' => 'Full mailing address',
            '{{contract_date}}' => 'Date the contract is issued',
            '{{highest_education}}' => 'Highest education level attained',
            '{{institution_name}}' => 'Previous educational institution name',
        ];
    }

    /**
     * Get admin-defined placeholders from the template body.
     * These are any {{...}} placeholders not in the SIS list.
     */
    public function getAdminPlaceholders(): array
    {
        $sisKeys = array_keys(static::getSisPlaceholders());

        preg_match_all('/\{\{(\w+)\}\}/', $this->body, $matches);

        $allPlaceholders = array_unique($matches[0]);
        $adminPlaceholders = array_diff($allPlaceholders, $sisKeys);

        return array_values($adminPlaceholders);
    }

    /**
     * Get the admin who created this template.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the admin who last updated this template.
     */
    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Get student contracts using this template.
     */
    public function studentContracts(): HasMany
    {
        return $this->hasMany(StudentContract::class, 'contract_template_id');
    }

    /**
     * Get the program data from LMS API.
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
     * Get the program name (convenience accessor).
     */
    public function getProgramNameAttribute(): ?string
    {
        return $this->program['name'] ?? null;
    }

    /**
     * Scope to active templates only.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to templates for a specific program.
     */
    public function scopeForProgram($query, int $programId)
    {
        return $query->where('program_id', $programId);
    }
}
