"use strict";

/**
 * Intakes Table Configuration
 *
 * Page-specific DataTable configuration for the intakes management page.
 * Uses ColumnRenderers for consistent column formatting.
 */
document.addEventListener('DOMContentLoaded', function() {
    const tableElement = document.getElementById('intakes-table');
    if (!tableElement) return;

    // Get translations from data attributes or use defaults
    const translations = {
        showing: tableElement.dataset.textShowing || 'Showing',
        to: tableElement.dataset.textTo || 'to',
        of: tableElement.dataset.textOf || 'of',
        entries: tableElement.dataset.textEntries || 'entries',
        filteredFrom: tableElement.dataset.textFilteredFrom || 'filtered from',
        total: tableElement.dataset.textTotal || 'total',
        noRecords: tableElement.dataset.textNoRecords || 'No intakes found'
    };

    // Initialize the DataTable
    const table = new AdminDataTable('intakes-table', {
        ajaxUrl: tableElement.dataset.ajaxUrl,
        columns: [
            {
                data: 'name',
                name: 'name',
                render: function(data, type, row) {
                    return `
                        <div class="d-flex align-items-center">
                            <div class="symbol symbol-40px me-3">
                                <span class="symbol-label bg-light-primary">
                                    <i class="ki-outline ki-calendar fs-4 text-primary"></i>
                                </span>
                            </div>
                            <div class="d-flex flex-column">
                                <span class="text-gray-900 fw-bold">${data}</span>
                                <span class="text-gray-500 fs-8">${row.slug}</span>
                            </div>
                        </div>
                    `;
                }
            },
            {
                data: 'is_active',
                name: 'is_active',
                className: 'text-center',
                render: ColumnRenderers.statusBadge({
                    field: 'is_active',
                    statusMap: {
                        true: { label: tableElement.dataset.textActive || 'Active', color: 'success' },
                        false: { label: tableElement.dataset.textInactive || 'Inactive', color: 'secondary' }
                    }
                })
            },
            {
                data: 'sort_order',
                name: 'sort_order',
                className: 'text-center',
                render: function(data, type, row) {
                    return `<span class="badge badge-light-info fs-7 fw-semibold">${data || 0}</span>`;
                }
            },
            {
                data: 'applications_count',
                name: 'applications_count',
                orderable: false,
                className: 'text-center',
                render: function(data, type, row) {
                    const applicationsText = tableElement.dataset.textApplications || 'applications';
                    return `
                        <div class="d-flex flex-column align-items-center">
                            <span class="badge badge-light-info fs-6 fw-bold py-2 px-4 mb-1">
                                ${data || 0}
                            </span>
                            <span class="text-gray-500 fs-8">${applicationsText}</span>
                        </div>
                    `;
                }
            },
            {
                data: 'created_at',
                name: 'created_at',
                className: 'text-center',
                render: function(data, type, row) {
                    return `<span class="text-gray-600 fs-7">${data}</span>`;
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
                    const editLabel = tableElement.dataset.textEdit || 'Edit';
                    const deleteLabel = tableElement.dataset.textDelete || 'Delete';
                    const deleteConfirm = tableElement.dataset.textDeleteConfirm || 'Are you sure you want to delete this intake?';

                    let deleteItem = '';
                    if (row.delete_url) {
                        deleteItem = `<li>
                            <a class="dropdown-item d-flex align-items-center py-2 text-danger" href="#"
                               onclick="if(confirm('${deleteConfirm.replace(/'/g, "\\'")}')) { document.getElementById('delete-form-${row.id}').submit(); } return false;">
                                <i class="ki-outline ki-trash fs-5 me-2"></i>
                                ${deleteLabel}
                            </a>
                            <form id="delete-form-${row.id}" action="${row.delete_url}" method="POST" style="display:none;">
                                <input type="hidden" name="_token" value="${csrfToken}">
                                <input type="hidden" name="_method" value="DELETE">
                            </form>
                        </li>`;
                    } else {
                        deleteItem = `<li>
                            <span class="dropdown-item d-flex align-items-center py-2 text-muted"
                                  data-bs-toggle="tooltip"
                                  title="Cannot delete - has applications">
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
                                <a class="dropdown-item d-flex align-items-center py-2" href="#"
                                   data-intake-edit-trigger
                                   data-intake-id="${row.id}">
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
        order: [[4, 'desc']], // Order by created_at descending (newest first)
        translations: translations
    });

    // Expose table instance for debugging if needed
    window.intakesTable = table;

    // Refresh table when AJAX form succeeds (create/edit intake)
    document.addEventListener('ajax-form-success', function(e) {
        // Check if the event is from an intake modal
        const modal = e.detail.modal;
        if (modal && (modal.id === 'intakeCreateModal' || modal.id === 'intakeEditModal')) {
            table.reload();
        }
    });

    // Handle intake edit trigger
    $(document).on('click', '[data-intake-edit-trigger]', function(e) {
        e.preventDefault();
        const intakeId = $(this).data('intake-id');
        const modal = $('#intakeEditModal');
        const form = modal.find('form');

        // Get the update URL template and replace placeholder
        const updateUrlTemplate = modal.find('[data-update-url-template]').data('update-url-template');
        if (updateUrlTemplate) {
            form.attr('action', updateUrlTemplate.replace('__ID__', intakeId));
        }

        // Reset form and show loading state
        form.find('.alert').addClass('d-none');

        // Fetch intake data
        $.ajax({
            url: `/admin/intakes/${intakeId}/data`,
            method: 'GET',
            success: function(data) {
                // Populate form fields
                form.find('[name="name"]').val(data.name);
                form.find('[name="sort_order"]').val(data.sort_order || 0);
                form.find('[name="description"]').val(data.description || '');
                form.find('[name="is_active"]').prop('checked', data.is_active);

                // Show the modal
                modal.modal('show');
            },
            error: function(xhr) {
                const message = xhr.responseJSON?.message || 'Failed to load intake data';
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: message
                });
            }
        });
    });
});
