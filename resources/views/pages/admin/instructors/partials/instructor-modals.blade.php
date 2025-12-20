{{--
 * Instructor Create/Edit/Delete Modals
 *
 * Provides AJAX modals for creating, updating, and deleting instructors.
 * Works with x-modals.ajax-form and the instructors table JS to reload data.
--}}

@php
    $updateUrlTemplate = route('admin.instructors.update', '__ID__');
@endphp

{{-- Create Instructor Modal --}}
<x-modals.ajax-form
    id="instructorCreateModal"
    title="{{ __('Add Instructor') }}"
    :action="route('admin.instructors.store')"
    method="POST"
    size="lg"
    successMessage="{{ __('Instructor created successfully!') }}"
    submitLabel="{{ __('Create Instructor') }}"
    :resetOnSuccess="true"
>
    <div class="mb-7">
        <label class="d-block fw-semibold fs-6 mb-5">{{ __('Avatar') }}</label>
        <style>
            .image-input-placeholder { background-image: url('{{ image('svg/files/blank-image.svg') }}'); }
            [data-bs-theme="dark"] .image-input-placeholder { background-image: url('{{ image('svg/files/blank-image-dark.svg') }}'); }
        </style>
        <div class="image-input image-input-outline image-input-placeholder image-input-empty" data-kt-image-input="true">
            <div class="image-input-wrapper w-125px h-125px"></div>
            <label class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow" data-kt-image-input-action="change" data-bs-toggle="tooltip" title="{{ __('Change avatar') }}">
                {!! getIcon('pencil', 'fs-7') !!}
                <input type="file" name="avatar" accept=".png, .jpg, .jpeg" />
                <input type="hidden" name="avatar_remove" />
            </label>
            <span class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow" data-kt-image-input-action="cancel" data-bs-toggle="tooltip" title="{{ __('Cancel avatar') }}">
                {!! getIcon('cross', 'fs-2') !!}
            </span>
            <span class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow" data-kt-image-input-action="remove" data-bs-toggle="tooltip" title="{{ __('Remove avatar') }}">
                {!! getIcon('cross', 'fs-2') !!}
            </span>
        </div>
        <div class="form-text">{{ __('Allowed file types: png, jpg, jpeg.') }}</div>
    </div>

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
</x-modals.ajax-form>

{{-- Edit Instructor Modal --}}
<x-modals.ajax-form
    id="instructorEditModal"
    title="{{ __('Edit Instructor') }}"
    action="#"
    method="PUT"
    size="lg"
    successMessage="{{ __('Instructor updated successfully!') }}"
    submitLabel="{{ __('Update Instructor') }}"
    :resetOnSuccess="false"
>
    <div class="mb-7">
        <label class="d-block fw-semibold fs-6 mb-5">{{ __('Avatar') }}</label>
        <div class="image-input image-input-outline image-input-placeholder" data-kt-image-input="true" id="editInstructorAvatarInput">
            <div class="image-input-wrapper w-125px h-125px" data-instructor-avatar-preview></div>
            <label class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow" data-kt-image-input-action="change" data-bs-toggle="tooltip" title="{{ __('Change avatar') }}">
                {!! getIcon('pencil', 'fs-7') !!}
                <input type="file" name="avatar" accept=".png, .jpg, .jpeg" />
                <input type="hidden" name="avatar_remove" />
            </label>
            <span class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow" data-kt-image-input-action="cancel" data-bs-toggle="tooltip" title="{{ __('Cancel avatar') }}">
                {!! getIcon('cross', 'fs-2') !!}
            </span>
            <span class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow" data-kt-image-input-action="remove" data-bs-toggle="tooltip" title="{{ __('Remove avatar') }}">
                {!! getIcon('cross', 'fs-2') !!}
            </span>
        </div>
        <div class="form-text">{{ __('Allowed file types: png, jpg, jpeg.') }}</div>
    </div>

    <div class="row mb-5">
        <div class="col-md-6">
            <label class="required form-label">{{ __('First Name') }}</label>
            <input type="text"
                   name="first_name"
                   class="form-control form-control-solid"
                   placeholder="{{ __('Enter first name') }}"
                   maxlength="255"
                   required
                   data-instructor-field="first_name" />
        </div>
        <div class="col-md-6">
            <label class="required form-label">{{ __('Last Name') }}</label>
            <input type="text"
                   name="last_name"
                   class="form-control form-control-solid"
                   placeholder="{{ __('Enter last name') }}"
                   maxlength="255"
                   required
                   data-instructor-field="last_name" />
        </div>
    </div>

    <div class="mb-5">
        <label class="required form-label">{{ __('Email') }}</label>
        <input type="email"
               name="email"
               class="form-control form-control-solid"
               placeholder="{{ __('example@domain.com') }}"
               required
               data-instructor-field="email" />
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
        <div class="col-md-6" id="editInstructorPasswordConfirmRow" style="display: none;">
            <label class="required form-label">{{ __('Confirm Password') }}</label>
            <input type="password"
                   name="password_confirmation"
                   class="form-control form-control-solid"
                   placeholder="{{ __('Confirm Password') }}"
                   disabled />
        </div>
    </div>

    <input type="hidden" data-update-url-template="{{ $updateUrlTemplate }}">
</x-modals.ajax-form>

{{-- Delete Instructor Confirmation Modal --}}
<x-modals.ajax-form
    id="instructorDeleteModal"
    title="{{ __('Delete Instructor') }}"
    action="#"
    method="DELETE"
    size="md"
    successMessage="{{ __('Instructor deleted successfully!') }}"
    submitLabel="{{ __('Delete Instructor') }}"
    submitClass="btn-danger"
    :confirmOnSubmit="false"
>
    <div class="text-center py-5">
        <div class="symbol symbol-100px symbol-circle mb-5">
            <div class="symbol-label bg-light-danger">
                {!! getIcon('trash', 'fs-1 text-danger') !!}
            </div>
        </div>
        <h3 class="fw-bold mb-3">{{ __('Delete Instructor?') }}</h3>
        <p class="text-gray-600 mb-0">{{ __('Are you sure you want to delete') }}</p>
        <p class="fw-bold text-gray-800 fs-5" data-instructor-delete-name></p>
        <p class="text-gray-500 fs-7">{{ __('This action cannot be undone.') }}</p>
    </div>

    <input type="hidden" data-delete-url-template="{{ route('admin.instructors.destroy', '__ID__') }}">
</x-modals.ajax-form>
