"use strict";

/**
 * Agents Table Configuration
 *
 * Page-specific DataTable configuration for the Agent Management index.
 * Uses ColumnRenderers for consistent column formatting and AJAX modals.
 * Follows the same pattern as users-table.js
 */
document.addEventListener('DOMContentLoaded', function() {
    const tableElement = document.getElementById('agents-table');
    const createModal = document.getElementById('agentCreateModal');
    const editModal = document.getElementById('agentEditModal');
    const deleteModal = document.getElementById('agentDeleteModal');
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
        const nameInput = editModal.querySelector('[data-agent-field="name"]');
        const emailInput = editModal.querySelector('[data-agent-field="email"]');

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
        const confirmRow = document.getElementById('editAgentPasswordConfirmRow');
        if (confirmRow) confirmRow.style.display = 'none';

        // Set avatar preview if provided
        const avatarPreview = editModal.querySelector('[data-agent-avatar-preview]');
        const avatarInput = document.getElementById('editAgentAvatarInput');
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
        const nameEl = deleteModal.querySelector('[data-agent-delete-name]');

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
            id: button.dataset.agentId,
            name: button.dataset.agentName,
            email: button.dataset.agentEmail,
            profile_photo_url: button.dataset.agentAvatar
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
            id: button.dataset.agentId,
            name: button.dataset.agentName
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
        if (typeof payload.total_agents !== 'undefined') {
            document.querySelectorAll('[data-agent-total-count]').forEach(el => {
                el.textContent = payload.total_agents;
            });
        }
        if (typeof payload.active_agents !== 'undefined') {
            document.querySelectorAll('[data-agent-active-count]').forEach(el => {
                el.textContent = payload.active_agents;
            });
        }
        if (typeof payload.new_this_month !== 'undefined') {
            document.querySelectorAll('[data-agent-new-month-count]').forEach(el => {
                el.textContent = payload.new_this_month;
            });
        }
    };

    // Handle password field show/hide confirmation
    if (editModal) {
        const passwordInput = editModal.querySelector('input[name="password"]');
        const confirmRow = document.getElementById('editAgentPasswordConfirmRow');
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
            noRecords: tableElement.dataset.textNoRecords || 'No agents found'
        };

        tableInstance = new AdminDataTable('agents-table', {
            ajaxUrl: tableElement.dataset.ajaxUrl,
            columns: [
                {
                    data: 'name',
                    name: 'name',
                    className: 'ps-4',
                    render: ColumnRenderers.avatar({
                        avatarField: 'profile_photo_url',
                        nameField: 'name',
                        urlField: 'show_url',
                        size: 'md'
                    })
                },
                {
                    data: 'email',
                    name: 'email',
                    render: function(data, type, row) {
                        return `<a href="${row.show_url}" class="text-gray-800 text-hover-primary fw-semibold">${escapeAttr(data)}</a>`;
                    }
                },
                {
                    data: 'applications_count',
                    name: 'agent_applications_count',
                    className: 'text-center',
                    render: function(data, type, row) {
                        const count = data || 0;
                        const badgeClass = count > 0 ? 'badge-light-primary' : 'badge-light';
                        const badge = `<span class="badge ${badgeClass} fw-bold fs-7">${count}</span>`;
                        if (count > 0 && row.applications_url) {
                            return `<a href="${row.applications_url}" class="text-hover-primary">${badge}</a>`;
                        }
                        return badge;
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

                        const editAttrs = `data-agent-edit-trigger
                            data-agent-id="${row.id}"
                            data-agent-name="${escapeAttr(row.name)}"
                            data-agent-email="${escapeAttr(row.email)}"
                            data-agent-avatar="${escapeAttr(row.profile_photo_url || '')}"`;

                        const deleteItem = row.can_delete
                            ? `<li>
                                    <a class="dropdown-item d-flex align-items-center py-2 text-danger" href="#"
                                       data-agent-delete-trigger
                                       data-agent-id="${row.id}"
                                       data-agent-name="${escapeAttr(row.name)}">
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

        window.agentsTable = tableInstance;
    }

    // Delegate edit clicks
    document.addEventListener('click', function(e) {
        const editTrigger = e.target.closest('[data-agent-edit-trigger]');
        if (editTrigger) {
            e.preventDefault();
            showEditModal(editTrigger);
        }

        const deleteTrigger = e.target.closest('[data-agent-delete-trigger]');
        if (deleteTrigger) {
            e.preventDefault();
            showDeleteModal(deleteTrigger);
        }
    });

    // Refresh table and counters after modal success
    document.addEventListener('ajax-form-success', function(event) {
        const modal = event.detail?.modal;
        if (!modal) return;

        if (modal.id === 'agentCreateModal' || modal.id === 'agentEditModal' || modal.id === 'agentDeleteModal') {
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
            const avatarPreview = editModal.querySelector('[data-agent-avatar-preview]');
            const avatarInput = document.getElementById('editAgentAvatarInput');
            if (avatarPreview) {
                avatarPreview.style.backgroundImage = '';
            }
            if (avatarInput) {
                avatarInput.classList.add('image-input-empty');
            }
        });
    }
});
