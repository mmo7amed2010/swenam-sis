/**
 * ConditionalFields Alpine Component
 *
 * Reactive Alpine.js component for managing conditional field visibility.
 * Provides reactive show/hide logic based on form field values.
 *
 * @alpine-component conditional-fields
 * @author LMS Development Team
 *
 * @example
 * <div x-data="conditionalFields({
 *     fields: {
 *         'late-policy': { trigger: 'submission_type', showWhen: 'online' },
 *         'file-settings': { trigger: 'submission_type', showWhen: ['file', 'both'] }
 *     }
 * })">
 *     <!-- Form with conditional fields -->
 * </div>
 */

/**
 * Create ConditionalFields Alpine component
 *
 * @param {Object} config - Initial configuration
 * @param {Object} config.fields - Field visibility rules
 * @param {Object} config.initialValues - Initial field values
 * @returns {Object} Alpine component object
 */
export default function conditionalFields(config = {}) {
    return {
        // Reactive Data
        fields: config.fields || {},
        values: config.initialValues || {},
        visibilityMap: {},

        // Methods

        /**
         * Initialize component
         * Called automatically by Alpine when component is mounted
         */
        init() {
            console.log('ConditionalFields initialized', {
                fieldCount: Object.keys(this.fields).length
            });

            // Initialize visibility map
            Object.keys(this.fields).forEach(fieldKey => {
                this.visibilityMap[fieldKey] = this.shouldShow(fieldKey);
            });

            // Watch for value changes
            this.$watch('values', () => {
                this.updateAllVisibility();
            }, { deep: true });

            // Initial visibility update
            this.updateAllVisibility();
        },

        /**
         * Check if a field should be visible
         * @param {string} fieldKey - Field identifier
         * @returns {boolean}
         */
        shouldShow(fieldKey) {
            const fieldConfig = this.fields[fieldKey];

            if (!fieldConfig) {
                console.warn('Field config not found for:', fieldKey);
                return true;
            }

            const triggerValue = this.values[fieldConfig.trigger];
            const showWhen = Array.isArray(fieldConfig.showWhen)
                ? fieldConfig.showWhen
                : [fieldConfig.showWhen];

            // Special handling for boolean triggers (checkboxes)
            if (typeof triggerValue === 'boolean') {
                return showWhen.includes(triggerValue);
            }

            // String/number comparison
            return showWhen.includes(triggerValue);
        },

        /**
         * Update visibility for all configured fields
         */
        updateAllVisibility() {
            Object.keys(this.fields).forEach(fieldKey => {
                const visible = this.shouldShow(fieldKey);
                this.visibilityMap[fieldKey] = visible;

                // Emit custom event for each visibility change
                this.$dispatch('field-visibility-changed', {
                    field: fieldKey,
                    visible: visible
                });
            });
        },

        /**
         * Update a trigger value
         * @param {string} triggerName - Trigger field name
         * @param {*} value - New value
         */
        updateValue(triggerName, value) {
            this.values[triggerName] = value;
            console.log(`Value updated: ${triggerName} = ${value}`);
        },

        /**
         * Get visibility state for a field
         * @param {string} fieldKey - Field identifier
         * @returns {boolean}
         */
        isVisible(fieldKey) {
            return this.visibilityMap[fieldKey] !== false;
        },

        /**
         * Add a new conditional field rule
         * @param {string} fieldKey - Field identifier
         * @param {Object} config - Field configuration
         * @param {string} config.trigger - Trigger field name
         * @param {string|Array} config.showWhen - Value(s) to show field
         */
        addField(fieldKey, config) {
            if (!config.trigger || !config.showWhen) {
                console.error('Invalid field config:', config);
                return;
            }

            this.fields[fieldKey] = config;
            this.visibilityMap[fieldKey] = this.shouldShow(fieldKey);

            console.log(`Field added: ${fieldKey}`, config);
        },

        /**
         * Remove a conditional field rule
         * @param {string} fieldKey - Field identifier
         */
        removeField(fieldKey) {
            delete this.fields[fieldKey];
            delete this.visibilityMap[fieldKey];

            console.log(`Field removed: ${fieldKey}`);
        },

        /**
         * Get visibility classes for a field
         * @param {string} fieldKey - Field identifier
         * @returns {string} CSS classes
         */
        getVisibilityClass(fieldKey) {
            return this.isVisible(fieldKey) ? '' : 'd-none';
        },

        /**
         * Get visibility styles for a field
         * @param {string} fieldKey - Field identifier
         * @returns {Object} Style object
         */
        getVisibilityStyle(fieldKey) {
            return {
                display: this.isVisible(fieldKey) ? 'block' : 'none'
            };
        },

        /**
         * Show a field programmatically
         * @param {string} fieldKey - Field identifier
         */
        showField(fieldKey) {
            this.visibilityMap[fieldKey] = true;
            this.$dispatch('field-visibility-changed', {
                field: fieldKey,
                visible: true
            });
        },

        /**
         * Hide a field programmatically
         * @param {string} fieldKey - Field identifier
         */
        hideField(fieldKey) {
            this.visibilityMap[fieldKey] = false;
            this.$dispatch('field-visibility-changed', {
                field: fieldKey,
                visible: false
            });
        },

        /**
         * Reset all fields to initial state
         */
        reset() {
            this.values = config.initialValues || {};
            this.updateAllVisibility();
            console.log('ConditionalFields reset');
        },

        /**
         * Get summary of current state
         * @returns {Object} State summary
         */
        getState() {
            return {
                fields: this.fields,
                values: this.values,
                visibility: this.visibilityMap
            };
        },

        /**
         * Enable/disable fields inside hidden sections
         * @param {boolean} disableHidden - Whether to disable hidden fields
         */
        toggleFieldsInHiddenSections(disableHidden = true) {
            Object.keys(this.visibilityMap).forEach(fieldKey => {
                const sectionId = fieldKey;
                const section = document.getElementById(sectionId);

                if (section) {
                    const fields = section.querySelectorAll('input, select, textarea, button');
                    const isVisible = this.visibilityMap[fieldKey];

                    fields.forEach(field => {
                        if (disableHidden) {
                            field.disabled = !isVisible;
                        }

                        // Update aria-hidden attribute
                        section.setAttribute('aria-hidden', !isVisible);
                    });
                }
            });
        }
    };
}

