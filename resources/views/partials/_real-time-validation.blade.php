{{-- Real-Time Form Validation Component --}}

@push('scripts')
<script>
/**
 * Real-Time Form Validation Module
 * Provides instant feedback as users fill out forms
 */
(function() {
    'use strict';

    // Validation rules
    const validationRules = {
        required: (value) => value.trim().length > 0,
        email: (value) => /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value),
        min: (value, min) => value.length >= parseInt(min),
        max: (value, max) => value.length <= parseInt(max),
        minLength: (value, length) => value.trim().length >= parseInt(length),
        maxLength: (value, length) => value.trim().length <= parseInt(length),
        numeric: (value) => /^\d+$/.test(value),
        alpha: (value) => /^[a-zA-Z]+$/.test(value),
        alphaNum: (value) => /^[a-zA-Z0-9]+$/.test(value),
        url: (value) => {
            try {
                new URL(value);
                return true;
            } catch {
                return false;
            }
        },
        pattern: (value, pattern) => new RegExp(pattern).test(value),
        match: (value, matchValue) => value === matchValue
    };

    // Error messages
    const errorMessages = {
        required: 'This field is required',
        email: 'Please enter a valid email address',
        min: 'Minimum value is {min}',
        max: 'Maximum value is {max}',
        minLength: 'Minimum length is {length} characters',
        maxLength: 'Maximum length is {length} characters',
        numeric: 'Please enter only numbers',
        alpha: 'Please enter only letters',
        alphaNum: 'Please enter only letters and numbers',
        url: 'Please enter a valid URL',
        pattern: 'Invalid format',
        match: 'Fields do not match'
    };

    /**
     * Show validation error on a field
     */
    function showFieldError(field, message) {
        field.classList.remove('is-valid');
        field.classList.add('is-invalid');
        field.setAttribute('aria-invalid', 'true');

        // Find or create feedback element
        let feedback = field.parentElement.querySelector('.invalid-feedback');
        if (!feedback) {
            feedback = document.createElement('div');
            feedback.className = 'invalid-feedback';
            feedback.setAttribute('role', 'alert');
            field.parentElement.appendChild(feedback);
        }

        feedback.textContent = message;
        feedback.style.display = 'block';
    }

    /**
     * Show validation success on a field
     */
    function showFieldSuccess(field) {
        field.classList.remove('is-invalid');
        field.classList.add('is-valid');
        field.setAttribute('aria-invalid', 'false');

        const feedback = field.parentElement.querySelector('.invalid-feedback');
        if (feedback) {
            feedback.style.display = 'none';
        }
    }

    /**
     * Clear validation state
     */
    function clearFieldValidation(field) {
        field.classList.remove('is-invalid', 'is-valid');
        field.removeAttribute('aria-invalid');

        const feedback = field.parentElement.querySelector('.invalid-feedback');
        if (feedback) {
            feedback.style.display = 'none';
        }
    }

    /**
     * Validate a single field
     */
    function validateField(field) {
        const value = field.value;
        const rules = field.dataset.validate ? field.dataset.validate.split('|') : [];

        // Check if field is required
        if (field.hasAttribute('required') || rules.includes('required')) {
            if (!validationRules.required(value)) {
                showFieldError(field, errorMessages.required);
                return false;
            }
        }

        // If field is empty and not required, skip other validations
        if (value.trim() === '' && !field.hasAttribute('required')) {
            clearFieldValidation(field);
            return true;
        }

        // Apply other validation rules
        for (let rule of rules) {
            if (rule === 'required') continue; // Already checked

            const [ruleName, ruleParam] = rule.split(':');

            if (validationRules[ruleName]) {
                if (!validationRules[ruleName](value, ruleParam)) {
                    let message = errorMessages[ruleName];
                    if (ruleParam) {
                        message = message.replace(`{${ruleName}}`, ruleParam);
                        message = message.replace('{length}', ruleParam);
                        message = message.replace('{min}', ruleParam);
                        message = message.replace('{max}', ruleParam);
                    }
                    showFieldError(field, message);
                    return false;
                }
            }
        }

        // Check for custom validation
        if (field.dataset.validateCustom) {
            const customValidator = window[field.dataset.validateCustom];
            if (typeof customValidator === 'function') {
                const result = customValidator(value, field);
                if (result !== true) {
                    showFieldError(field, result);
                    return false;
                }
            }
        }

        showFieldSuccess(field);
        return true;
    }

    /**
     * Validate matching fields (e.g., password confirmation)
     */
    function validateMatch(field) {
        const matchFieldId = field.dataset.match;
        if (!matchFieldId) return true;

        const matchField = document.getElementById(matchFieldId);
        if (!matchField) return true;

        if (field.value !== matchField.value) {
            showFieldError(field, 'Fields do not match');
            return false;
        }

        showFieldSuccess(field);
        return true;
    }

    /**
     * Initialize real-time validation on all forms
     */
    function initRealTimeValidation() {
        // Find all forms with real-time validation
        document.querySelectorAll('form[data-realtime-validation="true"]').forEach(form => {
            // Get all input, select, and textarea fields
            const fields = form.querySelectorAll('input:not([type="hidden"]), select, textarea');

            fields.forEach(field => {
                // Skip fields that opt-out
                if (field.dataset.noValidation === 'true') return;

                // Validate on blur
                field.addEventListener('blur', function() {
                    if (this.value.trim() !== '') {
                        validateField(this);
                        validateMatch(this);
                    }
                });

                // Clear error and re-validate on input
                field.addEventListener('input', function() {
                    if (this.classList.contains('is-invalid')) {
                        validateField(this);
                        validateMatch(this);
                    }
                });

                // Special handling for required fields
                if (field.hasAttribute('required')) {
                    field.addEventListener('change', function() {
                        validateField(this);
                    });
                }
            });

            // Validate entire form on submit
            form.addEventListener('submit', function(e) {
                let isValid = true;
                let firstInvalidField = null;

                fields.forEach(field => {
                    if (field.dataset.noValidation === 'true') return;

                    const fieldValid = validateField(field) && validateMatch(field);
                    if (!fieldValid) {
                        isValid = false;
                        if (!firstInvalidField) {
                            firstInvalidField = field;
                        }
                    }
                });

                if (!isValid) {
                    e.preventDefault();

                    // Focus first invalid field
                    if (firstInvalidField) {
                        firstInvalidField.focus();
                        firstInvalidField.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    }

                    // Show error toast
                    if (typeof showToast === 'function') {
                        showToast('Please correct the errors in the form', 'error');
                    }
                }
            });
        });

        // Also enable on forms with class 'realtime-validation'
        document.querySelectorAll('form.realtime-validation').forEach(form => {
            form.setAttribute('data-realtime-validation', 'true');
        });
    }

    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initRealTimeValidation);
    } else {
        initRealTimeValidation();
    }

    // Re-initialize when new content is loaded (for AJAX)
    window.reinitRealTimeValidation = initRealTimeValidation;

})();

