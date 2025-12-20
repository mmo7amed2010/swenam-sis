/**
 * Conditional Fields Module
 *
 * Manages field visibility based on form values (conditional logic).
 * Used for showing/hiding sections based on select, radio, or checkbox values.
 *
 * @module forms/conditional-fields
 * @author LMS Development Team
 */

/**
 * Toggle field/section visibility based on trigger value
 *
 * @param {string|Element} triggerSelector - Trigger element selector or element
 * @param {string|Element} targetSelector - Target element selector or element
 * @param {string|Array} showValues - Value(s) that should show the target
 * @param {Object} options - Configuration options
 * @param {string} options.effect - Show/hide effect ('slide', 'fade', 'none') default: 'slide'
 * @param {number} options.duration - Animation duration in ms (default: 300)
 * @returns {void}
 *
 * @example
 * // Show late policy when submission type is 'online'
 * toggleFieldVisibility(
 *     '#submission_type',
 *     '#late-policy-section',
 *     'online'
 * );
 *
 * // Show file upload settings when submission is 'file'
 * toggleFieldVisibility(
 *     'select[name="submission_type"]',
 *     '.file-upload-settings',
 *     ['file', 'both'],
 *     { effect: 'fade' }
 * );
 */
export function toggleFieldVisibility(triggerSelector, targetSelector, showValues, options = {}) {
    const {
        effect = 'slide',
        duration = 300
    } = options;

    // Get elements
    const trigger = typeof triggerSelector === 'string'
        ? document.querySelector(triggerSelector)
        : triggerSelector;

    const target = typeof targetSelector === 'string'
        ? document.querySelector(targetSelector)
        : targetSelector;

    if (!trigger || !target) {
        console.error('Trigger or target element not found', {
            trigger: triggerSelector,
            target: targetSelector
        });
        return;
    }

    // Normalize showValues to array
    const showValuesArray = Array.isArray(showValues) ? showValues : [showValues];

    // Get current value based on input type
    const getCurrentValue = () => {
        if (trigger.type === 'checkbox') {
            return trigger.checked;
        } else if (trigger.type === 'radio') {
            const checkedRadio = document.querySelector(`input[name="${trigger.name}"]:checked`);
            return checkedRadio ? checkedRadio.value : null;
        } else {
            return trigger.value;
        }
    };

    // Show/hide logic
    const updateVisibility = () => {
        const currentValue = getCurrentValue();
        const shouldShow = showValuesArray.includes(currentValue);

        if (shouldShow) {
            showElement(target, effect, duration);
            // Enable form fields inside target
            enableFields(target);
        } else {
            hideElement(target, effect, duration);
            // Disable form fields inside target
            disableFields(target);
        }
    };

    // Attach event listener
    const eventType = trigger.type === 'checkbox' || trigger.type === 'radio' ? 'change' : 'input';
    trigger.addEventListener(eventType, updateVisibility);

    // If radio, attach to all radios with same name
    if (trigger.type === 'radio') {
        const radios = document.querySelectorAll(`input[name="${trigger.name}"]`);
        radios.forEach(radio => {
            if (radio !== trigger) {
                radio.addEventListener('change', updateVisibility);
            }
        });
    }

    // Initial check
    updateVisibility();
}

/**
 * Initialize conditional fields from configuration object
 *
 * Sets up multiple conditional field rules at once.
 *
 * @param {Array} config - Array of field configuration objects
 * @param {string} config[].trigger - Trigger field selector
 * @param {string} config[].target - Target field/section selector
 * @param {string|Array} config[].showValues - Value(s) to show target
 * @param {Object} config[].options - Optional display options
 * @returns {void}
 *
 * @example
 * initConditionalFields([
 *     {
 *         trigger: '#submission_type',
 *         target: '#late-policy-section',
 *         showValues: 'online'
 *     },
 *     {
 *         trigger: 'select[name="quiz_type"]',
 *         target: '.timed-quiz-settings',
 *         showValues: ['timed', 'exam'],
 *         options: { effect: 'fade' }
 *     }
 * ]);
 */
