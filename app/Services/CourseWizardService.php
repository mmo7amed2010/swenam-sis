<?php

namespace App\Services;

use App\Models\Course;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class CourseWizardService
{
    /**
     * Save wizard step data.
     *
     * @param  Course  $course  Course being created/edited
     * @param  int  $step  Step number (1-4)
     * @param  array  $data  Step data
     * @return Course Updated course
     *
     * @throws \InvalidArgumentException If step is invalid
     */
    public function saveStep(Course $course, int $step, array $data): Course
    {
        return match ($step) {
            1 => $this->saveStepOne($course, $data),
            2 => $this->saveStepTwo($course, $data),
            3 => $this->saveStepThree($course, $data),
            4 => $this->saveStepFour($course, $data),
            default => throw new \InvalidArgumentException("Invalid step: {$step}"),
        };
    }

    /**
     * Validate wizard step data.
     *
     * @param  int  $step  Step number
     * @param  array  $data  Data to validate
     * @return array Validation errors (empty if valid)
     */
    public function validateStep(int $step, array $data): array
    {
        $validator = Validator::make($data, $this->getStepRules($step));

        return $validator->fails() ? $validator->errors()->all() : [];
    }

    /**
     * Check if step is complete.
     *
     * @param  Course  $course  Course to check
     * @param  int  $step  Step number
     * @return bool True if step is complete
     */
    public function isStepComplete(Course $course, int $step): bool
    {
        return match ($step) {
            1 => $this->isStepOneComplete($course),
            2 => $this->isStepTwoComplete($course),
            3 => $this->isStepThreeComplete($course),
            4 => true, // Review step always accessible
            default => false,
        };
    }

    /**
     * Get validation rules for a wizard step.
     *
     * @param  int  $step  Step number
     * @return array Validation rules
     */
    private function getStepRules(int $step): array
    {
        return match ($step) {
            1 => [
                'course_code' => 'required|string|max:20',
                'name' => 'required|string|max:255',
                'department' => 'required|string',
                'program' => 'required|string',
            ],
            2 => [
                'semester' => 'required|string',
                'start_date' => 'required|date',
                'end_date' => 'required|date|after:start_date',
                'max_enrollment' => 'nullable|integer|min:1',
            ],
            3 => [
                'syllabus' => 'nullable|string',
                'prerequisites' => 'nullable|string|max:1000',
                'description' => 'nullable|string|max:1000',
            ],
            4 => [
                'action' => 'required|in:publish,save_draft',
            ],
            default => [],
        };
    }

    /**
     * Save Step 1: Basic course information.
     *
     * @param  Course  $course  Course to update (or create new)
     * @param  array  $data  Step 1 data
     * @return Course Updated course
     */
    private function saveStepOne(Course $course, array $data): Course
    {
        // If course doesn't exist yet, create it
        if (! $course->exists) {
            $course = Course::create([
                'course_code' => strtoupper($data['course_code']),
                'name' => $data['name'],
                'department' => $data['department'],
                'program' => $data['program'],
                'version' => 1,
                'status' => 'draft',
                'created_by_admin_id' => auth()->id(),
            ]);

            // Store course ID in session for wizard steps
            session()->put('course_wizard.course_id', $course->id);
            session()->put('course_wizard.step', 1);

            Log::info('Course wizard Step 1 completed', [
                'course_id' => $course->id,
                'course_code' => $course->course_code,
                'user_id' => auth()->id(),
            ]);
        } else {
            // Update existing course
            $course->update([
                'course_code' => strtoupper($data['course_code']),
                'name' => $data['name'],
                'department' => $data['department'],
                'program' => $data['program'],
            ]);

            session()->put('course_wizard.step', 1);
        }

        return $course->fresh();
    }

    /**
     * Save Step 2: Schedule settings.
     *
     * @param  Course  $course  Course to update
     * @param  array  $data  Step 2 data
     * @return Course Updated course
     */
    private function saveStepTwo(Course $course, array $data): Course
    {
        // Calculate duration in weeks
        $startDate = Carbon::parse($data['start_date']);
        $endDate = Carbon::parse($data['end_date']);
        $durationWeeks = $startDate->diffInWeeks($endDate);

        // Update course with Step 2 data
        $course->update([
            'semester' => $data['semester'],
            'start_date' => $data['start_date'],
            'end_date' => $data['end_date'],
            'max_enrollment' => $data['max_enrollment'] ?? null,
            'duration_weeks' => $durationWeeks,
        ]);

        // Update wizard session
        session()->put('course_wizard.step', 2);

        Log::info('Course wizard Step 2 completed', [
            'course_id' => $course->id,
            'course_code' => $course->course_code,
            'semester' => $course->semester,
            'duration_weeks' => $durationWeeks,
            'user_id' => auth()->id(),
        ]);

        return $course->fresh();
    }

    /**
     * Save Step 3: Content settings.
     *
     * @param  Course  $course  Course to update
     * @param  array  $data  Step 3 data
     * @return Course Updated course
     */
    private function saveStepThree(Course $course, array $data): Course
    {
        // Update course with Step 3 data
        $course->update([
            'syllabus' => $data['syllabus'] ?? null,
            'prerequisites' => $data['prerequisites'] ?? null,
            'description' => $data['description'] ?? null,
        ]);

        // Update wizard session
        session()->put('course_wizard.step', 3);

        Log::info('Course wizard Step 3 completed', [
            'course_id' => $course->id,
            'course_code' => $course->course_code,
            'user_id' => auth()->id(),
        ]);

        return $course->fresh();
    }

    /**
     * Save Step 4: Review and finalize.
     *
     * @param  Course  $course  Course to finalize
     * @param  array  $data  Step 4 data
     * @return Course Updated course
     */
    private function saveStepFour(Course $course, array $data): Course
    {
        $action = $data['action'] ?? 'save_draft';

        if ($action === 'publish') {
            // Publish the course (sets status to active)
            $course->update([
                'status' => 'active',
                'published_at' => now(),
            ]);

            Log::info('Course published via wizard', [
                'course_id' => $course->id,
                'course_code' => $course->course_code,
                'user_id' => auth()->id(),
            ]);
        } else {
            // Save as draft
            Log::info('Course saved as draft from wizard', [
                'course_id' => $course->id,
                'course_code' => $course->course_code,
                'user_id' => auth()->id(),
            ]);
        }

        // Clear wizard session
        session()->forget('course_wizard');

        return $course->fresh();
    }

    /**
     * Check if Step 1 is complete.
     *
     * @param  Course  $course  Course to check
     * @return bool True if step is complete
     */
    private function isStepOneComplete(Course $course): bool
    {
        return ! empty($course->course_code) && ! empty($course->name);
    }

    /**
     * Check if Step 2 is complete.
     *
     * @param  Course  $course  Course to check
     * @return bool True if step is complete
     */
    private function isStepTwoComplete(Course $course): bool
    {
        return ! empty($course->semester) && ! empty($course->start_date) && ! empty($course->end_date);
    }

    /**
     * Check if Step 3 is complete.
     *
     * @param  Course  $course  Course to check
     * @return bool True if step is complete
     */
    private function isStepThreeComplete(Course $course): bool
    {
        // Step 3 is optional, so it's always considered complete if we reach it
        return true;
    }
}
