{{--
 * Module Content Component
 *
 * Renders the content list for a single module.
 * Used for AJAX refresh after add/edit/delete operations.
 * Supports both admin and instructor contexts via the context parameter.
 *
 * @param string $context - 'admin' or 'instructor'
 * @param \App\Models\Program $program
 * @param \App\Models\Course $course
 * @param \App\Models\CourseModule $module
--}}

@props(['context', 'program', 'course', 'module'])

@php
    $isAdmin = $context === 'admin';
    $items = $module->items;
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

    // Context-aware routes
    $refreshUrl = $isAdmin
        ? route('admin.programs.courses.modules.content', [$program, $course, $module])
        : route('instructor.courses.modules.content', [$program, $course, $module]);
    $reorderUrl = $isAdmin
        ? route('admin.programs.courses.modules.items.reorder', [$program, $course, $module])
        : route('instructor.courses.modules.items.reorder', [$program, $course, $module]);
@endphp

<div id="module-content-{{ $module->id }}"
     data-refresh-url="{{ $refreshUrl }}">
{{-- Content Section Header --}}
<div class="d-flex align-items-center justify-content-between mb-4">
    <h6 class="fw-semibold text-gray-800 mb-0">
        {!! getIcon('element-11', 'fs-5 me-2 text-primary') !!}
        {{ __('Content') }}
        <span class="badge badge-light-primary ms-2">{{ $items->count() }}</span>
    </h6>
    <div class="dropdown">
        <button class="btn btn-sm btn-light-primary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
            {!! getIcon('plus', 'fs-5 me-1') !!}{{ __('Add Content') }}
        </button>
        <ul class="dropdown-menu dropdown-menu-end">
            <li>
                <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#kt_modal_add_lesson_{{ $module->id }}">
                    {!! getIcon('document', 'fs-6 me-2 text-primary') !!}{{ __('Lesson') }}
                </a>
            </li>
            <li>
                <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#kt_modal_add_quiz_{{ $module->id }}">
                    {!! getIcon('question-2', 'fs-6 me-2 text-info') !!}{{ __('Quiz') }}
                </a>
            </li>
            <li>
                <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#kt_modal_add_assignment_{{ $module->id }}">
                    {!! getIcon('notepad', 'fs-6 me-2 text-success') !!}{{ __('Assignment') }}
                </a>
            </li>
        </ul>
    </div>
</div>

