{{--
    Simplified Course Card Component

    A cleaner, more scannable course card component that emphasizes the most important
    information while maintaining all functionality.

    @props
    - course: Course model with relationships (instructors, modules, grade)
    - showInstructor: bool - Whether to show instructor info (default: true)
    - showGrade: bool - Whether to show grade information (default: true)

    @example
    <x-student.course-card-simple :course="$course" />
    <x-student.course-card-simple :course="$course" :showGrade="false" />
--}}

@props([
    'course',
    'showInstructor' => true,
    'showProgress' => true,
    'showGrade' => true,
    'showModules' => true,
])

@php
    // Support both Course model and array data (from ProgramProgressService)
    $isModel = $course instanceof \App\Models\Course;

    // Normalize data access - handle both model and array
    if ($isModel) {
        $statusLabel = $course->status_label ?? 'not_started';
        $courseCode = $course->course_code;
        $courseName = $course->name;
        $courseId = $course->id;
        $courseSemester = $course->semester ?? null;
        $courseCredits = $course->credits ?? null;
        $progressPercentage = $course->progress_percentage ?? 0;
        $completedLessons = $course->completed_lessons ?? 0;
        $totalLessons = $course->total_lessons ?? 0;
        $moduleCount = $course->relationLoaded('modules') ? $course->modules->count() : 0;

        // Get content counts from modules via items (same approach as student course detail page)
        $quizCount = 0;
        $assignmentCount = 0;
        $lessonCount = 0;

        if ($course->relationLoaded('modules') && $course->modules->isNotEmpty()) {
            // Load items with itemable if not already loaded (same as student course detail page)
            $moduleIds = $course->modules->pluck('id');
            if ($moduleIds->isNotEmpty()) {
                \App\Models\ModuleItem::whereIn('module_id', $moduleIds)
                    ->with('itemable')
                    ->get()
                    ->groupBy('module_id')
                    ->each(function ($items, $moduleId) use ($course) {
                        $module = $course->modules->firstWhere('id', $moduleId);
                        if ($module) {
                            $module->setRelation('items', $items);
                        }
                    });
            }
            
            // Count lessons from loaded items (exactly like student course detail page)
            $lessonCount = $course->modules->sum(function ($module) {
                return $module->items
                    ->where('itemable_type', \App\Models\ModuleLesson::class)
                    ->count();
            });
            
            // Count assignments from loaded items (exactly like student course detail page)
            $assignmentCount = $course->modules->sum(function ($module) {
                return $module->items
                    ->where('itemable_type', \App\Models\Assignment::class)
                    ->count();
            });
            
            // Count quizzes (non-exam quizzes only) from loaded items
            $quizCount = $course->modules->sum(function ($module) {
                return $module->items
                    ->where('itemable_type', \App\Models\Quiz::class)
                    ->filter(function ($item) {
                        if (!$item->itemable) {
                            return false;
                        }
                        $quiz = $item->itemable;
                        // Only count non-exam quizzes
                        return $quiz->assessment_type !== 'exam' && !$quiz->isExam();
                    })
                    ->count();
            });
        }

        // Get instructor name from relationship (simplified: one instructor per course)
        $instructorName = __('No instructor');
        if ($course->relationLoaded('instructors') && $course->instructors->isNotEmpty()) {
            $instructorAssignment = $course->instructors
                ->whereNull('removed_at')
                ->first();
            $instructorName = $instructorAssignment?->instructor?->name ?? __('No instructor');
        }

        // Get grade information from relationship
        $hasGrade = $course->grade && $course->grade->percentage;
        $gradePercentage = $hasGrade ? $course->grade->percentage : null;
        $letterGrade = $hasGrade ? ($course->grade->letter_grade ?? null) : null;
        // Get last accessed at for model (if available)
        $lastAccessedAt = null;
    } else {
        // Array data from ProgramProgressService or StudentDashboardService
        $statusLabel = $course['status'] ?? 'not_started';
        $courseCode = $course['course_code'] ?? '';
        $courseName = $course['name'] ?? '';
        $courseId = $course['id'] ?? 0;
        $courseSemester = $course['semester'] ?? null;
        $courseCredits = $course['credits'] ?? null;
        $progressPercentage = $course['progress_percentage'] ?? 0;
        $completedLessons = $course['completed_items'] ?? ($course['completed_lessons'] ?? 0);
        $totalLessons = $course['total_items'] ?? ($course['total_lessons'] ?? 0);
        $moduleCount = $course['module_count'] ?? 0;

        // Get content counts from array data
        $quizCount = $course['quiz_count'] ?? 0;
        $assignmentCount = $course['assignment_count'] ?? 0;
        $lessonCount = $course['lesson_count'] ?? 0;

        // Handle both naming conventions: instructor_name (program) and instructor (dashboard)
        $instructorName = $course['instructor_name'] ?? ($course['instructor'] ?? __('No instructor'));
        // Handle both naming conventions: grade_percentage (program) and current_grade (dashboard)
        $gradePercentage = $course['grade_percentage'] ?? ($course['current_grade'] ?? null);
        $hasGrade = !empty($gradePercentage);
        $letterGrade = $course['letter_grade'] ?? null;
        // Get last accessed at from array
        $lastAccessedAt = $course['last_accessed_at'] ?? null;
    }

    // Determine status and color mapping with enhanced visual distinction
    $statusConfig = match($statusLabel) {
        'completed' => [
            'color' => 'success',
            'bgColor' => 'bg-light-success',
            'textColor' => 'text-success',
            'borderColor' => 'border-success',
            'text' => __('Completed'),
            'icon' => 'check-circle',
            'btnVariant' => 'btn-light-success',
            'btnIcon' => 'eye',
            'btnText' => __('Review Course'),
        ],
        'in_progress' => [
            'color' => 'primary',
            'bgColor' => 'bg-light-primary',
            'textColor' => 'text-primary',
            'borderColor' => 'border-primary',
            'text' => __('In Progress'),
            'icon' => 'loading',
            'btnVariant' => 'btn-primary',
            'btnIcon' => 'arrow-right',
            'btnText' => __('Continue Learning'),
        ],
        default => [
            'color' => 'secondary',
            'bgColor' => 'bg-light-secondary',
            'textColor' => 'text-secondary',
            'borderColor' => 'border-secondary',
            'text' => __('Not Started'),
            'icon' => 'timer',
            'btnVariant' => 'btn-light-primary',
            'btnIcon' => 'rocket',
            'btnText' => __('Start Course'),
        ],
    };

    // Grade color mapping
    $gradeDisplay = $hasGrade ? number_format($gradePercentage, 0) . '%' : null;
    $gradeColor = 'text-muted';
    if ($hasGrade && $gradePercentage !== null) {
        $gradeColor = match(true) {
            $gradePercentage >= 90 => 'text-success',
            $gradePercentage >= 80 => 'text-info',
            $gradePercentage >= 70 => 'text-primary',
            $gradePercentage >= 60 => 'text-warning',
            default => 'text-danger',
        };
    }

    // Generate instructor initials for avatar
    $instructorInitials = collect(explode(' ', $instructorName))
        ->filter()
        ->take(2)
        ->map(fn($word) => strtoupper(substr($word, 0, 1)))
        ->join('');
