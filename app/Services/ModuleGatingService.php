<?php

namespace App\Services;

use App\Models\CourseModule;
use App\Models\ModuleProgress;
use App\Models\Quiz;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

/**
 * Service for handling module gating logic.
 *
 * Manages module accessibility based on exam pass requirements.
 * Handles primary and retake exam flow with single attempt per exam.
 */
class ModuleGatingService
{
    /**
     * Check if a module is accessible to a student.
     *
     * This checks ALL preceding modules with gating enabled, not just the immediate previous one.
     * A student must pass exams in all gated modules before accessing subsequent modules.
     *
     * @return array ['accessible' => bool, 'reason' => string|null, 'blocking_module' => CourseModule|null]
     */
    public function isModuleAccessible(User $student, CourseModule $module): array
    {
        // First module is always accessible
        if ($module->isFirstModule()) {
            return ['accessible' => true, 'reason' => null, 'blocking_module' => null];
        }

        // Get all preceding modules that require exam pass
        $precedingGatedModules = $this->getPrecedingGatedModules($module);

        // If no preceding modules require exam pass, this module is accessible
        if ($precedingGatedModules->isEmpty()) {
            return ['accessible' => true, 'reason' => null, 'blocking_module' => null];
        }

        // Check each preceding gated module - student must pass ALL of them
        foreach ($precedingGatedModules as $gatedModule) {
            $progress = ModuleProgress::where('module_id', $gatedModule->id)
                ->where('student_id', $student->id)
                ->first();

            // No progress yet - must complete exam
            if (! $progress) {
                return [
                    'accessible' => false,
                    'reason' => __('You must complete the exam in ":module" to access this module.', [
                        'module' => $gatedModule->title,
                    ]),
                    'blocking_module' => $gatedModule,
                ];
            }

            // Check if blocked (both exams failed)
            if ($progress->isBlockedFromProgression()) {
                return [
                    'accessible' => false,
                    'reason' => __('You have failed both exams in ":module". Please contact your instructor.', [
                        'module' => $gatedModule->title,
                    ]),
                    'blocking_module' => $gatedModule,
                ];
            }

            // Student hasn't passed this gated module yet
            if (! $progress->hasPassedAnyExam()) {
                return [
                    'accessible' => false,
                    'reason' => __('You must pass the exam in ":module" to access this module.', [
                        'module' => $gatedModule->title,
                    ]),
                    'blocking_module' => $gatedModule,
                ];
            }
        }

        // All preceding gated modules passed
        return ['accessible' => true, 'reason' => null, 'blocking_module' => null];
    }

    /**
     * Get all preceding modules that actually require exam pass.
     * A module requires exam pass only if:
     * 1. requires_exam_pass flag is true AND
     * 2. The module has a primary exam
     *
     * @return Collection<CourseModule>
     */
    protected function getPrecedingGatedModules(CourseModule $module): Collection
    {
        return CourseModule::where('course_id', $module->course_id)
            ->where('order_index', '<', $module->order_index)
            ->where('requires_exam_pass', true)
            ->where('status', 'published')
            ->orderBy('order_index')
            ->get()
            ->filter(fn ($m) => $m->requiresExamToUnlockNext());
    }

    /**
     * Get all modules for a course with accessibility info for a student.
     *
     * @return Collection Modules with is_accessible and lock_reason attributes
     */
    public function getAccessibleModules(User $student, int $courseId): Collection
    {
        $modules = CourseModule::where('course_id', $courseId)
            ->where('status', 'published')
            ->orderBy('order_index')
            ->get();

        return $modules->map(function ($module) use ($student) {
            $accessibility = $this->isModuleAccessible($student, $module);
            $module->is_accessible = $accessibility['accessible'];
            $module->lock_reason = $accessibility['reason'];
            $module->locked_by_module = $accessibility['blocking_module'];

            return $module;
        });
    }

    /**
     * Check if a retake exam should be visible to a student.
     * Legacy method - now delegates to isExamVisible for backward compatibility.
     */
    public function isRetakeExamVisible(User $student, Quiz $retakeExam): bool
    {
        return $this->isExamVisible($student, $retakeExam);
    }

    /**
     * Check if an exam should be visible to a student.
     *
     * Exams in a module are shown sequentially:
     * - First exam (by order) is always visible
     * - Subsequent exams are only visible after failing the previous one
     */
    public function isExamVisible(User $student, Quiz $exam): bool
    {
        // Only applies to module-level exams
        if (! $exam->isExam() || ! $exam->isModuleLevel()) {
            return true;
        }

        $module = $exam->module;
        if (! $module) {
            return true;
        }

        // Get all module-level exams in this module, ordered by their position in module_items
        $moduleExams = $this->getOrderedModuleExams($module);

        if ($moduleExams->isEmpty()) {
            return true;
        }

        // Find the position of this exam in the ordered list
        $examPosition = $moduleExams->search(fn ($e) => $e->id === $exam->id);

        // First exam is always visible
        if ($examPosition === 0 || $examPosition === false) {
            return true;
        }

        // For subsequent exams, check if student failed the previous exam
        $previousExam = $moduleExams->get($examPosition - 1);
        if (! $previousExam) {
            return true;
        }

        // Check if student has failed the previous exam
        return $this->hasFailedExam($student, $previousExam);
    }

