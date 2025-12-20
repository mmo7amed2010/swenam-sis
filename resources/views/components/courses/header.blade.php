{{--
 * Course Header Card Component
 *
 * Shared header for course show pages (admin & instructor).
 * Displays course title, status, metadata and role-specific actions.
 *
 * @param \App\Models\Program $program
 * @param \App\Models\Course $course
 * @param string $context - 'admin' or 'instructor'
 * @param int|null $studentCount - Student count (instructor context)
 * @param int|null $pendingGrading - Pending grading count (instructor context)
--}}

@props([
    'program',
    'course',
    'context' => 'admin',
    'studentCount' => null,
    'pendingGrading' => null,
])

@php
    $statusGradients = [
        'active' => ['bg' => '#f0fdf4', 'icon' => 'linear-gradient(135deg, #10b981, #059669)'],
        'published' => ['bg' => '#eff6ff', 'icon' => 'linear-gradient(135deg, #3b82f6, #2563eb)'],
        'draft' => ['bg' => '#fefce8', 'icon' => 'linear-gradient(135deg, #eab308, #ca8a04)'],
        'archived' => ['bg' => '#f8fafc', 'icon' => 'linear-gradient(135deg, #94a3b8, #64748b)'],
    ];
    $gradient = $statusGradients[$course->status] ?? $statusGradients['draft'];
    $isAdmin = $context === 'admin';
    $isInstructor = $context === 'instructor';
@endphp

<div class="card mb-6 border-0 shadow-sm overflow-hidden">
    <div class="card-body p-0">
        <div class="d-flex flex-column flex-lg-row">
            {{-- Left: Course Icon & Info --}}
            <div class="d-flex align-items-center p-8 flex-grow-1" style="background: linear-gradient(135deg, {{ $gradient['bg'] }} 0%, #ffffff 100%);">
                {{-- Course Icon --}}
                <div class="symbol symbol-70px symbol-circle me-5" style="background: {{ $gradient['icon'] }};">
                    <span class="symbol-label bg-transparent">
                        {!! getIcon('book', 'fs-2x text-white') !!}
                    </span>
                </div>

                {{-- Course Title & Meta --}}
                <div class="d-flex flex-column">
                    <div class="d-flex align-items-center flex-wrap mb-2">
                        <h1 class="fs-2 fw-bold text-gray-900 me-3 mb-0">{{ $course->name }}</h1>
                        @if($course->version > 1)
                            <span class="badge badge-light-info fs-8 fw-semibold me-2">v{{ $course->version }}</span>
                        @endif
                        <x-tables.status-badge :status="$course->status" />
                    </div>
                    <div class="d-flex flex-wrap text-gray-500 fs-7">
                        <span class="me-4">
                            {!! getIcon('abstract-26', 'fs-7 me-1') !!}
                            {{ $course->course_code }}
                        </span>
                        {{-- Show student count for BOTH contexts --}}
                        <span class="me-4">
                            {!! getIcon('people', 'fs-7 me-1') !!}
                            {{ $studentCount ?? 0 }} {{ __('Students') }}
                        </span>
                        {{-- Show pending grading for BOTH contexts --}}
                        @if(($pendingGrading ?? 0) > 0)
                            <span class="text-danger me-4">
                                {!! getIcon('notification-bing', 'fs-7 me-1') !!}
                                {{ $pendingGrading }} {{ __('Pending Grading') }}
                            </span>
                        @endif
                        @if($isAdmin)
                            <span class="me-4">
                                {!! getIcon('calendar', 'fs-7 me-1') !!}
                                {{ __('Created') }}: {{ $course->created_at->format('M d, Y') }}
                            </span>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Right: Action Buttons --}}
            <div class="d-flex align-items-center justify-content-end p-6 border-start border-gray-200 bg-gray-50">
                <div class="d-flex flex-wrap gap-2">
                    {{-- Quick Links for BOTH contexts --}}
                    @php
                        $assignmentsRoute = $isAdmin
                            ? route('admin.programs.courses.assignments.index', [$program, $course])
                            : route('instructor.courses.assignments.index', [$program, $course]);
                        $quizzesRoute = $isAdmin
                            ? route('admin.programs.courses.quizzes.index', [$program, $course])
                            : route('instructor.courses.quizzes.index', [$program, $course]);
                        $studentsRoute = $isAdmin
                            ? route('admin.programs.courses.students.index', [$program, $course])
                            : route('instructor.courses.students.index', [$program, $course]);
                    @endphp

                    <a href="{{ $assignmentsRoute }}" class="btn btn-sm btn-flex btn-light-success">
                        {!! getIcon('notepad', 'fs-6 me-1') !!}
                        {{ __('Assignments') }}
                    </a>
                    <a href="{{ $quizzesRoute }}" class="btn btn-sm btn-flex btn-light-info">
                        {!! getIcon('question-2', 'fs-6 me-1') !!}
                        {{ __('Quizzes') }}
                    </a>
                    <a href="{{ $studentsRoute }}" class="btn btn-sm btn-flex btn-light-primary">
                        {!! getIcon('people', 'fs-6 me-1') !!}
                        {{ __('Students') }}
                    </a>

                    {{-- Admin-only Actions (Edit, Actions dropdown) --}}
                    @if($isAdmin)
                        <a href="{{ route('admin.programs.courses.edit', [$program, $course]) }}" class="btn btn-sm btn-flex btn-primary">
                            {!! getIcon('pencil', 'fs-6 me-1') !!}
                            {{ __('Edit') }}
                        </a>
                        @if($course->status === 'draft')
                            <x-actions.dropdown buttonText="{{ __('Actions') }}" buttonClass="btn-sm btn-light" buttonIcon="down">
                                <x-actions.form-button
                                    action="{{ route('admin.programs.courses.publish', [$program, $course]) }}"
                                    permission="publish courses"
                                    confirm="{{ __('Publish this course?') }}"
                                    icon="rocket"
                                >
                                    {{ __('Publish Course') }}
                                </x-actions.form-button>

                                <x-actions.separator />
                                <x-actions.form-button
                                    action="{{ route('admin.programs.courses.destroy', [$program, $course]) }}"
                                    method="DELETE"
                                    permission="delete courses"
                                    confirm="{{ __('Delete this course permanently?') }}"
                                    icon="trash"
                                    :danger="true"
                                >
                                    {{ __('Delete Course') }}
                                </x-actions.form-button>
                            </x-actions.dropdown>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
