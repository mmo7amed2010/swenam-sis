<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\CourseInstructor;
use App\Models\Program;
use App\Models\User;
use App\Services\CourseInstructorService;
use App\Traits\HandlesTransactions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

/**
 * CourseInstructorController
 *
 * Simplified controller for assigning/removing instructors from courses.
 * One instructor per course - assigned via course create/edit modal.
 */
class CourseInstructorController extends Controller
{
    use HandlesTransactions;

    public function __construct(
        private CourseInstructorService $instructorService
    ) {}

    /**
     * Assign an instructor to a course.
     * Called from course create/edit modal.
     */
    public function store(Request $request, Program $program, Course $course)
    {
        $validated = $request->validate([
            'instructor_id' => [
                'required',
                'exists:users,id',
                Rule::unique('course_instructors', 'user_id')
                    ->where('course_id', $course->id)
                    ->whereNull('removed_at'),
            ],
        ]);

        $user = User::findOrFail($validated['instructor_id']);

        // Verify user is an instructor
        if (! $user->isInstructor()) {
            return back()->with('error', 'Selected user must be an instructor.');
        }

        try {
            $result = $this->executeInTransaction(
                operation: function () use ($course, $user) {
                    return $this->instructorService->assignInstructor($course, $user);
                },
                successMessage: "Instructor '{$user->name}' assigned successfully.",
                errorMessage: 'Failed to assign instructor',
                redirectRoute: fn () => redirect()->route('admin.programs.courses.show', [$program, $course]),
                logContext: ['course_id' => $course->id, 'instructor_id' => $validated['instructor_id']]
            );

            return $result;
        } catch (\Exception $e) {
            Log::error('Failed to assign instructor', [
                'error' => $e->getMessage(),
                'course_id' => $course->id,
                'instructor_id' => $validated['instructor_id'] ?? null,
            ]);

            return back()
                ->with('error', $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove an instructor from a course (soft removal).
     */
    public function destroy(Program $program, Course $course, CourseInstructor $instructor)
    {
        // Verify instructor belongs to this course
        if ($instructor->course_id !== $course->id) {
            return back()->with('error', 'Instructor not found for this course.');
        }

        $instructorName = $instructor->instructor->name;

        try {
            return $this->executeInTransaction(
                operation: function () use ($course, $instructor) {
                    return $this->instructorService->removeInstructor($course, $instructor);
                },
                successMessage: "Instructor '{$instructorName}' removed successfully.",
                errorMessage: 'Failed to remove instructor',
                redirectRoute: fn () => redirect()->route('admin.programs.courses.show', [$program, $course]),
                logContext: ['course_id' => $course->id, 'instructor_id' => $instructor->id]
            );
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Restore a previously removed instructor.
     */
    public function restore(Program $program, Course $course, CourseInstructor $instructor)
    {
        // Verify instructor belongs to this course
        if ($instructor->course_id !== $course->id) {
            return back()->with('error', 'Instructor not found for this course.');
        }

        try {
            return $this->executeInTransaction(
                operation: fn () => $this->instructorService->restoreInstructor($course, $instructor),
                successMessage: "Instructor '{$instructor->instructor->name}' restored successfully.",
                errorMessage: 'Failed to restore instructor',
                redirectRoute: fn () => redirect()->route('admin.programs.courses.show', [$program, $course]),
                logContext: ['course_id' => $course->id, 'instructor_id' => $instructor->id]
            );
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
