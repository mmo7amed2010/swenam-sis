<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentContract extends Model
{
    use HasFactory;

    protected $fillable = [
        'application_id',
        'contract_template_id',
        'admin_field_values',
        'rendered_body',
        'generated_pdf_path',
        'signed_pdf_path',
        'issued_by',
        'issued_at',
        'signed_at',
    ];

    protected $casts = [
        'admin_field_values' => 'array',
        'issued_at' => 'datetime',
        'signed_at' => 'datetime',
    ];

    /**
     * Get the application this contract belongs to.
     */
    public function application(): BelongsTo
    {
        return $this->belongsTo(StudentApplication::class, 'application_id');
    }

    /**
     * Get the template used for this contract.
     */
    public function template(): BelongsTo
    {
        return $this->belongsTo(ContractTemplate::class, 'contract_template_id');
    }

    /**
     * Get the admin who issued this contract.
     */
    public function issuer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'issued_by');
    }

    /**
     * Check if the contract has been signed.
     */
    public function isSigned(): bool
    {
        return ! empty($this->signed_pdf_path);
    }
}
