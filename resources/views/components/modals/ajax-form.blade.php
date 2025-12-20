{{--
/**
 * AJAX Form Modal Component
 *
 * A reusable modal component for AJAX-based form submissions with partial refresh.
 * Handles CSRF, method spoofing, loading states, validation errors, and notifications.
 *
 * @param string $id - Unique modal ID (required)
 * @param string $title - Modal title
 * @param string $action - Form action URL (required)
 * @param string $method - HTTP method: POST|PUT|PATCH|DELETE (default: POST)
 * @param string|null $size - Modal size: sm|lg|xl (default: normal)
 * @param bool $centered - Center modal vertically (default: true)
 * @param bool $scrollable - Enable scrollable modal body (default: false)
 * @param bool $static - Disable backdrop click to close (default: false)
 * @param bool $hasFiles - Enable file upload support (default: false)
 * @param string|null $targetContainer - CSS selector for partial refresh container
 * @param string|null $refreshUrl - URL to fetch partial HTML after success
 * @param string $successMessage - Custom success message
 * @param bool $confirmOnSubmit - Show confirmation before destructive actions (default: false)
 * @param string $confirmText - Confirmation dialog text
 * @param string $confirmIcon - Confirmation dialog icon: warning|info|question (default: warning)
 * @param string $submitLabel - Submit button label (default: Save)
 * @param string $submitClass - Submit button CSS class (default: btn-primary)
 * @param string $cancelLabel - Cancel button label (default: Cancel)
 * @param string $headerClass - Additional CSS classes for header
 * @param string $bodyClass - Additional CSS classes for body
 * @param string $footerClass - Additional CSS classes for footer
 * @param bool $resetOnSuccess - Reset form after successful submission (default: true)
 *
 * @slot default - Form fields content
 * @slot footer - Custom footer content (overrides default buttons)
 *
 * @example Basic Add Form
 * <x-modals.ajax-form
 *     id="addLessonModal"
 *     title="Add Lesson"
 *     :action="route('lessons.store')"
 *     targetContainer="#lessons-list"
 *     successMessage="Lesson created successfully"
 * >
 *     <div class="mb-10 fv-row">
 *         <label class="required form-label">Title</label>
 *         <input type="text" name="title" class="form-control" required />
 *     </div>
 * </x-modals.ajax-form>
 *
 * @example Edit Form with PUT method
 * <x-modals.ajax-form
 *     id="editLessonModal"
 *     title="Edit Lesson"
 *     :action="route('lessons.update', $lesson)"
 *     method="PUT"
 *     targetContainer="#lesson-{{ $lesson->id }}"
 *     successMessage="Lesson updated successfully"
 * >
 *     <input type="text" name="title" value="{{ $lesson->title }}" class="form-control" />
 * </x-modals.ajax-form>
 *
 * @example Delete Confirmation
 * <x-modals.ajax-form
 *     id="deleteLessonModal"
 *     title="Delete Lesson"
 *     :action="route('lessons.destroy', $lesson)"
 *     method="DELETE"
 *     :confirmOnSubmit="true"
 *     confirmText="Are you sure you want to delete this lesson?"
 *     submitLabel="Delete"
 *     submitClass="btn-danger"
 *     targetContainer="#module-content"
 * >
 *     <p class="text-gray-600">This action cannot be undone.</p>
 * </x-modals.ajax-form>
 *
 * @example With File Upload
 * <x-modals.ajax-form
 *     id="uploadModal"
 *     title="Upload File"
 *     :action="route('files.store')"
 *     :hasFiles="true"
 *     targetContainer="#files-list"
 * >
 *     <input type="file" name="file" class="form-control" required />
 * </x-modals.ajax-form>
 */
--}}

