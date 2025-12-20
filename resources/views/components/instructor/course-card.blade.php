{{--
    Instructor Course Card Component

    A clean course card for instructors matching the student course-card-simple style
    but displaying instructor-relevant data (modules, students, manage actions).

    @props
    - course: Course model with relationships (program, modules)

    @example
    <x-instructor.course-card :course="$course" />
--}}

@props([
    'course',
])

@php
    $courseCode = $course->course_code;
    $courseName = $course->name ?? $course->title;
    $courseId = $course->id;
    $programId = $course->program_id;
    $description = $course->description ?? '';

    // Get counts
    $moduleCount = $course->relationLoaded('modules') ? $course->modules->count() : 0;
    $studentCount = $course->student_count ?? 0;
    $pendingGradingCount = $course->pending_grading_count ?? 0;

    // Get program name if loaded
    $programName = $course->relationLoaded('program') && $course->program
        ? $course->program->name
        : null;

    // Generate course code initials for the symbol
    $codeInitials = strtoupper(substr($courseCode, 0, 2));

    // Use primary color scheme for consistency
    $statusConfig = [
        'color' => 'primary',
        'bgColor' => 'bg-light-primary',
        'textColor' => 'text-primary',
    ];
@endphp

<article class="card card-flush h-100 hover-elevate-up instructor-course-card" aria-label="{{ $courseName }}">
    {{-- Status Indicator Bar --}}
    <div class="position-relative h-6px {{ $statusConfig['bgColor'] }}"></div>

    {{-- Card Body --}}
    <div class="card-body pt-4 pb-5">
        {{-- Course Code Badge --}}
        <div class="d-flex justify-content-between align-items-center mb-3">
            <span class="badge {{ $statusConfig['bgColor'] }} {{ $statusConfig['textColor'] }} fs-8 fw-bold px-3 py-2">
                {{ $courseCode }}
            </span>
            @if($programName)
                <span class="badge badge-light fs-8 fw-bold px-3 py-2">
                    {{ Str::limit($programName, 20) }}
                </span>
            @endif
        </div>

        {{-- Course Title (Prominent) --}}
        <a href="{{ route('instructor.courses.show', [$programId, $course]) }}"
           class="text-gray-900 fw-bolder text-hover-primary fs-3 d-block mb-4 lh-sm"
           title="{{ $courseName }}">
            {{ Str::limit($courseName, 60) }}
        </a>

        {{-- Course Description --}}
        @if($description)
            <p class="text-gray-600 fs-7 mb-4 lh-base">
                {{ Str::limit($description, 100) }}
            </p>
        @endif

        {{-- Stats Row --}}
        <div class="d-flex flex-wrap gap-4 mb-4">
            <x-stat-item
                :value="$moduleCount"
                :label="trans_choice('Module|Modules', $moduleCount)"
                icon="book"
                color="info"
            />

            <x-stat-item
                :value="$studentCount"
                :label="trans_choice('Student|Students', $studentCount)"
                icon="people"
                color="success"
            />

            <x-stat-item
                :value="$pendingGradingCount"
                :label="__('To Grade')"
                icon="notepad-edit"
                color="gray"
                activeColor="warning"
            />
        </div>
    </div>

    {{-- Card Footer --}}
    <div class="card-footer pt-0 pb-4 border-0 bg-transparent">
        <a href="{{ route('instructor.courses.show', [$programId, $course]) }}"
           class="btn btn-primary w-100 fw-semibold py-3"
           aria-label="{{ __('Manage Course') }} - {{ $courseName }}">
            {!! getIcon('eye', 'fs-5 me-2') !!}
            {{ __('Manage Course') }}
        </a>
    </div>
</article>

@once
@push('styles')
<style>
.instructor-course-card {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.instructor-course-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.12) !important;
}

/* Enhanced badge styling */
.instructor-course-card .badge {
    border-radius: 6px;
}

/* Button styling */
.instructor-course-card .btn {
    border-radius: 8px;
    transition: all 0.2s ease;
}

.instructor-course-card .btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

/* Mobile adjustments */
@media (max-width: 768px) {
    .instructor-course-card .fs-3 {
        font-size: 1.25rem !important;
    }

    .instructor-course-card .symbol-35px {
        width: 30px !important;
        height: 30px !important;
    }

    .instructor-course-card .fs-6 {
        font-size: 0.875rem !important;
    }

    .instructor-course-card .fs-7 {
        font-size: 0.75rem !important;
    }
}

/* Small mobile adjustments */
@media (max-width: 576px) {
    .instructor-course-card .fs-3 {
        font-size: 1.1rem !important;
    }

    .instructor-course-card .card-body {
        padding: 1rem !important;
    }

    .instructor-course-card .mb-4 {
        margin-bottom: 1rem !important;
    }

    .instructor-course-card .mb-3 {
        margin-bottom: 0.75rem !important;
    }

    .instructor-course-card .gap-4 {
        gap: 1rem !important;
    }
}
</style>
@endpush
@endonce
