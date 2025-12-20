"use strict";

/**
 * Instructors Table Configuration
 *
 * Page-specific DataTable configuration for the Instructors Management index.
 * Uses ColumnRenderers for consistent column formatting and AJAX modals.
 * Follows the same pattern as students-table.js
 */
document.addEventListener('DOMContentLoaded', function() {
    const tableElement = document.getElementById('instructors-table');
    const createModal = document.getElementById('instructorCreateModal');
    const editModal = document.getElementById('instructorEditModal');
    const deleteModal = document.getElementById('instructorDeleteModal');
    let tableInstance = null;

    const escapeAttr = (value) => {
        if (value === null || value === undefined) return '';
        return String(value)
            .replace(/&/g, '&amp;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#39;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;');
    };

    // Set edit modal data from row data
    const setEditModalData = (data) => {
        if (!editModal) return;
        const form = editModal.querySelector('form');
        const templateHolder = editModal.querySelector('[data-update-url-template]');
        const template = templateHolder ? templateHolder.dataset.updateUrlTemplate : '';

        // Update form action URL
        if (form && template && data.id) {
            form.action = template.replace('__ID__', data.id);
        }

        // Fill form fields
        const fields = ['first_name', 'last_name', 'email'];
        fields.forEach(field => {
            const input = editModal.querySelector(`[data-instructor-field="${field}"]`);
            if (input) {
                input.value = data[field] || '';
            }
        });

        // Clear password fields
        const passwordInput = editModal.querySelector('input[name="password"]');
        const confirmInput = editModal.querySelector('input[name="password_confirmation"]');
        if (passwordInput) passwordInput.value = '';
        if (confirmInput) {
            confirmInput.value = '';
            confirmInput.disabled = true;
        }

        // Hide password confirmation row
        const confirmRow = document.getElementById('editInstructorPasswordConfirmRow');
        if (confirmRow) confirmRow.style.display = 'none';

        // Set avatar preview if provided
        const avatarPreview = editModal.querySelector('[data-instructor-avatar-preview]');
        const avatarInput = document.getElementById('editInstructorAvatarInput');
        if (avatarPreview && data.profile_photo_url) {
            avatarPreview.style.backgroundImage = `url('${data.profile_photo_url}')`;
            if (avatarInput) avatarInput.classList.remove('image-input-empty');
        } else if (avatarPreview) {
            avatarPreview.style.backgroundImage = '';
            if (avatarInput) avatarInput.classList.add('image-input-empty');
        }
    };

    // Set delete modal data
    const setDeleteModalData = (data) => {
        if (!deleteModal) return;
        const form = deleteModal.querySelector('form');
        const templateHolder = deleteModal.querySelector('[data-delete-url-template]');
        const template = templateHolder ? templateHolder.dataset.deleteUrlTemplate : '';
        const nameEl = deleteModal.querySelector('[data-instructor-delete-name]');

        if (form && template && data.id) {
            form.action = template.replace('__ID__', data.id);
        }

        if (nameEl) {
            nameEl.textContent = data.name || '';
        }
    };

    // Show edit modal
    const showEditModal = (button) => {
        const data = {
            id: button.dataset.instructorId,
            first_name: button.dataset.instructorFirstName,
            last_name: button.dataset.instructorLastName,
            email: button.dataset.instructorEmail,
            profile_photo_url: button.dataset.instructorAvatar
        };
        setEditModalData(data);
        if (editModal) {
            const modal = bootstrap.Modal.getOrCreateInstance(editModal);
            modal.show();
        }
    };

    // Show delete modal
    const showDeleteModal = (button) => {
        const data = {
            id: button.dataset.instructorId,
            name: button.dataset.instructorName
        };
        setDeleteModalData(data);
        if (deleteModal) {
            const modal = bootstrap.Modal.getOrCreateInstance(deleteModal);
            modal.show();
        }
    };

    // Update stat counters from AJAX response
    const updateCounters = (payload) => {
        if (!payload) return;
        if (typeof payload.total_instructors !== 'undefined') {
            document.querySelectorAll('[data-instructor-total-count]').forEach(el => {
                el.textContent = payload.total_instructors;
            });
        }
        if (typeof payload.active_instructors !== 'undefined') {
            document.querySelectorAll('[data-instructor-active-count]').forEach(el => {
                el.textContent = payload.active_instructors;
            });
        }
        if (typeof payload.new_this_month !== 'undefined') {
            document.querySelectorAll('[data-instructor-new-month-count]').forEach(el => {
                el.textContent = payload.new_this_month;
            });
        }
    };

    // Handle password field show/hide confirmation
    if (editModal) {
        const passwordInput = editModal.querySelector('input[name="password"]');
        const confirmRow = document.getElementById('editInstructorPasswordConfirmRow');
        const confirmInput = editModal.querySelector('input[name="password_confirmation"]');

        // Initially disable confirmation field so it's not submitted when empty
        if (confirmInput) {
            confirmInput.disabled = true;
        }

        if (passwordInput && confirmRow) {
            passwordInput.addEventListener('input', function() {
                if (this.value.length > 0) {
                    confirmRow.style.display = 'block';
                    if (confirmInput) {
                        confirmInput.disabled = false;
                        confirmInput.setAttribute('required', 'required');
                    }
                } else {
                    confirmRow.style.display = 'none';
                    if (confirmInput) {
                        confirmInput.disabled = true;
                        confirmInput.removeAttribute('required');
                        confirmInput.value = '';
                    }
                }
            });
        }
    }

    // Initialize DataTable if present
    if (tableElement) {
        const translations = {
            showing: tableElement.dataset.textShowing || 'Showing',
            to: tableElement.dataset.textTo || 'to',
            of: tableElement.dataset.textOf || 'of',
            entries: tableElement.dataset.textEntries || 'entries',
            filteredFrom: tableElement.dataset.textFilteredFrom || 'filtered from',
            total: tableElement.dataset.textTotal || 'total',
            noRecords: tableElement.dataset.textNoRecords || 'No instructors found'
        };

        tableInstance = new AdminDataTable('instructors-table', {
            ajaxUrl: tableElement.dataset.ajaxUrl,
            columns: [
                {
                    data: 'name',
                    name: 'first_name',
                    className: 'ps-4',
                    render: ColumnRenderers.avatar({
                        avatarField: 'profile_photo_url',
                        nameField: 'name',
                        subtitleField: 'email',
                        size: 'md'
                    })
                },
                {
                    data: 'email',
                    name: 'email',
                    render: function(data, type, row) {
                        let html = `<span class="text-gray-800 fw-semibold d-block">${escapeAttr(data)}</span>`;
                        if (row.courses_count > 0) {
                            html += `<span class="badge badge-light-success mt-1">${row.courses_count} ${row.courses_count === 1 ? 'Course' : 'Courses'}</span>`;
                        } else {
                            html += `<span class="badge badge-light-warning mt-1">No Courses</span>`;
                        }
                        return html;
                    }
                },
                {
                    data: 'created_at',
                    name: 'created_at',
                    className: 'text-center',
                    render: function(data, type, row) {
                        if (!data) return '-';
                        return `<span class="badge badge-light fw-bold fs-7">${escapeAttr(row.created_at_formatted)}</span>`;
                    }
                },
                {
                    data: 'id',
                    name: 'actions',
                    orderable: false,
                    searchable: false,
                    className: 'text-end pe-4',
                    render: function(data, type, row) {
                        const viewLabel = tableElement.dataset.textView || 'View Details';
                        const editLabel = tableElement.dataset.textEdit || 'Edit';
                        const deleteLabel = tableElement.dataset.textDelete || 'Delete';

                        const editAttrs = `data-instructor-edit-trigger
                            data-instructor-id="${row.id}"
                            data-instructor-first-name="${escapeAttr(row.first_name)}"
                            data-instructor-last-name="${escapeAttr(row.last_name)}"
                            data-instructor-email="${escapeAttr(row.email)}"
                            data-instructor-avatar="${escapeAttr(row.profile_photo_url || '')}"`;

                        const deleteItem = row.can_delete
                            ? `<li>
                                    <a class="dropdown-item d-flex align-items-center py-2 text-danger" href="#"
                                       data-instructor-delete-trigger
                                       data-instructor-id="${row.id}"
                                       data-instructor-name="${escapeAttr(row.name)}">
                                        <i class="ki-outline ki-trash fs-5 me-2"></i>
                                        ${escapeAttr(deleteLabel)}
                                    </a>
                               </li>`
                            : `<li>
                                    <span class="dropdown-item d-flex align-items-center py-2 text-muted" data-bs-toggle="tooltip" title="Cannot delete - has active course assignments">
                                        <i class="ki-outline ki-trash fs-5 me-2 text-gray-400"></i>
                                        ${escapeAttr(deleteLabel)}
                                        <i class="ki-outline ki-lock fs-7 ms-auto text-gray-400"></i>
                                    </span>
                               </li>`;

                        return `<div class="d-flex align-items-center justify-content-end gap-2">
                            <div class="dropdown" style="position: static;">
                                <button class="btn btn-sm btn-icon btn-light btn-active-light-primary" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="ki-outline ki-dots-vertical fs-5"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end shadow-sm py-2" style="z-index: 1050;">
                                    <li>
                                        <a class="dropdown-item d-flex align-items-center py-2" href="${row.show_url}">
                                            <i class="ki-outline ki-eye fs-5 me-2 text-gray-500"></i>
                                            ${escapeAttr(viewLabel)}
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item d-flex align-items-center py-2" href="#" ${editAttrs}>
                                            <i class="ki-outline ki-pencil fs-5 me-2 text-gray-500"></i>
                                            ${escapeAttr(editLabel)}
                                        </a>
                                    </li>
                                    <li><hr class="dropdown-divider"></li>
                                    ${deleteItem}
                                </ul>
                            </div>
                        </div>`;
                    }
                }
            ],
            filters: {
                status: 'select[name="status"]'
            },
            order: [[2, 'desc']],
            translations: translations
        });

        window.instructorsTable = tableInstance;
    }

    // Delegate edit clicks
    document.addEventListener('click', function(e) {
        const editTrigger = e.target.closest('[data-instructor-edit-trigger]');
        if (editTrigger) {
            e.preventDefault();
            showEditModal(editTrigger);
        }

        const deleteTrigger = e.target.closest('[data-instructor-delete-trigger]');
        if (deleteTrigger) {
            e.preventDefault();
            showDeleteModal(deleteTrigger);
        }
    });

    // Refresh table and counters after modal success
    document.addEventListener('ajax-form-success', function(event) {
        const modal = event.detail?.modal;
        if (!modal) return;

        if (modal.id === 'instructorCreateModal' || modal.id === 'instructorEditModal' || modal.id === 'instructorDeleteModal') {
            // Refresh DataTable if present
            if (tableInstance && typeof tableInstance.reload === 'function') {
                tableInstance.reload(true);
            }
            // Update counters
            updateCounters(event.detail.data);
        }
    });

    // Reset avatar preview when edit modal is hidden
    if (editModal) {
        editModal.addEventListener('hidden.bs.modal', function() {
            const avatarPreview = editModal.querySelector('[data-instructor-avatar-preview]');
            const avatarInput = document.getElementById('editInstructorAvatarInput');
            if (avatarPreview) {
                avatarPreview.style.backgroundImage = '';
            }
            if (avatarInput) {
                avatarInput.classList.add('image-input-empty');
            }
        });
    }
});
