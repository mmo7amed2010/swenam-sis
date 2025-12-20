{{--
/**
 * Select Dropdown Component
 *
 * Dropdown select field with options, label, help text, and error handling.
 * Auto-generates option tags from array.
 *
 * @param string $name - Select name attribute (required)
 * @param string $label - Field label text
 * @param array $options - Options as key=>value pairs (required)
 * @param string|array|null $value - Selected value(s)
 * @param bool $required - Mark field as required (default: false)
 * @param string|null $placeholder - Placeholder option text (adds empty option)
 * @param string|null $help - Help text displayed below select
 * @param bool $multiple - Enable multiple selection (default: false)
 * @param string $selectClass - Additional CSS classes for select
 * @param string $groupClass - Additional CSS classes for wrapper
 * @param bool $disabled - Disable select (default: false)
 *
 * @example Basic Select
 * <x-forms.select
 *     name="difficulty_level"
 *     label="Difficulty Level"
 *     :options="['beginner' => 'Beginner', 'intermediate' => 'Intermediate', 'advanced' => 'Advanced']"
 *     :required="true"
 * />
 *
 * @example With Placeholder
 * <x-forms.select
 *     name="category"
 *     label="Category"
 *     :options="$categories"
 *     placeholder="Select a category..."
 * />
 *
 * @example Multi-select
 * <x-forms.select
 *     name="tags[]"
 *     label="Tags"
 *     :options="$tagList"
 *     :multiple="true"
 *     help="Hold Ctrl/Cmd to select multiple"
 * />
 *
 * @example With Old Value
 * <x-forms.select
 *     name="status"
 *     label="Status"
 *     :options="['draft' => 'Draft', 'published' => 'Published']"
 *     :value="old('status', $course->status)"
 * />
 */
--}}

@props([
    'name',
    'label',
    'options',
    'value' => null,
    'required' => false,
    'placeholder' => null,
    'help' => null,
    'multiple' => false,
    'selectClass' => '',
    'groupClass' => '',
    'disabled' => false,
])

@php
    $selectId = 'select_' . str_replace(['[', ']', '.'], '_', $name);
    $hasError = $errors->has(str_replace('[]', '', $name));
    $selectedValue = old(str_replace('[]', '', $name), $value);

    // Convert single value to array for consistent comparison
    $selectedValues = is_array($selectedValue) ? $selectedValue : [$selectedValue];

    $selectAttributes = [
        'name' => $name,
        'id' => $selectId,
        'class' => 'form-select ' . $selectClass . ($hasError ? ' is-invalid' : ''),
    ];

    if ($required) {
        $selectAttributes['required'] = true;
        $selectAttributes['aria-required'] = 'true';
    }

    if ($multiple) {
        $selectAttributes['multiple'] = true;
        $selectAttributes['size'] = min(count($options), 5);
    }

    if ($disabled) {
        $selectAttributes['disabled'] = true;
    }

    if ($hasError) $selectAttributes['aria-invalid'] = 'true';
    if ($help) $selectAttributes['aria-describedby'] = $selectId . '_help';
@endphp

<div class="mb-10 fv-row {{ $groupClass }}">
    {{-- Label --}}
    <label for="{{ $selectId }}" class="form-label {{ $required ? 'required' : '' }}">
        {{ $label }}
    </label>

    {{-- Select --}}
    <select {{ $attributes->merge($selectAttributes) }}>
        {{-- Placeholder Option --}}
        @if($placeholder && !$multiple)
        <option value="">{{ $placeholder }}</option>
        @endif

        {{-- Options --}}
        @foreach($options as $optionValue => $optionLabel)
        <option
            value="{{ $optionValue }}"
            {{ in_array($optionValue, $selectedValues) ? 'selected' : '' }}
        >
            {{ $optionLabel }}
        </option>
        @endforeach
    </select>

    {{-- Help Text --}}
    @if($help)
    <div id="{{ $selectId }}_help" class="form-text text-muted fs-7">{{ $help }}</div>
    @endif

    {{-- Error Message --}}
    @error(str_replace('[]', '', $name))
    <div class="invalid-feedback" role="alert">{{ $message }}</div>
    @enderror
</div>