{{-- Unified Content List --}}
@if($items->count() > 0)
<div class="module-content-list"
     id="contentList_{{ $module->id }}"
     data-module-id="{{ $module->id }}"
     data-reorder-url="{{ $reorderUrl }}">
    @foreach($items as $item)
    @php
        $contentType = $item->content_type;
        $typeInfo = $contentIcons[$contentType] ?? ['icon' => 'file', 'color' => 'gray-600'];
        $itemable = $item->itemable;
        $isQuizOrExam = $item->itemable_type === 'App\\Models\\Quiz';

        // Context-aware routes for item actions
        $lessonToggleUrl = $isAdmin
            ? route('admin.programs.courses.modules.lessons.toggle', [$program, $course, $module, $itemable])
            : route('instructor.courses.modules.lessons.toggle', [$program, $course, $module, $itemable]);
        $quizShowUrl = $isAdmin
            ? route('admin.programs.courses.quizzes.show', [$program, $course, $itemable])
            : route('instructor.courses.quizzes.show', [$program, $course, $itemable]);
        $itemDestroyUrl = $isAdmin
            ? route('admin.programs.courses.modules.items.destroy', [$program, $course, $module, $item])
            : route('instructor.courses.modules.items.destroy', [$program, $course, $module, $item]);
        $toggleRequiredUrl = $isAdmin
            ? route('admin.programs.courses.modules.items.toggle-required', [$program, $course, $module, $item])
            : route('instructor.courses.modules.items.toggle-required', [$program, $course, $module, $item]);
    @endphp
    <div class="content-item d-flex align-items-center py-3 px-4 rounded-2 mb-2 border border-transparent"
         data-item-id="{{ $item->id }}"
         data-type="{{ class_basename($item->itemable_type) }}">
        {{-- Drag Handle --}}
        <div class="drag-handle me-3 text-gray-400 cursor-grab">
            {!! getIcon('dots-vertical', 'fs-4') !!}
        </div>

        {{-- Content Type Icon --}}
        <div class="d-flex align-items-center justify-content-center w-35px h-35px rounded bg-light-{{ $typeInfo['color'] }} me-3 flex-shrink-0">
            {!! getIcon($typeInfo['icon'], 'fs-5 text-' . $typeInfo['color']) !!}
        </div>

        {{-- Content Info --}}
        <div class="flex-grow-1 min-w-0">
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <span class="fw-semibold text-gray-900 text-truncate">{{ $itemable->title ?? __('Untitled') }}</span>
                @if($isQuizOrExam && $itemable->isExam())
                    <span class="badge badge-light-danger fs-9">{{ __('Exam') }}</span>
                @endif
                <span class="badge badge-light-danger fs-9 required-badge {{ $item->is_required ? '' : 'd-none' }}">
                    {{ __('Required') }}
                </span>
                <span class="badge badge-light-{{ ($itemable->is_published ?? $itemable->status == 'published' || $itemable->is_published == true) ? 'success' : 'warning' }} fs-9">
                    {{ ucfirst($itemable->is_published ?? $itemable->status == 'published' || $itemable->is_published == true ? 'published' : 'draft') }}
                </span>
            </div>
            {{-- Enhanced metadata display for Quiz/Exam --}}
            @if($isQuizOrExam)
            <div class="d-flex flex-wrap align-items-center gap-3 text-gray-500 fs-8 mt-1">
                <span title="{{ __('Questions') }}">
                    {!! getIcon('questionnaire-tablet', 'fs-8 me-1') !!}
                    {{ $itemable->questions()->count() }} {{ __('questions') }}
                </span>
                @if($itemable->total_points)
                <span title="{{ __('Total Points') }}">
                    {!! getIcon('star', 'fs-8 me-1') !!}
                    {{ $itemable->total_points }} {{ __('pts') }}
                </span>
                @endif
                @if($itemable->hasTimeLimit())
                <span title="{{ __('Time Limit') }}">
                    {!! getIcon('timer', 'fs-8 me-1') !!}
                    {{ $itemable->time_limit }} {{ __('min') }}
                </span>
                @endif
                @if($itemable->max_attempts)
                <span title="{{ __('Attempts Allowed') }}">
                    {!! getIcon('arrows-loop', 'fs-8 me-1') !!}
                    {{ $itemable->max_attempts == -1 ? __('Unlimited') : $itemable->max_attempts }} {{ $itemable->max_attempts != -1 ? __('attempts') : '' }}
                </span>
                @endif
                @if($itemable->passing_score)
                <span title="{{ __('Passing Score') }}">
                    {!! getIcon('verify', 'fs-8 me-1') !!}
                    {{ $itemable->passing_score }}%
                </span>
                @endif
                @if($itemable->due_date)
                <span title="{{ __('Due Date') }}" class="{{ $itemable->isOverdue() ? 'text-danger' : '' }}">
                    {!! getIcon('calendar', 'fs-8 me-1') !!}
                    {{ $itemable->due_date->format('M d, Y') }}
                </span>
                @endif
            </div>
            @else
            <div class="text-gray-500 fs-8">
                {{ ucfirst(str_replace('_', ' ', $contentType)) }}
                @if($itemable->estimated_duration ?? null)
                    • {{ $itemable->estimated_duration }} {{ __('min') }}
                @endif
                @if($itemable->total_points ?? null)
                    • {{ $itemable->total_points }} {{ __('pts') }}
                @endif
            </div>
            @endif
        </div>

        {{-- Actions (show on hover) --}}
        <div class="content-actions d-flex gap-1">
            @if($item->itemable_type === 'App\\Models\\ModuleLesson')
            <button type="button" class="btn btn-sm btn-icon btn-light-primary"
                    data-bs-toggle="modal" data-bs-target="#kt_modal_edit_lesson_{{ $itemable->id }}"
                    title="{{ __('Edit') }}">
                {!! getIcon('pencil', 'fs-6') !!}
            </button>
            <button type="button"
                    class="btn btn-sm btn-icon btn-light"
                    data-lesson-toggle-trigger
                    data-lesson-id="{{ $itemable->id }}"
                    data-lesson-title="{{ $itemable->title }}"
                    data-lesson-status="{{ $itemable->status }}"
                    data-toggle-url="{{ $lessonToggleUrl }}"
                    title="{{ $itemable->status === 'published' ? __('Set to Draft') : __('Publish') }}">
                {!! getIcon($itemable->status === 'published' ? 'eye-slash' : 'eye', 'fs-6') !!}
            </button>
            <button type="button" class="btn btn-sm btn-icon btn-light-danger"
                    data-bs-toggle="modal" data-bs-target="#kt_modal_delete_lesson_{{ $itemable->id }}"
                    title="{{ __('Delete') }}">
                {!! getIcon('trash', 'fs-6') !!}
            </button>
            @elseif($item->itemable_type === 'App\\Models\\Quiz')
            <a href="{{ $quizShowUrl }}"
               class="btn btn-sm btn-light-info"
               title="{{ __('Manage Questions') }}">
                {!! getIcon('questionnaire-tablet', 'fs-6 me-1') !!}
                <span class="d-none d-md-inline">{{ __('Questions') }}</span>
            </a>
            <button type="button" class="btn btn-sm btn-icon btn-light-primary"
                    data-bs-toggle="modal" data-bs-target="#kt_modal_edit_quiz_{{ $itemable->id }}"
                    title="{{ __('Edit Settings') }}">
                {!! getIcon('setting-2', 'fs-6') !!}
            </button>
            <button type="button" class="btn btn-sm btn-icon btn-light-danger btn-remove-item"
                    data-item-id="{{ $item->id }}"
                    data-remove-url="{{ $itemDestroyUrl }}"
                    data-confirm="{{ __('Remove this item from the module?') }}"
                    title="{{ __('Remove') }}">
                {!! getIcon('trash', 'fs-6') !!}
            </button>
            @elseif($item->itemable_type === 'App\\Models\\Assignment')
            <button type="button" class="btn btn-sm btn-icon btn-light-primary"
                    data-bs-toggle="modal" data-bs-target="#kt_modal_edit_assignment_{{ $itemable->id }}"
                    title="{{ __('Edit') }}">
                {!! getIcon('pencil', 'fs-6') !!}
            </button>
            <button type="button" class="btn btn-sm btn-icon btn-light-danger btn-remove-item"
                    data-item-id="{{ $item->id }}"
                    data-remove-url="{{ $itemDestroyUrl }}"
                    data-confirm="{{ __('Remove this item from the module?') }}"
                    title="{{ __('Remove') }}">
                {!! getIcon('trash', 'fs-6') !!}
            </button>
            @endif
            <button type="button"
                    class="btn btn-sm btn-icon btn-light-warning btn-toggle-required"
                    data-toggle-url="{{ $toggleRequiredUrl }}"
                    data-required="{{ $item->is_required ? 1 : 0 }}"
                    title="{{ $item->is_required ? __('Mark optional') : __('Mark required') }}">
                {!! getIcon('shield-tick', 'fs-6') !!}
            </button>
        </div>
    </div>
    @endforeach
</div>
@else
<div class="text-center py-8 bg-light rounded-3">
    <div class="mb-3">
        {!! getIcon('element-plus', 'fs-2x text-gray-400') !!}
    </div>
    <div class="text-gray-600 fs-6 mb-2">{{ __('No content in this module yet') }}</div>
    <div class="text-gray-500 fs-7 mb-4">{{ __('Add lessons, quizzes, or assignments to build your module') }}</div>
    <button type="button" class="btn btn-sm btn-primary"
            data-bs-toggle="modal" data-bs-target="#kt_modal_add_lesson_{{ $module->id }}">
        {!! getIcon('plus', 'fs-5 me-1') !!}{{ __('Create First Lesson') }}
    </button>
</div>
@endif

</div>{{-- End module-content wrapper --}}

{{--
 * NOTE: Edit/Delete modals for module items are NOT included here.
 * They are managed in the parent view (show.blade.php) inside #moduleModalsContainer
 * to prevent duplicate modal IDs during AJAX refresh which causes Bootstrap errors.
 * See: x-courses.item-modals
--}}