@props([
    'id',
    'title' => null,
    'action',
    'method' => 'POST',
    'size' => null,
    'centered' => true,
    'scrollable' => false,
    'static' => false,
    'hasFiles' => false,
    'targetContainer' => null,
    'refreshUrl' => null,
    'successMessage' => __('Operation completed successfully'),
    'confirmOnSubmit' => false,
    'confirmText' => __('Are you sure you want to proceed?'),
    'confirmIcon' => 'warning',
    'submitLabel' => __('Save'),
    'submitClass' => 'btn-primary',
    'cancelLabel' => __('Cancel'),
    'headerClass' => '',
    'bodyClass' => '',
    'footerClass' => '',
    'resetOnSuccess' => true,
])

@php
    $dialogClass = 'modal-dialog';
    if ($centered) $dialogClass .= ' modal-dialog-centered';
    if ($scrollable) $dialogClass .= ' modal-dialog-scrollable';
    if ($size) $dialogClass .= ' modal-' . $size;

    $staticAttr = $static ? 'data-bs-backdrop="static" data-bs-keyboard="false"' : '';
    $enctype = $hasFiles ? 'multipart/form-data' : 'application/x-www-form-urlencoded';

    // Normalize method for form
    $formMethod = in_array(strtoupper($method), ['GET', 'POST']) ? strtoupper($method) : 'POST';
    $spoofMethod = !in_array(strtoupper($method), ['GET', 'POST']) ? strtoupper($method) : null;
@endphp

<div class="modal fade"
     id="{{ $id }}"
     tabindex="-1"
     aria-labelledby="{{ $id }}Label"
     aria-hidden="true"
     data-ajax-modal="true"
     data-target-container="{{ $targetContainer }}"
     data-refresh-url="{{ $refreshUrl }}"
     data-success-message="{{ $successMessage }}"
     data-confirm-on-submit="{{ $confirmOnSubmit ? 'true' : 'false' }}"
     data-confirm-text="{{ $confirmText }}"
     data-confirm-icon="{{ $confirmIcon }}"
     data-reset-on-success="{{ $resetOnSuccess ? 'true' : 'false' }}"
     {!! $staticAttr !!}>
    <div class="{{ $dialogClass }}">
        <div class="modal-content">
            {{-- Header --}}
            @if($title)
            <div class="modal-header {{ $headerClass }}">
                <h5 class="modal-title fw-bold" id="{{ $id }}Label">{{ $title }}</h5>
                <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal" aria-label="Close">
                    {!! getIcon('cross', 'fs-1') !!}
                </div>
            </div>
            @endif

            <form action="{{ $action }}"
                  method="{{ $formMethod }}"
                  id="{{ $id }}_form"
                  class="ajax-modal-form"
                  @if($hasFiles) enctype="{{ $enctype }}" @endif>
                @csrf
                @if($spoofMethod)
                    @method($spoofMethod)
                @endif

                {{-- Body --}}
                <div class="modal-body {{ $bodyClass }}">
                    {{-- Validation Errors Container --}}
                    <div class="alert alert-danger d-none ajax-form-errors mb-5" role="alert">
                        <div class="d-flex align-items-center">
                            {!! getIcon('shield-cross', 'fs-2hx text-danger me-3') !!}
                            <div class="d-flex flex-column">
                                <h4 class="mb-1 text-danger">{{ __('Validation Error') }}</h4>
                                <ul class="mb-0 error-list"></ul>
                            </div>
                        </div>
                    </div>

                    {{ $slot }}
                </div>

                {{-- Footer --}}
                <div class="modal-footer {{ $footerClass }}">
                    @if(isset($footer))
                        {{ $footer }}
                    @else
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                            {{ $cancelLabel }}
                        </button>
                        <button type="submit" class="btn {{ $submitClass }}" data-ajax-submit>
                            <span class="indicator-label">{{ $submitLabel }}</span>
                            <span class="indicator-progress">{{ __('Please wait...') }}
                                <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                            </span>
                        </button>
                    @endif
                </div>
            </form>
        </div>
    </div>
</div>
