{{--
/**
 * Form Field Group Component
 *
 * Standardized label + input + error pattern for form fields.
 *
 * @param string $label - Field label
 * @param string $name - Input name attribute
 * @param string|null $type - Input type (default: text)
 * @param bool $required - Show required indicator
 * @param string|null $help - Help text below input
 *
 * @slot default - Custom input content (overrides default input)
 *
 * @example
 * <x-forms.field-group label="Course Name" name="name" required />
 */
--}}

@props([
    'label',
    'name',
    'type' => 'text',
    'required' => false,
    'help' => null,
])

<div class="mb-5">
    <label class="form-label{{ $required ? ' required' : '' }}">
        {{ $label }}
    </label>

    @if (isset($slot) && trim($slot) !== '')
        {{ $slot }}
    @else
        <input type="{{ $type }}" name="{{ $name }}" id="{{ $name }}"
            class="form-control form-control-solid{{ $errors->has($name) ? ' is-invalid' : '' }}"
            value="{{ old($name) }}" {{ $required ? 'required' : '' }} {{ $attributes }} />
    @endif

    @error($name)
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror

    @if ($help)
        <div class="form-text">{{ $help }}</div>
    @endif
</div>

