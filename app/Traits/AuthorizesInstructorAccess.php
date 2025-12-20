<?php

namespace App\Traits;

use App\Models\Course;
use App\Models\Program;
use Illuminate\Support\Facades\Auth;

/**
 * Trait for authorizing instructor access to courses.
 *
 * Ensures instructors can only access courses they are assigned to.
 */
trait AuthorizesInstructorAccess
{
    /**
     * Authorize that the current user is assigned to the given course.
     *
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     */
    protected function authorizeInstructorCourse(Course $course): void
    {
        $user = Auth::user();

        if (! $course->hasInstructor($user) && ! $user->isAdmin()) {
            abort(403, 'You are not authorized to access this course.');
        }
    }

    /**
     * Authorize and verify course belongs to program.
     *
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     */
    protected function authorizeAndVerifyCourse(Program $program, Course $course): void
    {
        $this->authorizeInstructorCourse($course);

        if ($course->program_id !== $program->id) {
            abort(404, 'Course not found for this program.');
        }
    }
}
