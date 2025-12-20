{{--
 * Shared Add Assignment Modal Component
 *
 * Simplified AJAX-based modal for creating new assignments for self-paced online courses.
 * Used by both admin and instructor views with context-specific routing.
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
        ? route('admin.programs.courses.assignments.store', [$program, $course])
        : route('instructor.courses.assignments.store', [$program, $course]);
    $refreshUrl = $isAdmin
        ? route('admin.programs.courses.modules.content', [$program, $course, $module])
        : route('instructor.courses.modules.content', [$program, $course, $module]);
@endphp

<x-modals.ajax-form
    id="kt_modal_add_assignment_{{ $module->id }}"
    title="{{ __('Add Assignment') }} - {{ $module->title }}"
    :action="$action"
    method="POST"
    size="lg"
    targetContainer="#module-content-{{ $module->id }}"
    :refreshUrl="$refreshUrl"
    successMessage="{{ __('Assignment created successfully') }}"
    submitLabel="{{ __('Create Assignment') }}"
>
    <input type="hidden" name="module_id" value="{{ $module->id }}">

    {{-- Title --}}
    <div class="mb-10 fv-row">
        <label class="required form-label">{{ __('Title') }}</label>
        <input type="text" name="title" class="form-control"
               placeholder="{{ __('E.g., Week 1 Essay or Final Project') }}" required />
    </div>

    {{-- Description --}}
    <div class="mb-10 fv-row">
        <label class="form-label">{{ __('Description') }}</label>
        <textarea name="description" class="form-control" rows="3"
                  placeholder="{{ __('Assignment instructions and requirements...') }}"></textarea>
    </div>

    <div class="row">
        {{-- Total Points --}}
        <div class="col-md-6 mb-10 fv-row">
            <label class="required form-label">{{ __('Total Points') }}</label>
            <input type="number" name="total_points" class="form-control"
                   min="1" max="1000" value="100" required />
        </div>

        {{-- Passing Score --}}
        <div class="col-md-6 mb-10 fv-row">
            <label class="form-label">{{ __('Passing Score (%)') }}</label>
            <input type="number" name="passing_score" class="form-control"
                   min="0" max="100" value="60" />
            <div class="text-muted fs-7 mt-1">{{ __('Minimum percentage required to pass') }}</div>
        </div>
    </div>

    {{-- Submission Type --}}
    <div class="mb-10 fv-row">
        <label class="required form-label">{{ __('Submission Type') }}</label>
        <select name="submission_type" class="form-select submission-type-select" required>
            <option value="file_upload">{{ __('File Upload') }}</option>
            <option value="text_entry">{{ __('Text Entry') }}</option>
            <option value="url_submission">{{ __('URL Submission') }}</option>
            <option value="multiple">{{ __('Multiple Types') }}</option>
        </select>
    </div>

    {{-- Simplified File Upload Settings (conditional) --}}
    <div class="file-upload-settings" id="file_settings_{{ $module->id }}">
        <div class="row">
            <div class="col-md-6 mb-10 fv-row">
                <label class="form-label">{{ __('Max File Size (MB)') }}</label>
                <input type="number" name="max_file_size_mb" class="form-control"
                       min="1" max="50" value="10" />
                <div class="text-muted fs-7 mt-1">{{ __('Common file types accepted (PDF, DOCX, images, etc.)') }}</div>
            </div>
        </div>
    </div>

    {{-- Published Toggle --}}
    <div class="fv-row">
        <div class="form-check form-switch form-check-custom form-check-solid">
            <input class="form-check-input" type="checkbox"
                   name="is_published" value="1" id="is_published_{{ $module->id }}" />
            <label class="form-check-label" for="is_published_{{ $module->id }}">
                {{ __('Publish immediately') }}
            </label>
        </div>
        <div class="text-muted fs-7 mt-1">{{ __('If unchecked, assignment will be saved as draft') }}</div>
    </div>
</x-modals.ajax-form>
