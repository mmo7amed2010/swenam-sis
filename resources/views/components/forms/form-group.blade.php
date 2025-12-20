{{--
/**
 * Form Group Component
 *
 * Complete form field wrapper with label, input, help text, and error display.
 * Handles validation state and accessibility attributes automatically.
 *
 * @param string $name - Input name attribute (required)
 * @param string $label - Field label text
 * @param string $type - Input type: text|email|password|number|date|tel|url (default: text)
 * @param string|null $value - Input value (for edit forms)
 * @param bool $required - Mark field as required (default: false)
 * @param string|null $placeholder - Input placeholder text
 * @param string|null $help - Help text displayed below input
 * @param string|null $validation - Data validation rules (e.g., "required|maxLength:20")
 * @param int|null $min - Min value for number/date inputs
 * @param int|null $max - Max value for number/date inputs
 * @param float|null $step - Step value for number inputs
 * @param string|null $pattern - HTML5 pattern attribute
 * @param string $inputClass - Additional CSS classes for input
 * @param string $groupClass - Additional CSS classes for wrapper
 * @param bool $floating - Use floating label style (default: false)
 *
 * @example Basic Text Input
 * <x-forms.form-group
 *     name="course_code"
 *     label="Course Code"
 *     :required="true"
 *     placeholder="e.g., CS101"
 * />
 *
 * @example Email with Validation
 * <x-forms.form-group
 *     name="email"
 *     label="Email Address"
 *     type="email"
 *     :required="true"
 *     help="We'll never share your email"
 *     validation="required|email"
 * />
 *
 * @example Number Input with Range
 * <x-forms.form-group
 *     name="credits"
 *     label="Credits"
 *     type="number"
 *     :min="0.5"
 *     :max="10"
 *     :step="0.5"
 *     :value="3"
 * />
 *
 * @example With Old Value and Error
 * <x-forms.form-group
 *     name="title"
 *     label="Course Title"
 *     :value="old('title', $course->title ?? '')"
 *     :required="true"
 * />
 */
--}}

@props([
    'name',
    'label',
    'type' => 'text',
    'value' => null,
    'required' => false,
    'placeholder' => null,
    'help' => null,
    'validation' => null,
    'min' => null,
    'max' => null,
    'step' => null,
    'pattern' => null,
    'inputClass' => '',
    'groupClass' => '',
    'floating' => false,
])

@php
    $inputId = 'input_' . str_replace(['[', ']', '.'], '_', $name);
    $hasError = $errors->has($name);
    $inputValue = old($name, $value);

    $inputAttributes = [
        'type' => $type,
        'name' => $name,
        'id' => $inputId,
        'class' => 'form-control ' . $inputClass . ($hasError ? ' is-invalid' : ''),
        'value' => $inputValue,
    ];

    if ($required) {
        $inputAttributes['required'] = true;
        $inputAttributes['aria-required'] = 'true';
    }

    if ($placeholder) $inputAttributes['placeholder'] = $placeholder;
    if ($min !== null) $inputAttributes['min'] = $min;
    if ($max !== null) $inputAttributes['max'] = $max;
    if ($step !== null) $inputAttributes['step'] = $step;
    if ($pattern) $inputAttributes['pattern'] = $pattern;
    if ($validation) $inputAttributes['data-validate'] = $validation;
    if ($hasError) $inputAttributes['aria-invalid'] = 'true';
    if ($help) $inputAttributes['aria-describedby'] = $inputId . '_help';
@endphp

<div class="mb-10 fv-row {{ $groupClass }}">
    {{-- Label --}}
    <label for="{{ $inputId }}" class="form-label {{ $required ? 'required' : '' }}">
        {{ $label }}
    </label>

    {{-- Input --}}
    <input {{ $attributes->merge($inputAttributes) }} />

    {{-- Help Text --}}
    @if($help)
    <div id="{{ $inputId }}_help" class="form-text text-muted fs-7">{{ $help }}</div>
    @endif

    {{-- Error Message --}}
    @error($name)
    <div class="invalid-feedback" role="alert">{{ $message }}</div>
    @enderror
</div>
