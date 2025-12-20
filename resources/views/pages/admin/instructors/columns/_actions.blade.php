<x-actions.dropdown buttonText="{{ __('Actions') }}" buttonClass="btn-light btn-active-light-primary btn-sm" buttonIcon="down">
    <x-actions.link href="{{ route('admin.instructors.show', $instructor) }}">
        {{ __('View') }}
    </x-actions.link>

    <x-actions.link href="{{ route('admin.instructors.edit', $instructor) }}">
        {{ __('Edit') }}
    </x-actions.link>

    <x-actions.separator />

    <x-actions.form-button
        action="{{ route('admin.instructors.destroy', $instructor) }}"
        method="DELETE"
        confirm="{{ __('Are you sure you want to delete this instructor?') }}"
        :danger="true"
    >
        {{ __('Delete') }}
    </x-actions.form-button>
</x-actions.dropdown>