@endphp

<article class="card card-flush h-100 hover-elevate-up course-card-simple" aria-label="{{ $courseName }}">
    {{-- Status Indicator Bar --}}
    <div class="position-relative h-6px {{ $statusConfig['bgColor'] }}"></div>

    {{-- Card Body --}}
    <div class="card-body pt-4 pb-5">
        {{-- Course Code and Status Badge --}}
        <div class="d-flex justify-content-between align-items-center mb-3">
            <span class="badge {{ $statusConfig['bgColor'] }} {{ $statusConfig['textColor'] }} fs-8 fw-bold px-3 py-2">
                {{ $courseCode }}
            </span>
            <span class="badge {{ $statusConfig['bgColor'] }} {{ $statusConfig['textColor'] }} fs-8 fw-bold px-3 py-2">
                {!! getIcon($statusConfig['icon'], 'fs-7 me-1') !!}
                {{ $statusConfig['text'] }}
            </span>
        </div>

        {{-- Course Title (Prominent) --}}
        <a href="{{ route('student.courses.show', $courseId) }}"
           class="text-gray-900 fw-bolder text-hover-primary fs-3 d-block mb-4 lh-sm"
           title="{{ $courseName }}">
            {{ Str::limit($courseName, 60) }}
        </a>

        {{-- Credits & Semester Badges --}}
        <div class="d-flex flex-wrap gap-2 mb-3">
            @if($courseSemester)
                <span class="badge badge-light fs-8 fw-bold">
                    {{ $courseSemester }}
                </span>
            @endif
            @if($courseCredits)
                <span class="badge badge-light fs-8 fw-bold">
                    {{ number_format($courseCredits, 1) }} {{ __('Credits') }}
                </span>
            @endif
        </div>

        {{-- Instructor with Initials Avatar --}}
        @if($showInstructor)
            <div class="d-flex align-items-center mb-3">
                <div class="symbol symbol-35px me-3">
                    <span class="symbol-label {{ $statusConfig['bgColor'] }} {{ $statusConfig['textColor'] }} fw-bold fs-6">
                        {{ $instructorInitials ?: '?' }}
                    </span>
                </div>
                <span class="text-gray-600 fs-6">{{ $instructorName }}</span>
            </div>
        @endif

        {{-- Last Activity --}}
        <div class="d-flex align-items-center mb-4">
            {!! getIcon('time', 'fs-7 text-gray-400 me-2') !!}
            @if($lastAccessedAt)
                <span class="text-muted fs-8">
                    {{ __('Last accessed') }} {{ $lastAccessedAt->diffForHumans() }}
                </span>
            @else
                <span class="text-muted fs-8 fst-italic">{{ __('Not yet started') }}</span>
            @endif
        </div>

        {{-- Module Count --}}
        @if($showModules)
            <div class="mb-4">
                <div class="d-flex align-items-center">
                    {!! getIcon('category', 'fs-5 me-2 text-gray-500') !!}
                    <span class="text-gray-600 fs-7">
                        {{ $moduleCount }} {{ trans_choice('module|modules', $moduleCount) }}
                    </span>
                </div>
            </div>
        @endif

        {{-- Progress Section (Prominent) --}}
        @if($showProgress)
        <div class="mb-4">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="text-gray-700 fw-semibold fs-6">{{ __('Progress') }}</span>
                <span class="fw-bold fs-5 {{ $statusConfig['textColor'] }}">
                    {{ round($progressPercentage) }}%
                </span>
            </div>
            <div class="progress h-10px bg-light rounded">
                <div class="progress-bar {{ $statusConfig['bgColor'] }} rounded"
                     role="progressbar"
                     style="width: {{ $progressPercentage }}%"
                     aria-valuenow="{{ $progressPercentage }}"
                     aria-valuemin="0"
                     aria-valuemax="100"
                     aria-label="{{ __('Course progress') }}">
                </div>
            </div>
            <div class="d-flex justify-content-between align-items-center mt-2">
                <span class="text-muted fs-7">
                    @if($totalLessons > 0)
                        {{ $completedLessons }}/{{ $totalLessons }} {{ __('items') }}
                    @else
                        <span class="fst-italic">{{ __('Content coming soon') }}</span>
                    @endif
                </span>
                @if($showGrade && $hasGrade)
                    <span class="fs-7 fw-bold {{ $gradeColor }}">
                        @if($letterGrade)
                            {{ $letterGrade }} ({{ $gradeDisplay }})
                        @else
                            {{ __('Grade') }}: {{ $gradeDisplay }}
                        @endif
                    </span>
                @endif
            </div>
        </div>
        @endif
    </div>

    {{-- Card Footer --}}
    <div class="card-footer pt-0 pb-4 border-0 bg-transparent">
        <a href="{{ route('student.courses.show', $courseId) }}"
           class="btn {{ $statusConfig['btnVariant'] }} w-100 fw-semibold py-3"
           aria-label="{{ $statusConfig['btnText'] }} - {{ $courseName }}">
            {!! getIcon($statusConfig['btnIcon'], 'fs-5 me-2') !!}
            {{ $statusConfig['btnText'] }}
        </a>
    </div>
