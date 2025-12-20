"use strict";

/**
 * Quiz Attempts Table Configuration
 *
 * DataTable configuration for quiz attempts listing.
 * Works with both admin and instructor contexts using shared component.
 */
document.addEventListener('DOMContentLoaded', function() {
    const tableElement = document.getElementById('attempts-table');
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

    const tableInstance = new AdminDataTable('attempts-table', {
        ajaxUrl: tableElement.dataset.ajaxUrl,
        columns: [
            {
                data: 'student_name',
                name: 'student',
                orderable: false,
                render: function(data, type, row) {
                    return `<div class="d-flex align-items-center">
                        <div class="symbol symbol-circle symbol-40px me-3">
                            <span class="symbol-label bg-light-primary text-primary fs-6 fw-bold">
                                ${escapeAttr(row.student_initial)}
                            </span>
                        </div>
                        <div>
                            <span class="text-gray-800 fw-bold">${escapeAttr(data)}</span>
                            <div class="text-muted fs-7">${escapeAttr(row.student_email)}</div>
                        </div>
                    </div>`;
                }
            },
            {
                data: 'start_time',
                name: 'start_time',
                render: function(data) {
                    return data || '-';
                }
            },
            {
                data: 'end_time',
                name: 'end_time',
                render: function(data) {
                    return data || '-';
                }
            },
            {
                data: 'status',
                name: 'status',
                className: 'text-center',
                render: function(data) {
                    const badges = {
                        'graded': 'badge-light-success',
                        'completed': 'badge-light-warning',
                        'submitted': 'badge-light-warning',
                        'in_progress': 'badge-light-info'
                    };
                    const labels = {
                        'graded': 'Graded',
                        'completed': 'Pending',
                        'submitted': 'Pending',
                        'in_progress': 'In Progress'
                    };
                    const badgeClass = badges[data] || 'badge-light-secondary';
                    const label = labels[data] || data;
                    return `<span class="badge ${badgeClass}">${escapeAttr(label)}</span>`;
                }
            },
            {
                data: 'score',
                name: 'score',
                className: 'text-center',
                render: function(data, type, row) {
                    if (data === null) {
                        return '<span class="text-muted">-</span>';
                    }
                    const totalPoints = row.total_points || 100;
                    const passingScore = row.passing_score || 60;
                    const percentage = totalPoints > 0 ? (data / totalPoints) * 100 : 0;
                    const passed = percentage >= passingScore;
                    const colorClass = passed ? 'text-success' : 'text-danger';

                    return `<span class="fw-bold ${colorClass}">
                        ${parseFloat(data).toFixed(1)}/${totalPoints}
                    </span>
                    <div class="text-muted fs-8">${percentage.toFixed(0)}%</div>`;
                }
            },
            {
                data: 'show_url',
                name: 'actions',
                orderable: false,
                searchable: false,
                className: 'text-end',
                render: function(data, type, row) {
                    if (row.status === 'in_progress') {
                        return '<span class="text-muted fs-7">In progress...</span>';
                    }
                    return `<a href="${escapeAttr(data)}" class="btn btn-sm btn-light-primary">
                        <i class="ki-outline ki-eye fs-5 me-1"></i>View Details
                    </a>`;
                }
            }
        ],
        filters: {
            filter: 'select[data-filter="filter"]'
        },
        order: [[2, 'desc']], // end_time desc
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
    window.attemptsTable = tableInstance;
});
