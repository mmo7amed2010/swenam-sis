<x-default-layout>

    @section('title')
        {{ __('Edit Instructor') }} - {{ $instructor->name }}
    @endsection

    @section('breadcrumbs')
        {{ Breadcrumbs::render('admin.instructors.edit', $instructor) }}
    @endsection

    <form action="{{ route('admin.instructors.update', $instructor) }}" method="POST" id="editInstructorForm">
        @csrf
        @method('PUT')

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <x-forms.validation-errors />

        <x-forms.card-section title="Edit Instructor">
            <x-forms.form-group
                name="first_name"
                label="{{ __('First Name') }}"
                :value="old('first_name', $instructor->first_name)"
                :required="true"
            />

            <x-forms.form-group
                name="last_name"
                label="{{ __('Last Name') }}"
                :value="old('last_name', $instructor->last_name)"
                :required="true"
            />

            <x-forms.form-group
                name="email"
                label="{{ __('Email') }}"
                type="email"
                :value="old('email', $instructor->email)"
                :required="true"
            />

            <x-forms.form-group
                name="password"
                label="{{ __('Password') }}"
                type="password"
                placeholder="{{ __('Leave blank to keep current password') }}"
                help="{{ __('Leave blank to keep the current password. Minimum 8 characters if changing.') }}"
            />

            <x-forms.form-group
                name="password_confirmation"
                label="{{ __('Confirm Password') }}"
                type="password"
                placeholder="{{ __('Confirm new password') }}"
            />

            <div class="d-flex justify-content-end gap-3">
                <a href="{{ route('admin.instructors.show', $instructor) }}" class="btn btn-light">
                    {{ __('Cancel') }}
                </a>
                <button type="submit" class="btn btn-primary">
                    <span class="indicator-label">{{ __('Update Instructor') }}</span>
                    <span class="indicator-progress">
                        {{ __('Please wait...') }}
                        <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                    </span>
                </button>
            </div>
        </x-forms.card-section>
    </form>

</x-default-layout>
