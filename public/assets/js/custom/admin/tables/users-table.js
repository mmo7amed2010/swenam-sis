"use strict";

/**
 * Users Table Configuration
 *
 * Page-specific DataTable configuration for the Admin Users Management index.
 * Uses ColumnRenderers for consistent column formatting and AJAX modals.
 * Follows the same pattern as students-table.js
 */
document.addEventListener('DOMContentLoaded', function() {
    const tableElement = document.getElementById('users-table');
    const createModal = document.getElementById('adminCreateModal');
    const editModal = document.getElementById('adminEditModal');
    const deleteModal = document.getElementById('adminDeleteModal');
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
        const nameInput = editModal.querySelector('[data-admin-field="name"]');
        const emailInput = editModal.querySelector('[data-admin-field="email"]');

        if (nameInput) nameInput.value = data.name || '';
        if (emailInput) emailInput.value = data.email || '';

        // Clear password fields
        const passwordInput = editModal.querySelector('input[name="password"]');
        const confirmInput = editModal.querySelector('input[name="password_confirmation"]');
        if (passwordInput) passwordInput.value = '';
        if (confirmInput) {
            confirmInput.value = '';
            confirmInput.disabled = true;
        }

        // Hide password confirmation row
        const confirmRow = document.getElementById('editAdminPasswordConfirmRow');
        if (confirmRow) confirmRow.style.display = 'none';

        // Set avatar preview if provided
        const avatarPreview = editModal.querySelector('[data-admin-avatar-preview]');
        const avatarInput = document.getElementById('editAvatarInput');
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
        const nameEl = deleteModal.querySelector('[data-admin-delete-name]');

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
            id: button.dataset.adminId,
            name: button.dataset.adminName,
            email: button.dataset.adminEmail,
            profile_photo_url: button.dataset.adminAvatar
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
            id: button.dataset.adminId,
            name: button.dataset.adminName
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
        if (typeof payload.total_admins !== 'undefined') {
            document.querySelectorAll('[data-admin-total-count]').forEach(el => {
                el.textContent = payload.total_admins;
            });
        }
        if (typeof payload.active_admins !== 'undefined') {
            document.querySelectorAll('[data-admin-active-count]').forEach(el => {
                el.textContent = payload.active_admins;
            });
        }
        if (typeof payload.new_this_month !== 'undefined') {
            document.querySelectorAll('[data-admin-new-month-count]').forEach(el => {
                el.textContent = payload.new_this_month;
            });
        }
    };

    // Handle password field show/hide confirmation
    if (editModal) {
        const passwordInput = editModal.querySelector('input[name="password"]');
        const confirmRow = document.getElementById('editAdminPasswordConfirmRow');
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
            noRecords: tableElement.dataset.textNoRecords || 'No admins found'
        };

        tableInstance = new AdminDataTable('users-table', {
            ajaxUrl: tableElement.dataset.ajaxUrl,
            columns: [
                {
                    data: 'name',
                    name: 'name',
                    className: 'ps-4',
                    render: ColumnRenderers.avatar({
                        avatarField: 'profile_photo_url',
                        nameField: 'name',
                        size: 'md'
                    })
                },
                {
                    data: 'email',
                    name: 'email',
                    render: function(data, type, row) {
                        return `<span class="text-gray-800 fw-semibold">${escapeAttr(data)}</span>`;
                    }
                },

                {
                    data: 'last_login_at',
                    name: 'last_login_at',
                    orderable: false,
                    className: 'text-center',
                    render: function(data, type, row) {
                        return `
                            <div class="d-flex flex-column align-items-center">
                                <span class="badge badge-light fw-bold fs-7">${escapeAttr(data)}</span>
                                <span class="text-gray-500 fs-8">${escapeAttr(row.created_at_formatted)}</span>
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
                        const editLabel = tableElement.dataset.textEdit || 'Edit';
                        const deleteLabel = tableElement.dataset.textDelete || 'Delete';

                        const editAttrs = `data-admin-edit-trigger
                            data-admin-id="${row.id}"
                            data-admin-name="${escapeAttr(row.name)}"
                            data-admin-email="${escapeAttr(row.email)}"
                            data-admin-avatar="${escapeAttr(row.profile_photo_url || '')}"`;

                        const deleteItem = row.can_delete
                            ? `<li>
                                    <a class="dropdown-item d-flex align-items-center py-2 text-danger" href="#"
                                       data-admin-delete-trigger
                                       data-admin-id="${row.id}"
                                       data-admin-name="${escapeAttr(row.name)}">
                                        <i class="ki-outline ki-trash fs-5 me-2"></i>
                                        ${escapeAttr(deleteLabel)}
                                    </a>
                               </li>`
                            : `<li>
                                    <span class="dropdown-item d-flex align-items-center py-2 text-muted" data-bs-toggle="tooltip" title="Cannot delete yourself">
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
            },
            order: [[0, 'asc']],
            translations: translations
        });

        window.usersTable = tableInstance;
    }

    // Delegate edit clicks
    document.addEventListener('click', function(e) {
        const editTrigger = e.target.closest('[data-admin-edit-trigger]');
        if (editTrigger) {
            e.preventDefault();
            showEditModal(editTrigger);
        }

        const deleteTrigger = e.target.closest('[data-admin-delete-trigger]');
        if (deleteTrigger) {
            e.preventDefault();
            showDeleteModal(deleteTrigger);
        }
    });

    // Refresh table and counters after modal success
    document.addEventListener('ajax-form-success', function(event) {
        const modal = event.detail?.modal;
        if (!modal) return;

        if (modal.id === 'adminCreateModal' || modal.id === 'adminEditModal' || modal.id === 'adminDeleteModal') {
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
            const avatarPreview = editModal.querySelector('[data-admin-avatar-preview]');
            const avatarInput = document.getElementById('editAvatarInput');
            if (avatarPreview) {
                avatarPreview.style.backgroundImage = '';
            }
            if (avatarInput) {
                avatarInput.classList.add('image-input-empty');
            }
        });
    }
});
