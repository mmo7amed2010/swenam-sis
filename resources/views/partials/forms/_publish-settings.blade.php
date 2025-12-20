{{--
    Publish Settings Partial

    Configurable partial for publish/status settings used in sidebar of CRUD forms.

    Usage:
    @include('partials.forms._publish-settings', [
        'model' => $course ?? null,  // For edit mode
        'entityType' => 'course',    // Entity type name
        'statusField' => 'status',   // Status field name (default: 'status')
        'statuses' => [              // Available statuses
            'draft' => __('Draft'),
            'published' => __('Published')
        ],
        'publishToggle' => true,     // Show publish toggle (default: false)
    ])

    Configuration:
    - model: Existing model instance for edit mode (optional)
    - entityType: Type of entity (course, assignment, quiz, etc.)
    - statusField: Name of status field (default: 'status')
    - statuses: Array of available statuses
    - publishToggle: Show publish now checkbox (default: false)

    @see resources/views/pages/apps/courses/create.blade.php Original pattern
--}}

@php
    $model = $model ?? null;
    $isEditMode = !is_null($model);
    $entityType = $entityType ?? 'item';
    $statusField = $statusField ?? 'status';
    $publishToggle = $publishToggle ?? false;

    $defaultStatuses = [
        'draft' => __('Draft'),
        'published' => __('Published'),
        'archived' => __('Archived'),
    ];

    $statuses = $statuses ?? $defaultStatuses;
    $currentStatus = old($statusField, $isEditMode ? $model->{$statusField} : 'draft');
@endphp

{{-- Status Select Field --}}
<div class="mb-10">
    <label class="form-label required">{{ __('Status') }}</label>
    <select name="{{ $statusField }}" class="form-select" required>
        @foreach($statuses as $value => $label)
            <option value="{{ $value }}" @selected($currentStatus === $value)>
                {{ $label }}
            </option>
        @endforeach
    </select>
    <div class="form-text text-muted fs-7">
        {{ __('Select the publication status for this :entity', ['entity' => $entityType]) }}
    </div>
    @error($statusField)
        <div class="text-danger fs-7 mt-1">{{ $message }}</div>
    @enderror
</div>

{{-- Publish Toggle (if enabled) --}}
@if($publishToggle)
    <x-forms.checkbox
        name="publish_now"
        :label="__('Publish Now')"
        :checked="old('publish_now', false)"
        :switch="true"
        :help="__('Make this :entity immediately visible to users', ['entity' => $entityType])"
    />
@endif

{{-- Published At Display (Edit mode only) --}}
@if($isEditMode && isset($model->published_at) && $model->published_at)
    <div class="alert alert-info d-flex align-items-center p-4">
        <span class="svg-icon svg-icon-2hx svg-icon-info me-3">
            {!! getIcon('information', 'svg-icon-2hx svg-icon-info') !!}
        </span>
        <div class="d-flex flex-column">
            <span class="fw-bold">{{ __('Published') }}</span>
            <span class="fs-7">{{ $model->published_at->format('M d, Y \a\t H:i') }}</span>
        </div>
    </div>
@endif

{{-- Archived At Display (Edit mode only) --}}
@if($isEditMode && isset($model->archived_at) && $model->archived_at)
    <div class="alert alert-warning d-flex align-items-center p-4">
        <span class="svg-icon svg-icon-2hx svg-icon-warning me-3">
            {!! getIcon('information', 'svg-icon-2hx svg-icon-warning') !!}
        </span>
        <div class="d-flex flex-column">
            <span class="fw-bold">{{ __('Archived') }}</span>
            <span class="fs-7">{{ $model->archived_at->format('M d, Y \a\t H:i') }}</span>
        </div>
    </div>
@endif
