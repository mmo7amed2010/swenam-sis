/**
 * Form Validation Module
 *
 * Generic form validation utilities for common validation scenarios.
 * Provides reusable validation functions for forms throughout the application.
 *
 * @module forms/form-validation
 * @author LMS Development Team
 */

/**
 * Validate that all required fields are filled
 *
 * @param {NodeList|Array} fields - Fields to validate
 * @param {Object} options - Validation options
 * @param {boolean} options.trim - Trim whitespace before checking (default: true)
 * @param {boolean} options.showErrors - Show inline errors (default: true)
 * @returns {Object} Validation result {valid: boolean, errors: Array}
 *
 * @example
 * const fields = document.querySelectorAll('[required]');
 * const result = validateRequired(fields);
 * if (!result.valid) {
 *     console.log('Errors:', result.errors);
 * }
 */
export function validateRequired(fields, options = {}) {
    const {
        trim = true,
        showErrors = true
    } = options;

    const errors = [];

    fields.forEach((field, index) => {
        let value = field.value;

        // Trim whitespace if option enabled
        if (trim && typeof value === 'string') {
            value = value.trim();
        }

        // Check if field is empty
        const isEmpty = !value || value.length === 0;

        if (isEmpty) {
            const fieldName = field.name || field.id || `Field ${index + 1}`;
            const label = field.getAttribute('aria-label') ||
                         field.closest('.mb-10, .fv-row')?.querySelector('label')?.textContent ||
                         fieldName;

            errors.push({
                field: field,
                name: fieldName,
                label: label.trim(),
                message: `${label.trim()} is required`
            });

            // Show inline error if enabled
            if (showErrors) {
                field.classList.add('is-invalid');

                // Add error message if not exists
                let errorEl = field.nextElementSibling;
                if (!errorEl || !errorEl.classList.contains('invalid-feedback')) {
                    errorEl = document.createElement('div');
                    errorEl.className = 'invalid-feedback d-block';
                    field.parentNode.appendChild(errorEl);
                }
                errorEl.textContent = `${label.trim()} is required`;
            }
        } else {
            // Clear error state
            field.classList.remove('is-invalid');
            const errorEl = field.nextElementSibling;
            if (errorEl && errorEl.classList.contains('invalid-feedback')) {
                errorEl.remove();
            }
        }
    });

    return {
        valid: errors.length === 0,
        errors: errors
    };
}

/**
 * Validate that at least one checkbox/radio is checked
 *
 * @param {NodeList|Array} checkboxes - Checkboxes/radios to validate
 * @param {Object} options - Validation options
 * @param {string} options.message - Custom error message
 * @param {boolean} options.showError - Show error alert (default: true)
 * @returns {boolean} True if at least one is checked
 *
 * @example
 * const checkboxes = document.querySelectorAll('input[name="interests[]"]');
 * const isValid = validateAtLeastOneChecked(checkboxes, {
 *     message: 'Please select at least one interest'
 * });
 */
export function validateAtLeastOneChecked(checkboxes, options = {}) {
    const {
        message = 'Please select at least one option',
        showError = true
    } = options;

    const hasChecked = Array.from(checkboxes).some(checkbox => checkbox.checked);

    if (!hasChecked && showError) {
        showValidationError(message);
    }

    return hasChecked;
}

/**
 * Show validation error message
 *
 * Displays Bootstrap alert at top of form or specified container.
 *
 * @param {string} message - Error message to display
 * @param {string|Element} container - Container selector or element (default: first form)
 * @param {Object} options - Display options
 * @param {number} options.duration - Auto-dismiss after ms (0 = no auto-dismiss)
 * @param {boolean} options.dismissible - Show close button (default: true)
 * @returns {Element} Created alert element
 *
 * @example
 * showValidationError('Please fill all required fields', '#myForm');
 * showValidationError('Invalid input', document.getElementById('myForm'), {
 *     duration: 5000,
 *     dismissible: true
 * });
 */
