"use strict";

/**
 * Applications Table Configuration
 *
 * Page-specific DataTable configuration for the Applications admin index.
 * Uses ColumnRenderers for consistent column formatting.
 */
document.addEventListener('DOMContentLoaded', function() {
    const tableElement = document.getElementById('applications-table');
    if (!tableElement) return;

    // Get translations from data attributes or use defaults
    const translations = {
        showing: tableElement.dataset.textShowing || 'Showing',
        to: tableElement.dataset.textTo || 'to',
        of: tableElement.dataset.textOf || 'of',
        entries: tableElement.dataset.textEntries || 'entries',
        filteredFrom: tableElement.dataset.textFilteredFrom || 'filtered from',
        total: tableElement.dataset.textTotal || 'total',
        noRecords: tableElement.dataset.textNoRecords || 'No applications found'
    };

    // Initialize the DataTable
    const table = new AdminDataTable('applications-table', {
        ajaxUrl: tableElement.dataset.ajaxUrl,
        columns: [
            {
                data: 'reference_number',
                name: 'reference_number',
                render: function(data, type, row) {
                    return `
                        <div class="d-flex flex-column ps-4">
                            <a href="${row.show_url}"
                               class="text-gray-800 text-hover-primary fs-6 fw-bold mb-1">
                                ${data}
                            </a>
                            <span class="text-gray-500 fs-7">${row.phone || 'N/A'}</span>
                        </div>
                    `;
                }
            },
            {
                data: 'full_name',
                name: 'full_name',
                render: function(data, type, row) {
                    const statusColor = row.status === 'pending' ? 'warning' :
                                      row.status === 'initial_approved' ? 'info' :
                                      row.status === 'approved' ? 'success' : 'danger';

                    return `
                        <div class="d-flex align-items-center">
                            <div class="symbol symbol-45px me-3">
                                <span class="symbol-label bg-light-${statusColor}">
                                    <i class="ki-outline ki-profile-user fs-2x text-${statusColor}"></i>
                                </span>
                            </div>
                            <div class="d-flex flex-column">
                                <a href="${row.show_url}"
                                   class="text-gray-800 text-hover-primary fs-6 fw-bold mb-1">
                                    ${data}
                                </a>
                                <span class="text-gray-500 fs-7">${row.created_at_human}</span>
                            </div>
                        </div>
                    `;
                }
            },
            {
                data: 'email',
                name: 'email',
                render: function(data) {
                    return `<span class="text-gray-700">${data}</span>`;
                }
            },
            {
                data: 'program_name',
                name: 'program_name',
                orderable: false,
                render: function(data) {
                    return `<span class="badge badge-light-primary fs-7 fw-semibold px-3 py-2">${data}</span>`;
                }
            },
            {
                data: 'created_at',
                name: 'created_at',
                className: 'text-center',
                render: function(data, type, row) {
                    return `
                        <div class="d-flex flex-column align-items-center">
                            <span class="text-gray-800 fs-7 fw-bold">${data}</span>
                            <span class="text-gray-500 fs-8">${row.created_at_human}</span>
                        </div>
                    `;
                }
            },
            {
                data: 'status',
                name: 'status',
                className: 'text-center',
                render: ColumnRenderers.statusBadge({
                    field: 'status',
                    statusMap: {
                        'pending': { label: 'Pending', color: 'warning' },
                        'initial_approved': { label: 'Initial Approved', color: 'info' },
                        'approved': { label: 'Approved', color: 'success' },
                        'rejected': { label: 'Rejected', color: 'danger' }
                    }
                })
            },
            {
                data: 'id',
                name: 'actions',
                orderable: false,
                searchable: false,
                className: 'text-end pe-4',
                render: function(data, type, row) {
                    return `
                        <a href="${row.show_url}"
                           class="btn btn-sm btn-light btn-active-light-primary">
                            <i class="ki-outline ki-eye fs-6"></i>
                            View Details
                        </a>
                    `;
                }
            }
        ],
        filters: {
            status: 'select[name="status"]',
            from: 'input[name="from"]',
            to: 'input[name="to"]'
        },
        order: [[4, 'desc']], // Order by created_at column descending
        pageLength: 25,
        translations: translations
    });

    // Expose table instance for debugging if needed
    window.applicationsTable = table;
});

