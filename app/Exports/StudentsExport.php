<?php

namespace App\Exports;

use App\Models\Student;
use App\Services\LmsApiService;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class StudentsExport implements FromCollection, WithHeadings, WithMapping
{
    protected $applicationStatus;

    protected $programId;

    protected $suspensionStatus;

    protected $programMap = [];

    public function __construct($applicationStatus = null, $programId = null, $suspensionStatus = null)
    {
        $this->applicationStatus = $applicationStatus;
        $this->programId = $programId;
        $this->suspensionStatus = $suspensionStatus;

        $programs = app(LmsApiService::class)->getPrograms();
        foreach ($programs as $program) {
            $this->programMap[$program['id']] = $program['name'];
        }
    }

    public function collection()
    {
        $query = Student::query()->with(['user', 'studentApplication']);

        if ($this->applicationStatus === 'with') {
            $query->whereHas('studentApplication');
        } elseif ($this->applicationStatus === 'without') {
            $query->whereDoesntHave('studentApplication');
        }

        if ($this->programId) {
            $query->whereHas('user', function ($q) {
                $q->where('program_id', $this->programId);
            });
        }

        if ($this->suspensionStatus === 'suspended') {
            $query->whereHas('user', function ($q) {
                $q->where('is_suspended', true);
            });
        } elseif ($this->suspensionStatus === 'active') {
            $query->whereHas('user', function ($q) {
                $q->where('is_suspended', false);
            });
        }

        return $query->orderBy('created_at', 'desc')->get();
    }

    public function headings(): array
    {
        return [
            'Student Number',
            'First Name',
            'Last Name',
            'Email',
            'Phone',
            'Date of Birth',

            // Program
            'Program Name',
            'Enrollment Status',
            'Account Status',

            // Application
            'Has Application',
            'Application Reference',

            'Created Date',
        ];
    }

    public function map($student): array
    {
        return [
            $student->student_number,
            $student->first_name,
            $student->last_name,
            $student->email,
            $student->phone,
            $student->date_of_birth?->format('Y-m-d'),

            // Program
            $this->programMap[$student->user?->program_id] ?? 'N/A',
            $student->enrollment_status ?? 'N/A',
            ($student->user?->is_suspended ?? false) ? 'Suspended' : 'Active',

            // Application
            $student->studentApplication ? 'Yes' : 'No',
            $student->studentApplication?->reference_number ?? 'N/A',

            $student->created_at?->format('Y-m-d H:i:s'),
        ];
    }
}
