{{--
/**
 * Submit Button Component
 *
 * Form submit button with dual loading state (indicator-label and indicator-progress).
 * Automatically handles loading state and disables button during submission.
 *
 * @param string $text - Button text when not loading (default: "Submit")
 * @param string $loadingText - Text to show during loading (default: "Please wait...")
 * @param string|null $icon - Icon name from getIcon() helper (optional)
 * @param string $color - Button color: primary|success|danger|warning|info|light|dark (default: primary)
 * @param string $size - Button size: sm|md|lg (default: md)
 * @param string|null $form - Form ID to submit (optional)
 * @param string $buttonClass - Additional CSS classes
 *
 * @example Basic Submit Button
 * <x-buttons.submit
 *     text="Save Changes"
 *     loadingText="Saving..."
 * />
 *
 * @example With Icon
 * <x-buttons.submit
 *     text="Create Course"
 *     loadingText="Creating..."
 *     icon="plus"
 *     color="success"
 * />
 *
 * @example For External Form
 * <x-buttons.submit
 *     text="Submit Application"
 *     form="applicationForm"
 *     color="primary"
 *     size="lg"
 * />
 */
--}}

@props([
    'text' => 'Submit',
    'loadingText' => 'Please wait...',
    'icon' => null,
    'color' => 'primary',
    'size' => 'md',
    'form' => null,
    'buttonClass' => '',
])

@php
    $sizeClass = $size === 'md' ? '' : 'btn-' . $size;
    $baseClass = 'btn btn-' . $color . ' ' . $sizeClass . ' ' . $buttonClass;

    $buttonId = 'submit_btn_' . uniqid();

    $attrs = [
        'type' => 'submit',
        'id' => $buttonId,
        'class' => $baseClass,
        'data-kt-indicator' => 'off',
    ];

    if ($form) $attrs['form'] = $form;
@endphp

<button {{ $attributes->merge($attrs) }}>
    {{-- Normal State --}}
    <span class="indicator-label">
        @if($icon)
            {!! getIcon($icon, 'fs-2 me-2') !!}
        @endif
        {{ __($text) }}
    </span>

    {{-- Loading State --}}
    <span class="indicator-progress">
        {{ __($loadingText) }}
        <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
    </span>
</button>

@push('scripts')
<script>
(function() {
    const submitBtn = document.getElementById('{{ $buttonId }}');

    if (submitBtn) {
        // Get the form
        const form = @if($form) document.getElementById('{{ $form }}') @else submitBtn.closest('form') @endif;

        if (form) {
            // Handle form submission
            form.addEventListener('submit', function(e) {
                // Don't prevent default - let form submit naturally

                // Show loading state
                submitBtn.setAttribute('data-kt-indicator', 'on');
                submitBtn.disabled = true;

                // If submission fails or completes quickly, reset after a delay
                setTimeout(function() {
                    if (submitBtn.hasAttribute('data-kt-indicator')) {
                        submitBtn.setAttribute('data-kt-indicator', 'off');
                        submitBtn.disabled = false;
                    }
                }, 10000); // Reset after 10 seconds max
            });

            // Reset on page navigation (browser back/forward)
            window.addEventListener('pageshow', function(event) {
                if (event.persisted || performance.getEntriesByType('navigation')[0]?.type === 'back_forward') {
                    submitBtn.setAttribute('data-kt-indicator', 'off');
                    submitBtn.disabled = false;
                }
            });
        }
    }
})();
</script>
@endpush

@push('styles')
<style>
/* Submit button loading states */
button[data-kt-indicator="on"] .indicator-label {
    display: none;
}

button[data-kt-indicator="off"] .indicator-progress {
    display: none;
}

button[data-kt-indicator="on"] .indicator-progress {
    display: inline-block;
}
</style>
@endpush
