"use strict";

/**
 * Quizzes Table Configuration
 *
 * DataTable configuration for course quizzes listing.
 * Works with both admin and instructor contexts using shared component.
 */
document.addEventListener('DOMContentLoaded', function() {
    const tableElement = document.getElementById('quizzes-table');
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

    const tableInstance = new AdminDataTable('quizzes-table', {
        ajaxUrl: tableElement.dataset.ajaxUrl,
        columns: [
            {
                data: 'title',
                name: 'title',
                render: function(data, type, row) {
                    let html = `<span class="text-gray-800 fw-bold">${escapeAttr(data)}</span>`;
                    if (row.pending_grading > 0) {
                        html += `<span class="badge badge-light-warning ms-2">${row.pending_grading} pending</span>`;
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
                data: 'assessment_type',
                name: 'assessment_type',
                className: 'text-center',
                render: function(data) {
                    if (data === 'exam') {
                        return '<span class="badge badge-light-danger">Exam</span>';
                    }
                    return '<span class="badge badge-light-primary">Quiz</span>';
                }
            },
            {
                data: 'questions_count',
                name: 'questions_count',
                className: 'text-center',
                render: function(data) {
                    return `<span class="fw-bold">${data}</span>`;
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
                data: 'attempts_count',
                name: 'attempts_count',
                className: 'text-center',
                render: function(data, type, row) {
                    if (data === 0) {
                        return '<span class="text-muted">0</span>';
                    }
                    return `<a href="${escapeAttr(row.attempts_url)}" class="text-primary fw-bold">${data}</a>`;
                }
            },
            {
                data: 'published',
                name: 'published',
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
                    if (row.attempts_count > 0) {
                        html += `<a href="${escapeAttr(row.attempts_url)}" class="btn btn-sm btn-light-info">
                            <i class="ki-outline ki-notepad-edit fs-5 me-1"></i>Attempts
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
    window.quizzesTable = tableInstance;
});
