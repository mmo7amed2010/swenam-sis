{{--
 * Student Course Sidebar Component (Overlay Only)
 *
 * A clean overlay sidebar for course navigation. No rail - just a slide-out
 * panel triggered by a button in the content area.
 *
 * @param \App\Models\Course $course - The course being viewed
 * @param array $moduleProgress - Progress data keyed by module_id
 * @param array $itemCompletion - Completion data keyed by module_id => [item_id => bool]
 * @param \App\Models\ModuleLesson|null $currentLesson - Currently active lesson
 * @param \App\Models\Assignment|null $currentAssignment - Currently active assignment
 * @param \App\Models\Quiz|null $currentQuiz - Currently active quiz
 * @param array $progress - Overall course progress data
 * @param \Illuminate\Support\Collection|null $moduleAccessibility - Module accessibility data with is_accessible and lock_reason
 * @param array $examStatuses - Exam attempt statuses keyed by quiz_id => ['status' => 'passed'|'failed', 'percentage' => float]
--}}

@props([
    'course',
    'moduleProgress',
    'itemCompletion',
    'currentLesson' => null,
    'currentAssignment' => null,
    'currentQuiz' => null,
    'progress',
    'moduleAccessibility' => null,
    'examStatuses' => []
])

@php
    // Content icons configuration
    $contentIcons = [
        'video' => ['icon' => 'youtube', 'color' => 'danger'],
        'text' => ['icon' => 'document', 'color' => 'primary'],
        'text_html' => ['icon' => 'document', 'color' => 'primary'],
        'pdf' => ['icon' => 'folder-down', 'color' => 'warning'],
        'quiz' => ['icon' => 'question-2', 'color' => 'info'],
        'exam' => ['icon' => 'award', 'color' => 'danger'],
        'assignment' => ['icon' => 'notepad', 'color' => 'success'],
        'html' => ['icon' => 'code', 'color' => 'dark'],
        'external_link' => ['icon' => 'exit-right-corner', 'color' => 'info'],
    ];
@endphp

