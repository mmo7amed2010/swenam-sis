{{--
    Course Action Card Component
    A prominent CTA card with contextual messaging based on progress state.

    @param \App\Models\Course $course - The course model
    @param array $progress - Progress data with 'percentage'
    @param \App\Models\ModuleLesson|null $continueLesson - The next lesson to continue
    @param string|null $nextLessonTitle - Title of the next lesson
    @param string|null $nextModuleTitle - Title of the module containing next lesson
--}}

@props([
    'course',
    'progress',
    'continueLesson' => null,
    'nextLessonTitle' => null,
    'nextModuleTitle' => null,
])

@php
    $percentage = $progress['percentage'] ?? 0;

    // Determine the message and button text based on progress
    $config = match(true) {
        $percentage >= 100 => [
            'title' => __('Course Completed!'),
            'subtitle' => __('Great job! Review materials or check your grades.'),
            'icon' => 'check-circle',
            'titleClass' => 'text-success',
            'btnText' => __('Review Course'),
            'btnVariant' => 'btn-light-success',
        ],
        $percentage > 0 => [
            'title' => __('Continue where you left off'),
            'subtitle' => $nextLessonTitle && $nextModuleTitle
                ? sprintf('%s in %s', $nextLessonTitle, $nextModuleTitle)
                : __('Pick up from your last lesson'),
            'icon' => 'arrow-right',
            'titleClass' => 'text-gray-900',
            'btnText' => __('Continue Learning'),
            'btnVariant' => 'btn-primary',
        ],
        default => [
            'title' => __('Ready to begin?'),
            'subtitle' => __('Start your learning journey with the first lesson.'),
            'icon' => 'rocket',
            'titleClass' => 'text-gray-900',
            'btnText' => __('Start Learning'),
            'btnVariant' => 'btn-primary',
        ],
    };
@endphp

<div class="card card-flush mb-6">
    <div class="card-body d-flex flex-wrap align-items-center gap-4 py-5">
        {{-- Icon --}}
        <div class="d-none d-sm-flex flex-shrink-0">
            <div class="symbol symbol-50px">
                <span class="symbol-label bg-light-{{ $percentage >= 100 ? 'success' : 'primary' }}">
                    {!! getIcon($config['icon'], 'fs-2x text-' . ($percentage >= 100 ? 'success' : 'primary')) !!}
                </span>
            </div>
        </div>

        {{-- Message --}}
        <div class="flex-grow-1">
            <h4 class="fw-bold mb-1 {{ $config['titleClass'] }}">
                {{ $config['title'] }}
            </h4>
            <p class="text-muted mb-0 fs-7">{{ $config['subtitle'] }}</p>
        </div>

        {{-- Action Buttons --}}
        <div class="d-flex flex-wrap gap-3">
            @if($continueLesson)
                <a href="{{ route('student.courses.lessons.show', [$course->id, $continueLesson->id]) }}"
                   class="btn {{ $config['btnVariant'] }} btn-sm btn-lg-default">
                    {!! getIcon($config['icon'], 'fs-4 me-2') !!}
                    {{ $config['btnText'] }}
                </a>
            @elseif($percentage >= 100)
                {{-- Completed state without continue lesson --}}
                <a href="{{ route('student.courses.show', $course->id) }}"
                   class="btn {{ $config['btnVariant'] }} btn-sm btn-lg-default">
                    {!! getIcon('eye', 'fs-4 me-2') !!}
                    {{ __('Browse Content') }}
                </a>
            @endif

            <a href="{{ route('student.grades.show', $course->id) }}"
               class="btn btn-light-primary btn-sm btn-lg-default">
                {!! getIcon('chart-line-up', 'fs-4 me-2') !!}
                {{ __('View Grades') }}
            </a>
        </div>
    </div>
</div>

@once
@push('styles')
<style>
@media (min-width: 992px) {
    .btn-lg-default {
        padding: 0.75rem 1.5rem;
        font-size: 1rem;
    }
}
</style>
@endpush
@endonce
