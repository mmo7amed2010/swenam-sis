{{--
 * Edit Lesson Form Component
 *
 * Shared AJAX-based modal for editing lesson content with content type switching.
 * Supports both admin and instructor contexts via the context parameter.
 *
 * @param string $context - 'admin' or 'instructor'
 * @param \App\Models\Program $program
 * @param \App\Models\Course $course
 * @param \App\Models\CourseModule $module
 * @param \App\Models\ModuleLesson $lesson
--}}

@props(['context', 'program', 'course', 'module', 'lesson'])

@php
$isAdmin = $context === 'admin';
$action = $isAdmin
    ? route('admin.programs.courses.modules.lessons.update', [$program, $course, $module, $lesson])
    : route('instructor.courses.modules.lessons.update', [$program, $course, $module, $lesson]);
$refreshUrl = $isAdmin
    ? route('admin.programs.courses.modules.content', [$program, $course, $module])
    : route('instructor.courses.modules.content', [$program, $course, $module]);
@endphp

<x-modals.ajax-form
    id="kt_modal_edit_lesson_{{ $lesson->id }}"
    title="{{ __('Edit Lesson') }} - {{ $lesson->title }}"
    :action="$action"
    method="PUT"
    size="lg"
    :hasFiles="true"
    targetContainer="#module-content-{{ $module->id }}"
    :refreshUrl="$refreshUrl"
    successMessage="{{ __('Lesson updated successfully') }}"
    submitLabel="{{ __('Update Lesson') }}"