{{-- Overlay Sidebar --}}
<aside class="course-sidebar-overlay" id="courseSidebar" aria-hidden="true">
    {{-- Close Button --}}
    <button class="sidebar-close-btn btn btn-icon btn-light position-absolute"
            type="button"
            onclick="toggleCourseSidebar()"
            aria-label="{{ __('Close navigation') }}">
        {!! getIcon('cross', 'fs-3') !!}
    </button>

    {{-- Sidebar Header --}}
    <div class="sidebar-header">
        {{-- Course Title --}}
        <h4 class="fw-bold text-gray-900 mb-1 text-truncate" title="{{ $course->name }}">
            {{ $course->name }}
        </h4>
        <span class="badge badge-light-primary fs-8 mb-4">{{ $course->course_code }}</span>

        {{-- Course Progress --}}
        <div class="progress-section p-4 bg-gray-100 rounded-3">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="fs-7 fw-semibold text-gray-700">{{ __('Progress') }}</span>
                <span class="fs-7 fw-bold text-gray-900">{{ round($progress['percentage']) }}%</span>
            </div>
            <div class="progress h-8px bg-gray-200 rounded mb-2">
                <div class="progress-bar bg-{{ $progress['percentage'] >= 100 ? 'success' : 'primary' }} rounded"
                     role="progressbar"
                     style="width: {{ $progress['percentage'] }}%"
                     aria-valuenow="{{ $progress['percentage'] }}"
                     aria-valuemin="0"
                     aria-valuemax="100">
                </div>
            </div>
            <span class="text-muted fs-8">
                {{ $progress['completed_items'] }} {{ __('of') }} {{ $progress['total_items'] }} {{ __('items completed') }}
            </span>
        </div>
    </div>

    {{-- Sidebar Content - Module List --}}
    <div class="sidebar-content">
        <nav class="module-nav">
            @forelse($course->modules as $index => $module)
                @php
                    $modProgress = $moduleProgress[$module->id] ?? ['percentage' => 0, 'completed' => 0, 'total' => 0];

                    // Check module accessibility from the gating service
                    $accessibleModule = $moduleAccessibility?->firstWhere('id', $module->id);
                    $isModuleLocked = $accessibleModule && !$accessibleModule->is_accessible;
                    $lockReason = $accessibleModule?->lock_reason;

                    $moduleStatus = match(true) {
                        $isModuleLocked => 'locked',
                        $modProgress['percentage'] >= 100 => 'completed',
                        $modProgress['percentage'] > 0 => 'in_progress',
                        default => 'not_started',
                    };
                    // Check if any item in this module is currently active
                    $isExpanded = !$isModuleLocked && (($currentLesson && $module->lessons->contains('id', $currentLesson->id))
                        || ($currentAssignment && $module->items->contains(fn($i) => $i->itemable_type === 'App\\Models\\Assignment' && $i->itemable_id === $currentAssignment->id))
                        || ($currentQuiz && $module->items->contains(fn($i) => $i->itemable_type === 'App\\Models\\Quiz' && $i->itemable_id === $currentQuiz->id)));
                    $items = $module->items->filter(fn($item) => $item->itemable && (
                        ($item->itemable_type === 'App\\Models\\ModuleLesson' && $item->itemable->status === 'published') ||
                        ($item->itemable_type === 'App\\Models\\Quiz' && ($item->itemable->published ?? false)) ||
                        ($item->itemable_type === 'App\\Models\\Assignment' && ($item->itemable->is_published ?? false))
                    ))->sortBy('order_position');
                @endphp

                <div class="module-group {{ $moduleStatus }}" data-module-id="{{ $module->id }}">
                    {{-- Module Header --}}
                    <button class="module-header w-100 d-flex align-items-center gap-3 p-3 border-0 bg-transparent text-start {{ $isModuleLocked ? 'module-locked' : '' }}"
                            type="button"
                            @if(!$isModuleLocked)
                                data-bs-toggle="collapse"
                                data-bs-target="#module-items-{{ $module->id }}"
                            @endif
                            aria-expanded="{{ $isExpanded ? 'true' : 'false' }}"
                            aria-controls="module-items-{{ $module->id }}"
                            @if($isModuleLocked)
                                title="{{ $lockReason }}"
                                disabled
                            @endif>
                        {{-- Status Icon --}}
                        <div class="module-status-icon flex-shrink-0">
                            @if($isModuleLocked)
                                <span class="symbol symbol-30px">
                                    <span class="symbol-label bg-warning">
                                        {!! getIcon('lock', 'fs-6 text-white') !!}
                                    </span>
                                </span>
                            @elseif($moduleStatus === 'completed')
                                <span class="symbol symbol-30px">
                                    <span class="symbol-label bg-success">
                                        {!! getIcon('check', 'fs-6 text-white') !!}
                                    </span>
                                </span>
                            @elseif($moduleStatus === 'in_progress')
                                <span class="symbol symbol-30px">
                                    <span class="symbol-label bg-primary">
                                        <span class="text-white fs-8 fw-bold">{{ round($modProgress['percentage']) }}%</span>
                                    </span>
                                </span>
                            @else
                                <span class="symbol symbol-30px">
                                    <span class="symbol-label bg-gray-200">
                                        <span class="text-gray-600 fs-8 fw-bold">{{ $index + 1 }}</span>
                                    </span>
                                </span>
                            @endif
                        </div>

                        {{-- Module Title --}}
                        <div class="flex-grow-1 min-w-0">
                            <span class="module-title d-block fw-semibold {{ $isModuleLocked ? 'text-gray-500' : 'text-gray-800' }} text-truncate" title="{{ $module->title }}">
                                {{ $module->title }}
                            </span>
                            @if($isModuleLocked)
                                <span class="text-warning fs-8">
                                    {!! getIcon('lock', 'fs-9 me-1') !!}{{ __('Locked') }}
                                </span>
                            @else
                                <span class="text-muted fs-8">
                                    {{ $items->count() }} {{ trans_choice('item|items', $items->count()) }}
                                    · {{ $modProgress['completed'] }}/{{ $modProgress['total'] }} {{ __('done') }}
                                </span>
                            @endif
                        </div>

                        {{-- Expand Icon (only for unlocked modules) --}}
                        @if(!$isModuleLocked)
                            <span class="module-expand-icon flex-shrink-0">
                                {!! getIcon('down', 'fs-5 text-gray-500 transition-transform') !!}
                            </span>
                        @endif
                    </button>

                    {{-- Module Items (only for unlocked modules) --}}
                    @if(!$isModuleLocked)
                    <div class="collapse {{ $isExpanded ? 'show' : '' }}" id="module-items-{{ $module->id }}">
                        <div class="module-items-list ps-3">
                            @foreach($items as $item)
                                @php
                                    $itemable = $item->itemable;
                                    $isCompleted = ($itemCompletion[$module->id][$item->id] ?? false);

                                    // Check if this item is currently active
                                    $isCurrent = match($item->itemable_type) {
                                        'App\\Models\\ModuleLesson' => $currentLesson && $item->itemable_id === $currentLesson->id,
                                        'App\\Models\\Assignment' => $currentAssignment && $item->itemable_id === $currentAssignment->id,
                                        'App\\Models\\Quiz' => $currentQuiz && $item->itemable_id === $currentQuiz->id,
                                        default => false,
                                    };

                                    // Determine content type for icon
                                    $contentType = match($item->itemable_type) {
                                        'App\\Models\\ModuleLesson' => $itemable->content_type ?? 'text',
                                        'App\\Models\\Quiz' => $itemable->isExam() ? 'exam' : 'quiz',
                                        'App\\Models\\Assignment' => 'assignment',
                                        default => 'text',
                                    };
                                    $typeInfo = $contentIcons[$contentType] ?? ['icon' => 'file', 'color' => 'gray-600'];

                                    // Determine URL
                                    $itemUrl = match($item->itemable_type) {
                                        'App\\Models\\ModuleLesson' => route('student.courses.lessons.show', [$course->id, $item->itemable_id]),
                                        'App\\Models\\Quiz' => route('student.courses.quizzes.view', [$course->id, $item->itemable_id]),
                                        'App\\Models\\Assignment' => route('student.courses.assignments.show', [$course->id, $item->itemable_id]),
                                        default => '#',
                                    };

                                    // Get exam status for exams
                                    $examStatus = null;
                                    if ($item->itemable_type === 'App\\Models\\Quiz' && $itemable->isExam()) {
                                        $examStatus = $examStatuses[$itemable->id] ?? null;
                                    }
                                @endphp

                                <a href="{{ $itemUrl }}"
                                   class="sidebar-item d-flex align-items-center gap-2 py-2 px-3 rounded-2 text-decoration-none {{ $isCurrent ? 'active' : '' }} {{ $isCompleted ? 'completed' : '' }}"
                                   data-item-id="{{ $item->id }}">
                                    {{-- Item Status/Type Icon --}}
                                    <span class="lesson-status-icon flex-shrink-0">
                                        @if($examStatus && $examStatus['status'] === 'passed')
                                            {!! getIcon('check', 'fs-6 text-success') !!}
                                        @elseif($examStatus && $examStatus['status'] === 'failed')
                                            {!! getIcon('cross', 'fs-6 text-danger') !!}
                                        @elseif($isCompleted)
                                            {!! getIcon('check', 'fs-6 text-success') !!}
                                        @else
                                            {!! getIcon($typeInfo['icon'], 'fs-6 text-' . $typeInfo['color']) !!}
                                        @endif
                                    </span>

                                    {{-- Item Title --}}
                                    <span class="lesson-title flex-grow-1 text-truncate {{ $isCurrent ? 'fw-semibold text-primary' : 'text-gray-700' }}">
                                        {{ $itemable->title ?? __('Untitled') }}
                                        @if($examStatus)
                                            <span class="badge badge-light-{{ $examStatus['status'] === 'passed' ? 'success' : 'danger' }} fs-9 ms-1">
                                                {{ $examStatus['status'] === 'passed' ? __('Passed') : __('Failed') }}
                                            </span>
                                        @endif
                                    </span>

                                    {{-- Duration/Points Badge --}}
                                    @if($item->itemable_type === 'App\\Models\\ModuleLesson' && $itemable->estimated_duration)
                                        <span class="badge badge-light fs-9">{{ $itemable->estimated_duration }}m</span>
                                    @elseif($item->itemable_type === 'App\\Models\\Quiz' && $itemable->total_points)
                                        <span class="badge badge-light-info fs-9">{{ $itemable->total_points }}pts</span>
                                    @elseif($item->itemable_type === 'App\\Models\\Assignment' && ($itemable->total_points ?? $itemable->max_points))
                                        <span class="badge badge-light-success fs-9">{{ $itemable->total_points ?? $itemable->max_points }}pts</span>
                                    @endif
                                </a>
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>
            @empty
                <div class="text-center py-8 text-muted">
                    <div class="mb-3">
                        {!! getIcon('folder', 'fs-2x text-gray-400') !!}
                    </div>
                    <p class="mb-0 fs-7">{{ __('No modules available yet') }}</p>
                </div>
            @endforelse
        </nav>
    </div>

    {{-- Sidebar Footer --}}
    <div class="sidebar-footer p-4 border-top">
        <a href="{{ route('student.program.index') }}" class="btn btn-light-primary w-100">
            {!! getIcon('arrow-left', 'fs-5 me-2') !!}
            {{ __('Back to Program') }}
        </a>
    </div>
