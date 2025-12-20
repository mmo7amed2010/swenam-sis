{{--
/**
 * Radio Group Component
 *
 * Group of radio buttons with label and help text.
 * Auto-generates radio inputs from options array.
 *
 * @param string $name - Input name attribute (required)
 * @param string $label - Group label text (required)
 * @param array $options - Options as key=>value pairs (required)
 * @param string|null $value - Selected value
 * @param bool $required - Mark field as required (default: false)
 * @param string|null $help - Help text displayed below group
 * @param bool $inline - Display radio buttons inline (default: false)
 * @param string $radioClass - Additional CSS classes for radio inputs
 * @param string $groupClass - Additional CSS classes for wrapper
 * @param bool $disabled - Disable all radios (default: false)
 *
 * @example Basic Radio Group
 * <x-forms.radio-group
 *     name="course_type"
 *     label="Course Type"
 *     :options="['online' => 'Online Course', 'hybrid' => 'Hybrid', 'in-person' => 'In-Person']"
 *     :required="true"
 * />
 *
 * @example Inline Radio Group
 * <x-forms.radio-group
 *     name="difficulty"
 *     label="Difficulty Level"
 *     :options="['easy' => 'Easy', 'medium' => 'Medium', 'hard' => 'Hard']"
 *     :inline="true"
 * />
 *
 * @example With Pre-selected Value
 * <x-forms.radio-group
 *     name="status"
 *     label="Status"
 *     :options="['active' => 'Active', 'inactive' => 'Inactive']"
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
    'help' => null,
    'inline' => false,
    'radioClass' => '',
    'groupClass' => '',
    'disabled' => false,
])

@php
    $groupId = 'radio_group_' . str_replace(['[', ']', '.'], '_', $name);
    $hasError = $errors->has($name);
    $selectedValue = old($name, $value);

    $wrapperClass = 'mb-10 fv-row ' . $groupClass;
@endphp

<div class="{{ $wrapperClass }}">
    {{-- Group Label --}}
    <label class="form-label {{ $required ? 'required' : '' }}">{{ $label }}</label>

    {{-- Radio Options --}}
    <div id="{{ $groupId }}" role="radiogroup" @if($required) aria-required="true" @endif>
        @foreach($options as $optionValue => $optionLabel)
        @php
            $radioId = $groupId . '_' . str_replace([' ', '.', '-'], '_', $optionValue);
            $isChecked = (string)$selectedValue === (string)$optionValue;
        @endphp

        <div class="form-check {{ $inline ? 'form-check-inline' : '' }} form-check-custom form-check-solid">
            <input
                type="radio"
                name="{{ $name }}"
                id="{{ $radioId }}"
                value="{{ $optionValue }}"
                class="form-check-input {{ $radioClass }} {{ $hasError ? 'is-invalid' : '' }}"
                {{ $isChecked ? 'checked' : '' }}
                {{ $disabled ? 'disabled' : '' }}
                {{ $required ? 'required' : '' }}
            />
            <label class="form-check-label" for="{{ $radioId }}">
                {{ $optionLabel }}
            </label>
        </div>
        @endforeach
    </div>

    {{-- Help Text --}}
    @if($help)
    <div class="form-text text-muted fs-7 mt-1">{{ $help }}</div>
    @endif

    {{-- Error Message --}}
    @error($name)
    <div class="invalid-feedback d-block" role="alert">{{ $message }}</div>
    @enderror
</div>
