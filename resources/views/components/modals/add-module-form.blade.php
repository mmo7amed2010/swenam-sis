{{--
 * Add Module Form Component
 *
 * Shared AJAX modal for creating a new module in a course.
 * Supports both admin and instructor contexts via the context parameter.
 * Note: Permission checks (@can) should be handled by the calling view.
 *
 * @param string $context - 'admin' or 'instructor'
 * @param \App\Models\Program $program
 * @param \App\Models\Course $course
 * @param \Illuminate\Support\Collection $modules - for calculating next order
--}}

@props(['context', 'program', 'course', 'modules'])

@php
$isAdmin = $context === 'admin';
$action = $isAdmin
    ? route('admin.programs.courses.modules.store', [$program, $course])
    : route('instructor.courses.modules.store', [$program, $course]);
$refreshUrl = $isAdmin
    ? route('admin.programs.courses.modules-content', [$program, $course])
    : route('instructor.courses.modules-content', [$program, $course]);
@endphp

<x-modals.ajax-form
    id="kt_modal_add_module"
    title="{{ __('Add Module') }}"
    :action="$action"
    method="POST"
    size="lg"
    targetContainer="#modulesTabContent"
    :refreshUrl="$refreshUrl"
    successMessage="{{ __('Module created successfully!') }}"
    submitLabel="{{ __('Create Module') }}"
    :resetOnSuccess="true"
>
    <div class="mb-10 fv-row">
        <label class="required form-label">{{ __('Module Title') }}</label>
        <input type="text"
               name="title"
               class="form-control form-control-solid"
               placeholder="{{ __('E.g., Introduction to Programming') }}"
               required />
    </div>

    <div class="mb-10 fv-row">
        <label class="form-label">{{ __('Description') }}</label>
        <textarea name="description"
                  id="module_description"
                  class="form-control form-control-solid"
                  rows="5"
                  placeholder="{{ __('Module description...') }}"></textarea>
        <div class="text-muted fs-7 mt-1">{{ __('Rich text editor for module description') }}</div>
    </div>

    <div class="mb-10 fv-row">
        <label class="form-label">{{ __('Status') }}</label>
        <select name="status" class="form-select form-select-solid">
            <option value="draft" selected>{{ __('Draft') }}</option>
            <option value="published">{{ __('Published') }}</option>
        </select>
        <div class="text-muted fs-7 mt-1">{{ __('Module status (inherits course visibility rules)') }}</div>
    </div>

    <div class="mb-10 fv-row">
        <label class="form-label d-flex align-items-center gap-2">
            <span class="form-check form-switch form-check-custom form-check-solid">
                {{-- Hidden input ensures value is sent when checkbox is unchecked --}}
                <input type="hidden" name="requires_exam_pass" value="0" />
                <input class="form-check-input" type="checkbox" name="requires_exam_pass" value="1" />
            </span>
            <span>{{ __('Require Exam Pass for Next Module') }}</span>
        </label>
        <div class="text-muted fs-7 mt-1">
            {{ __('If enabled, students must pass this module\'s exam before accessing the next module.') }}
        </div>
    </div>

    <div class="text-muted fs-7">
        <strong>{{ __('Note:') }}</strong> {{ __('Order number will be auto-calculated (Module') }} {{ $modules->count() + 1 }}{{ __(')') }}
    </div>
</x-modals.ajax-form>
