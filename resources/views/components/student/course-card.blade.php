{{--
    Course Card Component - Metronic Design System Aligned

    A reusable course card component for displaying course information
    in a consistent, accessible manner across the student portal.

    @props
    - course: Course model with relationships (instructors, modules, grade)
    - variant: 'default' | 'compact' - Card size variant
    - showInstructor: bool - Whether to show instructor info
    - showProgress: bool - Whether to show progress bar
    - showGrade: bool - Whether to show grade information
    - showModules: bool - Whether to show module/lesson counts

    @example
    <x-student.course-card :course="$course" />
    <x-student.course-card :course="$course" variant="compact" :showGrade="false" />
--}}

@props([
    'course',
    'variant' => 'default',
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
        // Handle both naming conventions: instructor_name (program) and instructor (dashboard)
        $instructorName = $course['instructor_name'] ?? ($course['instructor'] ?? __('No instructor'));
        // Handle both naming conventions: grade_percentage (program) and current_grade (dashboard)
        $gradePercentage = $course['grade_percentage'] ?? ($course['current_grade'] ?? null);
        $hasGrade = !empty($gradePercentage);
        $letterGrade = $course['letter_grade'] ?? null;
        // Get last accessed at from array
        $lastAccessedAt = $course['last_accessed_at'] ?? null;
    }

    // Determine status and color mapping (Metronic standard colors)
    $statusConfig = match($statusLabel) {
        'completed' => [
            'color' => 'success',
            'text' => __('Completed'),
            'icon' => 'check-circle',
            'btnVariant' => 'btn-light-success',
            'btnIcon' => 'eye',
            'btnText' => __('Review Course'),
        ],
        'in_progress' => [
            'color' => 'primary',
            'text' => __('In Progress'),
            'icon' => 'loading',
            'btnVariant' => 'btn-primary',
            'btnIcon' => 'arrow-right',
            'btnText' => __('Continue Learning'),
        ],
        default => [
            'color' => 'secondary',
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

    // Determine card height based on variant
    $headerHeight = $variant === 'compact' ? 'min-h-80px' : 'min-h-100px';

    // Generate instructor initials for avatar
    $instructorInitials = collect(explode(' ', $instructorName))
        ->filter()
        ->take(2)
        ->map(fn($word) => strtoupper(substr($word, 0, 1)))
        ->join('');
@endphp

<article class="card card-flush h-100 hover-elevate-up" aria-label="{{ $courseName }}">
    {{-- Card Header with Primary Background --}}
    <div class="card-header p-0 position-relative overflow-hidden border-0 {{ $headerHeight }} bg-primary">
        {{-- Status Badge --}}
        <div class="position-absolute top-0 end-0 mt-4 me-4">
            <span class="badge badge-light-{{ $statusConfig['color'] }} fs-8 fw-bold">
                {!! getIcon($statusConfig['icon'], 'fs-7 me-1') !!}
                {{ $statusConfig['text'] }}
            </span>
        </div>

        {{-- Course Code --}}
        <div class="position-absolute bottom-0 start-0 mb-4 ms-5">
            <span class="text-white fw-bold fs-3">{{ $courseCode }}</span>
        </div>

        {{-- Credits & Semester Badges --}}
        <div class="position-absolute bottom-0 end-0 mb-4 me-4 d-flex gap-2">
            @if($courseSemester)
                <span class="badge bg-white bg-opacity-25 text-white fs-9">{{ $courseSemester }}</span>
            @endif
            @if($courseCredits)
                <span class="badge badge-light fs-9 fw-bold">
                    {{ number_format($courseCredits, 1) }} {{ __('Credits') }}
                </span>
            @endif
        </div>
    </div>

    {{-- Card Body --}}
    <div class="card-body pt-5">
        {{-- Course Title --}}
        <a href="{{ route('student.courses.show', $courseId) }}"
           class="text-gray-900 fw-bold text-hover-primary fs-4 d-block mb-3"
           title="{{ $courseName }}">
            {{ Str::limit($courseName, 50) }}
        </a>

        {{-- Instructor with Initials Avatar --}}
        @if($showInstructor)
            <div class="d-flex align-items-center mb-3">
                <div class="symbol symbol-30px me-2">
                    <span class="symbol-label bg-light-{{ $statusConfig['color'] }} text-{{ $statusConfig['color'] }} fw-bold fs-7">
                        {{ $instructorInitials ?: '?' }}
                    </span>
                </div>
                <span class="text-gray-600 fs-7">{{ $instructorName }}</span>
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

        {{-- Module & Lesson Counts --}}
        @if($showModules)
            <div class="d-flex align-items-center justify-content-between mb-4">
                <div class="d-flex align-items-center">
                    {!! getIcon('category', 'fs-5 me-2 text-gray-500') !!}
                    <span class="text-gray-600 fs-7">
                        {{ $moduleCount }} {{ trans_choice('module|modules', $moduleCount) }}
                    </span>
                </div>
                <div class="d-flex align-items-center">
                    {!! getIcon('document', 'fs-5 me-2 text-gray-500') !!}
                    <span class="text-gray-600 fs-7">
                        {{ $totalLessons }} {{ trans_choice('lesson|lessons', $totalLessons) }}
                    </span>
                </div>
            </div>
        @endif

        {{-- Progress Section --}}
        @if($showProgress)
            <div class="separator separator-dashed my-4"></div>

            <div class="mb-4">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="text-gray-600 fw-semibold fs-7">{{ __('Progress') }}</span>
                    <span class="fw-bold fs-6 text-{{ $progressPercentage >= 100 ? 'success' : 'primary' }}">
                        {{ round($progressPercentage) }}%
                    </span>
                </div>
                <div class="progress h-8px bg-light-{{ $statusConfig['color'] }} rounded">
                    <div class="progress-bar bg-{{ $statusConfig['color'] }} rounded"
                         role="progressbar"
                         style="width: {{ $progressPercentage }}%"
                         aria-valuenow="{{ $progressPercentage }}"
                         aria-valuemin="0"
                         aria-valuemax="100"
                         aria-label="{{ __('Course progress') }}">
                    </div>
                </div>
                <div class="d-flex justify-content-between align-items-center mt-2">
                    <span class="text-muted fs-8">
                        {!! getIcon('book-open', 'fs-8 me-1') !!}
                        @if($totalLessons > 0)
                            {{ $completedLessons }}/{{ $totalLessons }} {{ __('items') }}
                        @else
                            <span class="fst-italic">{{ __('Content coming soon') }}</span>
                        @endif
                    </span>
                    @if($showGrade && $hasGrade)
                        <span class="fs-8 fw-bold {{ $gradeColor }}">
                            {!! getIcon('chart-line-up', 'fs-8 me-1') !!}
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
    <div class="card-footer pt-0 border-0">
        <a href="{{ route('student.courses.show', $courseId) }}"
           class="btn btn-sm {{ $statusConfig['btnVariant'] }} w-100"
           aria-label="{{ $statusConfig['btnText'] }} - {{ $courseName }}">
            {!! getIcon($statusConfig['btnIcon'], 'fs-5 me-2') !!}
            {{ $statusConfig['btnText'] }}
        </a>
    </div>
</article>
