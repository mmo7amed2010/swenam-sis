{{--
    Validation Errors Component

    Displays validation errors in a Bootstrap alert with icon.

    Usage:
    <x-forms.validation-errors />

    Features:
    - Only renders if validation errors exist ($errors->any())
    - Uses Bootstrap alert-danger styling
    - Displays icon using getIcon helper
    - Lists all validation errors
    - Dismissible alert

    @see resources/views/pages/apps/courses/create.blade.php Original pattern
--}}
@if ($errors->any())
    <div class="alert alert-danger d-flex align-items-center p-5 mb-10">
        <span class="svg-icon svg-icon-2hx svg-icon-danger me-4">
            {!! getIcon('information', 'svg-icon-2hx svg-icon-danger') !!}
        </span>

        <div class="d-flex flex-column">
            <h4 class="mb-1 text-danger">{{ __('Validation Errors') }}</h4>
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    </div>
@endif
