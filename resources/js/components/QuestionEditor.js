/**
 * QuestionEditor Alpine Component
 *
 * Reactive Alpine.js component for managing quiz questions.
 * Provides reactive data binding and methods for MCQ and True/False questions.
 *
 * @alpine-component question-editor
 * @author LMS Development Team
 *
 * @example
 * <div x-data="questionEditor({ type: 'mcq', answers: [] })">
 *     <!-- Question editor UI -->
 * </div>
 */

/**
 * Create QuestionEditor Alpine component
 *
 * @param {Object} config - Initial configuration
 * @param {string} config.type - Question type ('mcq' or 'true_false')
 * @param {Array} config.answers - Initial answers array
 * @param {number} config.maxAnswers - Maximum answers allowed (default: 6)
 * @param {number} config.minAnswers - Minimum answers required (default: 2)
 * @returns {Object} Alpine component object
 */
export default function questionEditor(config = {}) {
    return {
        // Reactive Data
        questionType: config.type || 'mcq',
        answers: config.answers || [
            { text: '', is_correct: false },
            { text: '', is_correct: false }
        ],
        maxAnswers: config.maxAnswers || 6,
        minAnswers: config.minAnswers || 2,
        trueFalseAnswer: null,

        // Computed Properties

        /**
         * Check if more answers can be added
         * @returns {boolean}
         */
        get canAddMore() {
            return this.answers.length < this.maxAnswers;
        },

        /**
         * Check if answers can be removed
         * @returns {boolean}
         */
        get canRemove() {
            return this.answers.length > this.minAnswers;
        },

        /**
         * Check if at least one answer is marked as correct
         * @returns {boolean}
         */
        get hasCorrectAnswer() {
            return this.answers.some(answer => answer.is_correct);
        },

        /**
         * Get count of correct answers
         * @returns {number}
         */
        get correctAnswerCount() {
            return this.answers.filter(answer => answer.is_correct).length;
        },

        /**
         * Check if MCQ section should be visible
         * @returns {boolean}
         */
        get isMcq() {
            return this.questionType === 'mcq';
        },

        /**
         * Check if True/False section should be visible
         * @returns {boolean}
         */
        get isTrueFalse() {
            return this.questionType === 'true_false';
        },

        // Methods

        /**
         * Initialize component
         * Called automatically by Alpine when component is mounted
         */
        init() {
            console.log('QuestionEditor initialized', {
                type: this.questionType,
                answerCount: this.answers.length
            });

            // Watch for changes to update validation state
            this.$watch('answers', () => {
                this.updateValidationState();
            }, { deep: true });
        },

        /**
         * Add a new answer option
         */
        addAnswer() {
            if (!this.canAddMore) {
                console.warn(`Cannot add more than ${this.maxAnswers} answers`);
                return;
            }

            this.answers.push({
                text: '',
                is_correct: false
            });

            console.log(`Answer added. Total: ${this.answers.length}`);
        },

        /**
         * Remove an answer at specified index
         * @param {number} index - Index of answer to remove
         */
        removeAnswer(index) {
            if (!this.canRemove) {
                console.warn(`Cannot have fewer than ${this.minAnswers} answers`);
                return;
            }

            if (index < 0 || index >= this.answers.length) {
                console.error('Invalid answer index:', index);
                return;
            }

            this.answers.splice(index, 1);
            console.log(`Answer removed at index ${index}. Total: ${this.answers.length}`);
        },

        /**
         * Toggle question type between MCQ and True/False
         * @param {string} type - New question type
         */
        toggleType(type) {
            if (type !== 'mcq' && type !== 'true_false') {
                console.error('Invalid question type:', type);
                return;
            }

            this.questionType = type;
            console.log('Question type changed to:', type);

            // Reset answers if switching to MCQ from True/False
            if (type === 'mcq' && this.answers.length < this.minAnswers) {
                this.answers = [
                    { text: '', is_correct: false },
                    { text: '', is_correct: false }
                ];
            }
        },

        /**
         * Mark an answer as correct (and optionally unmark others)
         * @param {number} index - Index of answer to mark as correct
         * @param {boolean} exclusive - If true, unmark all other answers
         */
        markCorrect(index, exclusive = false) {
            if (index < 0 || index >= this.answers.length) {
                console.error('Invalid answer index:', index);
                return;
            }

            if (exclusive) {
                // Unmark all others (for single-correct MCQ)
                this.answers.forEach((answer, i) => {
                    answer.is_correct = i === index;
                });
            } else {
                // Toggle the specific answer
                this.answers[index].is_correct = !this.answers[index].is_correct;
            }

            console.log(`Answer ${index} marked as correct:`, this.answers[index].is_correct);
        },

        /**
         * Set True/False answer
         * @param {boolean} answer - True or False
         */
        setTrueFalseAnswer(answer) {
            this.trueFalseAnswer = answer;
            console.log('True/False answer set to:', answer);
        },

        /**
         * Validate question before submission
         * @returns {Object} Validation result {valid: boolean, errors: Array}
         */
        validate() {
            const errors = [];

            if (this.questionType === 'mcq') {
                // Check if there are enough answers
                if (this.answers.length < this.minAnswers) {
                    errors.push(`At least ${this.minAnswers} answers are required`);
                }

                // Check if all answers have text
                const emptyAnswers = this.answers.filter(a => !a.text || a.text.trim() === '');
                if (emptyAnswers.length > 0) {
                    errors.push('All answer options must have text');
                }

                // Check if at least one answer is correct
                if (!this.hasCorrectAnswer) {
                    errors.push('At least one answer must be marked as correct');
                }
            } else if (this.questionType === 'true_false') {
                // Check if answer is selected
                if (this.trueFalseAnswer === null) {
                    errors.push('Please select the correct answer (True or False)');
                }
            }

            return {
                valid: errors.length === 0,
                errors: errors
            };
        },

        /**
         * Get form data for submission
         * @returns {Object} Form data object
         */
        getFormData() {
            if (this.questionType === 'mcq') {
                return {
                    question_type: 'mcq',
                    answers: this.answers.filter(a => a.text && a.text.trim() !== '')
                };
            } else {
                return {
                    question_type: 'true_false',
                    correct_answer: this.trueFalseAnswer
                };
            }
        },

        /**
         * Update hidden validation state field
         * @private
         */
        updateValidationState() {
            const validationField = document.getElementById('has_correct_answer');
            if (validationField) {
                validationField.value = this.hasCorrectAnswer ? '1' : '0';
            }
        },

        /**
         * Reset component to initial state
         */
        reset() {
            this.questionType = 'mcq';
            this.answers = [
                { text: '', is_correct: false },
                { text: '', is_correct: false }
            ];
            this.trueFalseAnswer = null;
            console.log('QuestionEditor reset');
        }
    };
}

// Register with Alpine if available
if (typeof window.Alpine !== 'undefined') {
    window.Alpine.data('questionEditor', questionEditor);
    console.log('QuestionEditor component registered with Alpine');
}
