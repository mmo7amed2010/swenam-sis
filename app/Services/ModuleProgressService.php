<?php

namespace App\Services;

use App\Models\CourseModule;
use App\Models\ModuleProgress;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class ModuleProgressService
{
    /**
     * Get or create a module progress record for a student.
     */
    public function getOrCreateProgress(User $student, CourseModule $module): ModuleProgress
    {
        return ModuleProgress::firstOrCreate(
            ['student_id' => $student->id, 'module_id' => $module->id],
            ['status' => ModuleProgress::STATUS_NOT_STARTED]
        );
    }

    /**
     * Check if a student can start a module exam.
     */
    public function canStartExam(User $student, CourseModule $module): array
    {
        $progress = $this->getOrCreateProgress($student, $module);
        $exam = $module->exam;

        if (! $exam) {
            return ['can_start' => false, 'reason' => 'No exam configured for this module'];
        }

        if (! $exam->published) {
            return ['can_start' => false, 'reason' => 'Exam is not published'];
        }

        if ($progress->status === ModuleProgress::STATUS_COMPLETED) {
            return ['can_start' => false, 'reason' => 'You have already passed this module exam'];
        }

        if ($progress->exam_attempts_used >= 2) {
            return ['can_start' => false, 'reason' => 'You have used all attempts for this exam'];
        }

        if ($exam->isOverdue()) {
            return ['can_start' => false, 'reason' => 'The exam deadline has passed'];
        }

        return ['can_start' => true];
    }

    /**
     * Start a module exam attempt for a student.
     */
    public function startExam(User $student, CourseModule $module): ModuleProgress
    {
        $progress = $this->getOrCreateProgress($student, $module);

        if ($progress->status === ModuleProgress::STATUS_NOT_STARTED) {
            $progress->start();
        }

        return $progress;
    }

    /**
     * Process exam results and update progress.
     */
    public function processExamResult(User $student, CourseModule $module, float $score, array $answers): ModuleProgress
    {
        $progress = $this->getOrCreateProgress($student, $module);
        $exam = $module->exam;

        if (! $exam) {
            throw new \Exception('No exam found for this module');
        }

        // Calculate percentage
        $percentage = $exam->total_points > 0 ? ($score / $exam->total_points) * 100 : 0;

        if ($exam->isPassingScore($score)) {
            // Student passed the exam
            $progress->markExamPassed($score);

            // Log successful completion
            Log::info('Module exam passed', [
                'student_id' => $student->id,
                'module_id' => $module->id,
                'score' => $score,
                'percentage' => $percentage,
                'attempts_used' => $progress->exam_attempts_used + 1,
            ]);

            // Trigger next module unlock (to be implemented in Story 4.17)
            $this->unlockNextModule($student, $module);
        } else {
            // Student failed the exam
            $progress->markExamFailed($score);

            // Log failed attempt
            Log::info('Module exam failed', [
                'student_id' => $student->id,
                'module_id' => $module->id,
                'score' => $score,
                'percentage' => $percentage,
                'attempts_used' => $progress->exam_attempts_used,
                'remaining_attempts' => $progress->getRemainingExamAttempts(),
            ]);
        }

        return $progress;
    }

    /**
     * Get module progress for a course.
     */
    public function getCourseProgress(User $student, int $courseId): array
    {
        $modules = CourseModule::where('course_id', $courseId)
            ->with('exam')
            ->orderBy('order_number')
            ->get();

        $progress = ModuleProgress::where('student_id', $student->id)
            ->whereIn('module_id', $modules->pluck('id'))
            ->with('module.exam')
            ->get()
            ->keyBy('module_id');

        $totalModules = $modules->count();
        $completedModules = 0;
        $overallProgress = [];

        foreach ($modules as $module) {
            $moduleProgress = $progress->get($module->id);
            $progressPercentage = $moduleProgress ? $moduleProgress->getProgressPercentage() : 0;

            $overallProgress[] = [
                'module' => $module,
                'progress' => $moduleProgress,
                'percentage' => $progressPercentage,
                'status' => $moduleProgress ? $moduleProgress->getStatusLabel() : 'Not Started',
                'status_color' => $moduleProgress ? $moduleProgress->getStatusColor() : 'secondary',
                'has_exam' => $module->exam !== null,
                'exam_attempts_used' => $moduleProgress ? $moduleProgress->exam_attempts_used : 0,
                'exam_best_score' => $moduleProgress ? $moduleProgress->exam_best_score : null,
            ];

            if ($moduleProgress && $moduleProgress->status === ModuleProgress::STATUS_COMPLETED) {
                $completedModules++;
            }
        }

        $overallPercentage = $totalModules > 0 ? ($completedModules / $totalModules) * 100 : 0;

        return [
            'modules' => $overallProgress,
            'total_modules' => $totalModules,
            'completed_modules' => $completedModules,
            'overall_percentage' => $overallPercentage,
        ];
    }

    /**
     * Unlock next module after passing current one (Story 4.17).
     */
    private function unlockNextModule(User $student, CourseModule $completedModule): void
    {
        // Get the next module in the course
        $nextModule = CourseModule::where('course_id', $completedModule->course_id)
            ->where('order_number', '>', $completedModule->order_number)
            ->orderBy('order_number')
            ->first();

        if ($nextModule) {
            // Create or update progress for the next module
            $nextProgress = $this->getOrCreateProgress($student, $nextModule);

            // If the next module was not started, mark it as in progress
            if ($nextProgress->status === ModuleProgress::STATUS_NOT_STARTED) {
                $nextProgress->update([
                    'status' => ModuleProgress::STATUS_IN_PROGRESS,
                ]);
            }

            Log::info('Next module unlocked', [
                'student_id' => $student->id,
                'completed_module_id' => $completedModule->id,
                'unlocked_module_id' => $nextModule->id,
            ]);
        }
    }

    /**
     * Get students who need attention (failed exams, etc.).
     */
    public function getStudentsNeedingAttention(CourseModule $module): array
    {
        $progressRecords = ModuleProgress::where('module_id', $module->id)
            ->where('status', ModuleProgress::STATUS_EXAM_FAILED)
            ->with(['student', 'module.exam'])
            ->get();

        return $progressRecords->map(function ($progress) {
            return [
                'student' => $progress->student,
                'progress' => $progress,
                'exam_attempts_used' => $progress->exam_attempts_used,
                'exam_best_score' => $progress->exam_best_score,
                'last_attempt_date' => $progress->updated_at,
            ];
        })->toArray();
    }

    /**
     * Get module completion statistics.
     */
    public function getModuleStatistics(CourseModule $module): array
    {
        // Count students assigned to this course's program (via User.program_id)
        $programId = $module->course->program_id ?? $module->course->program?->id;
        $totalStudents = $programId
            ? \App\Models\User::where('program_id', $programId)
                ->where('user_type', 'student')
                ->count()
            : 0;

        $progressRecords = ModuleProgress::where('module_id', $module->id)
            ->get();

        $statistics = [
            'total_students' => $totalStudents,
            'not_started' => 0,
            'in_progress' => 0,
            'completed' => 0,
            'exam_failed' => 0,
            'exam_locked' => 0,
            'completion_rate' => 0,
            'pass_rate' => 0,
            'average_attempts' => 0,
        ];

        foreach ($progressRecords as $progress) {
            $statistics[$progress->status]++;

            if ($progress->exam_attempts_used > 0) {
                $statistics['average_attempts'] += $progress->exam_attempts_used;
            }
        }

        if ($progressRecords->isNotEmpty()) {
            $statistics['average_attempts'] = $statistics['average_attempts'] / $progressRecords->count();
            $statistics['completion_rate'] = ($statistics['completed'] / $totalStudents) * 100;

            $passedStudents = $progressRecords->where('status', ModuleProgress::STATUS_COMPLETED)->count();
            $attemptedStudents = $progressRecords->where('exam_attempts_used', '>', 0)->count();

            if ($attemptedStudents > 0) {
                $statistics['pass_rate'] = ($passedStudents / $attemptedStudents) * 100;
            }
        }

        return $statistics;
    }
}
