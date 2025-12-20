{{--
 * Shared Edit Assignment Modal Component
 *
 * Simplified AJAX-based modal for editing assignments for self-paced online courses.
 * Used by both admin and instructor views with context-specific routing.
 *
 * @param string $context - 'admin' or 'instructor'
 * @param \App\Models\Program $program
 * @param \App\Models\Course $course
 * @param \App\Models\Assignment $assignment
 * @param \App\Models\CourseModule $module (optional)
--}}

@props(['context', 'program', 'course', 'assignment', 'module' => null])

@php
    $isAdmin = $context === 'admin';
    $hasSubmissions = $assignment->submissions()->count() > 0;
    $hasPublishedGrades = $assignment->submissions()->whereHas('grades', function ($q) {
        $q->where('is_published', true);
    })->exists();
    $moduleForRefresh = $module ?? $assignment->module;

    $action = $isAdmin
        ? route('admin.programs.courses.assignments.update', [$program, $course, $assignment])
        : route('instructor.courses.assignments.update', [$program, $course, $assignment]);
    $refreshUrl = $moduleForRefresh
        ? ($isAdmin
            ? route('admin.programs.courses.modules.content', [$program, $course, $moduleForRefresh])
            : route('instructor.courses.modules.content', [$program, $course, $moduleForRefresh]))
        : null;
@endphp

<x-modals.ajax-form
    id="kt_modal_edit_assignment_{{ $assignment->id }}"
    title="{{ __('Edit Assignment') }}"
    :action="$action"
    method="PUT"
    size="lg"
    :targetContainer="$moduleForRefresh ? '#module-content-' . $moduleForRefresh->id : null"
    :refreshUrl="$refreshUrl"
    successMessage="{{ __('Assignment updated successfully') }}"
    submitLabel="{{ __('Update Assignment') }}"
>
    @if($assignment->module_id)
    <input type="hidden" name="module_id" value="{{ $assignment->module_id }}">
    @endif

    @if($hasSubmissions)
    <div class="alert alert-warning mb-5">
        <div class="d-flex align-items-center">
            {!! getIcon('information-5', 'fs-2 text-warning me-3') !!}
            <div>
                <h6 class="mb-1">{{ __('Submissions Received') }}</h6>
                <p class="mb-0 small">{{ $assignment->submissions()->count() }} {{ __('submission(s) already received for this assignment.') }}</p>
            </div>
        </div>
    </div>
    @endif

    {{-- Title --}}
    <div class="mb-10 fv-row">
        <label class="required form-label">{{ __('Title') }}</label>
        <input type="text" name="title" class="form-control"
               placeholder="{{ __('E.g., Week 1 Essay or Final Project') }}"
               value="{{ old('title', $assignment->title) }}" required />
    </div>

    {{-- Description --}}
    <div class="mb-10 fv-row">
        <label class="form-label">{{ __('Description') }}</label>
        <textarea name="description" class="form-control" rows="3"
                  placeholder="{{ __('Assignment instructions and requirements...') }}">{{ old('description', $assignment->description) }}</textarea>
    </div>

    <div class="row">
        {{-- Total Points --}}
        <div class="col-md-6 mb-10 fv-row">
            <label class="required form-label">{{ __('Total Points') }}</label>
            @if($hasPublishedGrades)
                <input type="text" class="form-control" value="{{ $assignment->total_points ?? $assignment->max_points ?? 100 }}" readonly />
                <input type="hidden" name="total_points" value="{{ $assignment->total_points ?? $assignment->max_points ?? 100 }}" />
                <div class="text-warning fs-7 mt-1">
                    {!! getIcon('information', 'fs-7 me-1') !!}
                    {{ __('Cannot change points when grades are published') }}
                </div>
            @else
                <input type="number" name="total_points" class="form-control"
                       min="1" max="1000" value="{{ old('total_points', $assignment->total_points ?? $assignment->max_points ?? 100) }}" required />
            @endif
        </div>

        {{-- Passing Score --}}
        <div class="col-md-6 mb-10 fv-row">
            <label class="form-label">{{ __('Passing Score (%)') }}</label>
            <input type="number" name="passing_score" class="form-control"
                   min="0" max="100" value="{{ old('passing_score', $assignment->passing_score ?? 60) }}" />
            <div class="text-muted fs-7 mt-1">{{ __('Minimum percentage required to pass') }}</div>
        </div>
    </div>

    {{-- Submission Type --}}
    <div class="mb-10 fv-row">
        <label class="required form-label">{{ __('Submission Type') }}</label>
        <select name="submission_type" class="form-select submission-type-select" required>
            <option value="file_upload" {{ old('submission_type', $assignment->submission_type) === 'file_upload' ? 'selected' : '' }}>{{ __('File Upload') }}</option>
            <option value="text_entry" {{ old('submission_type', $assignment->submission_type) === 'text_entry' ? 'selected' : '' }}>{{ __('Text Entry') }}</option>
            <option value="url_submission" {{ old('submission_type', $assignment->submission_type) === 'url_submission' ? 'selected' : '' }}>{{ __('URL Submission') }}</option>
            <option value="multiple" {{ old('submission_type', $assignment->submission_type) === 'multiple' ? 'selected' : '' }}>{{ __('Multiple Types') }}</option>
        </select>
    </div>

    {{-- Simplified File Upload Settings (conditional) --}}
    <div class="file-upload-settings" id="file_settings_edit_{{ $assignment->id }}" style="display: {{ in_array($assignment->submission_type ?? 'file_upload', ['file_upload', 'multiple']) ? 'block' : 'none' }};">
        <div class="row">
            <div class="col-md-6 mb-10 fv-row">
                <label class="form-label">{{ __('Max File Size (MB)') }}</label>
                <input type="number" name="max_file_size_mb" class="form-control"
                       min="1" max="50" value="{{ old('max_file_size_mb', $assignment->max_file_size_mb ?? 10) }}" />
                <div class="text-muted fs-7 mt-1">{{ __('Common file types accepted (PDF, DOCX, images, etc.)') }}</div>
            </div>
        </div>
    </div>

    {{-- Published Toggle --}}
    <div class="fv-row">
        <div class="form-check form-switch form-check-custom form-check-solid">
            <input class="form-check-input" type="checkbox"
                   name="is_published" value="1" id="is_published_edit_{{ $assignment->id }}"
                   {{ old('is_published', $assignment->is_published) ? 'checked' : '' }} />
            <label class="form-check-label" for="is_published_edit_{{ $assignment->id }}">
                {{ __('Published') }}
            </label>
        </div>
        <div class="text-muted fs-7 mt-1">{{ __('Make available to students for self-paced learning') }}</div>
    </div>
</x-modals.ajax-form>
