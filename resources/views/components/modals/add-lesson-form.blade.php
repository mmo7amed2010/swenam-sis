{{--
 * Add Lesson Form Component
 *
 * Shared AJAX-based modal for creating new lessons with content type selection.
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
$action = $isAdmin
    ? route('admin.programs.courses.modules.lessons.store', [$program, $course, $module])
    : route('instructor.courses.modules.lessons.store', [$program, $course, $module]);
$refreshUrl = $isAdmin
    ? route('admin.programs.courses.modules.content', [$program, $course, $module])
    : route('instructor.courses.modules.content', [$program, $course, $module]);
@endphp

<x-modals.ajax-form
    id="kt_modal_add_lesson_{{ $module->id }}"
    title="{{ __('Add Lesson') }} - {{ $module->title }}"
    :action="$action"
    method="POST"
    size="lg"
    :hasFiles="true"
    targetContainer="#module-content-{{ $module->id }}"
    :refreshUrl="$refreshUrl"
    successMessage="{{ __('Lesson created successfully') }}"
    submitLabel="{{ __('Create Lesson') }}"
>
    {{-- Lesson Title --}}
    <div class="mb-10 fv-row">
        <label class="required form-label">{{ __('Lesson Title') }}</label>
        <input type="text" name="title" class="form-control" placeholder="{{ __('E.g., Introduction to Variables') }}" value="{{ old('title') }}" required />
    </div>

    {{-- Content Type Selector --}}
    <div class="mb-10 fv-row">
        <label class="required form-label">{{ __('Content Type') }}</label>
        <select name="content_type" class="form-select" required data-content-type-select>
            <option value="">{{ __('Select content type') }}</option>
            <option value="text_html" {{ old('content_type') === 'text_html' ? 'selected' : '' }}>{{ __('Text/HTML') }}</option>
            <option value="video" {{ old('content_type') === 'video' ? 'selected' : '' }}>{{ __('Video (YouTube/Vimeo)') }}</option>
            <option value="video_upload" {{ old('content_type') === 'video_upload' ? 'selected' : '' }}>{{ __('Video Upload') }}</option>
            <option value="pdf" {{ old('content_type') === 'pdf' ? 'selected' : '' }}>{{ __('PDF Upload') }}</option>
            <option value="external_link" {{ old('content_type') === 'external_link' ? 'selected' : '' }}>{{ __('External Link') }}</option>
        </select>
    </div>

    {{-- Dynamic content fields based on type --}}
    <div class="content-fields-container">
        {{-- Text/HTML --}}
        <div class="mb-10 fv-row content-field" data-type="text_html" style="display: none;">
            <label class="required form-label">{{ __('Content') }}</label>
            <div id="kt_lesson_content_editor_{{ $module->id }}" style="min-height: 300px;"></div>
            <textarea name="content" id="kt_lesson_content_textarea_{{ $module->id }}" class="form-control d-none" data-required="true">{{ old('content') }}</textarea>
            <div class="text-muted fs-7 mt-1">{{ __('Rich text editor for lesson content') }}</div>
        </div>

        {{-- Video (YouTube/Vimeo) --}}
        <div class="mb-10 fv-row content-field" data-type="video" style="display: none;">
            <label class="required form-label">{{ __('Video URL') }}</label>
            <input type="url" name="content_url" class="form-control" placeholder="{{ __('https://www.youtube.com/watch?v=... or https://vimeo.com/...') }}" value="{{ old('content_url') }}" data-required="true" data-video-url-input />
            <div class="text-muted fs-7 mt-1">{{ __('Enter YouTube or Vimeo URL') }}</div>
            <div class="video-preview mt-3" style="display: none;"></div>
        </div>

        {{-- Video Upload --}}
        <div class="mb-10 fv-row content-field" data-type="video_upload" style="display: none;">
            <label class="required form-label">{{ __('Video File') }}</label>
            <input type="file" name="content_file" class="form-control" accept="video/*" data-required="true" />
            <div class="text-muted fs-7 mt-1">{{ __('Maximum file size: 100MB. Supported formats: MP4, WebM, MOV') }}</div>
            <div class="video-preview mt-3" style="display: none;"></div>
        </div>

        {{-- PDF --}}
        <div class="mb-10 fv-row content-field" data-type="pdf" style="display: none;">
            <label class="required form-label">{{ __('PDF File') }}</label>
            <input type="file" name="content_file" class="form-control" accept=".pdf" data-required="true" />
            <div class="text-muted fs-7 mt-1">{{ __('Maximum file size: 25MB') }}</div>
            <div class="pdf-preview mt-3" style="display: none;"></div>
        </div>

        {{-- External Link --}}
        <div class="mb-10 fv-row content-field" data-type="external_link" style="display: none;">
            <label class="required form-label">{{ __('Link URL') }}</label>
            <input type="url" name="content_url" class="form-control" placeholder="{{ __('https://example.com') }}" value="{{ old('content_url') }}" data-required="true" />
            <div class="form-check form-check-custom form-check-solid mt-3">
                <input class="form-check-input" type="checkbox" name="open_new_tab" value="1" id="open_new_tab_{{ $module->id }}" {{ old('open_new_tab') ? 'checked' : '' }} />
                <label class="form-check-label" for="open_new_tab_{{ $module->id }}">
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
                <option value="draft" {{ old('status', 'draft') === 'draft' ? 'selected' : '' }}>{{ __('Draft') }}</option>
                <option value="published" {{ old('status') === 'published' ? 'selected' : '' }}>{{ __('Published') }}</option>
            </select>
        </div>
        <div class="col-md-6 mb-10 fv-row">
            <label class="form-label">{{ __('Estimated Duration (minutes)') }}</label>
            <input type="number" name="estimated_duration" class="form-control" min="1" placeholder="{{ __('Optional') }}" value="{{ old('estimated_duration') }}" />
        </div>
    </div>
</x-modals.ajax-form>
