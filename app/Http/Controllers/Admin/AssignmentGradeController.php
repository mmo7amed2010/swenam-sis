<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\GradeSubmissionRequest;
use App\Models\Assignment;
use App\Models\Course;
use App\Models\Submission;
use App\Services\AssignmentGradingService;
use App\Traits\HandlesTransactions;
use Illuminate\Support\Facades\Storage;

class AssignmentGradeController extends Controller
{
    use HandlesTransactions;

    public function __construct(
        private AssignmentGradingService $gradingService
    ) {}

    /**
     * Show the grading interface for a submission (Story 3.7 AC #1, #3, #6).
     */
    public function show(\App\Models\Program $program, Course $course, Assignment $assignment, Submission $submission)
    {
        // Verify submission belongs to this assignment
        if ($submission->assignment_id !== $assignment->id) {
            abort(404, 'Submission not found for this assignment.');
        }

        // Verify assignment belongs to this course
        if ($assignment->course_id !== $course->id) {
            abort(404, 'Assignment not found for this course.');
        }

        // Load relationships
        $submission->load(['student', 'assignment.course', 'grades.grader']);
        $assignment->load(['course.program']);

        // Get existing grade (draft or published)
        $existingGrade = $submission->grades()->latest('version')->first();

        // Get previous submissions for this student (Story 3.7 AC #1)
        $previousSubmissions = Submission::where('assignment_id', $assignment->id)
            ->where('user_id', $submission->user_id)
            ->where('id', '!=', $submission->id)
            ->orderBy('submitted_at', 'desc')
            ->with('grades')
            ->get();

        // Get navigation context (Story 3.7 AC #6)
        $navigationContext = $this->gradingService->getNavigationContext($submission, $assignment);

        // Calculate late penalty (Story 3.7 AC #8)
        $latePenalty = $this->gradingService->calculateLatePenalty($submission, $assignment);

        // Calculate max points after late penalty
        $maxPoints = $assignment->total_points ?? $assignment->max_points ?? 100;
        $maxPointsAfterPenalty = $maxPoints;
        if ($latePenalty > 0) {
            $maxPointsAfterPenalty = $maxPoints * (1 - ($latePenalty / 100));
        }

        return view('pages.admin.courses.assignments.grade', [
            'program' => $program,
            'course' => $course,
            'assignment' => $assignment,
            'submission' => $submission,
            'existingGrade' => $existingGrade,
            'previousSubmissions' => $previousSubmissions,
            'previousSubmissionId' => $navigationContext['previous_submission_id'],
            'nextSubmissionId' => $navigationContext['next_submission_id'],
            'latePenalty' => $latePenalty,
            'maxPoints' => $maxPoints,
            'maxPointsAfterPenalty' => $maxPointsAfterPenalty,
        ]);
    }

    /**
     * Store or update a grade (Story 3.7 AC #2, #4, #5, #7, #8).
     */
    public function store(GradeSubmissionRequest $request, \App\Models\Program $program, Course $course, Assignment $assignment, Submission $submission)
    {
        // Verify submission belongs to this assignment
        if ($submission->assignment_id !== $assignment->id) {
            abort(404, 'Submission not found for this assignment.');
        }

        // Verify assignment belongs to this course
        if ($assignment->course_id !== $course->id) {
            abort(404, 'Assignment not found for this course.');
        }

        $isPublish = $request->input('action') === 'publish';
        $message = $isPublish
            ? 'Grade published successfully! Student has been notified via email.'
            : 'Grade saved as draft.';

        return $this->executeInTransaction(
            operation: fn () => $this->gradingService->gradeSubmission(
                $submission,
                $request->validated(),
                $assignment,
                $isPublish
            ),
            successMessage: $message,
            errorMessage: 'Failed to save grade',
            redirectRoute: fn () => redirect()->route('admin.programs.courses.assignments.grade', [$program, $course, $assignment, $submission]),
            logContext: ['submission_id' => $submission->id]
        );
    }

    /**
     * Preview a submission file (Story 3.7 AC #3).
     */
    public function previewFile(\App\Models\Program $program, Course $course, Assignment $assignment, Submission $submission)
    {
        // Verify submission belongs to this assignment
        if ($submission->assignment_id !== $assignment->id) {
            abort(404, 'Submission not found for this assignment.');
        }

        if (! $submission->file_path || ! Storage::disk('public')->exists($submission->file_path)) {
            abort(404, 'File not found.');
        }

        $filePath = Storage::disk('public')->path($submission->file_path);
        $mimeType = Storage::disk('public')->mimeType($submission->file_path);

        return response()->file($filePath, [
            'Content-Type' => $mimeType,
        ]);
    }

    /**
     * Download a submission file (Story 3.7 AC #3).
     */
    public function downloadFile(\App\Models\Program $program, Course $course, Assignment $assignment, Submission $submission)
    {
        // Verify submission belongs to this assignment
        if ($submission->assignment_id !== $assignment->id) {
            abort(404, 'Submission not found for this assignment.');
        }

        if (! $submission->file_path || ! Storage::disk('public')->exists($submission->file_path)) {
            abort(404, 'File not found.');
        }

        $fileName = $submission->file_name ?? 'submission-'.$submission->id;

        return Storage::disk('public')->download($submission->file_path, $fileName);
    }
}
