<?php

namespace App\Services;

use App\Mail\GradePublishedMail;
use App\Models\Assignment;
use App\Models\Grade;
use App\Models\Submission;
use Illuminate\Support\Facades\Mail;

class AssignmentGradingService
{
    
    /**
     * Grade a submission (create or update grade).
     *
     * @param  Submission  $submission  Submission to grade
     * @param  array  $gradeData  Grade data (points_awarded, feedback, etc.)
     * @param  Assignment  $assignment  Assignment the submission belongs to
     * @param  bool  $isPublish  Whether to publish the grade immediately
     * @return Grade Created or updated grade
     */
    public function gradeSubmission(Submission $submission, array $gradeData, Assignment $assignment, bool $isPublish): Grade
    {
        // Get existing grade or create new one
        $existingGrade = $submission->grades()->latest('version')->first();

        // Calculate max points (simplified for self-paced courses)
        $maxPoints = $assignment->total_points ?? $assignment->max_points ?? 100;

        // Get points awarded
        $pointsAwarded = $gradeData['points_awarded'];

        // Determine version number
        $version = $existingGrade ? ($existingGrade->version + 1) : 1;

        // Create grade
        $grade = Grade::create([
            'submission_id' => $submission->id,
            'points_awarded' => $pointsAwarded,
            'max_points' => $maxPoints,
            'feedback' => $gradeData['feedback'] ?? null,
            'rubric_scores' => $gradeData['rubric_scores'] ?? null,
            'graded_by_user_id' => auth()->id(),
            'graded_at' => now(),
            'version' => $version,
            'is_published' => $isPublish,
            'published_at' => $isPublish ? now() : null,
        ]);

        // Update submission status if published
        if ($isPublish) {
            $submission->status = 'graded';
            $submission->save();

            // Send email to student
            $submission->load(['student', 'assignment.course']);
            Mail::to($submission->student->email)
                ->queue(new GradePublishedMail(
                    $grade,
                    $submission,
                    $assignment,
                    $submission->student,
                    auth()->user()
                ));
        }

        return $grade;
    }

    /**
     * Publish a grade (mark as published and notify student).
     *
     * @param  Grade  $grade  Grade to publish
     * @return Grade Published grade
     */
    public function publishGrade(Grade $grade): Grade
    {
        if ($grade->is_published) {
            return $grade;
        }

        $grade->update([
            'is_published' => true,
            'published_at' => now(),
        ]);

        $submission = $grade->submission;
        $submission->status = 'graded';
        $submission->save();

        // Send email to student
        $submission->load(['student', 'assignment.course']);
        $assignment = $submission->assignment;

        Mail::to($submission->student->email)
            ->queue(new GradePublishedMail(
                $grade,
                $submission,
                $assignment,
                $submission->student,
                auth()->user()
            ));

        return $grade->fresh();
    }

    /**
     * Calculate late penalty for a submission.
     *
     * Note: For self-paced online courses, there are no due dates or late penalties.
     * This method returns 0 as all assignments are open-ended.
     *
     * @param  Submission  $submission  Submission to check
     * @param  Assignment  $assignment  Assignment the submission belongs to
     * @return float Late penalty percentage (0-100), always 0 for self-paced courses
     */
    public function calculateLatePenalty(Submission $submission, Assignment $assignment): float
    {
        // Self-paced online courses don't have due dates or late penalties
        // All submissions are accepted at full value
        return 0.0;
    }

    /**
     * Get navigation context for a submission (previous/next submissions).
     *
     * @param  Submission  $submission  Current submission
     * @param  Assignment  $assignment  Assignment the submission belongs to
     * @return array Navigation context with previous_submission_id and next_submission_id
     */
    public function getNavigationContext(Submission $submission, Assignment $assignment): array
    {
        // Get all submissions for navigation
        $allSubmissions = Submission::where('assignment_id', $assignment->id)
            ->where('status', 'submitted')
            ->orderBy('submitted_at', 'asc')
            ->pluck('id')
            ->values()
            ->toArray();

        $currentIndex = array_search($submission->id, $allSubmissions);

        // Handle case where submission not found in array
        if ($currentIndex === false) {
            return [
                'previous_submission_id' => null,
                'next_submission_id' => null,
            ];
        }

        return [
            'previous_submission_id' => $currentIndex > 0 ? $allSubmissions[$currentIndex - 1] : null,
            'next_submission_id' => $currentIndex < count($allSubmissions) - 1 ? $allSubmissions[$currentIndex + 1] : null,
        ];
    }
}