/**
 * Simplified conditional field helper
 *
 * Creates a basic conditional field component with minimal config.
 *
 * @param {string} trigger - Trigger field name
 * @param {string|Array} showWhen - Value(s) to show content
 * @returns {Object} Alpine component object
 *
 * @example
 * <div x-data="conditionalField('submission_type', 'online')">
 *     <div x-show="visible">
 *         <!-- Content shown when submission_type is 'online' -->
 *     </div>
 * </div>
 */
export function conditionalField(trigger, showWhen) {
    return {
        trigger: trigger,
        showWhen: Array.isArray(showWhen) ? showWhen : [showWhen],
        value: null,
        visible: false,

        init() {
            // Find trigger element
            const triggerEl = document.querySelector(`[name="${this.trigger}"]`);

            if (triggerEl) {
                // Get initial value
                this.value = this.getTriggerValue(triggerEl);
                this.updateVisibility();

                // Watch for changes
                const eventType = triggerEl.type === 'checkbox' || triggerEl.type === 'radio'
                    ? 'change'
                    : 'input';

                triggerEl.addEventListener(eventType, () => {
                    this.value = this.getTriggerValue(triggerEl);
                    this.updateVisibility();
                });

                console.log('ConditionalField initialized', {
                    trigger: this.trigger,
                    value: this.value,
                    visible: this.visible
                });
            } else {
                console.error('Trigger element not found:', this.trigger);
            }
        },

        getTriggerValue(element) {
            if (element.type === 'checkbox') {
                return element.checked;
            } else if (element.type === 'radio') {
                const checked = document.querySelector(`input[name="${element.name}"]:checked`);
                return checked ? checked.value : null;
            } else {
                return element.value;
            }
        },

        updateVisibility() {
            this.visible = this.showWhen.includes(this.value);
        }
    };
}

// Register with Alpine if available
if (typeof window.Alpine !== 'undefined') {
    window.Alpine.data('conditionalFields', conditionalFields);
    window.Alpine.data('conditionalField', conditionalField);
    console.log('ConditionalFields components registered with Alpine');
}
