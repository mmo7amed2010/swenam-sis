"use strict";

/**
 * Students Table Configuration
 *
 * DataTable configuration for course students listing with progress.
 * Works with both admin and instructor contexts using shared component.
 */
document.addEventListener('DOMContentLoaded', function() {
    const tableElement = document.getElementById('students-table');
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

    const tableInstance = new AdminDataTable('students-table', {
        ajaxUrl: tableElement.dataset.ajaxUrl,
        columns: [
            {
                data: 'name',
                name: 'name',
                render: function(data, type, row) {
                    return `<div class="d-flex align-items-center">
                        <div class="symbol symbol-circle symbol-40px me-3">
                            <span class="symbol-label bg-light-primary text-primary fs-6 fw-bold">
                                ${escapeAttr(row.initial)}
                            </span>
                        </div>
                        <div>
                            <span class="text-gray-800 fw-bold">${escapeAttr(data)}</span>
                            <div class="text-muted fs-7">${escapeAttr(row.email)}</div>
                        </div>
                    </div>`;
                }
            },
            {
                data: 'content_progress',
                name: 'content_progress',
                orderable: false,
                render: function(data, type, row) {
                    const progress = parseInt(data) || 0;
                    let progressClass = 'bg-danger';
                    if (progress >= 75) progressClass = 'bg-success';
                    else if (progress >= 50) progressClass = 'bg-warning';
                    else if (progress >= 25) progressClass = 'bg-info';

                    return `<div class="d-flex flex-column">
                        <div class="d-flex align-items-center mb-1">
                            <span class="text-gray-800 fw-bold me-2">${progress}%</span>
                            <span class="text-muted fs-7">${row.completed_items}/${row.total_items} items</span>
                        </div>
                        <div class="progress h-6px w-150px">
                            <div class="progress-bar ${progressClass}" role="progressbar"
                                 style="width: ${progress}%" aria-valuenow="${progress}"
                                 aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>`;
                }
            },
            {
                data: 'assignments_submitted',
                name: 'assignments_submitted',
                className: 'text-center',
                orderable: false,
                render: function(data, type, row) {
                    const submitted = parseInt(data) || 0;
                    const total = parseInt(row.total_assignments) || 0;
                    const percentage = total > 0 ? Math.round((submitted / total) * 100) : 0;
                    const colorClass = percentage >= 100 ? 'text-success' : (percentage >= 50 ? 'text-warning' : 'text-muted');

                    return `<span class="fw-bold ${colorClass}">${submitted}/${total}</span>`;
                }
            },
            {
                data: 'quizzes_completed',
                name: 'quizzes_completed',
                className: 'text-center',
                orderable: false,
                render: function(data, type, row) {
                    const completed = parseInt(data) || 0;
                    const total = parseInt(row.total_quizzes) || 0;
                    const percentage = total > 0 ? Math.round((completed / total) * 100) : 0;
                    const colorClass = percentage >= 100 ? 'text-success' : (percentage >= 50 ? 'text-warning' : 'text-muted');

                    return `<span class="fw-bold ${colorClass}">${completed}/${total}</span>`;
                }
            },
            {
                data: 'show_url',
                name: 'actions',
                orderable: false,
                searchable: false,
                className: 'text-end',
                render: function(data) {
                    return `<a href="${escapeAttr(data)}" class="btn btn-sm btn-light-primary">
                        <i class="ki-outline ki-eye fs-5 me-1"></i>View Details
                    </a>`;
                }
            }
        ],
        filters: {},
        order: [[0, 'asc']], // name asc
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
    window.studentsTable = tableInstance;
});