>
    {{-- Store original content type for change detection --}}
    <input type="hidden" name="_original_content_type" value="{{ $lesson->content_type }}" data-original-content-type="{{ $lesson->content_type }}">

    {{-- Lesson Title --}}
    <div class="mb-10 fv-row">
        <label class="required form-label">{{ __('Lesson Title') }}</label>
        <input type="text" name="title" class="form-control" value="{{ old('title', $lesson->title) }}" required />
    </div>

    {{-- Content Type Selector --}}
    <div class="mb-10 fv-row">
        <label class="required form-label">{{ __('Content Type') }}</label>
        <select name="content_type" class="form-select" required data-content-type-select>
            <option value="text_html" {{ old('content_type', $lesson->content_type) === 'text_html' ? 'selected' : '' }}>{{ __('Text/HTML') }}</option>
            <option value="video" {{ old('content_type', $lesson->content_type) === 'video' ? 'selected' : '' }}>{{ __('Video (YouTube/Vimeo)') }}</option>
            <option value="video_upload" {{ old('content_type', $lesson->content_type) === 'video_upload' ? 'selected' : '' }}>{{ __('Video Upload') }}</option>
            <option value="pdf" {{ old('content_type', $lesson->content_type) === 'pdf' ? 'selected' : '' }}>{{ __('PDF Upload') }}</option>
            <option value="external_link" {{ old('content_type', $lesson->content_type) === 'external_link' ? 'selected' : '' }}>{{ __('External Link') }}</option>
        </select>
    </div>

    {{-- Content type change warning --}}
    <div class="content-change-confirmation alert alert-warning mb-5" style="display: none;" data-content-change-confirmation>
        <div class="d-flex align-items-start">
            {!! getIcon('information-5', 'fs-2hx text-warning me-4 mt-1') !!}
            <div class="d-flex flex-column">
                <h4 class="mb-1 text-warning">{{ __('Content Type Change Warning') }}</h4>
                <p class="mb-3 text-gray-700">{{ __('Changing the content type will replace the current content. This action cannot be undone.') }}</p>
                <div class="form-check form-check-custom form-check-solid">
                    <input class="form-check-input" type="checkbox" name="content_type_confirmation" value="1" id="content_type_confirmation_{{ $lesson->id }}" />
                    <label class="form-check-label text-gray-800" for="content_type_confirmation_{{ $lesson->id }}">
                        {{ __('I understand and want to proceed') }}
                    </label>
                </div>
            </div>
        </div>
    </div>

    {{-- Dynamic content fields based on type --}}
    <div class="content-fields-container">
        {{-- Text/HTML --}}
        <div class="mb-10 fv-row content-field" data-type="text_html" style="display: {{ old('content_type', $lesson->content_type) === 'text_html' ? 'block' : 'none' }};">
            <label class="required form-label">{{ __('Content') }}</label>
            <div id="kt_lesson_content_editor_{{ $lesson->id }}" style="min-height: 300px;"></div>
            <textarea name="content" id="kt_lesson_content_textarea_{{ $lesson->id }}" class="form-control d-none" data-required="true">{{ old('content', $lesson->content_type === 'text_html' ? $lesson->content : '') }}</textarea>
            <div class="text-muted fs-7 mt-1">{{ __('Rich text editor for lesson content') }}</div>
        </div>

        {{-- Video (YouTube/Vimeo) --}}
        <div class="mb-10 fv-row content-field" data-type="video" style="display: {{ old('content_type', $lesson->content_type) === 'video' ? 'block' : 'none' }};">
            <label class="required form-label">{{ __('Video URL') }}</label>
            <input type="url" name="content_url" class="form-control" placeholder="{{ __('https://www.youtube.com/watch?v=... or https://vimeo.com/...') }}" value="{{ old('content_url', $lesson->content_type === 'video' ? $lesson->content_url : '') }}" data-required="true" data-video-url-input />
            <div class="text-muted fs-7 mt-1">{{ __('Enter YouTube or Vimeo URL') }}</div>
            <div class="video-preview mt-3" style="display: none;"></div>
        </div>

        {{-- Video Upload --}}
        <div class="mb-10 fv-row content-field" data-type="video_upload" style="display: {{ old('content_type', $lesson->content_type) === 'video_upload' ? 'block' : 'none' }};">
            <label class="form-label">{{ __('Video File') }}</label>
            @if($lesson->content_type === 'video_upload' && $lesson->file_path)
            <div class="alert alert-info mb-3">
                <div class="d-flex align-items-center">
                    {!! getIcon('video', 'fs-2 text-info me-3') !!}
                    <div>
                        <div class="fw-bold text-gray-800">{{ __('Current Video') }}:</div>
                        <a href="{{ Storage::url($lesson->file_path) }}" target="_blank" class="text-primary fw-semibold">{{ basename($lesson->file_path) }}</a>
                    </div>
                </div>
            </div>
            @endif
            <input type="file" name="content_file" class="form-control" accept="video/*" />
            <div class="text-muted fs-7 mt-1">{{ __('Maximum file size: 100MB. Supported formats: MP4, WebM, MOV') }}. {{ __('Leave empty to keep current file.') }}</div>
            <div class="video-preview mt-3" style="display: none;"></div>
        </div>

        {{-- PDF --}}
        <div class="mb-10 fv-row content-field" data-type="pdf" style="display: {{ old('content_type', $lesson->content_type) === 'pdf' ? 'block' : 'none' }};">
            <label class="form-label">{{ __('PDF File') }}</label>
            @if($lesson->content_type === 'pdf' && $lesson->file_path)
            <div class="alert alert-info mb-3">
                <div class="d-flex align-items-center">
                    {!! getIcon('document', 'fs-2 text-info me-3') !!}
                    <div>
                        <div class="fw-bold text-gray-800">{{ __('Current PDF') }}:</div>
                        <a href="{{ Storage::url($lesson->file_path) }}" target="_blank" class="text-primary fw-semibold">{{ basename($lesson->file_path) }}</a>
                    </div>
                </div>
            </div>
            @endif
            <input type="file" name="content_file" class="form-control" accept=".pdf" />
            <div class="text-muted fs-7 mt-1">{{ __('Maximum file size: 25MB') }}. {{ __('Leave empty to keep current file.') }}</div>
            <div class="pdf-preview mt-3" style="display: none;"></div>
        </div>

        {{-- External Link --}}
        <div class="mb-10 fv-row content-field" data-type="external_link" style="display: {{ old('content_type', $lesson->content_type) === 'external_link' ? 'block' : 'none' }};">
            <label class="required form-label">{{ __('Link URL') }}</label>
            <input type="url" name="content_url" class="form-control" placeholder="{{ __('https://example.com') }}" value="{{ old('content_url', $lesson->content_type === 'external_link' ? $lesson->content_url : '') }}" data-required="true" />
            <div class="form-check form-check-custom form-check-solid mt-3">
                <input class="form-check-input" type="checkbox" name="open_new_tab" value="1" id="edit_open_new_tab_{{ $lesson->id }}" {{ old('open_new_tab', $lesson->open_new_tab) ? 'checked' : '' }} />
                <label class="form-check-label" for="edit_open_new_tab_{{ $lesson->id }}">
                    {{ __('Open in new tab') }}
                </label>
            </div>
        </div>
    </div>

    {{-- Status and Duration --}}
    <div class="row">
        <div class="col-md-6 mb-10 fv-row">
            <label class="form-label">{{ __('Status') }}</label>
            <select name="status" class="form-select">
                <option value="draft" {{ old('status', $lesson->status) === 'draft' ? 'selected' : '' }}>{{ __('Draft') }}</option>
                <option value="published" {{ old('status', $lesson->status) === 'published' ? 'selected' : '' }}>{{ __('Published') }}</option>
            </select>
        </div>
        <div class="col-md-6 mb-10 fv-row">
            <label class="form-label">{{ __('Estimated Duration (minutes)') }}</label>
            <input type="number" name="estimated_duration" class="form-control" min="1" placeholder="{{ __('Optional') }}" value="{{ old('estimated_duration', $lesson->estimated_duration) }}" />
        </div>
    </div>
</x-modals.ajax-form>
