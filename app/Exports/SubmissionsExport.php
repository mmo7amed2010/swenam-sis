<?php

namespace App\Exports;

use App\Models\Submission;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class SubmissionsExport implements FromCollection, WithHeadings, WithMapping
{
    protected $assignmentId;

    protected $filter;

    public function __construct($assignmentId, $filter = 'all')
    {
        $this->assignmentId = $assignmentId;
        $this->filter = $filter;
    }

    /**
     * Build the query with filters (Story 3.6 AC #3).
     */
    public function collection()
    {
        $query = Submission::where('assignment_id', $this->assignmentId)
            ->where('status', 'submitted')
            ->with(['student', 'assignment', 'publishedGrade']);

        // Apply filters (Story 3.6 AC #3)
        switch ($this->filter) {
            case 'ungraded':
                $query->whereDoesntHave('grades', function ($q) {
                    $q->where('is_published', true);
                });
                break;
            case 'graded':
                $query->whereHas('grades', function ($q) {
                    $q->where('is_published', true);
                });
                break;
            case 'late':
                $query->where('is_late', true);
                break;
        }

        return $query->orderBy('submitted_at', 'desc')->get();
    }

    /**
     * Define column headings (Story 3.6 AC #2).
     */
    public function headings(): array
    {
        return [
            'Student Name',
            'Student Email',
            'Submission Date',
            'Status',
            'Grade',
            'Points Awarded',
            'Total Points',
            'Percentage',
            'Is Late',
            'Late Days',
            'Submission Type',
            'File Name',
            'File Size',
            'External URL',
        ];
    }

    /**
     * Map data for each row (Story 3.6 AC #2).
     */
    public function map($submission): array
    {
        $grade = $submission->publishedGrade();
        $student = $submission->student;

        return [
            $student ? $student->name : 'N/A',
            $student ? $student->email : 'N/A',
            $submission->submitted_at->format('Y-m-d H:i:s'),
            ucfirst($submission->status),
            $grade ? number_format($grade->points_awarded, 2).' / '.number_format($grade->max_points, 2) : 'Not Graded',
            $grade ? number_format($grade->points_awarded, 2) : '-',
            $grade ? number_format($grade->max_points, 2) : '-',
            $grade ? number_format($grade->percentage, 2).'%' : '-',
            $submission->is_late ? 'Yes' : 'No',
            $submission->is_late ? $submission->late_days : 0,
            ucfirst(str_replace('_', ' ', $submission->submission_type ?? 'N/A')),
            $submission->file_name ?? '-',
            $submission->file_size ? $submission->file_size_human : '-',
            $submission->external_url ?? '-',
        ];
    }
}