</aside>

{{-- Sidebar Backdrop --}}
<div class="sidebar-backdrop" onclick="toggleCourseSidebar()"></div>

@push('styles')
<style>
/* ============================================
   Overlay Sidebar
   ============================================ */
.course-sidebar-overlay {
    position: fixed;
    left: 0;
    top: 0;
    width: 320px;
    height: 100vh;
    background: #fff;
    transform: translateX(-100%);
    transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    z-index: 1050;
    display: flex;
    flex-direction: column;
    overflow: hidden;
}

.course-sidebar-overlay.open {
    transform: translateX(0);
    box-shadow: 0 0 50px rgba(0, 0, 0, 0.15);
}

.sidebar-close-btn {
    top: 1rem;
    right: 1rem;
    z-index: 10;
}

.sidebar-header {
    padding: 1.5rem;
    padding-top: 3.5rem; /* Account for close button */
    border-bottom: 1px solid var(--bs-gray-100);
    flex-shrink: 0;
}

.sidebar-content {
    flex: 1;
    overflow-y: auto;
    padding: 0.75rem;
}

.sidebar-footer {
    flex-shrink: 0;
}

/* ============================================
   Sidebar Backdrop
   ============================================ */
.sidebar-backdrop {
    position: fixed;
    inset: 0;
    background: rgba(0, 0, 0, 0.4);
    opacity: 0;
    visibility: hidden;
    transition: all 0.3s ease;
    z-index: 1040;
    backdrop-filter: blur(2px);
}

