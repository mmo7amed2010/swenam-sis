"use strict";

/**
 * Programs Table Configuration
 *
 * Page-specific DataTable configuration for the Programs admin index.
 * Uses ColumnRenderers for consistent column formatting.
 */
document.addEventListener('DOMContentLoaded', function() {
    const tableElement = document.getElementById('programs-table');
    const editModal = document.getElementById('programEditModal');
    const createModal = document.getElementById('programCreateModal');
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

    const updateCharCount = (input) => {
        const target = input.dataset.charCountTarget;
        const counter = target ? document.querySelector(`[data-char-count="${target}"]`) : null;
        if (counter) {
            counter.textContent = input.value.length;
        }
    };

    const initCharCounters = () => {
        document.querySelectorAll('[data-program-name-input]').forEach(input => {
            updateCharCount(input);
            input.addEventListener('input', () => updateCharCount(input));
        });
    };

    const setEditModalData = (data) => {
        if (!editModal) return;
        const form = editModal.querySelector('form');
        const nameInput = editModal.querySelector('input[name="name"]');
        const activeInput = editModal.querySelector('input[name="is_active"]');
        const templateHolder = editModal.querySelector('[data-update-url-template]');
        const template = templateHolder ? templateHolder.dataset.updateUrlTemplate : '';

        if (form && template && data.id) {
            form.action = template.replace('__ID__', data.id);
        }
        if (nameInput && data.name !== undefined) {
            nameInput.value = data.name;
            updateCharCount(nameInput);
        }
        if (activeInput) {
            activeInput.checked = Boolean(Number(data.is_active ?? data.active ?? 0));
        }
    };

    const showEditModal = (button) => {
        const data = {
            id: button.dataset.programId,
            name: button.dataset.programName,
            is_active: button.dataset.programActive
        };
        setEditModalData(data);
        if (editModal) {
            const modal = bootstrap.Modal.getOrCreateInstance(editModal);
            modal.show();
        }
    };

    const updateCounters = (payload) => {
        if (!payload) return;
        if (typeof payload.total_active !== 'undefined') {
            document.querySelectorAll('[data-program-active-count]').forEach(el => {
                el.textContent = payload.total_active;
            });
        }
        if (typeof payload.total_inactive !== 'undefined') {
            document.querySelectorAll('[data-program-inactive-count]').forEach(el => {
                el.textContent = payload.total_inactive;
            });
        }
    };

    // Initialize DataTable if present
    if (tableElement) {
        const translations = {
            showing: tableElement.dataset.textShowing || 'Showing',
            to: tableElement.dataset.textTo || 'to',
            of: tableElement.dataset.textOf || 'of',
            entries: tableElement.dataset.textEntries || 'entries',
            filteredFrom: tableElement.dataset.textFilteredFrom || 'filtered from',
            total: tableElement.dataset.textTotal || 'total',
            noRecords: tableElement.dataset.textNoRecords || 'No matching records found'
        };

        tableInstance = new AdminDataTable('programs-table', {
            ajaxUrl: tableElement.dataset.ajaxUrl,
            columns: [
                {
                    data: 'id',
                    name: 'id',
                    className: 'ps-4',
                    render: function(data) {
                        return `<span class="text-gray-700 fw-bold">${data}</span>`;
                    }
                },
                {
                    data: 'name',
                    name: 'name',
                    render: ColumnRenderers.nameCell({
                        icon: 'abstract-26',
                        nameField: 'name',
                        urlField: 'show_url',
                        subtitleField: 'created_at',
                        subtitlePrefix: tableElement.dataset.textCreated || 'Created',
                        statusField: 'is_active'
                    })
                },
                {
                    data: 'courses_count',
                    name: 'courses_count',
                    orderable: false,
                    className: 'text-center',
                    render: ColumnRenderers.countBadge({
                        countField: 'courses_count',
                        urlField: 'courses_url',
                        icon: 'book',
                        label: tableElement.dataset.textCourses || 'courses',
                        emptyLabel: tableElement.dataset.textNoCourses || 'No courses',
                        color: 'primary'
                    })
                },
                {
                    data: 'is_active',
                    name: 'is_active',
                    className: 'text-center',
                    render: ColumnRenderers.statusBadge({
                        field: 'is_active',
                        activeLabel: tableElement.dataset.textActive || 'Active',
                        inactiveLabel: tableElement.dataset.textInactive || 'Inactive'
                    })
                },
                {
                    data: 'id',
                    name: 'actions',
                    orderable: false,
                    searchable: false,
                    className: 'text-end pe-4',
                    render: function(data, type, row) {
                        const editLabel = tableElement.dataset.textEdit || 'Edit';
                        const coursesLabel = tableElement.dataset.textViewCourses || 'View Courses';
                        const deleteLabel = tableElement.dataset.textDelete || 'Delete';
                        const deleteConfirm = tableElement.dataset.textDeleteConfirm || 'Are you sure you want to delete this program?';
                        const editAttrs = `data-program-edit-trigger data-program-id="${row.id}" data-program-name="${escapeAttr(row.name)}" data-program-active="${row.is_active ? 1 : 0}"`;

                        const coursesBadge = row.courses_count > 0
                            ? `<span class="badge badge-light-primary ms-auto">${row.courses_count}</span>`
                            : '';

                        const deleteItem = row.delete_url
                            ? `<li>
                                    <a class="dropdown-item d-flex align-items-center py-2 text-danger" href="#"
                                       onclick="if(confirm('${escapeAttr(deleteConfirm)}')) { document.getElementById('delete-form-${row.id}').submit(); } return false;">
                                        <i class="ki-outline ki-trash fs-5 me-2"></i>
                                        ${escapeAttr(deleteLabel)}
                                    </a>
                                    <form id="delete-form-${row.id}" action="${row.delete_url}" method="POST" style="display:none;">
                                        <input type="hidden" name="_token" value="${document.querySelector('meta[name=\"csrf-token\"]')?.content || ''}">
                                        <input type="hidden" name="_method" value="DELETE">
                                    </form>
                               </li>`
                            : `<li>
                                    <span class="dropdown-item d-flex align-items-center py-2 text-muted" data-bs-toggle="tooltip" title="${escapeAttr(tableElement.dataset.textDeleteConfirm || 'Cannot delete')}">
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
                                        <a class="dropdown-item d-flex align-items-center py-2" href="${row.courses_url}">
                                            <i class="ki-outline ki-book fs-5 me-2 text-gray-500"></i>
                                            ${escapeAttr(coursesLabel)}
                                            ${coursesBadge}
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
            order: [[0, 'desc']],
            translations: translations
        });

        window.programsTable = tableInstance;
    }

    // Delegate edit clicks (works for DataTable and static buttons)
    document.addEventListener('click', function(e) {
        const editTrigger = e.target.closest('[data-program-edit-trigger]');
        if (editTrigger) {
            e.preventDefault();
            showEditModal(editTrigger);
        }
    });

    // Refresh table and counters after modal success
    document.addEventListener('ajax-form-success', function(event) {
        const modal = event.detail?.modal;
        if (!modal) return;

        if (modal.id === 'programCreateModal' || modal.id === 'programEditModal') {
            // Refresh DataTable if present
            if (tableInstance && typeof tableInstance.reload === 'function') {
                tableInstance.reload(true);
            }
            // Reset search/filter to keep current view intact
            if (tableInstance && tableInstance.table) {
                tableInstance.table.search('').draw();
            }
            updateCounters(event.detail.data);
        }
    });

    initCharCounters();
});
