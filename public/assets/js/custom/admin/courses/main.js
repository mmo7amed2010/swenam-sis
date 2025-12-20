"use strict";

/**
 * KTCourseModuleMain
 *
 * Main JavaScript module for Course management.
 * Handles AJAX form submissions, partial refreshes, and UI interactions.
 *
 * @requires Bootstrap 5
 * @requires SweetAlert2
 * @requires KTUtil (Metronic)
 */
var KTCourseModuleMain = function () {
    // Private variables
    var _forms = [];

    /**
     * Initialize all AJAX modal forms
     */
    var initAjaxForms = () => {
        const forms = document.querySelectorAll('.ajax-modal-form');

        forms.forEach(form => {
            if (_forms.includes(form)) return; // Skip already initialized
            _forms.push(form);

            form.addEventListener('submit', function(e) {
                e.preventDefault();
                handleFormSubmit(form);
            });
        });
    };

    /**
     * Handle form submission with AJAX
     *
     * @param {HTMLFormElement} form
     */
    var handleFormSubmit = (form) => {
        const modal = form.closest('.modal');
        const modalData = modal.dataset;
        const submitBtn = form.querySelector('[data-ajax-submit]') || form.querySelector('[type="submit"]');

        // Check if confirmation is required
        if (modalData.confirmOnSubmit === 'true') {
            Swal.fire({
                text: modalData.confirmText || 'Are you sure?',
                icon: modalData.confirmIcon || 'warning',
                showCancelButton: true,
                buttonsStyling: false,
                confirmButtonText: 'Yes, proceed',
                cancelButtonText: 'Cancel',
                customClass: {
                    confirmButton: 'btn btn-primary',
                    cancelButton: 'btn btn-light'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    executeSubmit(form, modal, submitBtn, modalData);
                }
            });
        } else {
            executeSubmit(form, modal, submitBtn, modalData);
        }
    };

    /**
     * Execute the actual form submission
     *
     * @param {HTMLFormElement} form
     * @param {HTMLElement} modal
     * @param {HTMLElement} submitBtn
     * @param {DOMStringMap} modalData
     */
    var executeSubmit = (form, modal, submitBtn, modalData) => {
        // Show loading state
        if (submitBtn) {
            submitBtn.setAttribute('data-kt-indicator', 'on');
            submitBtn.disabled = true;
        }

        // Clear previous errors
        clearValidationErrors(form);

        // Prepare form data
        const formData = new FormData(form);

        // Execute fetch request
        fetch(form.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => {
            // Handle non-JSON responses
            const contentType = response.headers.get('content-type');
            if (contentType && contentType.includes('application/json')) {
                return response.json().then(data => ({
                    ok: response.ok,
                    status: response.status,
                    data: data
                }));
            }
            throw new Error('Server returned non-JSON response');
        })
        .then(result => {
            // Remove loading state
            if (submitBtn) {
                submitBtn.removeAttribute('data-kt-indicator');
                submitBtn.disabled = false;
            }

            if (result.ok && result.data.success) {
                handleSuccess(form, modal, modalData, result.data);
            } else if (result.status === 422) {
                // Validation errors
                showValidationErrors(form, result.data.errors || {});
            } else {
                // Other errors
                handleError(result.data.message || 'An error occurred');
            }
        })
        .catch(error => {
            // Remove loading state
            if (submitBtn) {
                submitBtn.removeAttribute('data-kt-indicator');
                submitBtn.disabled = false;
            }

            console.error('AJAX Form Error:', error);
            handleError('An error occurred. Please try again.');
        });
    };

    /**
     * Handle successful form submission
     *
     * @param {HTMLFormElement} form
     * @param {HTMLElement} modal
     * @param {DOMStringMap} modalData
     * @param {Object} data
     */
    var handleSuccess = (form, modal, modalData, data) => {
        // Close modal
        const bsModal = bootstrap.Modal.getInstance(modal);
        if (bsModal) {
            bsModal.hide();
        }

        // Show success message
        Swal.fire({
            text: data.message || modalData.successMessage || 'Operation completed successfully',
            icon: 'success',
            buttonsStyling: false,
            confirmButtonText: 'Ok',
            customClass: {
                confirmButton: 'btn btn-primary'
            }
        });

        // Reset form if configured
        if (modalData.resetOnSuccess === 'true') {
            form.reset();
            clearValidationErrors(form);
        }

        // Partial refresh
        const targetContainer = modalData.targetContainer;
        if (targetContainer) {
            if (data.html) {
                // Server returned HTML directly
                refreshContainer(targetContainer, data.html);
            } else if (modalData.refreshUrl) {
                // Fetch HTML from refresh URL
                fetchAndRefresh(modalData.refreshUrl, targetContainer);
            }
        }

        // Trigger custom event for additional handling
        const event = new CustomEvent('ajax-form-success', {
            detail: { form, modal, data }
        });
        document.dispatchEvent(event);
    };

    /**
     * Handle error response
     *
     * @param {string} message
     */
    var handleError = (message) => {
        Swal.fire({
            text: message,
            icon: 'error',
            buttonsStyling: false,
            confirmButtonText: 'Ok',
            customClass: {
                confirmButton: 'btn btn-primary'
            }
        });
    };

    /**
     * Show validation errors in form
     *
     * @param {HTMLFormElement} form
     * @param {Object} errors
     */
    var showValidationErrors = (form, errors) => {
        const errorContainer = form.querySelector('.ajax-form-errors');
        const errorList = errorContainer ? errorContainer.querySelector('.error-list') : null;

        // Clear previous errors
        clearValidationErrors(form);

        // Build error list
        const errorMessages = [];
        Object.keys(errors).forEach(field => {
            const messages = errors[field];
            const input = form.querySelector(`[name="${field}"]`);

            // Add invalid class to input
            if (input) {
                input.classList.add('is-invalid');

                // Add feedback element if not exists
                let feedback = input.parentNode.querySelector('.invalid-feedback');
                if (!feedback) {
                    feedback = document.createElement('div');
                    feedback.className = 'invalid-feedback';
                    input.parentNode.appendChild(feedback);
                }
                feedback.textContent = messages[0];
            }

            // Collect all messages for summary
            messages.forEach(msg => errorMessages.push(msg));
        });

        // Show error summary
        if (errorContainer && errorList && errorMessages.length > 0) {
            errorList.innerHTML = errorMessages.map(msg => `<li>${msg}</li>`).join('');
            errorContainer.classList.remove('d-none');
        }
    };

    /**
     * Clear validation errors from form
     *
     * @param {HTMLFormElement} form
     */
    var clearValidationErrors = (form) => {
        // Remove invalid classes
        form.querySelectorAll('.is-invalid').forEach(el => {
            el.classList.remove('is-invalid');
        });

        // Remove feedback elements
        form.querySelectorAll('.invalid-feedback').forEach(el => {
            el.remove();
        });

        // Hide error container
        const errorContainer = form.querySelector('.ajax-form-errors');
        if (errorContainer) {
            errorContainer.classList.add('d-none');
            const errorList = errorContainer.querySelector('.error-list');
            if (errorList) {
                errorList.innerHTML = '';
            }
        }
    };

    /**
     * Refresh container with new HTML content
     *
     * @param {string} selector
     * @param {string} html
     */
    var refreshContainer = (selector, html) => {
        const container = document.querySelector(selector);
        if (container) {
            // Fade out
            container.style.opacity = '0.5';
            container.style.transition = 'opacity 0.2s ease';

            setTimeout(() => {
                container.innerHTML = html;

                // Reinitialize Metronic components
                reinitializeComponents(container);

                // Reinitialize AJAX forms in new content
                initAjaxForms();

                // Reinitialize Quill editors in refreshed content
                if (typeof KTLessonModals !== 'undefined' && KTLessonModals.reinitializeQuillEditors) {
                    KTLessonModals.reinitializeQuillEditors();
                }

                // Fade in
                container.style.opacity = '1';
            }, 200);
        }
    };

    /**
     * Fetch HTML from URL and refresh container
     *
     * @param {string} url
     * @param {string} containerSelector
     */
    var fetchAndRefresh = (url, containerSelector) => {
        const container = document.querySelector(containerSelector);
        if (!container) return;

        // Show loading
        container.style.opacity = '0.5';
        container.style.transition = 'opacity 0.2s ease';

        fetch(url, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'text/html'
            }
        })
        .then(response => response.text())
        .then(html => {
            container.innerHTML = html;

            // Reinitialize components
            reinitializeComponents(container);

            // Reinitialize AJAX forms
            initAjaxForms();

            // Reinitialize Quill editors in refreshed content
            if (typeof KTLessonModals !== 'undefined' && KTLessonModals.reinitializeQuillEditors) {
                KTLessonModals.reinitializeQuillEditors();
            }

            // Fade in
            container.style.opacity = '1';
        })
        .catch(error => {
            console.error('Refresh Error:', error);
            container.style.opacity = '1';
        });
    };

    /**
     * Reinitialize Metronic/Bootstrap components after DOM update
     *
     * @param {HTMLElement} container
     */
    var reinitializeComponents = (container) => {
        // Reinitialize Metronic components if available
        if (typeof KTMenu !== 'undefined') {
            KTMenu.init();
        }
        if (typeof KTScroll !== 'undefined') {
            KTScroll.init();
        }
        if (typeof KTDrawer !== 'undefined') {
            KTDrawer.init();
        }

        // Reinitialize Bootstrap tooltips
        const tooltips = container.querySelectorAll('[data-bs-toggle="tooltip"]');
        tooltips.forEach(el => {
            new bootstrap.Tooltip(el);
        });

        // Reinitialize Bootstrap dropdowns
        const dropdowns = container.querySelectorAll('[data-bs-toggle="dropdown"]');
        dropdowns.forEach(el => {
            new bootstrap.Dropdown(el);
        });

        // Reinitialize Bootstrap popovers
        const popovers = container.querySelectorAll('[data-bs-toggle="popover"]');
        popovers.forEach(el => {
            new bootstrap.Popover(el);
        });
    };

    /**
     * Initialize content type field toggling for lessons
     */
    var initContentTypeFields = () => {
        document.addEventListener('change', function(e) {
            if (e.target.matches('[data-content-type-select]') || e.target.matches('select[name="content_type"]')) {
                const select = e.target;
                const container = select.closest('.modal-body') || select.closest('form');
                const selectedType = select.value;

                // Hide all content fields
                const contentFields = container.querySelectorAll('.content-field, [data-content-type]');
                contentFields.forEach(field => {
                    field.style.display = 'none';
                    // Disable required on hidden fields
                    const inputs = field.querySelectorAll('input, textarea, select');
                    inputs.forEach(input => {
                        input.removeAttribute('required');
                    });
                });

                // Show selected type field
                if (selectedType) {
                    const targetField = container.querySelector(`.content-field[data-type="${selectedType}"], [data-content-type="${selectedType}"]`);
                    if (targetField) {
                        targetField.style.display = 'block';
                        // Restore required on visible fields
                        const requiredInputs = targetField.querySelectorAll('[data-required="true"]');
                        requiredInputs.forEach(input => {
                            input.setAttribute('required', 'required');
                        });
                    }
                }
            }
        });
    };

    /**
     * Initialize video URL preview functionality
     */
    var initVideoPreview = () => {
        document.addEventListener('blur', function(e) {
            if (e.target.matches('input[name="content_url"]') || e.target.matches('[data-video-url-input]')) {
                const input = e.target;
                const container = input.closest('.content-field') || input.closest('.fv-row');
                const previewContainer = container.querySelector('[id*="video_preview"], .video-preview');

                if (!previewContainer) return;

                const url = input.value.trim();
                if (!url) {
                    previewContainer.style.display = 'none';
                    previewContainer.innerHTML = '';
                    return;
                }

                // Extract video ID and generate embed
                const embedHtml = generateVideoEmbed(url);
                if (embedHtml) {
                    previewContainer.innerHTML = embedHtml;
                    previewContainer.style.display = 'block';
                } else {
                    previewContainer.style.display = 'none';
                    previewContainer.innerHTML = '';
                }
            }
        }, true);
    };

    /**
     * Generate video embed HTML from URL
     *
     * @param {string} url
     * @returns {string|null}
     */
    var generateVideoEmbed = (url) => {
        // YouTube
        const youtubeMatch = url.match(/(?:youtube\.com\/(?:watch\?v=|embed\/)|youtu\.be\/)([a-zA-Z0-9_-]{11})/);
        if (youtubeMatch) {
            return `<div class="ratio ratio-16x9">
                <iframe src="https://www.youtube.com/embed/${youtubeMatch[1]}" allowfullscreen></iframe>
            </div>`;
        }

        // Vimeo
        const vimeoMatch = url.match(/vimeo\.com\/(\d+)/);
        if (vimeoMatch) {
            return `<div class="ratio ratio-16x9">
                <iframe src="https://player.vimeo.com/video/${vimeoMatch[1]}" allowfullscreen></iframe>
            </div>`;
        }

        return null;
    };

    /**
     * Initialize PDF file preview functionality
     */
    var initPdfPreview = () => {
        document.addEventListener('change', function(e) {
            if (e.target.matches('input[type="file"][accept=".pdf"], input[name="content_file"]')) {
                const input = e.target;
                const container = input.closest('.content-field') || input.closest('.fv-row');
                const previewContainer = container.querySelector('[id*="pdf_preview"], .pdf-preview');

                if (!previewContainer) return;

                const file = input.files[0];
                if (!file) {
                    previewContainer.style.display = 'none';
                    previewContainer.innerHTML = '';
                    return;
                }

                // Show file info
                const fileSize = (file.size / 1024 / 1024).toFixed(2);
                previewContainer.innerHTML = `
                    <div class="d-flex align-items-center p-5 bg-light-primary rounded">
                        <i class="ki-duotone ki-file-added fs-3x text-primary me-4">
                            <span class="path1"></span>
                            <span class="path2"></span>
                        </i>
                        <div class="flex-grow-1">
                            <span class="fw-bold text-gray-800 d-block">${file.name}</span>
                            <span class="text-muted fs-7">${fileSize} MB</span>
                        </div>
                    </div>
                `;
                previewContainer.style.display = 'block';
            }
        });
    };

    /**
     * Initialize drag and drop reordering
     */
    var initDragAndDrop = () => {
        // Delegate to specific module if needed
        // This is a placeholder for drag-drop initialization
        // The actual implementation depends on the drag-drop library used
    };

    /**
     * Initialize remove item buttons with event delegation
     * Uses event delegation to handle dynamically added buttons after AJAX refresh
     */
    var initRemoveItemButtons = () => {
        // Use event delegation - attach to document once, works for all current and future buttons
        document.addEventListener('click', function(e) {
            const button = e.target.closest('.btn-remove-item');
            if (!button) return;

            e.preventDefault();
            const url = button.dataset.removeUrl;
            if (!url) return;

            const confirmMsg = button.dataset.confirm || 'Remove this item?';

            Swal.fire({
                text: confirmMsg,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, remove',
                cancelButtonText: 'Cancel',
                buttonsStyling: false,
                customClass: {
                    confirmButton: 'btn btn-danger',
                    cancelButton: 'btn btn-light ms-3'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    // Show loading state on button
                    button.setAttribute('data-kt-indicator', 'on');
                    button.disabled = true;

                    fetch(url, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        button.removeAttribute('data-kt-indicator');
                        button.disabled = false;

                        if (data.success) {
                            Swal.fire({
                                text: data.message || 'Item removed successfully',
                                icon: 'success',
                                buttonsStyling: false,
                                confirmButtonText: 'Ok',
                                customClass: {
                                    confirmButton: 'btn btn-primary'
                                }
                            });

                            // Find and refresh the module content container
                            const moduleContent = button.closest('[id^="module-content-"]');
                            if (moduleContent) {
                                const moduleId = moduleContent.id.replace('module-content-', '');
                                const refreshUrl = moduleContent.dataset.refreshUrl;
                                if (refreshUrl) {
                                    fetchAndRefresh(refreshUrl, '#' + moduleContent.id);
                                } else {
                                    // Fallback: remove the item from DOM
                                    const contentItem = button.closest('.content-item');
                                    if (contentItem) {
                                        contentItem.remove();
                                    }
                                }
                            } else {
                                // Fallback: remove the item from DOM
                                const contentItem = button.closest('.content-item');
                                if (contentItem) {
                                    contentItem.remove();
                                }
                            }
                        } else {
                            Swal.fire({
                                text: data.message || 'Failed to remove item',
                                icon: 'error',
                                buttonsStyling: false,
                                confirmButtonText: 'Ok',
                                customClass: {
                                    confirmButton: 'btn btn-primary'
                                }
                            });
                        }
                    })
                    .catch(error => {
                        button.removeAttribute('data-kt-indicator');
                        button.disabled = false;

                        console.error('Remove item error:', error);
                        Swal.fire({
                            text: 'An error occurred. Please try again.',
                            icon: 'error',
                            buttonsStyling: false,
                            confirmButtonText: 'Ok',
                            customClass: {
                                confirmButton: 'btn btn-primary'
                            }
                        });
                    });
                }
            });
        });
    };

    /**
     * Public function to manually refresh a section
     *
     * @param {string} url
     * @param {string} containerSelector
     */
    var refreshSection = (url, containerSelector) => {
        fetchAndRefresh(url, containerSelector);
    };

    /**
     * Public function to manually submit a form
     *
     * @param {string|HTMLFormElement} formOrSelector
     */
    var submitForm = (formOrSelector) => {
        const form = typeof formOrSelector === 'string'
            ? document.querySelector(formOrSelector)
            : formOrSelector;

        if (form) {
            handleFormSubmit(form);
        }
    };

    // Public API
    return {
        init: function () {
            initAjaxForms();
            initContentTypeFields();
            initVideoPreview();
            initPdfPreview();
            initDragAndDrop();
            initRemoveItemButtons();

            // Re-init forms when modals are shown (for dynamically added modals)
            document.addEventListener('shown.bs.modal', function(e) {
                if (e.target.dataset.ajaxModal === 'true') {
                    initAjaxForms();
                }
            });

            // Clear form errors when modal is hidden
            document.addEventListener('hidden.bs.modal', function(e) {
                const form = e.target.querySelector('.ajax-modal-form');
                if (form) {
                    clearValidationErrors(form);
                }
            });
        },

        // Expose public methods
        refreshSection: refreshSection,
        submitForm: submitForm,
        handleFormSubmit: handleFormSubmit,
        showValidationErrors: showValidationErrors,
        clearValidationErrors: clearValidationErrors,
        reinitializeComponents: reinitializeComponents,
        generateVideoEmbed: generateVideoEmbed,
        initAjaxForms: initAjaxForms
    };
}();

// Initialize on DOM ready
KTUtil.onDOMContentLoaded(function () {
    KTCourseModuleMain.init();
});
