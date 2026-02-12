<?php

namespace App\Exports;

use App\Models\StudentApplication;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ApplicationsExport implements FromCollection, WithHeadings, WithMapping
{
    protected $status;

    protected $from;

    protected $to;

    protected $noaStatus;

    protected $msfaaStatus;

    protected $agentId;

    public function __construct($status = 'all', $from = null, $to = null, $noaStatus = null, $msfaaStatus = null, $agentId = null)
    {
        $this->status = $status;
        $this->from = $from;
        $this->to = $to;
        $this->noaStatus = $noaStatus;
        $this->msfaaStatus = $msfaaStatus;
        $this->agentId = $agentId;
    }

    public function collection()
    {
        $query = StudentApplication::with(['reviewer', 'agent']);

        if ($this->status && $this->status !== 'all') {
            $query->byStatus($this->status);
        }

        if ($this->from && $this->to) {
            $query->byDateRange($this->from, $this->to);
        }

        if ($this->noaStatus && $this->noaStatus !== 'all') {
            $query->where('noa_status', $this->noaStatus);
        }

        if ($this->msfaaStatus && $this->msfaaStatus !== 'all') {
            $query->where('msfaa_status', $this->msfaaStatus);
        }

        if ($this->agentId) {
            $query->where('agent_id', $this->agentId);
        }

        return $query->orderBy('created_at', 'desc')->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Reference Number',
            'Status',

            // Personal Information
            'First Name',
            'Last Name',
            'Email',
            'Phone',
            'Date of Birth',
            'Country of Citizenship',
            'Residency Status',
            'Primary Language',
            'Address Line 1',
            'Address Line 2',
            'City',
            'State/Province',
            'Postal Code',
            'Country',

            // Program Information
            'Program',
            'Funding Type',
            'Has Referral',
            'Referral Agency Name',

            // Education History
            'Highest Education Level',
            'Education Field',
            'Institution Name',
            'Education Completed',
            'Education Country',
            'Has Disciplinary Action',

            // Work History
            'Has Work Experience',
            'Position Level',
            'Position Title',
            'Organization Name',
            'Work Start Date',
            'Work End Date',
            'Years of Experience',

            // Agent
            'Agent Name',

            // NOA Tracking
            'NOA Status',
            'NOA Requested At',
            'NOA Uploaded At',
            'NOA Approved At',
            'NOA Skipped',

            // MSFAA Tracking
            'MSFAA Status',
            'MSFAA Requested At',
            'MSFAA Confirmed At',
            'MSFAA Approved At',


            'Rejection Reason',
            'Admin Notes',
            'Submitted Date',
        ];
    }

    public function map($application): array
    {
        return [
            $application->id,
            $application->reference_number,
            ucfirst(str_replace('_', ' ', $application->status)),

            // Personal Information
            $application->first_name,
            $application->last_name,
            $application->email,
            $application->phone,
            $application->date_of_birth?->format('Y-m-d'),
            $application->country_of_citizenship,
            $application->residency_status,
            $application->primary_language,
            $application->address_line1,
            $application->address_line2,
            $application->city,
            $application->state_province,
            $application->postal_code,
            $application->country,

            // Program Information
            $application->program_name ?? 'N/A',
            $application->funding_type ?? 'N/A',
            $application->has_referral ? 'Yes' : 'No',
            $application->referral_agency_name,

            // Education History
            $application->highest_education_level,
            $application->education_field,
            $application->institution_name,
            $application->education_completed,
            $application->education_country,
            $application->has_disciplinary_action ? 'Yes' : 'No',

            // Work History
            $application->has_work_experience ? 'Yes' : 'No',
            $application->position_level,
            $application->position_title,
            $application->organization_name,
            $application->work_start_date?->format('Y-m-d'),
            $application->work_end_date?->format('Y-m-d'),
            $application->years_of_experience,

            // Agent
            $application->agent?->name ?? 'N/A',

            // NOA Tracking
            $application->noa_status ? ucfirst($application->noa_status) : 'N/A',
            $application->noa_requested_at?->format('Y-m-d H:i:s'),
            $application->noa_uploaded_at?->format('Y-m-d H:i:s'),
            $application->noa_approved_at?->format('Y-m-d H:i:s'),
            $application->noa_skipped ? 'Yes' : 'No',

            // MSFAA Tracking
            $application->msfaa_status ? ucfirst($application->msfaa_status) : 'N/A',
            $application->msfaa_requested_at?->format('Y-m-d H:i:s'),
            $application->msfaa_confirmed_at?->format('Y-m-d H:i:s'),
            $application->msfaa_approved_at?->format('Y-m-d H:i:s'),


            $application->rejection_reason,
            $application->admin_notes,
            $application->created_at?->format('Y-m-d H:i:s'),
        ];
    }
}
