<?php

namespace App\Services;

use App\Models\Course;
use App\Models\CourseChangeLog;
use App\Models\CourseInstructor;
use App\Models\Program;
use App\Traits\LogsCourseAudit;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;

class CourseManagementService
{
    use LogsCourseAudit;

    /**
     * Create a new course within a program.
     *
     * @param  array  $data  Course data
     * @param  Program  $program  Parent program
     * @return Course Newly created course
     */
    public function createCourse(array $data, Program $program): Course
    {
        $course = Course::create([
            'course_code' => $data['course_code'],
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'program_id' => $program->id,
            'credits' => $data['credits'] ?? null,
            'start_date' => $data['start_date'] ?? null,
            'end_date' => $data['end_date'] ?? null,
            'max_enrollment' => $data['max_enrollment'] ?? null,
            'status' => 'draft',
            'created_by_admin_id' => auth()->id(),
        ]);

        // Auto-assign instructor if creator is instructor
        if (auth()->user()->user_type === 'instructor') {
            CourseInstructor::create([
                'course_id' => $course->id,
                'user_id' => auth()->id(),
                'assigned_by_admin_id' => auth()->id(),
                'assigned_at' => now(),
            ]);
        } elseif (isset($data['instructor_id']) && $data['instructor_id']) {
            // Assign provided instructor
            CourseInstructor::create([
                'course_id' => $course->id,
                'user_id' => $data['instructor_id'],
                'assigned_by_admin_id' => auth()->id(),
                'assigned_at' => now(),
            ]);
        }

        // Log course creation
        $this->logCourseEvent(
            $course,
            'created',
            null,
            $course->toArray(),
            "Course '{$course->course_code}' created"
        );

        return $course;
    }

    /**
     * Update an existing course.
     *
     * @param  Course  $course  Course to update
     * @param  array  $data  Update data
     * @return Course Updated course
     */
    public function updateCourse(Course $course, array $data): Course
    {
        $oldValues = $course->getOriginal();

        // Track changes for change log
        $changes = [];
        $fieldsToTrack = ['course_code', 'name', 'description', 'credits', 'start_date', 'end_date', 'max_enrollment'];

        foreach ($fieldsToTrack as $field) {
            $oldValue = $oldValues[$field] ?? null;
            $newValue = $data[$field] ?? null;

            if ($oldValue != $newValue) {
                $changes[] = [
                    'course_id' => $course->id,
                    'user_id' => auth()->id(),
                    'field_changed' => $field,
                    'old_value' => $oldValue ? (string) $oldValue : null,
                    'new_value' => $newValue ? (string) $newValue : null,
                    'created_at' => now(),
                ];
            }
        }

        // Update course (program_id is read-only, not updated)
        $updateData = [
            'name' => $data['name'],
            'description' => $data['description'] ?? $course->description,
            'credits' => $data['credits'] ?? $course->credits,
            'start_date' => $data['start_date'] ?? $course->start_date,
            'end_date' => $data['end_date'] ?? $course->end_date,
            'max_enrollment' => $data['max_enrollment'] ?? $course->max_enrollment,
        ];

        // Allow course_code to be updated if provided
        if (isset($data['course_code']) && $data['course_code'] !== $course->course_code) {
            $updateData['course_code'] = $data['course_code'];
        }

        $course->update($updateData);

        // Handle instructor assignment/update
        if (isset($data['instructor_id'])) {
            // Handle empty string, null, or 0 as "no instructor"
            $newInstructorId = (! empty($data['instructor_id']) && $data['instructor_id'] !== '0')
                ? (int) $data['instructor_id']
                : null;

            // Get current instructor (simplified: one instructor per course)
            $currentInstructor = $course->instructors()
                ->whereNull('removed_at')
                ->first();

            $currentInstructorId = $currentInstructor ? $currentInstructor->user_id : null;

            // Only update if instructor has changed
            if ($newInstructorId !== $currentInstructorId) {
                // Remove current instructor if exists
                if ($currentInstructor) {
                    $currentInstructor->update(['removed_at' => now()]);
                }

                // Assign new instructor if provided
                if ($newInstructorId) {
                    // Check if instructor was previously assigned (but removed)
                    $existingInstructor = $course->instructors()
                        ->where('user_id', $newInstructorId)
                        ->first();

                    if ($existingInstructor) {
                        // Restore the assignment
                        $existingInstructor->update([
                            'removed_at' => null,
                            'assigned_by_admin_id' => auth()->id(),
                            'assigned_at' => now(),
                        ]);
                    } else {
                        // Create new instructor assignment
                        CourseInstructor::create([
                            'course_id' => $course->id,
                            'user_id' => $newInstructorId,
                            'assigned_by_admin_id' => auth()->id(),
                            'assigned_at' => now(),
                        ]);
                    }
                }
            }
        }

        // Create change log entries
        if (! empty($changes) && class_exists(CourseChangeLog::class)) {
            CourseChangeLog::insert($changes);
        }

        // Log course update
        $this->logCourseEvent(
            $course,
            'updated',
            $oldValues,
            $course->fresh()->toArray(),
            "Course '{$course->course_code}' updated"
        );

        return $course->fresh();
    }