</article>

@once
@push('styles')
<style>
.course-card-simple {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.course-card-simple:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.12) !important;
}

/* Progress bar styling */
.course-card-simple .progress {
    overflow: hidden;
}

.course-card-simple .progress-bar {
    transition: width 0.6s cubic-bezier(0.4, 0, 0.2, 1);
}

/* Enhanced status indicators */
.course-card-simple .badge {
    border-radius: 6px;
}

/* Button styling */
.course-card-simple .btn {
    border-radius: 8px;
    transition: all 0.2s ease;
}

.course-card-simple .btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

/* Mobile adjustments */
@media (max-width: 768px) {
    .course-card-simple .fs-3 {
        font-size: 1.25rem !important;
    }

    .course-card-simple .symbol-35px {
        width: 30px !important;
        height: 30px !important;
    }

    .course-card-simple .fs-6 {
        font-size: 0.875rem !important;
    }

    .course-card-simple .fs-7 {
        font-size: 0.75rem !important;
    }

    .course-card-simple .progress {
        height: 8px !important;
    }
}

/* Small mobile adjustments */
@media (max-width: 576px) {
    .course-card-simple .fs-3 {
        font-size: 1.1rem !important;
    }

    .course-card-simple .card-body {
        padding: 1rem !important;
    }

    .course-card-simple .mb-4 {
        margin-bottom: 1rem !important;
    }

    .course-card-simple .mb-3 {
        margin-bottom: 0.75rem !important;
    }
}
</style>
@endpush
@endonce
