{{--
 * Student Create/Edit Modals
 *
 * Provides AJAX modals for creating and updating students.
 * Works with x-modals.ajax-form and the students table JS to reload data.
 *
 * @param \Illuminate\Database\Eloquent\Collection $programs Available programs for selection
--}}

@php
    $updateUrlTemplate = route('admin.students.update', '__ID__');
@endphp

{{-- Create Student Modal --}}
<x-modals.ajax-form
    id="studentCreateModal"
    title="{{ __('Add Student') }}"
    :action="route('admin.students.store')"
    method="POST"
    size="lg"
    successMessage="{{ __('Student created successfully!') }}"
    submitLabel="{{ __('Create Student') }}"
    :resetOnSuccess="true"
>
    <div class="row mb-5">
        <div class="col-md-6">
            <label class="required form-label">{{ __('First Name') }}</label>
            <input type="text"
                   name="first_name"
                   class="form-control form-control-solid"
                   placeholder="{{ __('Enter first name') }}"
                   maxlength="255"
                   required />
        </div>
        <div class="col-md-6">
            <label class="required form-label">{{ __('Last Name') }}</label>
            <input type="text"
                   name="last_name"
                   class="form-control form-control-solid"
                   placeholder="{{ __('Enter last name') }}"
                   maxlength="255"
                   required />
        </div>
    </div>

    <div class="mb-5">
        <label class="required form-label">{{ __('Email') }}</label>
        <input type="email"
               name="email"
               class="form-control form-control-solid"
               placeholder="{{ __('example@domain.com') }}"
               required />
    </div>

    <div class="row mb-5">
        <div class="col-md-6">
            <label class="required form-label">{{ __('Password') }}</label>
            <input type="password"
                   name="password"
                   class="form-control form-control-solid"
                   placeholder="{{ __('Password') }}"
                   minlength="8"
                   required />
            <div class="form-text">{{ __('Minimum 8 characters') }}</div>
        </div>
        <div class="col-md-6">
            <label class="required form-label">{{ __('Confirm Password') }}</label>
            <input type="password"
                   name="password_confirmation"
                   class="form-control form-control-solid"
                   placeholder="{{ __('Confirm Password') }}"
                   required />
        </div>
    </div>

    <div class="row mb-5">
        <div class="col-md-6">
            <label class="form-label">{{ __('Phone') }}</label>
            <input type="text"
                   name="phone"
                   class="form-control form-control-solid"
                   placeholder="{{ __('Phone number') }}"
                   maxlength="20" />
        </div>
        <div class="col-md-6">
            <label class="form-label">{{ __('Date of Birth') }}</label>
            <input type="date"
                   name="date_of_birth"
                   class="form-control form-control-solid" />
        </div>
    </div>

    <div class="mb-2">
        <label class="required form-label">{{ __('Program') }}</label>
        <select name="program_id" class="form-select form-select-solid" required>
            <option value="">{{ __('Select a program...') }}</option>
            @foreach ($programs as $program)
                <option value="{{ $program['id'] }}">{{ $program['name'] }}</option>
            @endforeach
        </select>
        <div class="form-text">{{ __('Select the program the student will be enrolled in.') }}</div>
    </div>
</x-modals.ajax-form>

{{-- Edit Student Modal --}}
<x-modals.ajax-form
    id="studentEditModal"
    title="{{ __('Edit Student') }}"
    action="#"
    method="PUT"
    size="lg"
    successMessage="{{ __('Student updated successfully!') }}"
    submitLabel="{{ __('Update Student') }}"
    :resetOnSuccess="false"