    /**
     * Delete a course.
     *
     * @param  Course  $course  Course to delete
     * @return bool Success status
     */
    public function deleteCourse(Course $course): bool
    {
        $courseCode = $course->course_code;

        // Cascade soft delete to child records
        $course->modules()->delete();
        $course->assignments()->delete();
        // Note: Quizzes would be deleted here if they exist

        // Log before deletion
        $this->logCourseEvent(
            $course,
            'deleted',
            $course->toArray(),
            null,
            "Course '{$courseCode}' deleted"
        );

        // Create change log entry for deletion
        if (class_exists(CourseChangeLog::class)) {
            CourseChangeLog::create([
                'course_id' => $course->id,
                'user_id' => auth()->id(),
                'field_changed' => 'deleted',
                'old_value' => null,
                'new_value' => now()->toDateTimeString(),
            ]);
        }

        return $course->delete();
    }

    /**
     * Archive multiple courses.
     *
     * @param  array<int>  $courseIds  Course IDs to archive
     * @return int Number of courses archived
     */
    public function bulkArchive(array $courseIds): int
    {
        $courses = Course::whereIn('id', $courseIds)
            ->whereIn('status', ['published', 'active'])
            ->get();

        if ($courses->isEmpty()) {
            return 0;
        }

        $archivedCount = 0;
        foreach ($courses as $course) {
            $oldStatus = $course->status;
            $course->update([
                'status' => 'archived',
                'archived_at' => now(),
            ]);

            $this->logCourseEvent(
                $course,
                'archived',
                ['status' => $oldStatus],
                ['status' => 'archived'],
                "Course '{$course->course_code}' bulk archived"
            );

            $archivedCount++;
        }

        return $archivedCount;
    }

    /**
     * Generate export file for courses.
     *
     * @param  array<int>  $courseIds  Course IDs to export
     * @return string Export file path
     */
    public function bulkExport(array $courseIds): string
    {
        $courses = Course::with(['program', 'instructors.instructor', 'modules', 'enrollments'])
            ->whereIn('id', $courseIds)
            ->get();

        return $this->generateExportFile($courses);
    }

    /**
     * Generate CSV export file.
     *
     * @param  Collection  $courses  Courses to export
     * @return string File path
     */
    private function generateExportFile(Collection $courses): string
    {
        $filename = 'courses_export_'.now()->format('Y-m-d_His').'.csv';
        $directory = 'exports';
        $filePath = $directory.'/'.$filename;

        // Ensure directory exists
        if (! Storage::disk('local')->exists($directory)) {
            Storage::disk('local')->makeDirectory($directory);
        }

        $fullPath = storage_path('app/'.$filePath);
        $file = fopen($fullPath, 'w');

        // Headers
        fputcsv($file, [
            'Course Code',
            'Course Name',
            'Program',
            'Status',
            'Instructors',
            'Modules',
            'Students in Program',
            'Created At',
        ]);

        // Data
        foreach ($courses as $course) {
            $instructors = $course->instructors
                ->map(fn ($ci) => $ci->instructor->name ?? '')
                ->filter()
                ->implode(', ');

            // Count students assigned to this course's program (via User.program_id)
            $studentCount = \App\Models\User::where('program_id', $course->program_id)
                ->where('user_type', 'student')
                ->count();

            fputcsv($file, [
                $course->course_code,
                $course->name,
                $course->program->name ?? '',
                $course->status,
                $instructors,
                $course->modules->count(),
                $studentCount,
                $course->created_at->format('Y-m-d H:i:s'),
            ]);
        }

        fclose($file);

        return $fullPath;
    }
}
