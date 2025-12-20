<x-default-layout>

    @section('title')
        {{ __('Create Instructor') }}
    @endsection

    @section('breadcrumbs')
        {{ Breadcrumbs::render('admin.instructors.create') }}
    @endsection

    <form action="{{ route('admin.instructors.store') }}" method="POST" id="createInstructorForm">
        @csrf

        <x-forms.validation-errors />

        <x-forms.card-section title="Create New Instructor">
            <x-forms.form-group
                name="first_name"
                label="{{ __('First Name') }}"
                :value="old('first_name')"
                :required="true"
            />

            <x-forms.form-group
                name="last_name"
                label="{{ __('Last Name') }}"
                :value="old('last_name')"
                :required="true"
            />

            <x-forms.form-group
                name="email"
                label="{{ __('Email') }}"
                type="email"
                :value="old('email')"
                :required="true"
            />

            <x-forms.form-group
                name="password"
                label="{{ __('Password') }}"
                type="password"
                :required="true"
                help="{{ __('Minimum 8 characters.') }}"
            />

            <x-forms.form-group
                name="password_confirmation"
                label="{{ __('Confirm Password') }}"
                type="password"
                :required="true"
            />

            <x-forms.form-actions
                cancel-route="admin.instructors.index"
                submit-text="Create Instructor"
            />
        </x-forms.card-section>
    </form>

</x-default-layout>
