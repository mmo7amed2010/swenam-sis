"use strict";

/**
 * Submissions Table Configuration
 *
 * Page-specific DataTable configuration for Assignment Submissions.
 * Used by both admin and instructor views.
 */
document.addEventListener('DOMContentLoaded', function() {
    const tableElement = document.getElementById('submissions-table');

    if (!tableElement) {
        return;
    }

    const escapeAttr = (value) => {
        if (value === null || value === undefined) return '';
        return String(value)
            .replace(/&/g, '&amp;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#39;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;');
    };

    const translations = {
        showing: tableElement.dataset.textShowing || 'Showing',
        to: tableElement.dataset.textTo || 'to',
        of: tableElement.dataset.textOf || 'of',
        entries: tableElement.dataset.textEntries || 'entries',
        noRecords: tableElement.dataset.textNoRecords || 'No submissions found'
    };

    const textGrade = tableElement.dataset.textGrade || 'Grade';
    const textViewEdit = tableElement.dataset.textViewEdit || 'View/Edit Grade';
    const textNotGraded = tableElement.dataset.textNotGraded || 'Not Graded';

    const tableInstance = new AdminDataTable('submissions-table', {
        ajaxUrl: tableElement.dataset.ajaxUrl,
        columns: [
            {
                // Student Name Column
                data: 'student_name',
                name: 'student_name',
                className: 'ps-4',
                render: function(data, type, row) {
                    const name = escapeAttr(row.student_name || 'N/A');
                    const email = escapeAttr(row.student_email || 'N/A');

                    return `<div class="d-flex align-items-center">
                        <div class="d-flex flex-column">
                            <span class="text-gray-800 fw-bold">${name}</span>
                            <span class="text-muted fs-7">${email}</span>
                        </div>
                    </div>`;
                }
            },
            {
                // Submission Date Column
                data: 'submitted_at',
                name: 'submitted_at',
                render: function(data, type, row) {
                    if (!data) return '<span class="text-muted">N/A</span>';
                    return `<span class="text-gray-700 fw-semibold">${escapeAttr(data)}</span>`;
                }
            },
            {
                // Status Column
                data: 'status',
                name: 'status',
                className: 'text-center',
                render: function(data, type, row) {
                    const statusColors = {
                        'submitted': 'primary',
                        'graded': 'success'
                    };
                    const color = statusColors[data] || 'secondary';
                    const label = data ? data.charAt(0).toUpperCase() + data.slice(1) : 'Unknown';
                    return `<span class="badge badge-light-${color}">${escapeAttr(label)}</span>`;
                }
            },
            {
                // Grade Column
                data: 'grade_points',
                name: 'published_grade_points',
                className: 'text-center',
                render: function(data, type, row) {
                    if (row.has_grade && row.grade_points !== null) {
                        const points = parseFloat(row.grade_points).toFixed(1);
                        const max = parseFloat(row.grade_max).toFixed(1);
                        const percentage = parseFloat(row.grade_percentage).toFixed(1);
                        return `<div>
                            <span class="fw-bold text-primary">${points} / ${max}</span>
                            <div class="text-muted fs-7">${percentage}%</div>
                        </div>`;
                    }
                    return `<span class="text-muted">${escapeAttr(textNotGraded)}</span>`;
                }
            },
            {
                // Actions Column
                data: 'grade_url',
                name: 'actions',
                orderable: false,
                searchable: false,
                className: 'text-end pe-4',
                render: function(data, type, row) {
                    const buttonText = row.has_grade ? textViewEdit : textGrade;
                    return `<a href="${escapeAttr(row.grade_url)}" class="btn btn-sm btn-primary">
                        ${escapeAttr(buttonText)}
                    </a>`;
                }
            }
        ],
        filters: {
            filter: 'select[name="filter"]'
        },
        order: [[1, 'desc']], // Default sort by submitted_at descending
        translations: translations
    });

    // Export for potential external use
    window.submissionsTable = tableInstance;
});
