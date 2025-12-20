"use strict";

/**
 * KTQuizModals
 *
 * Handles quiz/exam-specific modal functionality including assessment type switching,
 * max attempts control, and form validation.
 *
 * @requires KTCourseModuleMain
 * @requires Bootstrap 5
 * @requires SweetAlert2
 */
var KTQuizModals = function () {

    /**
     * Initialize all quiz modal functionality
     */
    var initQuizModals = () => {
        initAssessmentTypeChange();
    };

    /**
     * Handle assessment type change in add/edit quiz modals
     * Adjusts max attempts and UI hints based on quiz vs exam selection
     */
    var initAssessmentTypeChange = () => {
        document.addEventListener('change', function(e) {
            const radio = e.target;
            if (!radio.matches('.assessment-type-radio, input[name="assessment_type"]')) return;

            const modal = radio.closest('.modal');
            if (!modal) return;

            const form = modal.querySelector('form');
            const isExam = radio.value === 'exam';

            // Toggle hint visibility
            const examHints = form.querySelectorAll('.exam-hint');
            const quizHints = form.querySelectorAll('.quiz-hint');

            examHints.forEach(el => el.classList.toggle('d-none', !isExam));
            quizHints.forEach(el => el.classList.toggle('d-none', isExam));

            // Handle max attempts field
            const moduleId = radio.dataset.moduleId;
            const quizId = radio.dataset.quizId;
            const suffix = moduleId || quizId;

            let maxAttemptsSelect = form.querySelector(`#max_attempts_${suffix}`);
            if (!maxAttemptsSelect) {
                maxAttemptsSelect = form.querySelector('select[name="max_attempts"]');
            }

            if (maxAttemptsSelect) {
                if (isExam) {
                    // Store original value before forcing to 2
                    if (!maxAttemptsSelect.dataset.originalValue) {
                        maxAttemptsSelect.dataset.originalValue = maxAttemptsSelect.value;
                    }
                    maxAttemptsSelect.value = '2';
                    maxAttemptsSelect.disabled = true;
                } else {
                    // Restore original value if available
                    if (maxAttemptsSelect.dataset.originalValue) {
                        maxAttemptsSelect.value = maxAttemptsSelect.dataset.originalValue;
                    }
                    maxAttemptsSelect.disabled = false;
                }
            }

            // Handle show_correct_answers field - remove "after_each_attempt" for exams
            const showCorrectSelect = form.querySelector('select[name="show_correct_answers"]');
            if (showCorrectSelect) {
                const afterEachOption = showCorrectSelect.querySelector('option[value="after_each_attempt"]');
                if (afterEachOption) {
                    if (isExam) {
                        // If currently selected, switch to after_all_attempts
                        if (showCorrectSelect.value === 'after_each_attempt') {
                            showCorrectSelect.value = 'after_all_attempts';
                        }
                        afterEachOption.disabled = true;
                        afterEachOption.style.display = 'none';
                    } else {
                        afterEachOption.disabled = false;
                        afterEachOption.style.display = '';
                    }
                }
            }
        });
    };

    /**
     * Validate quiz form before submission
     *
     * @param {HTMLFormElement} form
     * @returns {boolean}
     */
    var validateQuizForm = (form) => {
        const errors = [];

        // Check title
        const title = form.querySelector('[name="title"]');
        if (title && !title.value.trim()) {
            errors.push('Title is required.');
        }

        // Check passing score
        const passingScore = form.querySelector('[name="passing_score"]');
        if (passingScore) {
            const score = parseInt(passingScore.value);
            if (isNaN(score) || score < 0 || score > 100) {
                errors.push('Passing score must be between 0 and 100.');
            }
        }

        // Check time limit if provided
        const timeLimit = form.querySelector('[name="time_limit"]');
        if (timeLimit && timeLimit.value) {
            const limit = parseInt(timeLimit.value);
            if (limit < 1 || limit > 480) {
                errors.push('Time limit must be between 1 and 480 minutes.');
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
     * Reset quiz modal to default state
     *
     * @param {string} modalId
     */
    var resetModal = (modalId) => {
        const modal = document.getElementById(modalId);
        if (!modal) return;

        const form = modal.querySelector('form');
        if (form) {
            form.reset();

            // Reset assessment type to quiz
            const quizRadio = form.querySelector('input[name="assessment_type"][value="quiz"]');
            if (quizRadio) {
                quizRadio.checked = true;
                quizRadio.dispatchEvent(new Event('change', { bubbles: true }));
            }

            // Reset max attempts
            const maxAttemptsSelect = form.querySelector('select[name="max_attempts"]');
            if (maxAttemptsSelect) {
                maxAttemptsSelect.disabled = false;
                maxAttemptsSelect.value = '-1'; // Unlimited by default
                delete maxAttemptsSelect.dataset.originalValue;
            }

            // Reset hints
            const examHints = form.querySelectorAll('.exam-hint');
            const quizHints = form.querySelectorAll('.quiz-hint');
            examHints.forEach(el => el.classList.add('d-none'));
            quizHints.forEach(el => el.classList.remove('d-none'));

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
            initQuizModals();
        },

        validateForm: validateQuizForm,
        resetModal: resetModal
    };
}();

// Initialize on DOM ready
KTUtil.onDOMContentLoaded(function () {
    KTQuizModals.init();
});
