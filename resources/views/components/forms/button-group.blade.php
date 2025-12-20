{{--
/**
 * Form Button Group Component
 *
 * Standardized submit + cancel button group for forms.
 *
 * @param string $submitLabel - Submit button label (default: Save)
 * @param string $submitColor - Submit button color (default: primary)
 * @param string|null $cancelRoute - Cancel button route
 * @param string $cancelLabel - Cancel button label (default: Cancel)
 *
 * @slot actions - Additional action buttons
 *
 * @example
 * <x-forms.button-group submit-label="Create Course" cancel-route="admin.courses.index" />
 */
--}}

@props([
    'submitLabel' => 'Save',
    'submitColor' => 'primary',
    'cancelRoute' => null,
    'cancelLabel' => 'Cancel',
])

<div class="d-flex justify-content-end gap-2">
    @if (isset($actions))
        {{ $actions }}
    @endif

    @if ($cancelRoute)
        <a href="{{ route($cancelRoute) }}" class="btn btn-light">
            {{ $cancelLabel }}
        </a>
    @endif

    <button type="submit" class="btn btn-{{ $submitColor }}">
        {{ $submitLabel }}
    </button>
</div>

