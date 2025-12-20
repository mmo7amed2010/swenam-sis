"use strict";

/**
 * KTQuizQuestions
 *
 * Handles quiz question management including:
 * - Drag-and-drop reordering
 * - Question duplication
 * - Reorder mode toggle
 *
 * @requires Bootstrap 5
 * @requires SweetAlert2
 * @requires Sortable.js (optional, for smoother drag-drop)
 */
var KTQuizQuestions = function () {

    let sortableInstance = null;
    const MAX_ANSWERS = 6;
    const MIN_ANSWERS = 2;
    const reorderStates = {};

    /**
     * Initialize all functionality
     */
    var init = () => {
        initReorderToggle();
        initDuplicateButtons();
        initRemoveButtons();
        initQuestionModals();
    };

    /**
     * Initialize reorder mode toggle button
     */
    var initReorderToggle = () => {
        document.addEventListener('click', function(event) {
            const button = event.target.closest('.btn-reorder-toggle');
            if (!button) return;

            const listSelector = button.dataset.reorderList;
            if (!listSelector) return;

            const list = document.querySelector(listSelector);
            if (!list) return;

            const isActive = reorderStates[listSelector] === true;
            reorderStates[listSelector] = !isActive;

            toggleReorderState(listSelector, list, reorderStates[listSelector]);
        });
    };

    /**
     * Toggle reorder mode UI state
     */
    var toggleReorderState = (selector, list, enable) => {
        const buttons = document.querySelectorAll(`.btn-reorder-toggle[data-reorder-list="${selector}"]`);
        buttons.forEach(btn => {
            btn.classList.toggle('active', enable);
            btn.innerHTML = enable
                ? getIcon('check', 'fs-5 me-1') + 'Done'
                : getIcon('menu', 'fs-5 me-1') + 'Reorder';
        });

        list.classList.toggle('reorder-mode', enable);

        if (enable) {
            initSortable(list);
        } else {
            destroySortable();
            // Save order on exit
            persistQuestionOrder(list);
            disableNativeDrag(list);
        }
    };

    /**
     * Initialize Sortable.js or fallback to native drag-drop
     */
    var initSortable = (list) => {
        if (typeof Sortable !== 'undefined') {
            sortableInstance = new Sortable(list, {
                animation: 150,
                handle: '.drag-handle',
                draggable: '.question-card',
                ghostClass: 'sortable-ghost',
                chosenClass: 'sortable-chosen',
                dragClass: 'sortable-drag',
                onEnd: function() {
                    // Order will be saved when exiting reorder mode
                }
            });
        } else {
            // Fallback to native drag-drop
            initNativeDragDrop(list);
        }
    };

    /**
     * Destroy Sortable instance
     */
    var destroySortable = () => {
        if (sortableInstance) {
            sortableInstance.destroy();
            sortableInstance = null;
        }
    };

    /**
     * Native drag-drop fallback
     */
    var initNativeDragDrop = (list) => {
        let draggedItem = null;

        list.querySelectorAll('.question-card').forEach(item => {
            item.setAttribute('draggable', true);

            item.addEventListener('dragstart', (e) => {
                draggedItem = item;
                item.classList.add('dragging');
            });

            item.addEventListener('dragend', () => {
                item.classList.remove('dragging');
                draggedItem = null;
            });
        });

        list.addEventListener('dragover', (e) => {
            e.preventDefault();
            const afterElement = getDragAfterElement(list, e.clientY);
            if (!draggedItem) return;

            if (afterElement == null) {
                list.appendChild(draggedItem);
            } else {
                list.insertBefore(draggedItem, afterElement);
            }
        });
    };

    /**
     * Disable native drag/drop when leaving reorder mode
     */
    var disableNativeDrag = (list) => {
        list.querySelectorAll('.question-card').forEach(item => {
            item.removeAttribute('draggable');
            item.classList.remove('dragging');
        });
    };

    /**
     * Get element to insert dragged item after
     */
    var getDragAfterElement = (container, y) => {
        const draggableElements = [...container.querySelectorAll('.question-card:not(.dragging)')];

        return draggableElements.reduce((closest, child) => {
            const box = child.getBoundingClientRect();
            const offset = y - box.top - box.height / 2;

            if (offset < 0 && offset > closest.offset) {
                return { offset, element: child };
            }
            return closest;
        }, { offset: Number.NEGATIVE_INFINITY }).element;
    };

    /**
     * Persist question order to server
     */
    var persistQuestionOrder = async (list) => {
        const reorderUrl = list.dataset.reorderUrl;
        if (!reorderUrl) return;

        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        if (!csrfToken) return;

        const order = Array.from(list.querySelectorAll('.question-card'))
            .map(item => parseInt(item.dataset.questionId, 10))
            .filter(Boolean);

        try {
            showLoadingOverlay(list);

            const formData = new FormData();
            order.forEach((id, index) => formData.append(`order[${index}]`, id));

            const response = await fetch(reorderUrl, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                },
                body: formData,
            });

            const data = await response.json();

            if (data.success) {
                // Update question numbers in UI
                updateQuestionNumbers(list);
                showToastMessage(data.message || 'Questions reordered successfully', 'success');
            } else {
                throw new Error(data.message || 'Failed to reorder questions');
            }
        } catch (error) {
            console.error('Reorder error:', error);
            showToastMessage('Failed to reorder questions. Please refresh the page.', 'error');
        } finally {
            hideLoadingOverlay(list);
        }
    };

    /**
     * Update question number badges after reorder
     */
    var updateQuestionNumbers = (list) => {
        list.querySelectorAll('.question-card').forEach((card, index) => {
            const badge = card.querySelector('.rounded-circle .fw-bold');
            if (badge) {
                badge.textContent = index + 1;
            }
        });
    };

    /**
     * Initialize question modal interactions
     */
    var initQuestionModals = () => {
        document.querySelectorAll('[id^="kt_modal_add_question_"], [id^="kt_modal_edit_question_"]').forEach(setupQuestionModal);

        document.addEventListener('change', handleQuestionTypeChange);
        document.addEventListener('click', handleAddAnswerClick);
        document.addEventListener('click', handleRemoveAnswerClick);
        document.addEventListener('change', handleCorrectCheckboxChange);
        document.addEventListener('submit', handleQuestionFormSubmit, true);
        document.addEventListener('ajax-form-success', function(event) {
            const detail = event.detail || {};
            if (!isQuestionModal(detail.modal)) return;
            updateQuizStatsFromResponse(detail.data);
        });

        document.addEventListener('shown.bs.modal', function(event) {
            if (isQuestionModal(event.target)) {
                setupQuestionModal(event.target);
            }
        });

        document.addEventListener('hidden.bs.modal', function(event) {
            if (isQuestionModal(event.target)) {
                resetQuestionModal(event.target);
            }
        });
    };

    /**
     * Determine if modal is the Add Question modal
     */
    var isAddQuestionModal = (modal) => {
        return modal && modal.id && modal.id.startsWith('kt_modal_add_question_');
    };

    /**
     * Determine if modal is the Edit Question modal
     */
    var isEditQuestionModal = (modal) => {
        return modal && modal.id && modal.id.startsWith('kt_modal_edit_question_');
    };

    /**
     * Determine if modal is a Question modal (add or edit)
     */
    var isQuestionModal = (modal) => {
        return isAddQuestionModal(modal) || isEditQuestionModal(modal);
    };

    /**
     * Prepare modal for interaction
     */
    var setupQuestionModal = (modal) => {
        if (!isQuestionModal(modal)) return;

        const answersContainer = modal.querySelector('.answers-container');
        if (answersContainer) {
            reindexAnswerRows(answersContainer);
            updateRemoveAnswerVisibility(answersContainer);
        }

        const selectedType = modal.querySelector('input[name="question_type"]:checked')?.value || 'mcq';
        toggleQuestionSections(modal, selectedType);
        updateAddAnswerButtonState(modal);
        syncHasCorrectAnswer(modal);
    };

    /**
     * Reset modal back to defaults
     */
    var resetQuestionModal = (modal) => {
        if (!isQuestionModal(modal)) return;

        if (isEditQuestionModal(modal)) {
            resetEditQuestionModal(modal);
            return;
        }

        const form = modal.querySelector('form');
        if (form) {
            form.reset();
        }

        const answersContainer = modal.querySelector('.answers-container');
        if (answersContainer) {
            const rows = Array.from(answersContainer.querySelectorAll('.answer-row'));
            rows.forEach((row, index) => {
                if (index >= MIN_ANSWERS) {
                    row.remove();
                }
            });
            reindexAnswerRows(answersContainer);
            updateRemoveAnswerVisibility(answersContainer);
        }

        const hiddenField = modal.querySelector('[name="has_correct_answer"]');
        if (hiddenField) {
            hiddenField.value = '0';
        }

        modal.querySelectorAll('input[name="correct_answer"]').forEach(radio => {
            radio.checked = false;
        });

        toggleQuestionSections(modal, 'mcq');
        updateAddAnswerButtonState(modal);
    };

    /**
     * Reset edit modal to its original data
     */
    var resetEditQuestionModal = (modal) => {
        const form = modal.querySelector('form');
        const answersContainer = modal.querySelector('.answers-container');

        if (form) {
            form.reset();
        }

        const originalType = answersContainer?.dataset.originalType || 'mcq';
        if (answersContainer) {
            const answers = parseAnswersData(answersContainer.dataset.originalAnswers);
            const normalizedAnswers = (answers && answers.length > 0) ? answers : Array.from({ length: MIN_ANSWERS }, () => ({ text: '', is_correct: false }));

            answersContainer.innerHTML = '';
            normalizedAnswers.forEach((answer, index) => {
                answersContainer.appendChild(buildAnswerRow(answersContainer, index, answer));
            });

            reindexAnswerRows(answersContainer);
            updateRemoveAnswerVisibility(answersContainer);
        }

        const tfSection = modal.querySelector('.true-false-section');
        if (tfSection) {
            const originalCorrect = tfSection.dataset.originalCorrectAnswer;
            const trueRadio = tfSection.querySelector('input[value="1"]');
            const falseRadio = tfSection.querySelector('input[value="0"]');

            if (originalCorrect === '1' || originalCorrect === 1 || originalCorrect === true || originalCorrect === 'true') {
                if (trueRadio) {
                    trueRadio.checked = true;
                    trueRadio.defaultChecked = true;
                }
                if (falseRadio) {
                    falseRadio.checked = false;
                    falseRadio.defaultChecked = false;
                }
            } else if (originalCorrect === '0' || originalCorrect === 0 || originalCorrect === false || originalCorrect === 'false') {
                if (trueRadio) {
                    trueRadio.checked = false;
                    trueRadio.defaultChecked = false;
                }
                if (falseRadio) {
                    falseRadio.checked = true;
                    falseRadio.defaultChecked = true;
                }
            } else {
                if (trueRadio) trueRadio.checked = false;
                if (falseRadio) falseRadio.checked = false;
            }
        }

        const hiddenField = modal.querySelector('[name="has_correct_answer"]');
        const originalHasCorrect = answersContainer?.dataset.originalHasCorrect === '1';
        if (hiddenField) {
            hiddenField.value = originalHasCorrect ? '1' : '0';
        }

        toggleQuestionSections(modal, originalType);
        updateAddAnswerButtonState(modal);
        syncHasCorrectAnswer(modal);
    };

    /**
     * Handle question type radio change
     */
    var handleQuestionTypeChange = (event) => {
        const radio = event.target;
        if (!radio.classList || !radio.classList.contains('question-type-radio')) return;

        const modal = radio.closest('.modal');
        if (!isQuestionModal(modal)) return;

        toggleQuestionSections(modal, radio.value);
    };

    /**
     * Toggle between MCQ and True/False sections
     */
    var toggleQuestionSections = (modal, questionType) => {
        const isMcq = questionType === 'mcq';

        const mcqSection = modal.querySelector('.mcq-section');
        const tfSection = modal.querySelector('.true-false-section');
        if (mcqSection) mcqSection.classList.toggle('d-none', !isMcq);
        if (tfSection) tfSection.classList.toggle('d-none', isMcq);

        const mcqNote = modal.querySelector('.mcq-note');
        const tfNote = modal.querySelector('.tf-note');
        if (mcqNote) mcqNote.classList.toggle('d-none', !isMcq);
        if (tfNote) tfNote.classList.toggle('d-none', isMcq);

        modal.querySelectorAll('.answers-container input[type="text"]').forEach(input => {
            input.required = isMcq;
            input.disabled = !isMcq;
        });

        modal.querySelectorAll('.correct-checkbox').forEach(checkbox => {
            checkbox.disabled = !isMcq;
            if (!isMcq) {
                checkbox.checked = false;
            }
        });

        const hiddenField = modal.querySelector('[name="has_correct_answer"]');
        if (hiddenField && !isMcq) {
            hiddenField.value = '0';
        }

        modal.querySelectorAll('input[name="correct_answer"]').forEach(radio => {
            radio.disabled = isMcq;
            radio.required = !isMcq;
            if (isMcq) {
                radio.checked = false;
            }
        });

        updateAddAnswerButtonState(modal);

        const answersContainer = modal.querySelector('.answers-container');
        if (answersContainer) {
            if (isMcq && answersContainer.querySelectorAll('.answer-row').length === 0) {
                for (let i = 0; i < MIN_ANSWERS; i++) {
                    answersContainer.appendChild(buildAnswerRow(answersContainer, i));
                }
                reindexAnswerRows(answersContainer);
            }
            updateRemoveAnswerVisibility(answersContainer);
        }
    };

    /**
     * Handle Add Answer button click
     */
    var handleAddAnswerClick = (event) => {
        const button = event.target.closest('.btn-add-answer');
        if (!button) return;

        const modal = button.closest('.modal');
        if (!isQuestionModal(modal)) return;

        event.preventDefault();

        const container = modal.querySelector(button.dataset.container);
        if (!container) return;

        const max = parseInt(button.dataset.maxAnswers || container.dataset.maxAnswers || MAX_ANSWERS, 10);
        const rows = container.querySelectorAll('.answer-row');
        if (rows.length >= max) {
            showToastMessage(`Maximum of ${max} answers reached.`, 'warning');
            return;
        }

        container.appendChild(buildAnswerRow(container, rows.length));
        reindexAnswerRows(container);
        updateRemoveAnswerVisibility(container);
        updateAddAnswerButtonState(modal);
    };

    /**
     * Handle Remove Answer button click
     */
    var handleRemoveAnswerClick = (event) => {
        const button = event.target.closest('.btn-remove-answer');
        if (!button) return;

        const modal = button.closest('.modal');
        if (!isQuestionModal(modal)) return;

        const container = button.closest('.answers-container');
        if (!container) return;

        const min = parseInt(container.dataset.minAnswers || MIN_ANSWERS, 10);
        const rows = container.querySelectorAll('.answer-row');
        if (rows.length <= min) return;

        const row = button.closest('.answer-row');
        if (row) {
            row.remove();
        }

        reindexAnswerRows(container);
        updateRemoveAnswerVisibility(container);
        updateAddAnswerButtonState(modal);
        syncHasCorrectAnswer(modal);
    };

    /**
     * Track checkbox changes for MCQ answers
     */
    var handleCorrectCheckboxChange = (event) => {
        const checkbox = event.target;
        if (!checkbox.classList || !checkbox.classList.contains('correct-checkbox')) return;

        const modal = checkbox.closest('.modal');
        if (!isQuestionModal(modal)) return;

        syncHasCorrectAnswer(modal);
    };

    /**
     * Ensure form has valid data before AJAX submit
     */
    var handleQuestionFormSubmit = (event) => {
        const form = event.target;
        if (!(form instanceof HTMLFormElement)) return;
        if (!form.matches('.ajax-modal-form')) return;

        const modal = form.closest('.modal');
        if (!isQuestionModal(modal)) return;

        const questionType = form.querySelector('input[name="question_type"]:checked')?.value || 'mcq';

        if (questionType === 'mcq') {
            const hasCorrect = syncHasCorrectAnswer(modal);
            if (!hasCorrect) {
                event.preventDefault();
                event.stopPropagation();
                showToastMessage('Mark at least one answer as correct.', 'error');
                return;
            }
        }

        if (questionType === 'true_false') {
            const selectedTf = form.querySelector('input[name="correct_answer"]:checked');
            if (!selectedTf) {
                event.preventDefault();
                event.stopPropagation();
                showToastMessage('Select whether the statement is True or False.', 'error');
            }
        }
    };

    /**
     * Keep hidden has_correct_answer flag in sync
     */
    var syncHasCorrectAnswer = (modal) => {
        const hiddenField = modal.querySelector('[name="has_correct_answer"]');
        if (!hiddenField) return false;

        const hasCorrect = modal.querySelectorAll('.correct-checkbox:checked').length > 0;
        hiddenField.value = hasCorrect ? '1' : '0';

        return hasCorrect;
    };

    /**
     * Parse answers JSON from data attributes
     */
    var parseAnswersData = (value) => {
        if (!value) return [];

        try {
            const parsed = JSON.parse(value);
            return Array.isArray(parsed) ? parsed : [];
        } catch (e) {
            return [];
        }
    };

    /**
     * Build dynamic answer row
     */
    var buildAnswerRow = (container, index, answer = {}) => {
        const modalId = container.dataset.modalId || 'quiz';
        const placeholder = container.dataset.placeholder || 'Enter answer option';
        const correctLabel = container.dataset.correctLabel || 'Correct';
        const removeLabel = container.dataset.removeLabel || 'Remove';
        const answerText = answer.text || '';
        const isCorrect = answer.is_correct === true || answer.is_correct === '1' || answer.is_correct === 1;

        const row = document.createElement('div');
        row.className = 'answer-row d-flex gap-3 align-items-center';
        row.dataset.index = index;
        row.dataset.answerRow = 'true';

        const badge = document.createElement('div');
        badge.className = 'd-flex align-items-center justify-content-center w-30px h-30px rounded-circle bg-light-primary flex-shrink-0';

        const letter = document.createElement('span');
        letter.className = 'fw-bold fs-7 text-primary answer-letter';
        letter.textContent = String.fromCharCode(65 + index);
        badge.appendChild(letter);

        const inputWrapper = document.createElement('div');
        inputWrapper.className = 'flex-grow-1';

        const textInput = document.createElement('input');
        textInput.type = 'text';
        textInput.className = 'form-control form-control-solid';
        textInput.name = `answers[${index}][text]`;
        textInput.placeholder = placeholder;
        textInput.required = true;
        textInput.value = answerText;
        textInput.defaultValue = answerText;
        inputWrapper.appendChild(textInput);

        const formCheck = document.createElement('div');
        formCheck.className = 'form-check form-check-custom form-check-success form-check-solid';

        const hiddenInput = document.createElement('input');
        hiddenInput.type = 'hidden';
        hiddenInput.name = `answers[${index}][is_correct]`;
        hiddenInput.value = '0';

        const checkbox = document.createElement('input');
        checkbox.className = 'form-check-input correct-checkbox';
        checkbox.type = 'checkbox';
        checkbox.name = `answers[${index}][is_correct]`;
        checkbox.value = '1';
        checkbox.id = `correct-${modalId}-${index}`;
        checkbox.checked = isCorrect;
        checkbox.defaultChecked = isCorrect;

        const label = document.createElement('label');
        label.className = 'form-check-label text-success fs-8';
        label.setAttribute('for', checkbox.id);
        label.textContent = correctLabel;

        formCheck.append(hiddenInput, checkbox, label);

        const removeBtn = document.createElement('button');
        removeBtn.type = 'button';
        removeBtn.className = 'btn btn-sm btn-icon btn-light-danger btn-remove-answer';
        removeBtn.title = removeLabel;
        removeBtn.innerHTML = getIcon('trash', 'fs-5');

        row.append(badge, inputWrapper, formCheck, removeBtn);

        return row;
    };

    /**
     * Reindex answer rows after add/remove
     */
    var reindexAnswerRows = (container) => {
        const modalId = container.dataset.modalId || 'quiz';
        const placeholder = container.dataset.placeholder || '';
        const correctLabel = container.dataset.correctLabel || '';
        const removeLabel = container.dataset.removeLabel || '';
        const rows = Array.from(container.querySelectorAll('.answer-row'));

        rows.forEach((row, index) => {
            row.dataset.index = index;

            const letterEl = row.querySelector('.answer-letter');
            if (letterEl) {
                letterEl.textContent = String.fromCharCode(65 + index);
            }

            const textInput = row.querySelector('input[type="text"]');
            if (textInput) {
                textInput.name = `answers[${index}][text]`;
                if (placeholder) {
                    textInput.placeholder = placeholder;
                }
            }

            const hiddenInput = row.querySelector('input[type="hidden"]');
            if (hiddenInput) {
                hiddenInput.name = `answers[${index}][is_correct]`;
            }

            const checkbox = row.querySelector('.correct-checkbox');
            if (checkbox) {
                checkbox.name = `answers[${index}][is_correct]`;
                checkbox.id = `correct-${modalId}-${index}`;
            }

            const label = row.querySelector('.form-check-label');
            if (label) {
                if (checkbox) {
                    label.setAttribute('for', checkbox.id);
                }
                if (correctLabel) {
                    label.textContent = correctLabel;
                }
            }

            const removeBtn = row.querySelector('.btn-remove-answer');
            if (removeBtn && removeLabel) {
                removeBtn.title = removeLabel;
            }
        });
    };

    /**
     * Toggle remove button visibility based on number of rows
     */
    var updateRemoveAnswerVisibility = (container) => {
        if (!container) return;

        const rows = container.querySelectorAll('.answer-row');
        const min = parseInt(container.dataset.minAnswers || MIN_ANSWERS, 10);
        const canRemove = rows.length > min;

        rows.forEach(row => {
            const button = row.querySelector('.btn-remove-answer');
            if (!button) return;
            if (canRemove) {
                button.classList.remove('d-none');
                button.disabled = false;
            } else {
                button.classList.add('d-none');
                button.disabled = true;
            }
        });
    };

    /**
     * Enable/disable Add Answer button based on limits and mode
     */
    var updateAddAnswerButtonState = (modal) => {
        if (!modal) return;

        const addButton = modal.querySelector('.btn-add-answer');
        const container = modal.querySelector('.answers-container');
        if (!addButton || !container) return;

        const max = parseInt(addButton.dataset.maxAnswers || container.dataset.maxAnswers || MAX_ANSWERS, 10);
        const isMcq = modal.querySelector('input[name="question_type"]:checked')?.value === 'mcq';
        const count = container.querySelectorAll('.answer-row').length;

        addButton.disabled = !isMcq || count >= max;
    };

    /**
     * Update header stats after AJAX creation
     */
    var updateQuizStatsFromResponse = (data) => {
        if (!data) return;

        if (typeof data.questions_count !== 'undefined') {
            document.querySelectorAll('[data-quiz-questions-count]').forEach(el => {
                el.textContent = data.questions_count;
            });
        }

        if (typeof data.total_points !== 'undefined') {
            document.querySelectorAll('[data-quiz-total-points]').forEach(el => {
                el.textContent = data.total_points;
            });

            document.querySelectorAll('[data-quiz-passing-score]').forEach(wrapper => {
                const percent = parseFloat(wrapper.dataset.quizPassingScore);
                if (isNaN(percent)) return;

                const target = wrapper.querySelector('[data-quiz-passing-points]');
                if (target) {
                    target.textContent = Math.round(data.total_points * percent / 100);
                }
            });
        }
    };

    /**
     * Initialize duplicate question buttons
     */
    var initDuplicateButtons = () => {
        document.addEventListener('click', async function(e) {
            const button = e.target.closest('.btn-duplicate-question');
            if (!button) return;

            const url = button.dataset.duplicateUrl;
            if (!url) return;

            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            if (!csrfToken) return;

            // Confirm duplication
            const confirmed = await confirmAction(
                'Duplicate Question',
                'This will create a copy of the question. Continue?',
                'Duplicate'
            );

            if (!confirmed) return;

            button.disabled = true;

            try {
                const response = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                    },
                });

                const data = await response.json();

                if (data.success) {
                    showToastMessage(data.message || 'Question duplicated successfully', 'success');
                    // Reload page to show new question
                    window.location.reload();
                } else {
                    throw new Error(data.message || 'Failed to duplicate question');
                }
            } catch (error) {
                console.error('Duplicate error:', error);
                showToastMessage('Failed to duplicate question. Please try again.', 'error');
                button.disabled = false;
            }
        });
    };

    /**
     * Initialize remove item buttons (for module content)
     */
    var initRemoveButtons = () => {
        document.addEventListener('click', async function(e) {
            const button = e.target.closest('.btn-remove-item');
            if (!button) return;

            const url = button.dataset.removeUrl;
            const confirmMsg = button.dataset.confirm || 'Remove this item?';
            if (!url) return;

            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            if (!csrfToken) return;

            const confirmed = await confirmAction('Remove Item', confirmMsg, 'Remove', 'warning');
            if (!confirmed) return;

            button.disabled = true;

            try {
                const response = await fetch(url, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                    },
                });

                const data = await response.json();

                if (data.success) {
                    // Remove item from DOM
                    const item = button.closest('.content-item');
                    if (item) {
                        item.style.transition = 'opacity 0.3s, transform 0.3s';
                        item.style.opacity = '0';
                        item.style.transform = 'translateX(20px)';
                        setTimeout(() => item.remove(), 300);
                    }
                    showToastMessage(data.message || 'Item removed successfully', 'success');
                } else {
                    throw new Error(data.message || 'Failed to remove item');
                }
            } catch (error) {
                console.error('Remove error:', error);
                showToastMessage('Failed to remove item. Please try again.', 'error');
                button.disabled = false;
            }
        });
    };

    /**
     * Show loading overlay on list
     */
    var showLoadingOverlay = (element) => {
        const overlay = document.createElement('div');
        overlay.className = 'loading-overlay position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center bg-white bg-opacity-75';
        overlay.innerHTML = '<div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div>';
        overlay.style.zIndex = '10';

        element.style.position = 'relative';
        element.appendChild(overlay);
    };

    /**
     * Hide loading overlay
     */
    var hideLoadingOverlay = (element) => {
        const overlay = element.querySelector('.loading-overlay');
        if (overlay) {
            overlay.remove();
        }
    };

    /**
     * Confirm action with SweetAlert2
     */
    var confirmAction = async (title, text, confirmButtonText = 'Yes', icon = 'question') => {
        if (typeof Swal !== 'undefined') {
            const result = await Swal.fire({
                title: title,
                text: text,
                icon: icon,
                showCancelButton: true,
                confirmButtonText: confirmButtonText,
                cancelButtonText: 'Cancel',
                customClass: {
                    confirmButton: 'btn btn-primary',
                    cancelButton: 'btn btn-light'
                },
                buttonsStyling: false
            });
            return result.isConfirmed;
        }
        return confirm(text);
    };

    /**
     * Show toast message
     */
    var showToastMessage = (message, type = 'info') => {
        if (typeof window.showToast === 'function') {
            window.showToast(message, type);
        } else if (typeof Swal !== 'undefined') {
            Swal.fire({
                text: message,
                icon: type === 'error' ? 'error' : 'success',
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000
            });
        } else {
            alert(message);
        }
    };

    /**
     * Get icon HTML helper
     */
    var getIcon = (name, classes = '') => {
        if (typeof window.getIcon === 'function') {
            return window.getIcon(name, classes);
        }
        return `<i class="ki-outline ki-${name} ${classes}"></i>`;
    };

    // Public API
    return {
        init: init,
        persistQuestionOrder: persistQuestionOrder,
        updateQuestionNumbers: updateQuestionNumbers
    };
}();

// Initialize on DOM ready
document.addEventListener('DOMContentLoaded', function() {
    if (typeof KTUtil !== 'undefined') {
        KTUtil.onDOMContentLoaded(function() {
            KTQuizQuestions.init();
        });
    } else {
        KTQuizQuestions.init();
    }
});

// Make functions globally available
window.KTQuizQuestions = KTQuizQuestions;
