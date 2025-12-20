/**
 * Question Management Module
 *
 * Provides functions for managing quiz questions (MCQ and True/False types).
 * Handles answer options, question type toggling, and validation.
 *
 * @module forms/question-management
 * @author LMS Development Team
 */

/**
 * Toggle between MCQ and True/False question sections
 *
 * @param {string} type - Question type ('mcq' or 'true_false')
 * @returns {void}
 *
 * @example
 * toggleQuestionType('mcq');  // Show MCQ section, hide True/False
 * toggleQuestionType('true_false');  // Show True/False, hide MCQ
 */
export function toggleQuestionType(type) {
    const mcqSection = document.getElementById('mcq-section');
    const trueFalseSection = document.getElementById('true-false-section');

    if (!mcqSection || !trueFalseSection) {
        console.warn('Question type sections not found');
        return;
    }

    if (type === 'mcq') {
        mcqSection.style.display = 'block';
        trueFalseSection.style.display = 'none';
        
        // Enable required validation for MCQ fields
        const mcqInputs = mcqSection.querySelectorAll('input[type="text"]');
        mcqInputs.forEach(input => input.setAttribute('required', 'required'));
        
        // Disable required validation for True/False fields
        const tfInputs = trueFalseSection.querySelectorAll('input[type="radio"]');
        tfInputs.forEach(input => input.removeAttribute('required'));
    } else {
        mcqSection.style.display = 'none';
        trueFalseSection.style.display = 'block';
        
        // Disable required validation for MCQ fields
        const mcqInputs = mcqSection.querySelectorAll('input[type="text"]');
        mcqInputs.forEach(input => input.removeAttribute('required'));
        
        // Enable required validation for True/False fields (at least one radio should be required)
        const tfInputs = trueFalseSection.querySelectorAll('input[type="radio"]');
        if (tfInputs.length > 0) {
            tfInputs[0].setAttribute('required', 'required');
        }
    }
}

/**
 * Add a new answer option to MCQ question
 *
 * @param {number} maxAnswers - Maximum number of answers allowed (default: 6)
 * @param {number} currentCount - Current answer count (optional, auto-detected)
 * @returns {boolean} Success status
 *
 * @example
 * addAnswer(6);  // Add answer with max 6 allowed
 */
export function addAnswer(maxAnswers = 6, currentCount = null) {
    const container = document.getElementById('answers-container');
    if (!container) {
        console.error('Answers container not found');
        return false;
    }

    // Auto-detect current count if not provided
    if (currentCount === null) {
        currentCount = container.querySelectorAll('.answer-row').length;
    }

    if (currentCount >= maxAnswers) {
        console.warn(`Maximum number of answers (${maxAnswers}) reached`);
        return false;
    }

    const index = currentCount;
    const row = document.createElement('div');
    row.className = 'd-flex gap-3 align-items-start answer-row';
    row.setAttribute('data-index', index);

    // Create row HTML
    row.innerHTML = `
        <div class="flex-grow-1">
            <input type="text"
                   class="form-control"
                   name="answers[${index}][text]"
                   placeholder="Option ${index + 1}"
                   required>
        </div>
        <div class="form-check form-check-custom form-check-solid">
            <input type="hidden" name="answers[${index}][is_correct]" value="0">
            <input class="form-check-input"
                   type="checkbox"
                   name="answers[${index}][is_correct]"
                   value="1"
                   id="correct-${index}">
            <label class="form-check-label" for="correct-${index}">
                Correct
            </label>
        </div>
        <button type="button"
                class="btn btn-sm btn-light-danger"
                data-remove-index="${index}">
            <i class="bi bi-trash fs-2"></i>
        </button>
    `;

    container.appendChild(row);

    // Attach event listener for correct answer checkbox
    const checkbox = row.querySelector('input[type="checkbox"]');
    if (checkbox) {
        checkbox.addEventListener('change', checkCorrectAnswer);
    }

    // Attach event listener for remove button
    const removeBtn = row.querySelector('.btn-light-danger');
    if (removeBtn) {
        removeBtn.addEventListener('click', function() {
            const index = parseInt(this.getAttribute('data-remove-index'));
            removeAnswer(index, 2, maxAnswers);
        });
    }

    updateUI(currentCount + 1, 2, maxAnswers);
    return true;
}

/**
 * Remove an answer option from MCQ question
 *
 * @param {number} index - Index of answer to remove
 * @param {number} minAnswers - Minimum number of answers required (default: 2)
 * @param {number} maxAnswers - Maximum number of answers allowed (default: 6)
 * @returns {boolean} Success status
 *
 * @example
 * removeAnswer(3, 2, 6);  // Remove answer at index 3, min 2, max 6
 */
