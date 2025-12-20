"use strict";

/**
 * Permissions Table Configuration
 *
 * Page-specific DataTable configuration for the Permissions Management index.
 * Uses ColumnRenderers for consistent column formatting.
 */
document.addEventListener('DOMContentLoaded', function() {
    const tableElement = document.getElementById('permissions-table');
    if (!tableElement) return;

    // Get translations from data attributes or use defaults
    const translations = {
        showing: tableElement.dataset.textShowing || 'Showing',
        to: tableElement.dataset.textTo || 'to',
        of: tableElement.dataset.textOf || 'of',
        entries: tableElement.dataset.textEntries || 'entries',
        filteredFrom: tableElement.dataset.textFilteredFrom || 'filtered from',
        total: tableElement.dataset.textTotal || 'total',
        noRecords: tableElement.dataset.textNoRecords || 'No permissions found'
    };

    // Initialize the DataTable
    const table = new AdminDataTable('permissions-table', {
        ajaxUrl: tableElement.dataset.ajaxUrl,
        columns: [
            {
                data: 'name',
                name: 'name',
                className: 'ps-4',
                render: function(data, type, row) {
                    // Format permission name (ucwords equivalent)
                    const formattedName = data.split(' ')
                        .map(word => word.charAt(0).toUpperCase() + word.slice(1))
                        .join(' ');

                    return `
                        <div class="d-flex flex-column">
                            <span class="text-gray-800 fw-bold fs-6">${formattedName}</span>
                            <span class="text-gray-500 fs-7">${data}</span>
                        </div>
                    `;
                }
            },

            {
                data: 'created_at',
                name: 'created_at',
                className: 'text-nowrap',
                render: function(data, type, row) {
                    if (!data) return '-';

                    // Parse the date
                    const date = new Date(data);
                    const options = {
                        year: 'numeric',
                        month: 'short',
                        day: '2-digit',
                        hour: '2-digit',
                        minute: '2-digit',
                        hour12: true
                    };
                    const formatted = date.toLocaleDateString('en-US', options);

                    return `
                        <div class="d-flex flex-column">
                            <span class="text-gray-800 fw-semibold fs-7">${formatted}</span>
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
                    if (!row.actions.can_view) {
                        return '';
                    }

                    return `
                        <button class="btn btn-icon btn-active-light-primary w-30px h-30px"
                                data-permission-id="${row.name}"
                                data-bs-toggle="modal"
                                data-bs-target="#kt_modal_update_permission"
                                title="View Permission">
                            <i class="ki-outline ki-eye fs-3"></i>
                        </button>
                    `;
                }
            }
        ],
        filters: {},
        order: [[0, 'asc']],
        translations: translations
    });

    // Expose table instance for debugging if needed
    window.permissionsTable = table;
});


