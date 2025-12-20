/**
 * Announcement Modals Handler
 *
 * Handles AJAX operations for announcement create, edit, and delete
 */

(function() {
    'use strict';

    const programId = window.location.pathname.split('/')[3];
    const courseId = window.location.pathname.split('/')[5];

    // Handle Add Announcement Form Submission
    const addForm = document.getElementById('kt_modal_add_announcement_form');
    if (addForm) {
        addForm.addEventListener('submit', function(e) {
            e.preventDefault();

            const submitBtn = addForm.querySelector('button[type="submit"]');
            const errorAlert = addForm.querySelector('.ajax-form-errors');
            const errorList = addForm.querySelector('.error-list');

            // Show loading state
            submitBtn.setAttribute('data-kt-indicator', 'on');
            submitBtn.disabled = true;

            // Hide previous errors
            errorAlert.classList.add('d-none');
            errorList.innerHTML = '';

            // Prepare form data
            const formData = new FormData(addForm);
            
            // Explicitly handle checkboxes (unchecked checkboxes don't send values)
            const isPublishedCheckbox = addForm.querySelector('#add_is_published');
            const sendEmailCheckbox = addForm.querySelector('input[name="send_email"]');
            
            if (!isPublishedCheckbox.checked) {
                formData.set('is_published', '0');
            }
            if (!sendEmailCheckbox.checked) {
                formData.set('send_email', '0');
            }

            // Submit via AJAX
            fetch(addForm.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                // Remove loading state
                submitBtn.removeAttribute('data-kt-indicator');
                submitBtn.disabled = false;

                if (data.success) {
                    // Update announcements list
                    const listContainer = document.getElementById('announcementsListContainer');
                    if (listContainer && data.html) {
                        listContainer.innerHTML = data.html;
                    }

                    // Close modal
                    const modal = bootstrap.Modal.getInstance(document.getElementById('kt_modal_add_announcement'));
                    if (modal) {
                        modal.hide();
                    }

                    // Reset form
                    addForm.reset();

                    // Show success message
                    Swal.fire({
                        text: data.message || 'Announcement created successfully.',
                        icon: 'success',
                        buttonsStyling: false,
                        confirmButtonText: 'Ok, got it!',
                        customClass: {
                            confirmButton: 'btn btn-primary'
                        }
                    });
                } else {
                    // Show error message
                    Swal.fire({
                        text: data.message || 'Failed to create announcement.',
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
                submitBtn.removeAttribute('data-kt-indicator');
                submitBtn.disabled = false;

                // Try to parse error response
                if (error.response) {
                    error.response.json().then(data => {
                        if (data.errors) {
                            // Display validation errors
                            errorList.innerHTML = '';
                            Object.values(data.errors).forEach(errorMessages => {
                                errorMessages.forEach(msg => {
                                    const li = document.createElement('li');
                                    li.textContent = msg;
                                    errorList.appendChild(li);
                                });
                            });
                            errorAlert.classList.remove('d-none');
                        }
                    });
                } else {
                    console.error('Add announcement error:', error);
                    Swal.fire({
                        text: 'An error occurred while creating the announcement.',
                        icon: 'error',
                        buttonsStyling: false,
                        confirmButtonText: 'Ok',
                        customClass: {
                            confirmButton: 'btn btn-primary'
                        }
                    });
                }
            });
        });
    }

    // Handle Edit Announcement Trigger
    document.addEventListener('click', function(e) {
        const editTrigger = e.target.closest('[data-announcement-edit-trigger]');
        if (!editTrigger) return;

        e.preventDefault();
        e.stopPropagation();

        const announcementId = editTrigger.dataset.announcementId;
        if (!announcementId) {
            console.error('Announcement ID not found');
            return;
        }

        const modal = document.getElementById('kt_modal_edit_announcement');
        const form = modal?.querySelector('form');

        if (!modal || !form) {
            console.error('Edit announcement modal or form not found');
            return;
        }

        // Get or create Bootstrap modal instance
        let bsModal = bootstrap.Modal.getInstance(modal);
        if (!bsModal) {
            bsModal = new bootstrap.Modal(modal);
        }

        // Update form action URL with announcement ID
        const originalAction = form.getAttribute('data-original-action') || form.action;
        if (!form.getAttribute('data-original-action')) {
            form.setAttribute('data-original-action', originalAction);
        }
        form.action = originalAction.replace('__ANNOUNCEMENT_ID__', announcementId);

        // Reset form and clear validation errors
        const errorAlert = form.querySelector('.ajax-form-errors');
        if (errorAlert) {
            errorAlert.classList.add('d-none');
        }
        const errorList = form.querySelector('.error-list');
        if (errorList) {
            errorList.innerHTML = '';
        }

        // Show loading state
        const submitBtn = form.querySelector('button[type="submit"]');
        if (submitBtn) {
            submitBtn.setAttribute('data-kt-indicator', 'on');
            submitBtn.disabled = true;
        }

        // Fetch announcement data
        const editUrl = `/instructor/programs/${programId}/courses/${courseId}/announcements/${announcementId}/edit`;

        fetch(editUrl, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => {
            if (!response.ok) {
                return response.json().then(data => {
                    throw new Error(data.message || 'Failed to load announcement data');
                });
            }
            return response.json();
        })
        .then(data => {
            // Remove loading state
            if (submitBtn) {
                submitBtn.removeAttribute('data-kt-indicator');
                submitBtn.disabled = false;
            }

            if (data.success && data.announcement) {
                const announcement = data.announcement;

                // Populate form fields
                const titleField = form.querySelector('#edit_announcement_title');
                const contentField = form.querySelector('#edit_announcement_content');
                const priorityField = form.querySelector('#edit_announcement_priority');
                const isPublishedField = form.querySelector('#edit_is_published');
                const sendEmailField = form.querySelector('#edit_send_email');

                if (titleField) titleField.value = announcement.title || '';
                if (contentField) contentField.value = announcement.content || '';
                if (priorityField) priorityField.value = announcement.priority || 'medium';
                if (isPublishedField) isPublishedField.checked = announcement.is_published || false;
                if (sendEmailField) sendEmailField.checked = announcement.send_email || false;

                // Show the modal AFTER data is loaded
                bsModal.show();
            } else {
                Swal.fire({
                    text: 'Failed to load announcement data',
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
            // Remove loading state
            if (submitBtn) {
                submitBtn.removeAttribute('data-kt-indicator');
                submitBtn.disabled = false;
            }

            console.error('Announcement edit error:', error);
            Swal.fire({
                text: error.message || 'An error occurred while loading announcement data',
                icon: 'error',
                buttonsStyling: false,
                confirmButtonText: 'Ok',
                customClass: {
                    confirmButton: 'btn btn-primary'
                }
            });
        });
    });

    // Handle Edit Announcement Form Submission
    const editForm = document.getElementById('kt_modal_edit_announcement_form');
    if (editForm) {
        editForm.addEventListener('submit', function(e) {
            e.preventDefault();

            const submitBtn = editForm.querySelector('button[type="submit"]');
            const errorAlert = editForm.querySelector('.ajax-form-errors');
            const errorList = editForm.querySelector('.error-list');

            // Show loading state
            submitBtn.setAttribute('data-kt-indicator', 'on');
            submitBtn.disabled = true;

            // Hide previous errors
            errorAlert.classList.add('d-none');
            errorList.innerHTML = '';

            // Prepare form data
            const formData = new FormData(editForm);
            
            // Explicitly handle checkboxes (unchecked checkboxes don't send values)
            const isPublishedCheckbox = editForm.querySelector('#edit_is_published');
            const sendEmailCheckbox = editForm.querySelector('input[name="send_email"]');
            
            if (!isPublishedCheckbox.checked) {
                formData.set('is_published', '0');
            }
            if (!sendEmailCheckbox.checked) {
                formData.set('send_email', '0');
            }

            // Submit via AJAX
            fetch(editForm.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                // Remove loading state
                submitBtn.removeAttribute('data-kt-indicator');
                submitBtn.disabled = false;

                if (data.success) {
                    // Update announcements list
                    const listContainer = document.getElementById('announcementsListContainer');
                    if (listContainer && data.html) {
                        listContainer.innerHTML = data.html;
                    }

                    // Close modal
                    const modal = bootstrap.Modal.getInstance(document.getElementById('kt_modal_edit_announcement'));
                    if (modal) {
                        modal.hide();
                    }

                    // Show success message
                    Swal.fire({
                        text: data.message || 'Announcement updated successfully.',
                        icon: 'success',
                        buttonsStyling: false,
                        confirmButtonText: 'Ok, got it!',
                        customClass: {
                            confirmButton: 'btn btn-primary'
                        }
                    });
                } else {
                    // Show error message
                    Swal.fire({
                        text: data.message || 'Failed to update announcement.',
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
                submitBtn.removeAttribute('data-kt-indicator');
                submitBtn.disabled = false;

                console.error('Update announcement error:', error);
                Swal.fire({
                    text: 'An error occurred while updating the announcement.',
                    icon: 'error',
                    buttonsStyling: false,
                    confirmButtonText: 'Ok',
                    customClass: {
                        confirmButton: 'btn btn-primary'
                    }
                });
            });
        });
    }

    // Handle Delete Announcement
    document.addEventListener('click', function(e) {
        const deleteTrigger = e.target.closest('[data-announcement-delete-trigger]');
        if (!deleteTrigger) return;

        e.preventDefault();
        e.stopPropagation();

        const announcementId = deleteTrigger.dataset.announcementId;
        const announcementTitle = deleteTrigger.dataset.announcementTitle || 'Announcement';
        const deleteUrl = deleteTrigger.dataset.deleteUrl;

        Swal.fire({
            text: `Are you sure you want to delete "${announcementTitle}" permanently? This action cannot be undone.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, delete',
            cancelButtonText: 'Cancel',
            buttonsStyling: false,
            customClass: {
                confirmButton: 'btn btn-danger',
                cancelButton: 'btn btn-light'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Deleting...',
                    text: 'Please wait while we delete the announcement',
                    icon: 'info',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    showConfirmButton: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

                const formData = new FormData();
                formData.append('_token', csrfToken);
                formData.append('_method', 'DELETE');

                fetch(deleteUrl, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Update announcements list
                        const listContainer = document.getElementById('announcementsListContainer');
                        if (listContainer && data.html) {
                            listContainer.innerHTML = data.html;
                        }

                        Swal.fire({
                            text: data.message || 'Announcement deleted successfully.',
                            icon: 'success',
                            buttonsStyling: false,
                            confirmButtonText: 'Ok, got it!',
                            customClass: {
                                confirmButton: 'btn btn-primary'
                            }
                        });
                    } else {
                        Swal.fire({
                            text: data.message || 'Failed to delete announcement.',
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
                    console.error('Delete announcement error:', error);
                    Swal.fire({
                        text: 'An error occurred while deleting the announcement.',
                        icon: 'error',
                        buttonsStyling: false,
                        confirmButtonText: 'Ok, got it!',
                        customClass: {
                            confirmButton: 'btn btn-primary'
                        }
                    });
                });
            }
        });
    });
})();
