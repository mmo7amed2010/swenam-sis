{{--
/**
 * Checkbox/Switch Component
 *
 * Checkbox or toggle switch input with label and help text.
 * Supports Bootstrap custom checkbox and switch styles.
 *
 * @param string $name - Input name attribute (required)
 * @param string $label - Checkbox label text (required)
 * @param bool|int $checked - Checked state (default: false)
 * @param string|int $value - Checkbox value (default: 1)
 * @param string|null $help - Help text displayed below checkbox
 * @param bool $switch - Use toggle switch style (default: false)
 * @param bool $inline - Display inline (default: false)
 * @param string $checkboxClass - Additional CSS classes for input
 * @param string $groupClass - Additional CSS classes for wrapper
 * @param bool $disabled - Disable checkbox (default: false)
 *
 * @example Basic Checkbox
 * <x-forms.checkbox
 *     name="accept_terms"
 *     label="I accept the terms and conditions"
 *     :required="true"
 * />
 *
 * @example Toggle Switch
 * <x-forms.checkbox
 *     name="enrollment_open"
 *     label="Enrollment Open"
 *     :switch="true"
 *     help="Allow admins to enroll students"
 * />
 *
 * @example Pre-checked with Old Value
 * <x-forms.checkbox
 *     name="is_public"
 *     label="Public Course"
 *     :checked="old('is_public', $course->is_public ?? false)"
 *     :switch="true"
 * />
 *
 * @example Inline Checkbox
 * <x-forms.checkbox
 *     name="featured"
 *     label="Featured"
 *     :inline="true"
 * />
 */
--}}

@props([
    'name',
    'label',
    'checked' => false,
    'value' => 1,
    'help' => null,
    'switch' => false,
    'inline' => false,
    'checkboxClass' => '',
    'groupClass' => '',
    'disabled' => false,
])

@php
    $checkboxId = 'checkbox_' . str_replace(['[', ']', '.'], '_', $name);
    $isChecked = old($name, $checked);

    // Convert to boolean
    $isChecked = filter_var($isChecked, FILTER_VALIDATE_BOOLEAN);

    $wrapperClass = 'form-check';
    if ($switch) $wrapperClass .= ' form-switch';
    $wrapperClass .= ' form-check-custom form-check-solid';
    if ($inline) $wrapperClass .= ' form-check-inline';

    $checkboxAttributes = [
        'type' => 'checkbox',
        'name' => $name,
        'id' => $checkboxId,
        'class' => 'form-check-input ' . $checkboxClass,
        'value' => $value,
    ];

    if ($isChecked) $checkboxAttributes['checked'] = true;
    if ($disabled) $checkboxAttributes['disabled'] = true;
    if ($help) $checkboxAttributes['aria-describedby'] = $checkboxId . '_help';
@endphp

<div class="{{ $wrapperClass }} {{ $groupClass }}" @if(!$inline) style="margin-bottom: {{ $help ? '0.5rem' : '1rem' }}" @endif>
    <input {{ $attributes->merge($checkboxAttributes) }} />

    <label class="form-check-label" for="{{ $checkboxId }}">
        {{ $label }}
    </label>
</div>

@if($help)
<div id="{{ $checkboxId }}_help" class="form-text text-muted fs-7 mt-1" style="margin-left: {{ $switch ? '3.5rem' : '1.5rem' }}; margin-bottom: 1rem;">
    {{ $help }}
</div>
@endif
