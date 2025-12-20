{{--
/**
 * Textarea Component
 *
 * Multi-line text input with label, help text, error handling, and optional character counter.
 *
 * @param string $name - Textarea name attribute (required)
 * @param string $label - Field label text
 * @param string|null $value - Textarea value (for edit forms)
 * @param int $rows - Number of visible rows (default: 4)
 * @param bool $required - Mark field as required (default: false)
 * @param string|null $placeholder - Placeholder text
 * @param string|null $help - Help text displayed below textarea
 * @param int|null $maxlength - Maximum character limit
 * @param bool $showCounter - Show character counter (default: false if no maxlength, true if maxlength set)
 * @param string $textareaClass - Additional CSS classes for textarea
 * @param string $groupClass - Additional CSS classes for wrapper
 * @param bool $autoResize - Auto-resize based on content (default: false)
 *
 * @example Basic Textarea
 * <x-forms.textarea
 *     name="description"
 *     label="Description"
 *     :rows="5"
 *     placeholder="Enter course description..."
 * />
 *
 * @example With Character Limit
 * <x-forms.textarea
 *     name="summary"
 *     label="Summary"
 *     :maxlength="500"
 *     :showCounter="true"
 *     help="Brief course summary"
 * />
 *
 * @example Required Field
 * <x-forms.textarea
 *     name="objectives"
 *     label="Learning Objectives"
 *     :required="true"
 *     :value="old('objectives', $course->objectives)"
 * />
 */
--}}

@props([
    'name',
    'label',
    'value' => null,
    'rows' => 4,
    'required' => false,
    'placeholder' => null,
    'help' => null,
    'maxlength' => null,
    'showCounter' => null,
    'textareaClass' => '',
    'groupClass' => '',
    'autoResize' => false,
])

@php
    $textareaId = 'textarea_' . str_replace(['[', ']', '.'], '_', $name);
    $hasError = $errors->has($name);
    $textareaValue = old($name, $value);

    // Auto-enable counter if maxlength is set and showCounter not explicitly false
    $displayCounter = $showCounter ?? ($maxlength !== null);

    $textareaAttributes = [
        'name' => $name,
        'id' => $textareaId,
        'class' => 'form-control ' . $textareaClass . ($hasError ? ' is-invalid' : ''),
        'rows' => $rows,
    ];

    if ($required) {
        $textareaAttributes['required'] = true;
        $textareaAttributes['aria-required'] = 'true';
    }

    if ($placeholder) $textareaAttributes['placeholder'] = $placeholder;
    if ($maxlength) $textareaAttributes['maxlength'] = $maxlength;
    if ($hasError) $textareaAttributes['aria-invalid'] = 'true';
    if ($help || $displayCounter) {
        $describedBy = [];
        if ($help) $describedBy[] = $textareaId . '_help';
        if ($displayCounter) $describedBy[] = $textareaId . '_counter';
        $textareaAttributes['aria-describedby'] = implode(' ', $describedBy);
    }
    if ($autoResize) $textareaAttributes['data-auto-resize'] = 'true';
@endphp

<div class="mb-10 fv-row {{ $groupClass }}">
    {{-- Label --}}
    <label for="{{ $textareaId }}" class="form-label {{ $required ? 'required' : '' }}">
        {{ $label }}
    </label>

    {{-- Textarea --}}
    <textarea {{ $attributes->merge($textareaAttributes) }}>{{ $textareaValue }}</textarea>

    {{-- Help Text and Counter --}}
    <div class="d-flex justify-content-between align-items-start mt-1">
        @if($help)
        <div id="{{ $textareaId }}_help" class="form-text text-muted fs-7 flex-grow-1">{{ $help }}</div>
        @endif

        @if($displayCounter)
        <div id="{{ $textareaId }}_counter" class="text-muted fs-7 ms-auto">
            <span class="current-count">{{ mb_strlen($textareaValue ?? '') }}</span>
            @if($maxlength)
            / <span class="max-count">{{ $maxlength }}</span>
            @endif
        </div>
        @endif
    </div>

    {{-- Error Message --}}
    @error($name)
    <div class="invalid-feedback" role="alert">{{ $message }}</div>
    @enderror
</div>

@if($displayCounter)
@push('scripts')
<script>
(function() {
    const textarea = document.getElementById('{{ $textareaId }}');
    const counter = document.getElementById('{{ $textareaId }}_counter');

    if (textarea && counter) {
        const currentCount = counter.querySelector('.current-count');

        textarea.addEventListener('input', function() {
            const length = this.value.length;
            currentCount.textContent = length;

            @if($maxlength)
            // Update counter color based on limit
            if (length >= {{ $maxlength }} * 0.9) {
                counter.classList.add('text-warning');
            } else {
                counter.classList.remove('text-warning');
            }
            if (length >= {{ $maxlength }}) {
                counter.classList.add('text-danger');
                counter.classList.remove('text-warning');
            }
            @endif
        });
    }
})();
</script>
@endpush
@endif

@if($autoResize)
@push('scripts')
<script>
(function() {
    const textarea = document.getElementById('{{ $textareaId }}');

    if (textarea) {
        function resize() {
            textarea.style.height = 'auto';
            textarea.style.height = textarea.scrollHeight + 'px';
        }

        textarea.addEventListener('input', resize);
        // Initial resize
        resize();
    }
})();
</script>
@endpush
@endif