/**
 * Custom validation example for course code format
 */
window.validateCourseCode = function(value) {
    // Course code should be 2-4 letters followed by 3 digits (e.g., CS101, MATH2301)
    if (!/^[A-Z]{2,4}\d{3,4}$/i.test(value)) {
        return 'Course code must be 2-4 letters followed by 3-4 digits (e.g., CS101)';
    }
    return true;
};

/**
 * Custom validation for credits
 */
window.validateCredits = function(value) {
    const credits = parseFloat(value);
    if (isNaN(credits) || credits < 0.5 || credits > 10) {
        return 'Credits must be between 0.5 and 10';
    }
    return true;
};

/**
 * Custom validation for duration weeks
 */
window.validateDuration = function(value) {
    const weeks = parseInt(value);
    if (isNaN(weeks) || weeks < 1 || weeks > 52) {
        return 'Duration must be between 1 and 52 weeks';
    }
    return true;
};
</script>

<style>
/* Validation animations */
.is-valid {
    animation: validPulse 0.3s ease-in-out;
    border-color: #50cd89 !important;
}

.is-invalid {
    animation: invalidShake 0.3s ease-in-out;
}

@keyframes validPulse {
    0%, 100% { transform: scale(1); }
    50% { transform: scale(1.02); }
}

@keyframes invalidShake {
    0%, 100% { transform: translateX(0); }
    25% { transform: translateX(-5px); }
    75% { transform: translateX(5px); }
}

/* Feedback visibility */
.invalid-feedback {
    display: none;
    margin-top: 0.25rem;
    font-size: 0.875rem;
    color: #f1416c;
}

.is-invalid ~ .invalid-feedback {
    display: block;
}

/* Valid feedback icon */
.is-valid {
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 8 8'%3e%3cpath fill='%2350cd89' d='M2.3 6.73L.6 4.53c-.4-1.04.46-1.4 1.1-.8l1.1 1.4 3.4-3.8c.6-.63 1.6-.27 1.2.7l-4 4.6c-.43.5-.8.4-1.1.1z'/%3e%3c/svg%3e");
    background-repeat: no-repeat;
    background-position: right calc(0.375em + 0.1875rem) center;
    background-size: calc(0.75em + 0.375rem) calc(0.75em + 0.375rem);
    padding-right: calc(1.5em + 0.75rem);
}

/* Focus styles */
input:focus, select:focus, textarea:focus {
    outline: none;
    box-shadow: 0 0 0 0.25rem rgba(59, 130, 246, 0.25);
}

.is-invalid:focus {
    box-shadow: 0 0 0 0.25rem rgba(241, 65, 108, 0.25);
}

.is-valid:focus {
    box-shadow: 0 0 0 0.25rem rgba(80, 205, 137, 0.25);
}
</style>
@endpush
