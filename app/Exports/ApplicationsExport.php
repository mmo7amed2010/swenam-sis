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

    public function __construct($status = 'all', $from = null, $to = null)
    {
        $this->status = $status;
        $this->from = $from;
        $this->to = $to;
    }

    public function collection()
    {
        $query = StudentApplication::with(['reviewer']);

        if ($this->status !== 'all') {
            $query->byStatus($this->status);
        }

        if ($this->from && $this->to) {
            $query->byDateRange($this->from, $this->to);
        }

        return $query->orderBy('created_at', 'desc')->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Reference Number',
            'First Name',
            'Last Name',
            'Email',
            'Phone',
            'Date of Birth',
            'Highest Education Level',
            'Education Field',
            'Institution Name',
            'Program',
            'Status',
            'Submitted Date',
            'Reviewed By',
            'Reviewed Date',
        ];
    }

    public function map($application): array
    {
        return [
            $application->id,
            $application->reference_number,
            $application->first_name,
            $application->last_name,
            $application->email,
            $application->phone,
            $application->date_of_birth?->format('Y-m-d'),
            $application->highest_education_level,
            $application->education_field,
            $application->institution_name,
            $application->program_name ?? 'N/A',
            ucfirst($application->status),
            $application->created_at?->format('Y-m-d H:i:s'),
            $application->reviewer?->name ?? 'N/A',
            $application->reviewed_at?->format('Y-m-d H:i:s') ?? 'N/A',
        ];
    }
}
