"use strict";

/**
 * Students Table Configuration
 *
 * Page-specific DataTable configuration for the Students admin index.
 * Uses ColumnRenderers for consistent column formatting and AJAX modals.
 * Follows the same pattern as programs-table.js
 */
document.addEventListener('DOMContentLoaded', function() {
    const tableElement = document.getElementById('students-table');
    const createModal = document.getElementById('studentCreateModal');
    const editModal = document.getElementById('studentEditModal');
    const deleteModal = document.getElementById('studentDeleteModal');
    let tableInstance = null;
    let originalProgramId = null;

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
        const fields = ['first_name', 'last_name', 'email', 'phone', 'date_of_birth'];
        fields.forEach(field => {
            const input = editModal.querySelector(`[data-student-field="${field}"]`);
            if (input) {
                input.value = data[field] || '';
            }
        });

        // Clear password fields
        const passwordInput = editModal.querySelector('input[name="password"]');
        const confirmInput = editModal.querySelector('input[name="password_confirmation"]');
        if (passwordInput) passwordInput.value = '';
        if (confirmInput) confirmInput.value = '';

        // Hide password confirmation row
        const confirmRow = document.getElementById('editPasswordConfirmRow');
        if (confirmRow) confirmRow.style.display = 'none';

        // Set program dropdown and track original
        originalProgramId = data.program_id ? String(data.program_id) : null;
        const programSelect = editModal.querySelector('[data-student-field="program_id"]');
        if (programSelect) {
            programSelect.value = originalProgramId || '';
        }

        // Hide program warning
        const warning = document.getElementById('programChangeWarning');
        if (warning) warning.classList.add('d-none');
    };

    // Set delete modal data
    const setDeleteModalData = (data) => {
        if (!deleteModal) return;
        const form = deleteModal.querySelector('form');
        const templateHolder = deleteModal.querySelector('[data-delete-url-template]');
        const template = templateHolder ? templateHolder.dataset.deleteUrlTemplate : '';
        const nameEl = deleteModal.querySelector('[data-student-delete-name]');

        if (form && template && data.id) {
            form.action = template.replace('__ID__', data.id);
        }

        if (nameEl) {
            nameEl.textContent = data.name || `${data.first_name} ${data.last_name}`;
        }
    };

    // Show edit modal
    const showEditModal = (button) => {
        const data = {
            id: button.dataset.studentId,
            first_name: button.dataset.studentFirstName,
            last_name: button.dataset.studentLastName,
            email: button.dataset.studentEmail,
            phone: button.dataset.studentPhone,
            date_of_birth: button.dataset.studentDob,
            program_id: button.dataset.studentProgramId
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
            id: button.dataset.studentId,
            name: button.dataset.studentName
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
        if (typeof payload.total_students !== 'undefined') {
            document.querySelectorAll('[data-student-total-count]').forEach(el => {
                el.textContent = payload.total_students;
            });
        }
        if (typeof payload.with_applications !== 'undefined') {
            document.querySelectorAll('[data-student-with-app-count]').forEach(el => {
                el.textContent = payload.with_applications;
            });
        }
        if (typeof payload.without_applications !== 'undefined') {
            document.querySelectorAll('[data-student-without-app-count]').forEach(el => {
                el.textContent = payload.without_applications;
            });
        }
        if (typeof payload.new_this_month !== 'undefined') {
            document.querySelectorAll('[data-student-new-month-count]').forEach(el => {
                el.textContent = payload.new_this_month;
            });
        }
    };

    // Handle password field show/hide confirmation
    if (editModal) {
        const passwordInput = editModal.querySelector('input[name="password"]');
        const confirmRow = document.getElementById('editPasswordConfirmRow');
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

        // Handle program change warning (dropdown)
        const programSelect = editModal.querySelector('[data-student-field="program_id"]');
        const warning = document.getElementById('programChangeWarning');

        if (programSelect && warning) {
            programSelect.addEventListener('change', function() {
                if (originalProgramId && this.value !== originalProgramId) {
                    warning.classList.remove('d-none');
                } else {
                    warning.classList.add('d-none');
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
            noRecords: tableElement.dataset.textNoRecords || 'No students found'
        };

        tableInstance = new AdminDataTable('students-table', {
            ajaxUrl: tableElement.dataset.ajaxUrl,
            columns: [
                {
                    data: 'name',
                    name: 'first_name',
                    className: 'ps-4',
                    render: ColumnRenderers.avatar({
                        avatarField: 'profile_photo_url',
                        nameField: 'name',
                        subtitleField: 'student_number',
                        size: 'md'
                    })
                },
                {
                    data: 'email',
                    name: 'email',
                    render: function(data, type, row) {
                        let html = `<span class="text-gray-800 fw-semibold d-block">${escapeAttr(data)}</span>`;
                        if (row.program_name) {
                            html += `<span class="badge badge-light-info mt-1">${escapeAttr(row.program_name)}</span>`;
                        }
                        return html;
                    }
                },
                {
                    data: 'application_reference',
                    orderable: false,
                    searchable: false,
                    className: 'text-center',
                    render: function(data, type, row) {
                        if (data && row.application_url) {
                            return `<a href="${row.application_url}" class="badge badge-light-success">${escapeAttr(data)}</a>`;
                        }
                        return '<span class="badge badge-light-warning">No Application</span>';
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
                        const editLabel = tableElement.dataset.textEdit || 'Edit';
                        const deleteLabel = tableElement.dataset.textDelete || 'Delete';
                        const viewLabel = tableElement.dataset.textView || 'View Details';

                        const editAttrs = `data-student-edit-trigger
                            data-student-id="${row.id}"
                            data-student-first-name="${escapeAttr(row.first_name)}"
                            data-student-last-name="${escapeAttr(row.last_name)}"
                            data-student-email="${escapeAttr(row.email)}"
                            data-student-phone="${escapeAttr(row.phone || '')}"
                            data-student-dob="${escapeAttr(row.date_of_birth || '')}"
                            data-student-program-id="${row.program_id || ''}"`;

                        const deleteItem = row.can_delete
                            ? `<li>
                                    <a class="dropdown-item d-flex align-items-center py-2 text-danger" href="#"
                                       data-student-delete-trigger
                                       data-student-id="${row.id}"
                                       data-student-name="${escapeAttr(row.name)}">
                                        <i class="ki-outline ki-trash fs-5 me-2"></i>
                                        ${escapeAttr(deleteLabel)}
                                    </a>
                               </li>`
                            : `<li>
                                    <span class="dropdown-item d-flex align-items-center py-2 text-muted" data-bs-toggle="tooltip" title="Cannot delete - has progress data">
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
                application_status: 'select[name="application_status"]',
                program_id: 'select[name="program_id"]'
            },
            order: [[3, 'desc']],
            translations: translations
        });

        window.studentsTable = tableInstance;
    }

    // Delegate edit clicks
    document.addEventListener('click', function(e) {
        const editTrigger = e.target.closest('[data-student-edit-trigger]');
        if (editTrigger) {
            e.preventDefault();
            showEditModal(editTrigger);
        }

        const deleteTrigger = e.target.closest('[data-student-delete-trigger]');
        if (deleteTrigger) {
            e.preventDefault();
            showDeleteModal(deleteTrigger);
        }
    });

    // Refresh table and counters after modal success
    document.addEventListener('ajax-form-success', function(event) {
        const modal = event.detail?.modal;
        if (!modal) return;

        if (modal.id === 'studentCreateModal' || modal.id === 'studentEditModal' || modal.id === 'studentDeleteModal') {
            // Refresh DataTable if present
            if (tableInstance && typeof tableInstance.reload === 'function') {
                tableInstance.reload(true);
            }
            // Update counters
            updateCounters(event.detail.data);
        }
    });

    // Reset original program ID when edit modal is hidden
    if (editModal) {
        editModal.addEventListener('hidden.bs.modal', function() {
            originalProgramId = null;
        });
    }
});
