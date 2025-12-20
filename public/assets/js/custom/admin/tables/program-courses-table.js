"use strict";

/**
 * Program Courses Table Configuration
 *
 * Page-specific DataTable configuration for the courses table on the program show page.
 * Uses ColumnRenderers for consistent column formatting.
 */
document.addEventListener('DOMContentLoaded', function() {
    const tableElement = document.getElementById('program-courses-table');
    if (!tableElement) return;

    // Helper function to escape HTML attributes
    const escapeAttr = (value) => {
        if (value === null || value === undefined) return '';
        return String(value)
            .replace(/&/g, '&amp;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#39;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;');
    };

    // Get translations from data attributes or use defaults
    const translations = {
        showing: tableElement.dataset.textShowing || 'Showing',
        to: tableElement.dataset.textTo || 'to',
        of: tableElement.dataset.textOf || 'of',
        entries: tableElement.dataset.textEntries || 'entries',
        filteredFrom: tableElement.dataset.textFilteredFrom || 'filtered from',
        total: tableElement.dataset.textTotal || 'total',
        noRecords: tableElement.dataset.textNoRecords || 'No courses found'
    };

    // Initialize the DataTable
    const table = new AdminDataTable('program-courses-table', {
        ajaxUrl: tableElement.dataset.ajaxUrl,
        columns: [
            {
                data: 'name',
                name: 'name',
                render: ColumnRenderers.nameCell({
                    icon: 'book',
                    nameField: 'name',
                    urlField: 'show_url',
                    subtitleField: 'description',
                    subtitlePrefix: '',
                    statusField: 'status',
                    statusMap: {
                        'active': true,
                        'draft': false,
                        'archived': false
                    }
                })
            },
            {
                data: 'course_code',
                name: 'course_code',
                className: 'text-start',
                render: function(data, type, row) {
                    return `<span class="badge badge-light-dark fs-7 fw-semibold px-3 py-2">${data}</span>`;
                }
            },
            {
                data: 'status',
                name: 'status',
                className: 'text-center',
                render: ColumnRenderers.statusBadge({
                    field: 'status',
                    statusMap: {
                        'active': { label: 'Active', color: 'success', icon: 'check-circle' },
                        'draft': { label: 'Draft', color: 'warning', icon: 'pencil' },
                        'archived': { label: 'Archived', color: 'secondary', icon: 'archive' }
                    }
                })
            },
            {
                data: 'students_count',
                name: 'enrollments_count',
                orderable: false,
                className: 'text-center',
                render: function(data, type, row) {
                    return `
                        <div class="d-flex flex-column align-items-center">
                            <span class="badge badge-light-info fs-6 fw-bold py-2 px-4 mb-1">
                                ${data || 0}
                            </span>
                            <span class="text-gray-500 fs-8">
                                ${tableElement.dataset.textStudents || 'students'}
                            </span>
                        </div>
                    `;
                }
            },
            {
                data: 'id',
                name: 'actions',
                orderable: false,
                searchable: false,
                className: 'text-end pe-4',
render: function(data, type, row) {
                    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
                    const viewLabel = tableElement.dataset.textViewDetails || 'View Details';
                    const editLabel = tableElement.dataset.textEdit || 'Edit';
                    const deleteLabel = tableElement.dataset.textDelete || 'Delete';
                    const deleteConfirm = tableElement.dataset.textDeleteConfirm || 'Are you sure you want to delete this course?';

                    let deleteItem = '';
                    if (row.delete_url) {
                        deleteItem = `<li>
                            <a class="dropdown-item d-flex align-items-center py-2 text-danger" href="#"
                               data-course-delete-trigger
                               data-course-id="${row.id}"
                               data-course-name="${escapeAttr(row.name)}"
                               data-course-code="${escapeAttr(row.course_code)}"
                               data-delete-url="${row.delete_url}"
                               data-program-id="${row.program_id || ''}">
                                <i class="ki-outline ki-trash fs-5 me-2"></i>
                                ${deleteLabel}
                            </a>
                        </li>`;
                    } else {
                        deleteItem = `<li>
                            <span class="dropdown-item d-flex align-items-center py-2 text-muted"
                                  data-bs-toggle="tooltip"
                                  title="Cannot delete - has dependencies">
                                <i class="ki-outline ki-trash fs-5 me-2 text-gray-400"></i>
                                ${deleteLabel}
                                <i class="ki-outline ki-lock fs-7 ms-auto text-gray-400"></i>
                            </span>
                        </li>`;
                    }

                    return `<div class="dropdown" style="position: static;">
                        <button class="btn btn-sm btn-icon btn-light btn-active-light-primary"
                                type="button"
                                data-bs-toggle="dropdown"
                                data-bs-boundary="viewport"
                                aria-expanded="false">
                            <i class="ki-outline ki-dots-vertical fs-5"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow-sm py-2" style="z-index: 1050;">
                            <li>
                                <a class="dropdown-item d-flex align-items-center py-2" href="${row.show_url}">
                                    <i class="ki-outline ki-eye fs-5 me-2 text-gray-500"></i>
                                    ${viewLabel}
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item d-flex align-items-center py-2" href="#"
                                   data-course-edit-trigger
                                   data-course-id="${row.id}"
                                   data-course-name="${row.name}">
                                    <i class="ki-outline ki-pencil fs-5 me-2 text-gray-500"></i>
                                    ${editLabel}
                                </a>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            ${deleteItem}
                        </ul>
                    </div>`;
                }
            }
        ],
        filters: {
            status: 'select[name="status"]'
        },
        order: [[0, 'asc']],
        translations: translations
    });

    // Expose table instance for debugging if needed
    window.programCoursesTable = table;

    // Refresh table after course create/edit modal success
    document.addEventListener('ajax-form-success', function(event) {
        const modal = event.detail?.modal;
        if (!modal) return;

        if (modal.id === 'courseCreateModal' || modal.id === 'courseEditModal') {
            // Refresh DataTable to show new/updated course
            if (table && typeof table.reload === 'function') {
                table.reload(false); // Don't reset paging, keep current page
            }
        }
    });

    // Handle course edit trigger
    $(document).on('click', '[data-course-edit-trigger]', function(e) {
        e.preventDefault();
        const courseId = $(this).data('course-id');
        const modal = $('#courseEditModal');
        const form = modal.find('form');

        // Get the update URL template and replace placeholder
        const updateUrlTemplate = modal.find('[data-update-url-template]').data('update-url-template');
        if (updateUrlTemplate) {
            form.attr('action', updateUrlTemplate.replace('__ID__', courseId));
        }

        // Reset form and show loading state
        form.find('.alert').addClass('d-none');
        modal.find('[data-course-field]').prop('disabled', false);
        modal.find('#limitedEditingWarning').addClass('d-none');

        // Get the program ID from the current URL
        const pathParts = window.location.pathname.split('/');
        const programIndex = pathParts.indexOf('programs');
        const programId = programIndex !== -1 ? pathParts[programIndex + 1] : null;

        if (!programId) {
            console.error('Could not determine program ID');
            return;
        }

        // Fetch course data
        $.ajax({
            url: `/admin/programs/${programId}/courses/${courseId}/data`,
            method: 'GET',
            success: function(data) {
                // Populate form fields
                form.find('[name="course_code"]').val(data.course_code);
                form.find('[name="name"]').val(data.name);
                form.find('[name="description"]').val(data.description);
                form.find('[name="credits"]').val(data.credits);
                form.find('[name="instructor_id"]').val(data.instructor_id || '');

                // Handle limited editing for non-draft courses
                if (!data.can_full_edit) {
                    modal.find('#limitedEditingWarning').removeClass('d-none');
                    // Note: course_code and credits are now editable for all courses
                    // Only show warning but don't disable fields
                }

                // Show the modal
                modal.modal('show');
            },
            error: function(xhr) {
                const message = xhr.responseJSON?.message || 'Failed to load course data';
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: message
                });
            }
        });
    });

    // Handle course delete trigger
    $(document).on('click', '[data-course-delete-trigger]', function(e) {
        e.preventDefault();
        const courseId = $(this).data('course-id');
        const courseName = $(this).data('course-name') || 'this course';
        const courseCode = $(this).data('course-code') || '';
        const deleteUrl = $(this).data('delete-url');
        const programId = $(this).data('program-id');

        if (!deleteUrl) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Delete URL not found'
            });
            return;
        }

        // Check if confirmation is required by fetching course data
        const pathParts = window.location.pathname.split('/');
        const programIndex = pathParts.indexOf('programs');
        const programIdFromUrl = programIndex !== -1 ? pathParts[programIndex + 1] : programId;

        // Fetch course data to check if confirmation is needed
        $.ajax({
            url: `/admin/programs/${programIdFromUrl}/courses/${courseId}/data`,
            method: 'GET',
            success: function(courseData) {
                const requiresConfirmation = courseData.requires_delete_confirmation || false;

                // Show appropriate confirmation dialog
                if (requiresConfirmation) {
                    // Show confirmation dialog with input field
                    Swal.fire({
                        title: 'Delete Course?',
                        html: `Are you sure you want to delete <strong>${escapeAttr(courseCode)} - ${escapeAttr(courseName)}</strong>?<br><br>This program has students enrolled. Type <strong>DELETE COURSE</strong> to confirm.`,
                        icon: 'warning',
                        input: 'text',
                        inputPlaceholder: 'Type DELETE COURSE',
                        showCancelButton: true,
                        confirmButtonText: 'Delete',
                        cancelButtonText: 'Cancel',
                        buttonsStyling: false,
                        customClass: {
                            confirmButton: 'btn btn-danger',
                            cancelButton: 'btn btn-light'
                        },
                        inputValidator: (value) => {
                            if (value !== 'DELETE COURSE') {
                                return 'You must type "DELETE COURSE" to confirm';
                            }
                        },
                        preConfirm: (value) => {
                            if (value !== 'DELETE COURSE') {
                                Swal.showValidationMessage('You must type "DELETE COURSE" to confirm');
                                return false;
                            }
                            return value;
                        }
                    }).then((result) => {
                        if (result.isConfirmed && result.value === 'DELETE COURSE') {
                            performDelete(deleteUrl, courseCode, courseName, 'DELETE COURSE');
                        }
                    });
                } else {
                    // Show simple confirmation dialog
                    Swal.fire({
                        title: 'Delete Course?',
                        html: `Are you sure you want to delete <strong>${escapeAttr(courseCode)} - ${escapeAttr(courseName)}</strong>?<br><br>This action cannot be undone.`,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Yes, delete it',
                        cancelButtonText: 'Cancel',
                        buttonsStyling: false,
                        customClass: {
                            confirmButton: 'btn btn-danger',
                            cancelButton: 'btn btn-light'
                        }
                    }).then((result) => {
                        if (result.isConfirmed) {
                            performDelete(deleteUrl, courseCode, courseName);
                        }
                    });
                }
            },
            error: function() {
                // If we can't fetch course data, show simple confirmation
                Swal.fire({
                    title: 'Delete Course?',
                    html: `Are you sure you want to delete <strong>${escapeAttr(courseCode)} - ${escapeAttr(courseName)}</strong>?<br><br>This action cannot be undone.`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, delete it',
                    cancelButtonText: 'Cancel',
                    buttonsStyling: false,
                    customClass: {
                        confirmButton: 'btn btn-danger',
                        cancelButton: 'btn btn-light'
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        performDelete(deleteUrl, courseCode, courseName);
                    }
                });
            }
        });

        // Function to perform the actual delete
        function performDelete(deleteUrl, courseCode, courseName, confirmationValue = null) {
            // Show loading state
            Swal.fire({
                title: 'Deleting...',
                text: 'Please wait while we delete the course',
                icon: 'info',
                allowOutsideClick: false,
                allowEscapeKey: false,
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            // Get CSRF token
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';

            // Prepare form data
            const formData = new FormData();
            formData.append('_token', csrfToken);
            formData.append('_method', 'DELETE');

            // Include confirmation if provided
            if (confirmationValue) {
                formData.append('confirmation', confirmationValue);
            }

            // Submit via AJAX
            fetch(deleteUrl, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(response => {
                return response.json().then(data => ({
                    ok: response.ok,
                    status: response.status,
                    data: data
                }));
            })
            .then(({ok, status, data}) => {
                if (ok) {
                    // Success
                    Swal.fire({
                        icon: 'success',
                        title: 'Deleted!',
                        text: data.message || 'Course deleted successfully.',
                        confirmButtonText: 'OK',
                        buttonsStyling: false,
                        customClass: {
                            confirmButton: 'btn btn-primary'
                        }
                    }).then(() => {
                        // Refresh the table
                        if (table && typeof table.reload === 'function') {
                            table.reload(false);
                        }
                    });
                } else {
                    // Error - check if it's a validation error for confirmation
                    if (status === 422 && data.errors && data.errors.confirmation) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Confirmation Required',
                            html: `This program has students enrolled.<br><br>Please type <strong>DELETE COURSE</strong> to confirm deletion.`,
                            confirmButtonText: 'OK',
                            buttonsStyling: false,
                            customClass: {
                                confirmButton: 'btn btn-primary'
                            }
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: data.message || 'Failed to delete course. Please try again.',
                            confirmButtonText: 'OK',
                            buttonsStyling: false,
                            customClass: {
                                confirmButton: 'btn btn-primary'
                            }
                        });
                    }
                }
            })
            .catch(error => {
                console.error('Delete error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'An error occurred while deleting the course. Please try again.',
                    confirmButtonText: 'OK',
                    buttonsStyling: false,
                    customClass: {
                        confirmButton: 'btn btn-primary'
                    }
                });
            });
        }
    });
});