export function removeAnswer(index, minAnswers = 2, maxAnswers = 6) {
    const container = document.getElementById('answers-container');
    if (!container) {
        console.error('Answers container not found');
        return false;
    }

    const currentCount = container.querySelectorAll('.answer-row').length;

    if (currentCount <= minAnswers) {
        console.warn(`Cannot remove answer: minimum ${minAnswers} answers required`);
        return false;
    }

    const row = document.querySelector(`.answer-row[data-index="${index}"]`);
    if (row) {
        row.remove();
        reindexAnswers();
        checkCorrectAnswer();
        updateUI(currentCount - 1, minAnswers, maxAnswers);
        return true;
    }

    return false;
}

/**
 * Reindex all answers after addition or removal
 *
 * Updates name attributes, IDs, and labels to maintain sequential indices.
 *
 * @returns {void}
 *
 * @example
 * reindexAnswers();  // Reindex all answer rows
 */
export function reindexAnswers() {
    const rows = document.querySelectorAll('.answer-row');

    rows.forEach((row, newIndex) => {
        row.setAttribute('data-index', newIndex);

        const textInput = row.querySelector('input[type="text"]');
        const hiddenInput = row.querySelector('input[type="hidden"]');
        const checkbox = row.querySelector('input[type="checkbox"]');
        const label = row.querySelector('label');
        const removeBtn = row.querySelector('.btn-light-danger');

        if (textInput) {
            textInput.name = `answers[${newIndex}][text]`;
            textInput.placeholder = `Option ${newIndex + 1}`;
        }
        if (hiddenInput) {
            hiddenInput.name = `answers[${newIndex}][is_correct]`;
        }
        if (checkbox) {
            checkbox.name = `answers[${newIndex}][is_correct]`;
            checkbox.id = `correct-${newIndex}`;
        }
        if (label) {
            label.setAttribute('for', `correct-${newIndex}`);
        }
        if (removeBtn) {
            removeBtn.setAttribute('data-remove-index', newIndex);
        }
    });
}

/**
 * Check if at least one answer is marked as correct
 *
 * Updates hidden field 'has_correct_answer' with validation result.
 *
 * @returns {boolean} True if at least one answer is correct
 *
 * @example
 * const hasCorrect = checkCorrectAnswer();
 * if (!hasCorrect) alert('Mark at least one answer as correct');
 */
export function checkCorrectAnswer() {
    const checkboxes = document.querySelectorAll('#mcq-section input[type="checkbox"][name*="[is_correct]"]');
    const hasCorrect = Array.from(checkboxes).some(cb => cb.checked);

    const hiddenField = document.getElementById('has_correct_answer');
    if (hiddenField) {
        hiddenField.value = hasCorrect ? '1' : '0';
    }

    return hasCorrect;
}

/**
 * Update UI elements based on current answer count
 *
 * Controls visibility of add button and remove buttons based on min/max limits.
 *
 * @param {number} currentCount - Current number of answers
 * @param {number} minAnswers - Minimum answers required
 * @param {number} maxAnswers - Maximum answers allowed
 * @returns {void}
 * @private
 */
function updateUI(currentCount, minAnswers, maxAnswers) {
    // Update add button visibility
    const addBtn = document.getElementById('add-answer-btn');
    if (addBtn) {
        addBtn.style.display = currentCount >= maxAnswers ? 'none' : 'block';
    }

    // Update remove button visibility
    const removeButtons = document.querySelectorAll('.answer-row .btn-light-danger');
    removeButtons.forEach(btn => {
        btn.style.display = currentCount <= minAnswers ? 'none' : 'block';
    });
}

/**
 * Initialize question management module
 *
 * Sets up event listeners and initializes UI state.
 * Call this on DOMContentLoaded or when dynamically loading questions.
 *
 * @param {Object} options - Configuration options
 * @param {number} options.initialCount - Initial answer count (default: 2)
 * @param {number} options.minAnswers - Minimum answers (default: 2)
 * @param {number} options.maxAnswers - Maximum answers (default: 6)
 * @returns {void}
 *
 * @example
 * initQuestionManagement({ initialCount: 4, minAnswers: 2, maxAnswers: 6 });
 */
export function initQuestionManagement(options = {}) {
    const {
        initialCount = 2,
        minAnswers = 2,
        maxAnswers = 6
    } = options;

    // Check initial correct answer state
    checkCorrectAnswer();

    // Update initial UI state
    updateUI(initialCount, minAnswers, maxAnswers);

    // Attach event listeners to existing checkboxes
    const checkboxes = document.querySelectorAll('#mcq-section input[type="checkbox"][name*="[is_correct]"]');
    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', checkCorrectAnswer);
    });

    // Attach event listeners to existing remove buttons
    const removeButtons = document.querySelectorAll('.answer-row .btn-light-danger');
    removeButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            const index = parseInt(this.getAttribute('data-remove-index'));
            removeAnswer(index, minAnswers, maxAnswers);
        });
    });

    console.log('Question management initialized');
}

// Export default initialization function
export default initQuestionManagement;

// Also expose functions globally for backward compatibility with onclick handlers
if (typeof window !== 'undefined') {
    window.QuestionManagement = {
        toggleQuestionType,
        addAnswer,
        removeAnswer,
        checkCorrectAnswer,
        reindexAnswers,
        initQuestionManagement
    };
}