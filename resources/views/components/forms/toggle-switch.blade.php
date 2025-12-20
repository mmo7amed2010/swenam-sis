{{--
/**
 * Toggle Switch Component
 *
 * Styled toggle switch for boolean form fields with optional status text.
 *
 * @param string $name - Input name attribute
 * @param string $label - Toggle label text
 * @param bool $checked - Whether toggle is on (default: false)
 * @param string|null $help - Help text below toggle
 * @param string|null $activeText - Text when toggle is on (e.g., "Active")
 * @param string|null $inactiveText - Text when toggle is off (e.g., "Inactive")
 * @param bool $showStatus - Show status indicator (default: true if activeText/inactiveText provided)
 * @param string $size - Toggle size: sm|md|lg (default: md)
 * @param bool $disabled - Disable the toggle
 * @param string $color - Toggle color when active: primary|success|warning|danger|info (default: primary)
 *
 * @example Basic Usage
 * <x-forms.toggle-switch
 *     name="is_active"
 *     label="Active Status"
 *     :checked="$model->is_active"
 * />
 *
 * @example With Status Text
 * <x-forms.toggle-switch
 *     name="is_published"
 *     label="Publication Status"
 *     active-text="Published"
 *     inactive-text="Draft"
 *     :checked="$article->is_published"
 *     help="Published articles are visible to all users."
 * />
 *
 * @example Colored Toggle
 * <x-forms.toggle-switch
 *     name="is_featured"
 *     label="Featured"
 *     color="success"
 *     :checked="old('is_featured', false)"
 * />
 */
--}}

@props([
    'name',
    'label',
    'checked' => false,
    'help' => null,
    'activeText' => null,
    'inactiveText' => null,
    'showStatus' => null,
    'size' => 'md',
    'disabled' => false,
    'color' => 'primary',
])

@php
    $uniqueId = $name . '_' . uniqid();
    $shouldShowStatus = $showStatus ?? ($activeText !== null || $inactiveText !== null);

    $sizeClasses = [
        'sm' => 'form-check-sm',
        'md' => '',
        'lg' => 'form-check-lg',
    ];

    $sizeClass = $sizeClasses[$size] ?? '';

    // Handle old() value for checkboxes
    $isChecked = old($name) !== null ? (bool)old($name) : $checked;
@endphp

<div {{ $attributes->merge(['class' => 'mb-5']) }}>
    <div class="d-flex align-items-center justify-content-between">
        <div class="d-flex align-items-center">
            <div class="form-check form-switch form-check-custom form-check-solid {{ $sizeClass }}">
                <input class="form-check-input toggle-switch-input"
                       type="checkbox"
                       name="{{ $name }}"
                       id="{{ $uniqueId }}"
                       value="1"
                       {{ $isChecked ? 'checked' : '' }}
                       {{ $disabled ? 'disabled' : '' }}
                       data-active-text="{{ $activeText }}"
                       data-inactive-text="{{ $inactiveText }}"
                       style="{{ $isChecked ? '--kt-form-check-input-checked-bg-color: var(--bs-' . $color . '); --kt-form-check-input-checked-border-color: var(--bs-' . $color . ');' : '' }}" />
                <label class="form-check-label fw-semibold text-gray-700" for="{{ $uniqueId }}">
                    {{ $label }}
                </label>
            </div>
        </div>

        @if ($shouldShowStatus)
            <span class="toggle-status-badge badge badge-light-{{ $isChecked ? 'success' : 'secondary' }} fs-7"
                  id="{{ $uniqueId }}_status">
                {{ $isChecked ? ($activeText ?? __('On')) : ($inactiveText ?? __('Off')) }}
            </span>
        @endif
    </div>

    @if ($help)
        <div class="form-text text-gray-500 mt-2 ps-10">{{ $help }}</div>
    @endif

    @error($name)
        <div class="invalid-feedback d-block ps-10">{{ $message }}</div>
    @enderror
</div>

@once
    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Toggle switch status update
                document.querySelectorAll('.toggle-switch-input').forEach(function(toggle) {
                    toggle.addEventListener('change', function() {
                        const statusBadge = document.getElementById(this.id + '_status');
                        if (statusBadge) {
                            const activeText = this.dataset.activeText || '{{ __("On") }}';
                            const inactiveText = this.dataset.inactiveText || '{{ __("Off") }}';

                            if (this.checked) {
                                statusBadge.textContent = activeText;
                                statusBadge.classList.remove('badge-light-secondary');
                                statusBadge.classList.add('badge-light-success');
                            } else {
                                statusBadge.textContent = inactiveText;
                                statusBadge.classList.remove('badge-light-success');
                                statusBadge.classList.add('badge-light-secondary');
                            }
                        }
                    });
                });
            });
        </script>
    @endpush
@endonce
