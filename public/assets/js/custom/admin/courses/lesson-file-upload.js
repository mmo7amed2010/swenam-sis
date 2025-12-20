"use strict";

/**
 * Lesson File Upload Handler
 *
 * Handles file uploads for lesson forms (video_upload and pdf content types)
 * Ensures file inputs are properly included in FormData before submission
 * 
 * This file intercepts FormData creation for lesson forms to ensure
 * file inputs are included even when they're in hidden containers.
 */
(function() {
    'use strict';

    // Store original FormData constructor
    const OriginalFormData = window.FormData;

    /**
     * Override FormData to ensure file inputs are included for lesson forms
     */
    window.FormData = function(form) {
        const formData = new OriginalFormData(form);
        
        // Only modify for lesson forms
        if (form && form.action && form.action.includes('/lessons')) {
            const contentType = form.querySelector('[name="content_type"]')?.value;
            
            // For file upload content types, ensure the file is explicitly included
            if (contentType === 'video_upload' || contentType === 'pdf') {
                const contentField = form.querySelector(`.content-field[data-type="${contentType}"]`);
                if (contentField) {
                    const fileInput = contentField.querySelector('[name="content_file"]');
                    if (fileInput && fileInput.files && fileInput.files.length > 0) {
                        // Ensure file is in FormData - remove and re-add to be sure
                        formData.delete('content_file');
                        formData.append('content_file', fileInput.files[0]);
                    }
                }
            }
        }
        
        return formData;
    };

    /**
     * Intercept form submission to ensure file inputs are visible and enabled
     * This runs in capture phase before main.js handlers
     */
    document.addEventListener('submit', function(e) {
        const form = e.target;
        if (!form || !form.matches('.ajax-modal-form')) return;
        if (!form.action || !form.action.includes('/lessons')) return;

        const contentType = form.querySelector('[name="content_type"]')?.value;
        
        // Only handle file upload content types
        if (contentType !== 'video_upload' && contentType !== 'pdf') return;

        // Find the file input
        const contentField = form.querySelector(`.content-field[data-type="${contentType}"]`);
        if (!contentField) return;

        const fileInput = contentField.querySelector('[name="content_file"]');
        if (!fileInput) return;

        // Ensure field is visible and enabled (critical for FormData to include it)
        contentField.style.display = 'block';
        contentField.style.visibility = 'visible';
        contentField.removeAttribute('hidden');
        
        // Ensure file input is enabled and not readonly
        fileInput.disabled = false;
        fileInput.removeAttribute('disabled');
        fileInput.removeAttribute('readonly');
        
        // Set required attribute
        if (fileInput.hasAttribute('data-required') || fileInput.type === 'file') {
            fileInput.setAttribute('required', 'required');
        }
    }, true); // Use capture phase to run early
})();

