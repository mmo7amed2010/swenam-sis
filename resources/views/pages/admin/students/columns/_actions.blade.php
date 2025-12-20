<x-actions.dropdown buttonText="{{ __('Actions') }}" buttonClass="btn-light btn-active-light-primary btn-sm" buttonIcon="down">
    <x-actions.link href="{{ route('admin.students.show', $student) }}">
        {{ __('View') }}
    </x-actions.link>

    <x-actions.link href="{{ route('admin.students.edit', $student) }}">
        {{ __('Edit') }}
    </x-actions.link>

    <x-actions.separator />

    <x-actions.form-button
        action="{{ route('admin.students.destroy', $student) }}"
        method="DELETE"
        confirm="{{ __('Are you sure you want to delete this student?') }}"
        :danger="true"
    >
        {{ __('Delete') }}
    </x-actions.form-button>
</x-actions.dropdown>