>
    <div class="row mb-5">
        <div class="col-md-6">
            <label class="required form-label">{{ __('First Name') }}</label>
            <input type="text"
                   name="first_name"
                   class="form-control form-control-solid"
                   placeholder="{{ __('Enter first name') }}"
                   maxlength="255"
                   required
                   data-student-field="first_name" />
        </div>
        <div class="col-md-6">
            <label class="required form-label">{{ __('Last Name') }}</label>
            <input type="text"
                   name="last_name"
                   class="form-control form-control-solid"
                   placeholder="{{ __('Enter last name') }}"
                   maxlength="255"
                   required
                   data-student-field="last_name" />
        </div>
    </div>

    <div class="mb-5">
        <label class="required form-label">{{ __('Email') }}</label>
        <input type="email"
               name="email"
               class="form-control form-control-solid"
               placeholder="{{ __('example@domain.com') }}"
               required
               data-student-field="email" />
    </div>

    <div class="row mb-5">
        <div class="col-md-6">
            <label class="form-label">{{ __('Password') }}</label>
            <input type="password"
                   name="password"
                   class="form-control form-control-solid"
                   placeholder="{{ __('Leave blank to keep current') }}"
                   minlength="8" />
            <div class="form-text">{{ __('Leave blank to keep current password. Minimum 8 characters if changing.') }}</div>
        </div>
        <div class="col-md-6" id="editPasswordConfirmRow" style="display: none;">
            <label class="required form-label">{{ __('Confirm Password') }}</label>
            <input type="password"
                   name="password_confirmation"
                   class="form-control form-control-solid"
                   placeholder="{{ __('Confirm Password') }}" />
        </div>
    </div>

    <div class="row mb-5">
        <div class="col-md-6">
            <label class="form-label">{{ __('Phone') }}</label>
            <input type="text"
                   name="phone"
                   class="form-control form-control-solid"
                   placeholder="{{ __('Phone number') }}"
                   maxlength="20"
                   data-student-field="phone" />
        </div>
        <div class="col-md-6">
            <label class="form-label">{{ __('Date of Birth') }}</label>
            <input type="date"
                   name="date_of_birth"
                   class="form-control form-control-solid"
                   data-student-field="date_of_birth" />
        </div>
    </div>

    {{-- Program Change Warning --}}
    <div class="alert alert-danger d-none mb-5" id="programChangeWarning">
        <div class="d-flex align-items-center">
            {!! getIcon('information-5', 'fs-2hx text-danger me-3') !!}
            <div class="d-flex flex-column">
                <h5 class="mb-1 text-danger fw-bold">{{ __('Warning: Program Change') }}</h5>
                <span class="text-gray-700">{{ __('Changing the program will reset all course progress tracking for this student. This action cannot be undone.') }}</span>
            </div>
        </div>
    </div>

    <div class="mb-2">
        <label class="required form-label">{{ __('Program') }}</label>
        <select name="program_id" class="form-select form-select-solid" required data-student-field="program_id">
            <option value="">{{ __('Select a program...') }}</option>
            @foreach ($programs as $program)
                <option value="{{ $program['id'] }}">{{ $program['name'] }}</option>
            @endforeach
        </select>
    </div>

    <input type="hidden" data-update-url-template="{{ $updateUrlTemplate }}">
</x-modals.ajax-form>

{{-- Delete Student Confirmation Modal --}}
<x-modals.ajax-form
    id="studentDeleteModal"
    title="{{ __('Delete Student') }}"
    action="#"
    method="DELETE"
    size="md"
    successMessage="{{ __('Student deleted successfully!') }}"
    submitLabel="{{ __('Delete Student') }}"
    submitClass="btn-danger"
    :confirmOnSubmit="false"
>
    <div class="text-center py-5">
        <div class="symbol symbol-100px symbol-circle mb-5">
            <div class="symbol-label bg-light-danger">
                {!! getIcon('trash', 'fs-1 text-danger') !!}
            </div>
        </div>
        <h3 class="fw-bold mb-3">{{ __('Delete Student?') }}</h3>
        <p class="text-gray-600 mb-0">{{ __('Are you sure you want to delete') }}</p>
        <p class="fw-bold text-gray-800 fs-5" data-student-delete-name></p>
        <p class="text-gray-500 fs-7">{{ __('This action cannot be undone.') }}</p>
    </div>

    <input type="hidden" data-delete-url-template="{{ route('admin.students.destroy', '__ID__') }}">
</x-modals.ajax-form>
