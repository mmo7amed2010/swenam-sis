{{--
 * Module Card Component
 *
 * Displays a single course module with header, content, and footer.
 * Supports both admin and instructor contexts via the context parameter.
 *
 * @param string $context - 'admin' or 'instructor'
 * @param \App\Models\Program $program
 * @param \App\Models\Course $course
 * @param \App\Models\CourseModule $module
 * @param int $iteration - Loop iteration number (1-based)
 * @param bool $isFirst - Whether this is the first module in the list
--}}

@props(['context', 'program', 'course', 'module', 'iteration', 'isFirst'])

@php
    $isAdmin = $context === 'admin';
    $items = $module->items;
    $contentId = 'module_content_' . $module->id;
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
    $toggleUrl = $isAdmin
        ? route('admin.programs.courses.modules.toggle', [$program, $course, $module])
        : route('instructor.courses.modules.toggle', [$program, $course, $module]);
    $deleteUrl = $isAdmin
        ? route('admin.programs.courses.modules.destroy', [$program, $course, $module])
        : route('instructor.courses.modules.destroy', [$program, $course, $module]);
@endphp

<div id="module-{{ $module->id }}"
     class="card module-card {{ $module->status === 'draft' ? 'opacity-50' : '' }}"
     data-module-id="{{ $module->id }}">

    <!--begin::Module Header-->
    <div class="card-header py-4 px-5 cursor-pointer border-0"
         data-bs-toggle="collapse"
         data-bs-target="#{{ $contentId }}"
         aria-expanded="{{ $isFirst ? 'true' : 'false' }}">
        <div class="d-flex align-items-center w-100">
            {{-- Order Badge --}}
            <div class="d-flex align-items-center justify-content-center w-40px h-40px rounded-circle bg-primary me-4 flex-shrink-0 order-badge">
                <span class="text-white fw-bold fs-5">{{ $iteration }}</span>
            </div>

            {{-- Title & Quick Info --}}
            <div class="flex-grow-1 min-w-0">
                <div class="d-flex align-items-center flex-wrap gap-2 mb-1">
                    <h4 class="fw-bold text-gray-900 mb-0 text-truncate">{{ $module->title }}</h4>
                    @if($module->release_date && $module->release_date->isFuture())
                        <span class="badge badge-light-warning">{{ __('Scheduled') }}</span>
                    @else
                        <x-tables.status-badge :status="$module->status" size="sm" />
                    @endif
                </div>
                <div class="d-flex flex-wrap gap-3 text-gray-500 fs-7">
                    <span>{!! getIcon('element-11', 'fs-7 me-1') !!} {{ $items->count() }} {{ __('items') }}</span>
                    @if($module->release_date)
                        <span>{!! getIcon('calendar', 'fs-7 me-1') !!} {{ $module->release_date->format('M d') }}</span>
                    @endif
                    <span>{!! getIcon('time', 'fs-7 me-1') !!} {{ $module->updated_at->diffForHumans() }}</span>
                </div>
            </div>

            {{-- Quick Actions --}}
            <div class="d-flex align-items-center gap-2 ms-3" onclick="event.stopPropagation()">
                <button class="btn btn-sm btn-icon btn-light collapse-indicator" type="button">
                    {!! getIcon('down', 'fs-4') !!}
                </button>
            </div>
        </div>
    </div>
    <!--end::Module Header-->

    <!--begin::Module Content-->
    <div id="{{ $contentId }}" class="collapse {{ $isFirst ? 'show' : '' }}">
        <div class="card-body pt-0 px-5 pb-5">
            {{-- Description --}}
            @if($module->description)
            <div class="bg-light-primary rounded-3 p-4 mb-5">
                <p class="text-gray-700 mb-0 fs-7">{!! nl2br(e(Str::limit($module->description, 200))) !!}</p>
            </div>
            @endif

            {{-- Module Content - Use shared component --}}
            <x-courses.module-content
                :context="$context"
                :program="$program"
                :course="$course"
                :module="$module"
            />
        </div>

        {{-- Module Footer with Metadata --}}
        <div class="card-footer bg-gray-100 py-3 px-5">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                <div class="d-flex flex-wrap gap-4 text-gray-600 fs-8">
                    <span>{{ __('Order') }}: <strong>{{ $module->order_index }}</strong></span>
                    <span>{{ __('Created') }}: <strong>{{ $module->created_at->format('M d, Y') }}</strong></span>
                    @if($module->release_date)
                        <span>{{ __('Release') }}:
                            <strong class="{{ $module->release_date->isFuture() ? 'text-warning' : '' }}">
                                {{ $module->release_date->format('M d, Y') }}
                            </strong>
                        </span>
                    @endif
                </div>
                <div class="d-flex gap-2">
                    <button type="button"
                            class="btn btn-sm btn-light"
                            data-module-edit-trigger
                            data-module-id="{{ $module->id }}">
                        {!! getIcon('notepad-edit', 'fs-5 me-1') !!}{{ __('Edit') }}
                    </button>
                    <button type="button"
                            class="btn btn-sm btn-light"
                            data-module-toggle-trigger
                            data-module-id="{{ $module->id }}"
                            data-module-title="{{ $module->title }}"
                            data-module-status="{{ $module->status }}"
                            data-toggle-url="{{ $toggleUrl }}">
                        {!! getIcon($module->status === 'published' ? 'eye-slash' : 'eye', 'fs-5 me-1') !!}
                        {{ $module->status === 'published' ? __('Set to Draft') : __('Publish') }}
                    </button>
                    <button type="button"
                            class="btn btn-sm btn-light-danger"
                            data-module-delete-trigger
                            data-module-id="{{ $module->id }}"
                            data-module-title="{{ $module->title }}"
                            data-delete-url="{{ $deleteUrl }}">
                        {!! getIcon('trash', 'fs-5 me-1') !!}{{ __('Delete') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
    <!--end::Module Content-->
</div>