export function showValidationError(message, container = null, options = {}) {
    const {
        duration = 0,
        dismissible = true
    } = options;

    // Find container
    let targetContainer;
    if (container) {
        targetContainer = typeof container === 'string'
            ? document.querySelector(container)
            : container;
    } else {
        targetContainer = document.querySelector('form');
    }

    if (!targetContainer) {
        console.error('Container for validation error not found');
        return null;
    }

    // Remove existing error alerts
    const existingAlerts = targetContainer.querySelectorAll('.alert-danger[data-validation-error]');
    existingAlerts.forEach(alert => alert.remove());

    // Create alert element
    const alert = document.createElement('div');
    alert.className = 'alert alert-danger d-flex align-items-center p-5 mb-10';
    alert.setAttribute('data-validation-error', 'true');
    alert.setAttribute('role', 'alert');

    alert.innerHTML = `
        <span class="svg-icon svg-icon-2hx svg-icon-danger me-4">
            <i class="bi bi-exclamation-triangle-fill fs-2hx"></i>
        </span>
        <div class="d-flex flex-column flex-grow-1">
            <h4 class="mb-1 text-danger">Validation Error</h4>
            <span>${message}</span>
        </div>
        ${dismissible ? `
            <button type="button" class="btn-close" aria-label="Close"></button>
        ` : ''}
    `;

    // Insert at beginning of container
    targetContainer.insertBefore(alert, targetContainer.firstChild);

    // Add close button handler
    if (dismissible) {
        const closeBtn = alert.querySelector('.btn-close');
        closeBtn.addEventListener('click', () => alert.remove());
    }

    // Auto-dismiss if duration specified
    if (duration > 0) {
        setTimeout(() => {
            alert.classList.add('fade');
            setTimeout(() => alert.remove(), 150);
        }, duration);
    }

    // Scroll to alert
    alert.scrollIntoView({ behavior: 'smooth', block: 'center' });

    return alert;
}

/**
 * Clear all validation errors from form
 *
 * Removes error classes, messages, and alert boxes.
 *
 * @param {string|Element} form - Form selector or element
 * @returns {void}
 *
 * @example
 * clearValidationErrors('#myForm');
 * clearValidationErrors(document.getElementById('myForm'));
 */
export function clearValidationErrors(form) {
    const formElement = typeof form === 'string'
        ? document.querySelector(form)
        : form;

    if (!formElement) {
        console.error('Form not found for clearing errors');
        return;
    }

    // Remove invalid classes
    formElement.querySelectorAll('.is-invalid').forEach(field => {
        field.classList.remove('is-invalid');
    });

    // Remove error messages
    formElement.querySelectorAll('.invalid-feedback').forEach(error => {
        error.remove();
    });

    // Remove alert boxes
    formElement.querySelectorAll('.alert-danger[data-validation-error]').forEach(alert => {
        alert.remove();
    });
}

/**
 * Validate email format
 *
 * @param {string} email - Email address to validate
 * @returns {boolean} True if valid email format
 *
 * @example
 * if (validateEmail(emailField.value)) {
 *     // Valid email
 * }
 */
export function validateEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
}

/**
 * Validate URL format
 *
 * @param {string} url - URL to validate
 * @returns {boolean} True if valid URL format
 *
 * @example
 * if (validateUrl(urlField.value)) {
 *     // Valid URL
 * }
 */
export function validateUrl(url) {
    try {
        new URL(url);
        return true;
    } catch (e) {
        return false;
    }
}

/**
 * Validate field length
 *
 * @param {string} value - Value to validate
 * @param {number} min - Minimum length (optional)
 * @param {number} max - Maximum length (optional)
 * @returns {Object} {valid: boolean, error: string|null}
 *
 * @example
 * const result = validateLength(field.value, 5, 50);
 * if (!result.valid) alert(result.error);
 */
export function validateLength(value, min = null, max = null) {
    const length = value ? value.length : 0;

    if (min !== null && length < min) {
        return {
            valid: false,
            error: `Minimum length is ${min} characters (current: ${length})`
        };
    }

    if (max !== null && length > max) {
        return {
            valid: false,
            error: `Maximum length is ${max} characters (current: ${length})`
        };
    }

    return { valid: true, error: null };
}

// Export default validation function
export default validateRequired;
