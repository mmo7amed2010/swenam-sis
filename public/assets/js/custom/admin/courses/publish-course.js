"use strict";

/**
 * Course Action Handlers (Publish, Delete)
 *
 * Handles course actions with SweetAlert2 confirmation and error display
 * following Metronic design system.
 *
 * - Publish: Uses AJAX with SweetAlert feedback
 * - Delete: Uses standard form submission after SweetAlert confirmation
 *
 * Follows the same patterns as KTCourseModuleMain for consistency.
 */
// Use capture phase to catch form submission early
document.addEventListener('submit', function(e) {
    const form = e.target;
    if (!form || !form.action) return;

    const submitButton = form.querySelector('button[type="submit"]');
    if (!submitButton) return;

    // Check if form has confirmation requirement
    const confirmMessage = submitButton.dataset.confirm || submitButton.getAttribute('data-confirm');
    if (!confirmMessage) return;

    // Check if this is a publish course form (needs AJAX handling)
    const publishUrl = form.action;
    const isPublishForm = publishUrl.includes('/publish') && publishUrl.includes('/courses/');

    // Check if form is already being handled
    if (form.dataset.ajaxHandled === 'true') {
        e.preventDefault();
        e.stopPropagation();
        return false;
    }

    // For all forms with data-confirm, show SweetAlert confirmation
    e.preventDefault();
    e.stopPropagation();
    e.stopImmediatePropagation();

    // Mark form as handled to prevent double submission
    form.dataset.ajaxHandled = 'true';

    // Determine button text based on action type
    let confirmButtonText = 'Yes, proceed';
    let icon = 'question';

    if (isPublishForm) {
        confirmButtonText = 'Yes, publish';
    } else if (publishUrl.includes('/destroy') || publishUrl.includes('/delete')) {
        confirmButtonText = 'Yes, delete';
        icon = 'warning';
    }

    // Show confirmation dialog using SweetAlert2
    Swal.fire({
        text: confirmMessage,
        icon: icon,
        showCancelButton: true,
        confirmButtonText: confirmButtonText,
        cancelButtonText: 'Cancel',
        buttonsStyling: false,
        customClass: {
            confirmButton: isPublishForm ? 'btn btn-primary' : (icon === 'warning' ? 'btn btn-danger' : 'btn btn-primary'),
            cancelButton: 'btn btn-light'
        }
    }).then((result) => {
        if (result.isConfirmed) {
            // Handle publish form with AJAX
            if (isPublishForm) {
                // Prevent any default form behavior
                form.setAttribute('onsubmit', 'return false;');

                // Show button loading state (following project pattern)
                submitButton.setAttribute('data-kt-indicator', 'on');
                submitButton.disabled = true;

                // Show loading dialog
                Swal.fire({
                    title: 'Publishing...',
                    text: 'Please wait while we publish the course',
                    icon: 'info',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    showConfirmButton: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                // Prepare form data (following project pattern from main.js)
                const formData = new FormData(form);

                // Submit the form via AJAX
                fetch(publishUrl, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                })
                .then(response => {
                    // Handle non-JSON responses (following project pattern)
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
                    // Remove button loading state
                    submitButton.removeAttribute('data-kt-indicator');
                    submitButton.disabled = false;

                    if (result.ok && result.data.success) {
                        // Update course status in the UI
                        const course = result.data.course;
                        if (course && course.status === 'active') {
                            // Find and update status badge in the course header
                            // Look for badge containing "Draft" text near the course title
                            const courseHeader = document.querySelector('.card-body');
                            if (courseHeader) {
                                const badges = courseHeader.querySelectorAll('.badge');
                                badges.forEach(badge => {
                                    const badgeText = badge.textContent.trim();
                                    // Check if this badge shows "Draft"
                                    if (badgeText === 'Draft' || badgeText.toLowerCase().includes('draft')) {
                                        // Update to Active status
                                        const icon = badge.querySelector('i');
                                        if (icon) {
                                            icon.className = 'ki-outline ki-check-circle fs-7';
                                        }
                                        // Update badge classes and text
                                        badge.className = 'badge badge-light-success fs-7 py-2 px-3 d-inline-flex align-items-center gap-1';
                                        badge.innerHTML = (icon ? icon.outerHTML : '') + 'Active';
                                    }
                                });
                            }

                            // Hide publish button - find the form that contains the publish action
                            const publishForms = document.querySelectorAll('form[action*="/publish"]');
                            publishForms.forEach(publishForm => {
                                if (publishForm.action.includes('/courses/') && publishForm.action.includes('/publish')) {
                                    const menuItem = publishForm.closest('.menu-item');
                                    if (menuItem) {
                                        menuItem.style.display = 'none';
                                    } else {
                                        publishForm.style.display = 'none';
                                    }
                                }
                            });
                        }

                        // Show success message (no reload after closing)
                        Swal.fire({
                            text: result.data.message || 'Course published successfully.',
                            icon: 'success',
                            buttonsStyling: false,
                            confirmButtonText: 'Ok, got it!',
                            customClass: {
                                confirmButton: 'btn btn-primary'
                            },
                            allowOutsideClick: false,
                            allowEscapeKey: true
                        });
                        // No callback - do nothing after closing SweetAlert to prevent any reload
                    } else if (result.status === 422) {
                        // Validation errors
                        Swal.fire({
                            text: result.data.message || 'Failed to publish course.',
                            icon: 'error',
                            buttonsStyling: false,
                            confirmButtonText: 'Ok, got it!',
                            customClass: {
                                confirmButton: 'btn btn-primary'
                            }
                        });
                    } else {
                        // Other errors
                        Swal.fire({
                            text: result.data.message || 'An error occurred while publishing the course.',
                            icon: 'error',
                            buttonsStyling: false,
                            confirmButtonText: 'Ok, got it!',
                            customClass: {
                                confirmButton: 'btn btn-primary'
                            }
                        });
                    }
                })
                .catch(error => {
                    // Remove button loading state
                    submitButton.removeAttribute('data-kt-indicator');
                    submitButton.disabled = false;

                    console.error('Publish course error:', error);

                    // Handle network errors and other exceptions
                    const errorMessage = error.message || 'An error occurred while publishing the course. Please try again.';

                    Swal.fire({
                        text: errorMessage,
                        icon: 'error',
                        buttonsStyling: false,
                        confirmButtonText: 'Ok, got it!',
                        customClass: {
                            confirmButton: 'btn btn-primary'
                        }
                    });
                });
            } else {
                // For Delete: Allow normal form submission after confirmation
                // Remove the preventDefault by submitting programmatically
                form.submit();
            }
        }
    });
});