.sidebar-backdrop.show {
    opacity: 1;
    visibility: visible;
}

/* ============================================
   Module Navigation Styles
   ============================================ */
.module-group {
    margin-bottom: 0.5rem;
    border-radius: 0.5rem;
    overflow: hidden;
}

.module-header {
    cursor: pointer;
    transition: background-color 0.2s ease;
    border-radius: 0.5rem;
}

.module-header:hover {
    background-color: var(--bs-gray-100);
}

.module-header[aria-expanded="true"] .module-expand-icon svg {
    transform: rotate(180deg);
}

.module-expand-icon svg {
    transition: transform 0.2s ease;
}

/* Locked Module Styles */
.module-header.module-locked {
    cursor: not-allowed;
    opacity: 0.7;
}

.module-header.module-locked:hover {
    background-color: transparent;
}

.module-group.locked {
    opacity: 0.8;
}

/* Sidebar Items */
.sidebar-item {
    transition: all 0.2s ease;
    margin-left: 1rem;
    border-left: 2px solid transparent;
}

.sidebar-item:hover {
    background-color: var(--bs-gray-100);
}

.sidebar-item.active {
    background-color: rgba(59, 130, 246, 0.08);
    border-left-color: var(--bs-primary);
}

.sidebar-item.completed .lesson-title {
    color: var(--bs-gray-500);
}

/* ============================================
   Responsive Adjustments
   ============================================ */
@media (max-width: 575.98px) {
    .course-sidebar-overlay {
        width: 100%;
    }
}
</style>
@endpush

@push('scripts')
<script>
function toggleCourseSidebar() {
    const sidebar = document.getElementById('courseSidebar');
    const backdrop = document.querySelector('.sidebar-backdrop');

    const isOpening = !sidebar?.classList.contains('open');

    sidebar?.classList.toggle('open');
    backdrop?.classList.toggle('show');
    document.body.classList.toggle('sidebar-open');

    // Update aria-hidden
    if (sidebar) {
        sidebar.setAttribute('aria-hidden', !isOpening);
    }

    // Trap focus when open
    if (isOpening && sidebar) {
        const closeBtn = sidebar.querySelector('.sidebar-close-btn');
        closeBtn?.focus();
    }
}

// Close sidebar on Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        const sidebar = document.getElementById('courseSidebar');
        if (sidebar?.classList.contains('open')) {
            toggleCourseSidebar();
        }
    }
});

// Close sidebar when clicking a link (for smoother navigation)
document.querySelectorAll('.course-sidebar-overlay .sidebar-item').forEach(item => {
    item.addEventListener('click', function() {
        setTimeout(() => {
            const sidebar = document.getElementById('courseSidebar');
            if (sidebar?.classList.contains('open')) {
                toggleCourseSidebar();
            }
        }, 100);
    });
});
</script>
@endpush
