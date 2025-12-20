{{--
 * Student Lesson Viewer Component
 *
 * Unified content display for lessons following Metronic design.
 *
 * @param \App\Models\Course $course
 * @param \App\Models\ModuleLesson $lesson
 * @param bool $isCompleted
 * @param array|null $previousItem - ['url' => string, 'title' => string, 'type' => string]
 * @param array|null $nextItem - ['url' => string, 'title' => string, 'type' => string]
--}}

@props([
    'course',
    'lesson',
    'isCompleted' => false,
    'previousItem' => null,
    'nextItem' => null,
])

@php
    // Content type configuration
    $contentTypeConfig = match($lesson->content_type) {
        'video' => ['icon' => 'youtube', 'color' => 'danger', 'label' => __('Video Lesson')],
        'video_upload' => ['icon' => 'film', 'color' => 'danger', 'label' => __('Video Lesson')],
        'text_html' => ['icon' => 'document', 'color' => 'primary', 'label' => __('Reading Material')],
        'pdf' => ['icon' => 'folder-down', 'color' => 'warning', 'label' => __('PDF Document')],
        'external_link' => ['icon' => 'exit-right-corner', 'color' => 'info', 'label' => __('External Resource')],
        default => ['icon' => 'file', 'color' => 'secondary', 'label' => __('Lesson')],
    };

    // Find current module for this lesson
    $lessonModule = $course->modules->first(fn($m) => $m->lessons->contains('id', $lesson->id));

    // Calculate lesson position
    $allLessons = $course->modules->flatMap(fn($m) => $m->lessons->where('status', 'published'));
    $lessonIndex = $allLessons->search(fn($l) => $l->id === $lesson->id) + 1;
    $totalLessons = $allLessons->count();
@endphp

<article class="lesson-viewer card card-flush shadow-sm">
    {{-- Back to Course Link --}}
    <x-student.content-viewer-back-link :courseUrl="route('student.courses.show', $course->id)" />

    {{-- Header Zone --}}
    <div class="card-header border-0 pt-6 pb-0">
        {{-- Badge Row --}}
        <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 w-100 mb-4">
            <div class="d-flex flex-wrap gap-2">
                @if($lessonModule)
                    <span class="badge badge-light-primary fs-7 py-2 px-3">
                        {!! getIcon('folder', 'fs-7 me-1') !!}
                        {{ $lessonModule->title }}
                    </span>
                @endif
                @if($isCompleted)
                    <span class="badge badge-light-success fs-7 py-2 px-3">
                        {!! getIcon('check-circle', 'fs-7 me-1') !!}
                        {{ __('Completed') }}
                    </span>
                @endif
            </div>
            <button type="button" class="btn btn-sm btn-light-primary" onclick="toggleCourseSidebar()">
                {!! getIcon('burger-menu-2', 'fs-5') !!}
                <span class="d-none d-sm-inline ms-2">{{ __('Contents') }}</span>
            </button>
        </div>

        {{-- Title --}}
        <h1 class="fw-bold text-gray-900 mb-4 fs-2">{{ $lesson->title }}</h1>

        {{-- Metadata Bar --}}
        <div class="d-flex flex-wrap align-items-center gap-4 p-4 bg-gray-100 rounded mb-4">
            {{-- Content Type --}}
            <div class="d-flex align-items-center gap-2">
                <span class="symbol symbol-35px">
                    <span class="symbol-label bg-light-{{ $contentTypeConfig['color'] }}">
                        {!! getIcon($contentTypeConfig['icon'], 'fs-5 text-' . $contentTypeConfig['color']) !!}
                    </span>
                </span>
                <span class="fw-semibold text-gray-800">{{ $contentTypeConfig['label'] }}</span>
            </div>

            {{-- Duration --}}
            @if($lesson->estimated_duration)
                <div class="d-flex align-items-center gap-2 text-gray-600">
                    {!! getIcon('timer', 'fs-5') !!}
                    <span>{{ $lesson->estimated_duration }} {{ __('min') }}</span>
                </div>
            @endif

            {{-- Lesson Position --}}
            <div class="d-flex align-items-center gap-2 text-gray-600 ms-auto">
                {!! getIcon('book-open', 'fs-5') !!}
                <span>{{ __('Lesson :current of :total', ['current' => $lessonIndex, 'total' => $totalLessons]) }}</span>
            </div>
        </div>
    </div>

    {{-- Description Zone --}}
    @if($lesson->description)
        <div class="card-body pt-0 pb-4">
            <div class="d-flex gap-3 p-4 bg-light-primary rounded border border-primary border-opacity-10">
                {!! getIcon('information-3', 'fs-2 text-primary flex-shrink-0') !!}
                <div>
                    <span class="fw-semibold text-gray-800 d-block mb-1">{{ __('About this lesson') }}</span>
                    <p class="text-gray-700 mb-0">{{ $lesson->description }}</p>
                </div>
            </div>
        </div>
    @endif

    {{-- Content Zone --}}
    <div class="card-body pt-0">
        <x-student.lesson-content :lesson="$lesson" :isCompleted="$isCompleted" />
    </div>

    {{-- Footer Zone --}}
    <x-student.content-viewer-footer
        :previousUrl="$previousItem['url'] ?? null"
        :nextUrl="$nextItem['url'] ?? null"
    />
</article>

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/css/custom/admin/courses/content-viewer.css') }}">
@endpush