export function initConditionalFields(config) {
    if (!Array.isArray(config)) {
        console.error('Config must be an array of field configurations');
        return;
    }

    config.forEach((field, index) => {
        if (!field.trigger || !field.target || !field.showValues) {
            console.error(`Invalid config at index ${index}:`, field);
            return;
        }

        toggleFieldVisibility(
            field.trigger,
            field.target,
            field.showValues,
            field.options || {}
        );
    });

    console.log(`Initialized ${config.length} conditional field rules`);
}

/**
 * Show element with animation
 * @private
 */
function showElement(element, effect, duration) {
    if (effect === 'fade') {
        element.style.display = 'block';
        element.style.opacity = '0';
        element.style.transition = `opacity ${duration}ms ease-in-out`;
        setTimeout(() => {
            element.style.opacity = '1';
        }, 10);
    } else if (effect === 'slide') {
        element.style.display = 'block';
        element.style.maxHeight = '0';
        element.style.overflow = 'hidden';
        element.style.transition = `max-height ${duration}ms ease-in-out`;
        setTimeout(() => {
            element.style.maxHeight = element.scrollHeight + 'px';
            setTimeout(() => {
                element.style.maxHeight = '';
                element.style.overflow = '';
            }, duration);
        }, 10);
    } else {
        element.style.display = 'block';
    }

    element.removeAttribute('aria-hidden');
}

/**
 * Hide element with animation
 * @private
 */
function hideElement(element, effect, duration) {
    if (effect === 'fade') {
        element.style.transition = `opacity ${duration}ms ease-in-out`;
        element.style.opacity = '0';
        setTimeout(() => {
            element.style.display = 'none';
        }, duration);
    } else if (effect === 'slide') {
        element.style.maxHeight = element.scrollHeight + 'px';
        element.style.overflow = 'hidden';
        element.style.transition = `max-height ${duration}ms ease-in-out`;
        setTimeout(() => {
            element.style.maxHeight = '0';
            setTimeout(() => {
                element.style.display = 'none';
                element.style.maxHeight = '';
                element.style.overflow = '';
            }, duration);
        }, 10);
    } else {
        element.style.display = 'none';
    }

    element.setAttribute('aria-hidden', 'true');
}

/**
 * Enable all form fields inside a container
 * @private
 */
function enableFields(container) {
    const fields = container.querySelectorAll('input, select, textarea, button');
    fields.forEach(field => {
        field.disabled = false;
    });
}

/**
 * Disable all form fields inside a container
 * @private
 */
function disableFields(container) {
    const fields = container.querySelectorAll('input, select, textarea, button');
    fields.forEach(field => {
        field.disabled = true;
    });
}

/**
 * Remove conditional field rule
 *
 * Removes event listeners and resets visibility.
 *
 * @param {string|Element} triggerSelector - Trigger element selector or element
 * @param {string|Element} targetSelector - Target element selector or element
 * @returns {void}
 *
 * @example
 * removeConditionalField('#submission_type', '#late-policy-section');
 */
export function removeConditionalField(triggerSelector, targetSelector) {
    const trigger = typeof triggerSelector === 'string'
        ? document.querySelector(triggerSelector)
        : triggerSelector;

    const target = typeof targetSelector === 'string'
        ? document.querySelector(targetSelector)
        : targetSelector;

    if (!trigger || !target) {
        return;
    }

    // Clone and replace to remove all event listeners
    const newTrigger = trigger.cloneNode(true);
    trigger.parentNode.replaceChild(newTrigger, trigger);

    // Reset target visibility
    target.style.display = 'block';
    target.removeAttribute('aria-hidden');
    enableFields(target);
}

// Export default initialization function
export default initConditionalFields;
