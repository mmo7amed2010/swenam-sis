{{--
    Form Actions Component

    Standard Cancel/Submit button group for forms.

    Usage:
    <x-forms.form-actions
        cancel-route="admin.courses.index"
        submit-text="Create Course"
    />

    Props:
    - cancelRoute (required): Route name or URL for cancel button
    - submitText (optional): Text for submit button (default: 'Save')

    Features:
    - Right-aligned button group
    - Cancel button: Light style with back navigation
    - Submit button: Primary style with loading indicator
    - Gap spacing between buttons (gap-3)

    @see App\View\Components\Forms\FormActions
    @see resources/views/pages/apps/courses/create.blade.php Original pattern
--}}
<div class="d-flex justify-content-end gap-3">
    {{-- Cancel Button --}}
    @php
        // Check if cancelRoute is already a URL (starts with http/https or is a full path)
        // Route names contain dots (e.g., "admin.programs.courses.index")
        $isUrl = str_starts_with($cancelRoute, 'http://') 
            || str_starts_with($cancelRoute, 'https://')
            || (str_starts_with($cancelRoute, '/') && !str_contains($cancelRoute, '.'));
        $cancelUrl = $isUrl ? $cancelRoute : route($cancelRoute);
    @endphp
    <a href="{{ $cancelUrl }}" class="btn btn-light">
        {{ __('Cancel') }}
    </a>

    {{-- Submit Button with Loading State --}}
    <button type="submit" class="btn btn-primary">
        <span class="indicator-label">{{ __($submitText) }}</span>
        <span class="indicator-progress">
            {{ __('Please wait...') }}
            <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
        </span>
    </button>
</div>