    /**
     * Get all module-level exams in a module, ordered by their position.
     */
    protected function getOrderedModuleExams(CourseModule $module): Collection
    {
        // Get exams through module_items to respect ordering
        $moduleItems = $module->items()
            ->where('itemable_type', 'App\\Models\\Quiz')
            ->orderBy('order_position')
            ->with('itemable')
            ->get();

        return $moduleItems
            ->map(fn ($item) => $item->itemable)
            ->filter(fn ($quiz) => $quiz && $quiz->isExam() && $quiz->isModuleLevel())
            ->values();
    }

    /**
     * Check if a student has failed a specific exam.
     */
    protected function hasFailedExam(User $student, Quiz $exam): bool
    {
        $attempt = $exam->attempts()
            ->where('student_id', $student->id)
            ->whereIn('status', ['submitted', 'graded'])
            ->orderBy('created_at', 'desc')
            ->first();

        if (! $attempt) {
            return false; // No attempt yet
        }

        // Check if the score is below passing
        $passingScore = $exam->passing_score ?? 0;
        $totalPoints = $exam->total_points ?? 100;

        if ($totalPoints <= 0) {
            return false;
        }

        $percentage = ($attempt->score / $totalPoints) * 100;

        return $percentage < $passingScore;
    }

    /**
     * Handle exam result (pass or fail) for primary or retake exam.
     */
    public function handleExamResult(User $student, Quiz $exam, bool $passed, float $score): void
    {
        $module = $exam->module;
        if (! $module) {
            Log::warning('Exam has no module', ['exam_id' => $exam->id]);

            return;
        }

        $progress = ModuleProgress::firstOrCreate(
            ['module_id' => $module->id, 'student_id' => $student->id],
            ['status' => ModuleProgress::STATUS_IN_PROGRESS]
        );

        if ($exam->isRetakeExam()) {
            $this->handleRetakeExamResult($progress, $passed, $score, $exam);
        } else {
            $this->handlePrimaryExamResult($progress, $passed, $score, $exam);
        }

        Log::info('Exam result processed', [
            'student_id' => $student->id,
            'exam_id' => $exam->id,
            'is_retake' => $exam->isRetakeExam(),
            'passed' => $passed,
            'score' => $score,
            'module_id' => $module->id,
        ]);
    }

    /**
     * Handle primary exam result.
     */
    protected function handlePrimaryExamResult(ModuleProgress $progress, bool $passed, float $score, Quiz $exam): void
    {
        if ($passed) {
            $progress->markExamPassed($score);
        } else {
            // Mark primary as failed and unlock retake
            $progress->markPrimaryExamFailed($score);
        }
    }

    /**
     * Handle retake exam result.
     */
    protected function handleRetakeExamResult(ModuleProgress $progress, bool $passed, float $score, Quiz $exam): void
    {
        if ($passed) {
            $progress->markRetakeExamPassed($score);
        } else {
            // Block progression - both exams failed
            $progress->markRetakeExamFailed($score);
        }
    }

    /**
     * Check if student can take a specific exam.
     *
     * Sequential exam logic:
     * - If student passed any exam in module → can't take any more exams
     * - If student already attempted this specific exam → can't take it again
     * - If exam is not visible (previous exam not failed) → can't take it
     *
     * @return array ['can_take' => bool, 'reason' => string|null]
     */
    public function canTakeExam(User $student, Quiz $exam): array
    {
        $module = $exam->module;
        if (! $module) {
            return ['can_take' => true, 'reason' => null];
        }

        $progress = ModuleProgress::where('module_id', $module->id)
            ->where('student_id', $student->id)
            ->first();

        // If student already passed any exam in this module, can't take more exams
        if ($progress && $progress->hasPassedAnyExam()) {
            return [
                'can_take' => false,
                'reason' => __('You have already passed an exam in this module.'),
            ];
        }

        // Check if student already attempted THIS specific exam (each exam has 1 attempt)
        $existingAttempt = $exam->attempts()
            ->where('student_id', $student->id)
            ->whereIn('status', ['submitted', 'graded'])
            ->exists();

        if ($existingAttempt) {
            return [
                'can_take' => false,
                'reason' => __('You have already taken this exam. Check if the next exam is available.'),
            ];
        }

        // Check if this exam is visible (sequential visibility - must fail previous exam first)
        if (! $this->isExamVisible($student, $exam)) {
            return [
                'can_take' => false,
                'reason' => __('This exam is not available yet. You must complete the previous exam first.'),
            ];
        }

        return ['can_take' => true, 'reason' => null];
    }
}
