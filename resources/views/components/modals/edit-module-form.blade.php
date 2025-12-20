{{--
 * Edit Module Form Component
 *
 * Shared AJAX modal for editing an existing module in a course.
 * Data is loaded dynamically via JavaScript when edit button is clicked.
 * Supports both admin and instructor contexts via the context parameter.
 * Note: Permission checks (@can) should be handled by the calling view.
 *
 * @param string $context - 'admin' or 'instructor'
 * @param \App\Models\Program $program
 * @param \App\Models\Course $course
--}}

@props(['context', 'program', 'course'])

@php
$isAdmin = $context === 'admin';
// Using placeholder __MODULE_ID__ that JavaScript will replace with actual module ID
$action = $isAdmin
    ? route('admin.programs.courses.modules.update', [$program, $course, '__MODULE_ID__'])
    : route('instructor.courses.modules.update', [$program, $course, '__MODULE_ID__']);
$refreshUrl = $isAdmin
    ? route('admin.programs.courses.modules-content', [$program, $course])
    : route('instructor.courses.modules-content', [$program, $course]);
@endphp

<x-modals.ajax-form
    id="kt_modal_edit_module"
    title="{{ __('Edit Module') }}"
    :action="$action"
    method="PUT"
    size="lg"
    targetContainer="#modulesTabContent"
    :refreshUrl="$refreshUrl"
    successMessage="{{ __('Module updated successfully!') }}"
    submitLabel="{{ __('Update Module') }}"
    :resetOnSuccess="false"
>
    <div class="mb-10 fv-row">
        <label class="required form-label">{{ __('Module Title') }}</label>
        <input type="text"
               name="title"
               id="edit_module_title"
               class="form-control form-control-solid"
               placeholder="{{ __('E.g., Introduction to Programming') }}"
               required />
    </div>

    <div class="mb-10 fv-row">
        <label class="form-label">{{ __('Description') }}</label>
        <textarea name="description"
                  id="edit_module_description"
                  class="form-control form-control-solid"
                  rows="5"
                  placeholder="{{ __('Module description...') }}"></textarea>
        <div class="text-muted fs-7 mt-1">{{ __('Rich text editor for module description') }}</div>
    </div>

    <div class="mb-10 fv-row">
        <label class="form-label">{{ __('Status') }}</label>
        <select name="status" id="edit_module_status" class="form-select form-select-solid">
            <option value="draft">{{ __('Draft') }}</option>
            <option value="published">{{ __('Published') }}</option>
        </select>
        <div class="text-muted fs-7 mt-1">{{ __('Module status (inherits course visibility rules)') }}</div>
    </div>

    <div class="mb-10 fv-row">
        <label class="form-label d-flex align-items-center gap-2">
            <span class="form-check form-switch form-check-custom form-check-solid">
                {{-- Hidden input ensures value is sent when checkbox is unchecked --}}
                <input type="hidden" name="requires_exam_pass" value="0" />
                <input class="form-check-input" type="checkbox" name="requires_exam_pass" id="edit_module_requires_exam_pass" value="1" />
            </span>
            <span>{{ __('Require Exam Pass for Next Module') }}</span>
        </label>
        <div class="text-muted fs-7 mt-1">
            {{ __('If enabled, students must pass this module\'s exam before accessing the next module.') }}
        </div>
    </div>
</x-modals.ajax-form>
