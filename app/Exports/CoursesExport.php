<?php

namespace App\Exports;

use App\Models\Course;
use App\Models\User;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class CoursesExport implements FromQuery, WithHeadings, WithMapping
{
    protected $filters;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    /**
     * Build the query with filters
     */
    public function query()
    {
        $query = Course::with(['creator', 'instructors.instructor'])
            ->withCount(['instructors']);

        // Apply search filter
        if (! empty($this->filters['search'])) {
            $search = $this->filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('course_code', 'like', "%{$search}%")
                    ->orWhere('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhereHas('instructors.instructor', function ($instructorQuery) use ($search) {
                        $instructorQuery->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
            });
        }

        // Apply status filter
        if (! empty($this->filters['status'])) {
            $query->where('status', $this->filters['status']);
        }

        // Apply department filter
        if (! empty($this->filters['department'])) {
            $query->where('department', $this->filters['department']);
        }

        // Apply category filter
        if (! empty($this->filters['category'])) {
            $query->where('category', $this->filters['category']);
        }

        return $query->orderBy('course_code');
    }

    /**
     * Define column headings
     */
    public function headings(): array
    {
        return [
            'Course Code',
            'Course Name',
            'Status',
            'Department',
            'Category',
            'Credits',
            'Difficulty',
            'Students in Program',
            'Instructors',
            'Created Date',
        ];
    }

    /**
     * Map data for each row
     */
    public function map($course): array
    {
        $instructorNames = $course->instructors
            ->pluck('instructor.name')
            ->filter()
            ->implode(', ');

        // Count students assigned to this course's program (via User.program_id)
        $studentCount = User::where('program_id', $course->program_id)
            ->where('user_type', 'student')
            ->count();

        return [
            $course->course_code,
            $course->name,
            ucfirst($course->status),
            $course->department ?? '-',
            $course->category ?? '-',
            $course->credits,
            ucfirst($course->difficulty_level),
            $studentCount,
            $instructorNames ?: '-',
            $course->created_at->format('Y-m-d'),
        ];
    }
}
