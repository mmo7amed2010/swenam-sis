"use strict";

/**
 * KTAssignmentModals
 *
 * Simplified assignment modal functionality for self-paced online courses.
 * Handles submission type switching for file upload settings and basic form validation.
 * Removed complex date, late policy, and file type restrictions.
 *
 * @requires KTCourseModuleMain
 * @requires Bootstrap 5
 * @requires SweetAlert2
 */
var KTAssignmentModals = function () {

    /**
     * Initialize all assignment modal functionality
     */
    var initAssignmentModals = () => {
        initSubmissionTypeChange();
    };

    /**
     * Handle submission type change in add/edit assignment modals
     * Shows/hides simplified file upload settings based on selection
     */
    var initSubmissionTypeChange = () => {
        document.addEventListener('change', function(e) {
            const select = e.target;
            if (!select.matches('select[name="submission_type"], .submission-type-select')) return;

            const modal = select.closest('.modal');
            if (!modal) return;

            const form = modal.querySelector('form');
            const selectedType = select.value;

            // Find file settings section
            const fileSettings = form.querySelector('.file-upload-settings');

            if (fileSettings) {
                if (selectedType === 'file_upload' || selectedType === 'multiple') {
                    fileSettings.style.display = 'block';
                } else {
                    fileSettings.style.display = 'none';
                }
            }
        });
    };

    /**
     * Validate assignment form before submission (simplified)
     *
     * @param {HTMLFormElement} form
     * @returns {boolean}
     */
    var validateAssignmentForm = (form) => {
        const errors = [];

        // Check title
        const title = form.querySelector('[name="title"]');
        if (title && !title.value.trim()) {
            errors.push('Title is required.');
        }

        // Check total points
        const totalPoints = form.querySelector('[name="total_points"]');
        if (totalPoints && (!totalPoints.value || parseInt(totalPoints.value) < 1)) {
            errors.push('Total points must be at least 1.');
        }

        // Check passing score is valid if provided
        const passingScore = form.querySelector('[name="passing_score"]');
        if (passingScore && passingScore.value) {
            const score = parseInt(passingScore.value);
            if (score < 0 || score > 100) {
                errors.push('Passing score must be between 0 and 100.');
            }
        }

        if (errors.length > 0) {
            Swal.fire({
                title: 'Validation Error',
                html: '<ul class="text-start"><li>' + errors.join('</li><li>') + '</li></ul>',
                icon: 'error',
                buttonsStyling: false,
                confirmButtonText: 'Ok',
                customClass: {
                    confirmButton: 'btn btn-primary'
                }
            });
            return false;
        }

        return true;
    };

    /**
     * Reset assignment modal to default state (simplified)
     *
     * @param {string} modalId
     */
    var resetModal = (modalId) => {
        const modal = document.getElementById(modalId);
        if (!modal) return;

        const form = modal.querySelector('form');
        if (form) {
            form.reset();

            // Reset file settings visibility (default to visible)
            const fileSettings = form.querySelector('.file-upload-settings');
            if (fileSettings) {
                fileSettings.style.display = 'block';
            }

            // Clear validation errors
            const errorContainer = form.querySelector('.form-errors');
            if (errorContainer) {
                errorContainer.classList.add('d-none');
                const errorList = errorContainer.querySelector('.error-list');
                if (errorList) {
                    errorList.innerHTML = '';
                }
            }

            // Clear any field-level errors
            if (typeof KTCourseModuleMain !== 'undefined') {
                KTCourseModuleMain.clearValidationErrors(form);
            }
        }
    };

    // Public API
    return {
        init: function () {
            initAssignmentModals();
        },

        validateForm: validateAssignmentForm,
        resetModal: resetModal
    };
}();

// Initialize on DOM ready
KTUtil.onDOMContentLoaded(function () {
    KTAssignmentModals.init();
});
