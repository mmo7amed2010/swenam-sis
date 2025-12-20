{{--
    General Info Fields Partial

    Configurable partial for common general information fields used across CRUD forms.

    Usage:
    @include('partials.forms._general-info-fields', [
        'model' => $course ?? null,  // For edit mode
        'fields' => [
            'title' => ['required' => true, 'help' => 'Enter title'],
            'description' => ['required' => true, 'rows' => 5],
            'due_date' => ['type' => 'date'],
        ]
    ])

    Configuration:
    - model: Existing model instance for edit mode (optional)
    - fields: Array of field configurations (default: title, description)

    Field Types:
    - text: Standard text input
    - textarea: Multi-line textarea
    - date: Date picker
    - number: Number input
    - select: Dropdown select

    @see resources/views/pages/apps/courses/create.blade.php Original pattern
--}}

@php
    $model = $model ?? null;
    $isEditMode = !is_null($model);
    $fields = $fields ?? ['title' => [], 'description' => []];
@endphp

{{-- Title Field (if configured) --}}
@if(isset($fields['title']))
    @php
        $titleConfig = array_merge([
            'label' => __('Title'),
            'required' => true,
            'placeholder' => __('Enter title'),
            'help' => null,
        ], $fields['title']);
    @endphp

    <x-forms.form-group
        name="title"
        :label="$titleConfig['label']"
        :required="$titleConfig['required']"
        :placeholder="$titleConfig['placeholder']"
        :value="old('title', $isEditMode ? $model->title : null)"
        :help="$titleConfig['help']"
    />
    @error('title')
        <div class="text-danger fs-7 mt-1">{{ $message }}</div>
    @enderror
@endif

{{-- Description Field (if configured) --}}
@if(isset($fields['description']))
    @php
        $descConfig = array_merge([
            'label' => __('Description'),
            'required' => false,
            'rows' => 5,
            'placeholder' => __('Enter description'),
            'help' => null,
        ], $fields['description']);
    @endphp

    <x-forms.textarea
        name="description"
        :label="$descConfig['label']"
        :rows="$descConfig['rows']"
        :placeholder="$descConfig['placeholder']"
        :value="old('description', $isEditMode ? $model->description : null)"
        :help="$descConfig['help']"
        :required="$descConfig['required']"
    />
    @error('description')
        <div class="text-danger fs-7 mt-1">{{ $message }}</div>
    @enderror
@endif

{{-- Due Date Field (if configured) --}}
@if(isset($fields['due_date']))
    @php
        $dueDateConfig = array_merge([
            'label' => __('Due Date'),
            'required' => false,
            'placeholder' => __('Select due date'),
            'help' => null,
        ], $fields['due_date']);
    @endphp

    <x-forms.form-group
        name="due_date"
        type="date"
        :label="$dueDateConfig['label']"
        :required="$dueDateConfig['required']"
        :placeholder="$dueDateConfig['placeholder']"
        :value="old('due_date', $isEditMode ? $model->due_date : null)"
        :help="$dueDateConfig['help']"
    />
    @error('due_date')
        <div class="text-danger fs-7 mt-1">{{ $message }}</div>
    @enderror
@endif

{{-- Points Field (if configured) --}}
@if(isset($fields['points']))
    @php
        $pointsConfig = array_merge([
            'label' => __('Points'),
            'required' => true,
            'placeholder' => '100',
            'min' => 0,
            'max' => 1000,
            'help' => null,
        ], $fields['points']);
    @endphp

    <x-forms.form-group
        name="points"
        type="number"
        :label="$pointsConfig['label']"
        :required="$pointsConfig['required']"
        :placeholder="$pointsConfig['placeholder']"
        :value="old('points', $isEditMode ? $model->points : 100)"
        :min="$pointsConfig['min']"
        :max="$pointsConfig['max']"
        :help="$pointsConfig['help']"
    />
    @error('points')
        <div class="text-danger fs-7 mt-1">{{ $message }}</div>
    @enderror
@endif
