"use strict";

/**
 * Assignments Table Configuration
 *
 * DataTable configuration for course assignments listing.
 * Works with both admin and instructor contexts using shared component.
 */
document.addEventListener('DOMContentLoaded', function() {
    const tableElement = document.getElementById('assignments-table');
    if (!tableElement) return;

    // Escape HTML attributes to prevent XSS
    const escapeAttr = (value) => {
        if (value === null || value === undefined) return '';
        return String(value)
            .replace(/&/g, '&amp;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#39;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;');
    };

    const tableInstance = new AdminDataTable('assignments-table', {
        ajaxUrl: tableElement.dataset.ajaxUrl,
        columns: [
            {
                data: 'title',
                name: 'title',
                render: function(data, type, row) {
                    let html = `<span class="text-gray-800 fw-bold">${escapeAttr(data)}</span>`;
                    if (row.pending_count > 0) {
                        html += `<span class="badge badge-light-warning ms-2">${row.pending_count} pending</span>`;
                    }
                    return html;
                }
            },
            {
                data: 'module_title',
                name: 'module_title',
                render: function(data) {
                    return data ? `<span class="text-muted">${escapeAttr(data)}</span>` : '<span class="text-muted">-</span>';
                }
            },
            {
                data: 'due_date',
                name: 'due_date',
                render: function(data) {
                    return data || '<span class="text-muted">No deadline</span>';
                }
            },
            {
                data: 'total_points',
                name: 'total_points',
                className: 'text-center',
                render: function(data) {
                    return `<span class="fw-bold">${data}</span>`;
                }
            },
            {
                data: 'submissions_count',
                name: 'submissions_count',
                className: 'text-center',
                render: function(data, type, row) {
                    if (data === 0) {
                        return '<span class="text-muted">0</span>';
                    }
                    return `<a href="${escapeAttr(row.submissions_url)}" class="text-primary fw-bold">${data}</a>`;
                }
            },
            {
                data: 'is_published',
                name: 'is_published',
                className: 'text-center',
                render: function(data) {
                    if (data) {
                        return '<span class="badge badge-light-success">Published</span>';
                    }
                    return '<span class="badge badge-light-secondary">Draft</span>';
                }
            },
            {
                data: 'show_url',
                name: 'actions',
                orderable: false,
                searchable: false,
                className: 'text-end',
                render: function(data, type, row) {
                    let html = `<a href="${escapeAttr(data)}" class="btn btn-sm btn-light-primary me-2">
                        <i class="ki-outline ki-eye fs-5 me-1"></i>View
                    </a>`;
                    if (row.submissions_count > 0) {
                        html += `<a href="${escapeAttr(row.submissions_url)}" class="btn btn-sm btn-light-info">
                            <i class="ki-outline ki-notepad fs-5 me-1"></i>Submissions
                        </a>`;
                    }
                    return html;
                }
            }
        ],
        filters: {
            filter: 'select[data-filter="filter"]'
        },
        order: [[0, 'asc']], // title asc
        defaultPageLength: 15,
        autoBindRefresh: true
    });

    // Bind refresh button
    const refreshBtn = document.querySelector('[data-refresh-table]');
    if (refreshBtn && tableInstance) {
        refreshBtn.addEventListener('click', function() {
            tableInstance.reload(false);
        });
    }

    // Expose instance globally for debugging
    window.assignmentsTable = tableInstance;
});
