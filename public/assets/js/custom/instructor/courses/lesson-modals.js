"use strict";

/**
 * KTLessonModals
 *
 * Handles lesson-specific modal functionality including content type switching,
 * file uploads, video previews, and content type change confirmations.
 *
 * @requires KTCourseModuleMain
 * @requires Bootstrap 5
 * @requires SweetAlert2
 */
var KTLessonModals = function () {

    /**
     * Initialize all lesson modal functionality
     */
    var initLessonModals = () => {
        initContentTypeChange();
        initEditLessonContentTypeConfirmation();
        initFileValidation();
        initEditModalVideoPreview();
        initTextEditor();
    };

    /**
     * Initialize video preview for edit modals when they open
     */
    var initEditModalVideoPreview = () => {
        // Listen for when edit lesson modals are shown
        document.addEventListener('shown.bs.modal', function(e) {
            const modal = e.target;
            if (!modal.id || !modal.id.startsWith('kt_modal_edit_lesson_')) return;

            const form = modal.querySelector('form');
            if (!form) return;

            // Check if this is a video content type
            const contentTypeSelect = form.querySelector('[name="content_type"]');
            if (!contentTypeSelect || contentTypeSelect.value !== 'video') return;

            // Find the video URL input
            const videoField = form.querySelector('.content-field[data-type="video"]');
            if (!videoField) return;

            const urlInput = videoField.querySelector('[name="content_url"]');
            if (!urlInput || !urlInput.value) return;

            // Find the preview container
            const previewContainer = videoField.querySelector('.video-preview');
            if (!previewContainer) return;

            // Generate preview if not already shown
            if (previewContainer.style.display === 'none' || previewContainer.innerHTML === '') {
                // Use KTCourseModuleMain's generateVideoEmbed if available
                if (typeof KTCourseModuleMain !== 'undefined' && KTCourseModuleMain.generateVideoEmbed) {
                    const embedHtml = KTCourseModuleMain.generateVideoEmbed(urlInput.value);
                    if (embedHtml) {
                        previewContainer.innerHTML = embedHtml;
                        previewContainer.style.display = 'block';
                    }
                } else {
                    // Fallback: trigger blur event to use existing preview logic
                    urlInput.dispatchEvent(new Event('blur', { bubbles: true }));
                }
            }
        });
    };

    /**
     * Handle content type change in add/edit lesson modals
     * Shows/hides appropriate fields based on selection
     */
    var initContentTypeChange = () => {
        document.addEventListener('change', function(e) {
            const select = e.target;
            if (!select.matches('select[name="content_type"]')) return;

            const modal = select.closest('.modal');
            if (!modal) return;

            const form = modal.querySelector('form');
            const selectedType = select.value;

            // Find all content field groups
            const contentFields = form.querySelectorAll('.content-field');

            // Hide all and remove required
            contentFields.forEach(field => {
                field.style.display = 'none';
                const inputs = field.querySelectorAll('input, textarea, select');
                inputs.forEach(input => {
                    input.removeAttribute('required');
                    // Only clear file inputs, preserve URL and text values
                    if (input.type === 'file') {
                        input.value = '';
                    }
                    // Ensure input is enabled
                    input.disabled = false;
                    input.removeAttribute('disabled');
                });
            });

            // Show selected type field and add required
            if (selectedType) {
                const targetField = form.querySelector(`.content-field[data-type="${selectedType}"]`);
                if (targetField) {
                    targetField.style.display = 'block';

                    // Mark primary input as required and ensure it's enabled
                    const primaryInput = targetField.querySelector('input:not([type="checkbox"]):not([type="hidden"]), textarea');
                    if (primaryInput) {
                        primaryInput.setAttribute('required', 'required');
                        primaryInput.disabled = false;
                        primaryInput.removeAttribute('disabled');
                    }
                }
            }
        });

        // Intercept form submission to ensure content_url is included for video/external_link types
        // and content_file is included for video_upload/pdf types
        // This runs before main.js's executeSubmit to ensure the field is ready
        document.addEventListener('submit', function(e) {
            const form = e.target;
            if (!form || !form.matches('.ajax-modal-form')) return;
            if (!form.action || !form.action.includes('/lessons')) return;

            const contentType = form.querySelector('[name="content_type"]')?.value;

            if (contentType === 'video' || contentType === 'external_link') {
                const contentUrlInput = form.querySelector(`.content-field[data-type="${contentType}"] [name="content_url"]`);
                if (contentUrlInput) {
                    // Ensure field is visible and enabled
                    const contentField = contentUrlInput.closest('.content-field');
                    if (contentField) {
                        contentField.style.display = 'block';
                    }
                    contentUrlInput.disabled = false;
                    contentUrlInput.removeAttribute('disabled');

                    // Create a hidden input to ensure the value is always included
                    // This is a workaround for cases where FormData might not include the field
                    let hiddenInput = form.querySelector('input[name="content_url"][type="hidden"]');
                    if (!hiddenInput && contentUrlInput.value) {
                        hiddenInput = document.createElement('input');
                        hiddenInput.type = 'hidden';
                        hiddenInput.name = 'content_url';
                        form.appendChild(hiddenInput);
                    }
                    if (hiddenInput) {
                        hiddenInput.value = contentUrlInput.value;
                    }
                }
            } else if (contentType === 'video_upload' || contentType === 'pdf') {
                // Ensure file input is visible and enabled
                const contentField = form.querySelector(`.content-field[data-type="${contentType}"]`);
                if (contentField) {
                    // Make field visible (critical for file inputs)
                    contentField.style.display = 'block';
                    contentField.style.visibility = 'visible';
                    contentField.removeAttribute('hidden');

                    const fileInput = contentField.querySelector('[name="content_file"]');
                    if (fileInput) {
                        fileInput.disabled = false;
                        fileInput.removeAttribute('disabled');
                        fileInput.removeAttribute('readonly');
                        // Ensure required attribute is set
                        if (fileInput.hasAttribute('data-required') || fileInput.type === 'file') {
                            fileInput.setAttribute('required', 'required');
                        }
                    }
                }
            }
        }, true); // Use capture phase to run early

        // Intercept form data creation to ensure file is included
        // This runs after the form submit handler to modify FormData before it's sent
        document.addEventListener('submit', function(e) {
            const form = e.target;
            if (!form || !form.matches('.ajax-modal-form')) return;
            if (!form.action || !form.action.includes('/lessons')) return;

            const contentType = form.querySelector('[name="content_type"]')?.value;

            // For file uploads, ensure the file is explicitly included in FormData
            if (contentType === 'video_upload' || contentType === 'pdf') {
                const contentField = form.querySelector(`.content-field[data-type="${contentType}"]`);
                if (contentField) {
                    const fileInput = contentField.querySelector('[name="content_file"]');
                    if (fileInput && fileInput.files && fileInput.files.length > 0) {
                        // Store the file for later use in the fetch request
                        form._uploadFile = fileInput.files[0];
                    }
                }
            }
        }, false); // Use bubble phase to run after capture phase handlers
    };

    /**
     * Handle content type change confirmation in edit modals
     * When changing content type, warn user about data loss
     */
    var initEditLessonContentTypeConfirmation = () => {
        document.addEventListener('change', function(e) {
            const select = e.target;
            if (!select.matches('select[name="content_type"]')) return;

            const modal = select.closest('.modal');
            if (!modal) return;

            // Check if this is an edit modal (has original content type)
            const originalType = modal.dataset.originalContentType;
            if (!originalType) return;

            const newType = select.value;

            // If type changed, show/require confirmation checkbox
            const confirmationSection = modal.querySelector('.content-change-confirmation, [data-content-change-confirmation]');
            if (confirmationSection) {
                if (newType !== originalType) {
                    confirmationSection.style.display = 'block';
                    const checkbox = confirmationSection.querySelector('input[type="checkbox"]');
                    if (checkbox) {
                        checkbox.required = true;
                        checkbox.checked = false;
                    }
                } else {
                    confirmationSection.style.display = 'none';
                    const checkbox = confirmationSection.querySelector('input[type="checkbox"]');
                    if (checkbox) {
                        checkbox.required = false;
                        checkbox.checked = false;
                    }
                }
            }
        });

        // Intercept form submission if confirmation not checked
        document.addEventListener('submit', function(e) {
            const form = e.target;
            if (!form.matches('.ajax-modal-form')) return;

            const modal = form.closest('.modal');
            if (!modal) return;

            const originalType = modal.dataset.originalContentType;
            if (!originalType) return;

            const contentTypeSelect = form.querySelector('select[name="content_type"]');
            if (!contentTypeSelect) return;

            const newType = contentTypeSelect.value;
            if (newType === originalType) return;

            const confirmationSection = modal.querySelector('.content-change-confirmation, [data-content-change-confirmation]');
            if (!confirmationSection) return;

            const checkbox = confirmationSection.querySelector('input[type="checkbox"]');
            if (checkbox && !checkbox.checked) {
                e.preventDefault();
                e.stopPropagation();

                Swal.fire({
                    text: 'You must confirm that you understand the content will be replaced.',
                    icon: 'warning',
                    buttonsStyling: false,
                    confirmButtonText: 'Ok',
                    customClass: {
                        confirmButton: 'btn btn-primary'
                    }
                });

                checkbox.focus();
                return false;
            }
        }, true);
    };

    /**
     * Initialize file validation for PDF and video uploads
     */
    var initFileValidation = () => {
        document.addEventListener('change', function(e) {
            const input = e.target;
            if (!input.matches('input[type="file"][name="content_file"]')) return;

            const file = input.files[0];
            if (!file) return;

            // Get the content type from the form
            const form = input.closest('form');
            if (!form) return;

            const contentTypeSelect = form.querySelector('[name="content_type"]');
            const contentType = contentTypeSelect ? contentTypeSelect.value : '';

            // Validate based on content type
            if (contentType === 'pdf') {
                // Validate PDF file type
                if (!file.type.includes('pdf')) {
                    Swal.fire({
                        text: 'Please select a valid PDF file.',
                        icon: 'error',
                        buttonsStyling: false,
                        confirmButtonText: 'Ok',
                        customClass: {
                            confirmButton: 'btn btn-primary'
                        }
                    });
                    input.value = '';
                    return;
                }

                // Validate file size (25MB max for PDF)
                const maxSize = 25 * 1024 * 1024; // 25MB in bytes
                if (file.size > maxSize) {
                    Swal.fire({
                        text: 'File size must be less than 25MB.',
                        icon: 'error',
                        buttonsStyling: false,
                        confirmButtonText: 'Ok',
                        customClass: {
                            confirmButton: 'btn btn-primary'
                        }
                    });
                    input.value = '';
                    return;
                }
            } else if (contentType === 'video_upload') {
                // Validate video file type
                const validVideoTypes = ['video/mp4', 'video/webm', 'video/quicktime', 'video/x-msvideo'];
                const validExtensions = ['.mp4', '.webm', '.mov', '.avi'];
                const fileName = file.name.toLowerCase();
                const hasValidExtension = validExtensions.some(ext => fileName.endsWith(ext));
                const hasValidMimeType = validVideoTypes.includes(file.type) || file.type.startsWith('video/');

                if (!hasValidExtension && !hasValidMimeType) {
                    Swal.fire({
                        text: 'Please select a valid video file (MP4, WebM, MOV, or AVI).',
                        icon: 'error',
                        buttonsStyling: false,
                        confirmButtonText: 'Ok',
                        customClass: {
                            confirmButton: 'btn btn-primary'
                        }
                    });
                    input.value = '';
                    return;
                }

                // Validate file size (100MB max for video)
                const maxSize = 100 * 1024 * 1024; // 100MB in bytes
                if (file.size > maxSize) {
                    Swal.fire({
                        text: 'File size must be less than 100MB.',
                        icon: 'error',
                        buttonsStyling: false,
                        confirmButtonText: 'Ok',
                        customClass: {
                            confirmButton: 'btn btn-primary'
                        }
                    });
                    input.value = '';
                    return;
                }
            }
        });
    };

    /**
     * Initialize CKEditor 5 for lesson content
     */
    var initTextEditor = () => {
        console.log('KTLessonModals: Initializing CKEditor 5...');

        // Global editor instances storage
        if (!window.lessonCKEditors) {
            window.lessonCKEditors = new Map();
        }

        /**
         * Initialize CKEditor 5 instance
         */
        window.initLessonTextEditor = function(editorId, textareaId) {
            const editorEl = document.getElementById(editorId);
            const textareaEl = document.getElementById(textareaId);

            if (!editorEl || !textareaEl) {
                console.warn('KTLessonModals: CKEditor elements not found', { editorId, textareaId });
                return null;
            }

            console.log('KTLessonModals: Initializing CKEditor 5 for', editorId);

            // Cleanup existing instance
            destroyTextEditor(editorId);

            // Clear the editor container
            editorEl.innerHTML = '';

            // Initialize CKEditor 5
            if (typeof ClassicEditor === 'undefined') {
                console.error('KTLessonModals: CKEditor 5 ClassicEditor not loaded');
                // Load CKEditor 5 dynamically
                loadCKEditor(() => {
                    initializeCKEditor(editorId, textareaId, editorEl, textareaEl);
                });
                return null;
            } else {
                return initializeCKEditor(editorId, textareaId, editorEl, textareaEl);
            }
        };

        /**
         * Load CKEditor 5 dynamically
         */
        function loadCKEditor(callback) {
            if (window.ckeditorLoaded) {
                callback();
                return;
            }

            // Check if script is already being loaded
            if (window.ckeditorLoading) {
                // Wait for existing load to complete
                const checkInterval = setInterval(() => {
                    if (window.ckeditorLoaded) {
                        clearInterval(checkInterval);
                        callback();
                    }
                }, 100);
                return;
            }

            window.ckeditorLoading = true;
            const script = document.createElement('script');
            script.src = '/assets/plugins/custom/ckeditor/ckeditor-classic.bundle.js';
            script.onload = function() {
                window.ckeditorLoaded = true;
                window.ckeditorLoading = false;
                callback();
            };
            script.onerror = function() {
                window.ckeditorLoading = false;
                console.error('Failed to load CKEditor script');
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Failed to load text editor. Please refresh the page and try again.'
                });
            };
            document.head.appendChild(script);
        }

        /**
         * Initialize CKEditor 5 with configuration
         */
        function initializeCKEditor(editorId, textareaId, editorEl, textareaEl) {
            // Ensure editor container is visible and has dimensions
            if (editorEl.offsetHeight === 0 || editorEl.offsetWidth === 0) {
                console.warn('KTLessonModals: Editor container not visible, waiting...');
                // Wait a bit more for the container to become visible
                return new Promise((resolve) => {
                    setTimeout(() => {
                        initializeCKEditor(editorId, textareaId, editorEl, textareaEl).then(resolve);
                    }, 300);
                });
            }

            return ClassicEditor
                .create(editorEl, {
                    toolbar: [
                        'bold', 'italic', 'underline', 'strikethrough',
                        '|', 'heading', '|',
                        'bulletedList', 'numberedList', 'outdent', 'indent',
                        '|', 'alignment', '|',
                        'link', 'imageUpload', 'insertTable', 'blockQuote',
                        '|', 'codeBlock', 'sourceEditing',
                        '|', 'undo', 'redo'
                    ],
                    heading: {
                        options: [
                            { model: 'paragraph', title: 'Paragraph', class: 'ck-heading_paragraph' },
                            { model: 'heading1', view: 'h1', title: 'Heading 1', class: 'ck-heading_heading1' },
                            { model: 'heading2', view: 'h2', title: 'Heading 2', class: 'ck-heading_heading2' },
                            { model: 'heading3', view: 'h3', title: 'Heading 3', class: 'ck_heading_heading3' }
                        ]
                    },
                    image: {
                        toolbar: [
                            'imageTextAlternative',
                            'imageStyle:full',
                            'imageStyle:side'
                        ]
                    },
                    table: {
                        contentToolbar: [
                            'tableColumn',
                            'tableRow',
                            'mergeTableCells',
                            'tableCellProperties',
                            'tableProperties'
                        ]
                    },
                    language: 'en',
                    height: '400px',
                    placeholder: 'Start typing your lesson content...'
                })
                .then(editor => {
                    // Set initial content
                    if (textareaEl.value) {
                        editor.setData(textareaEl.value);
                    }

                    // Store editor instance
                    const editorInstance = {
                        id: editorId,
                        element: editorEl,
                        textarea: textareaEl,
                        ckeditor: editor
                    };

                    window.lessonCKEditors.set(editorId, editorInstance);

                    // Setup content synchronization
                    setupCKEditorHandlers(editorInstance);

                    console.log('KTLessonModals: CKEditor 5 initialized successfully');
                    return editorInstance;
                })
                .catch(error => {
                    console.error('KTLessonModals: CKEditor 5 initialization error:', error);
                    return null;
                });
        }

        /**
         * Setup CKEditor 5 event handlers
         */
        function setupCKEditorHandlers(editorInstance) {
            const { ckeditor, textarea } = editorInstance;

            // Sync content to textarea on change
            ckeditor.model.document.on('change:data', () => {
                const content = ckeditor.getData();
                textarea.value = content;
            });

            // Handle blur for additional syncing
            ckeditor.editing.view.document.on('blur', () => {
                const content = ckeditor.getData();
                textarea.value = content;
            });
        }

        /**
         * Destroy CKEditor 5 instance
         */
        function destroyTextEditor(editorId) {
            const instance = window.lessonCKEditors.get(editorId);
            if (instance && instance.ckeditor) {
                return instance.ckeditor.destroy().then(() => {
                    window.lessonCKEditors.delete(editorId);
                    instance.element.innerHTML = '';
                }).catch(error => {
                    console.error('KTLessonModals: Error destroying CKEditor 5:', error);
                    window.lessonCKEditors.delete(editorId);
                    instance.element.innerHTML = '';
                });
            }
            return Promise.resolve();
        }

        /**
         * Handle content type changes
         */
        document.addEventListener('change', function(e) {
            const select = e.target;
            if (!select.matches('select[name="content_type"]')) return;

            const form = select.closest('form');
            if (!form || !form.action || !form.action.includes('/lessons')) return;

            if (select.value === 'text_html') {
                const contentField = form.querySelector('.content-field[data-type="text_html"]');
                if (!contentField) return;

                contentField.style.display = 'block';

                const editorEl = contentField.querySelector('[id^="kt_lesson_content_editor_"]');
                const textareaEl = contentField.querySelector('[id^="kt_lesson_content_textarea_"]');

                if (editorEl && textareaEl) {
                    // Wait a bit longer to ensure the field is visible and DOM is ready
                    setTimeout(() => {
                        // Check if ClassicEditor is available, if not load it first
                        if (typeof ClassicEditor === 'undefined') {
                            loadCKEditor(() => {
                                window.initLessonTextEditor(editorEl.id, textareaEl.id);
                            });
                        } else {
                            window.initLessonTextEditor(editorEl.id, textareaEl.id);
                        }
                    }, 200);
                }
            } else {
                // Destroy editor when switching away from text_html
                const contentField = form.querySelector('.content-field[data-type="text_html"]');
                if (contentField) {
                    const editorEl = contentField.querySelector('[id^="kt_lesson_content_editor_"]');
                    if (editorEl) {
                        destroyTextEditor(editorEl.id);
                    }
                }
            }
        });

        /**
         * Handle modal shown
         */
        document.addEventListener('shown.bs.modal', function(e) {
            const modal = e.target;
            if (!modal.id || (!modal.id.startsWith('kt_modal_add_lesson_') && !modal.id.startsWith('kt_modal_edit_lesson_'))) return;

            const form = modal.querySelector('form');
            if (!form || !form.action || !form.action.includes('/lessons')) return;

            const contentType = form.querySelector('[name="content_type"]')?.value;
            if (contentType === 'text_html') {
                const contentField = form.querySelector('.content-field[data-type="text_html"]');
                if (!contentField) return;

                contentField.style.display = 'block';

                const editorEl = contentField.querySelector('[id^="kt_lesson_content_editor_"]');
                const textareaEl = contentField.querySelector('[id^="kt_lesson_content_textarea_"]');

                if (editorEl && textareaEl) {
                    setTimeout(() => {
                        const editor = window.initLessonTextEditor(editorEl.id, textareaEl.id);
                        // Content will be set in the CKEditor initialization
                    }, 200);
                }
            }
        });

        /**
         * Handle modal hidden
         */
        document.addEventListener('hidden.bs.modal', function(e) {
            const modal = e.target;
            if (!modal.id || (!modal.id.startsWith('kt_modal_add_lesson_') && !modal.id.startsWith('kt_modal_edit_lesson_'))) return;

            const editorEl = modal.querySelector('[id^="kt_lesson_content_editor_"]');
            if (editorEl) {
                destroyTextEditor(editorEl.id);
            }
        });

        /**
         * Handle form submission
         */
        document.addEventListener('submit', function(e) {
            const form = e.target;
            if (!form.matches('.ajax-modal-form') || !form.action.includes('/lessons')) return;

            const textareaEl = form.querySelector('[id^="kt_lesson_content_textarea_"]');
            const editorEl = form.querySelector('[id^="kt_lesson_content_editor_"]');

            if (textareaEl && editorEl) {
                const editorInstance = window.lessonCKEditors.get(editorEl.id);
                if (editorInstance && editorInstance.ckeditor) {
                    // Get content from CKEditor and sync to textarea
                    const content = editorInstance.ckeditor.getData();
                    textareaEl.value = content;
                }
            }
        }, true);
    };

    /**
     * Populate edit lesson modal with existing data
     *
     * @param {string} modalId
     * @param {Object} lessonData
     */
    var populateEditModal = (modalId, lessonData) => {
        const modal = document.getElementById(modalId);
        if (!modal) return;

        const form = modal.querySelector('form');
        if (!form) return;

        // Store original content type for change detection
        modal.dataset.originalContentType = lessonData.content_type;

        // Populate basic fields
        const titleInput = form.querySelector('[name="title"]');
        if (titleInput) titleInput.value = lessonData.title;

        const statusSelect = form.querySelector('[name="status"]');
        if (statusSelect) statusSelect.value = lessonData.status;

        const durationInput = form.querySelector('[name="estimated_duration"]');
        if (durationInput) durationInput.value = lessonData.estimated_duration || '';

        // Set content type and trigger change
        const contentTypeSelect = form.querySelector('[name="content_type"]');
        if (contentTypeSelect) {
            contentTypeSelect.value = lessonData.content_type;
            contentTypeSelect.dispatchEvent(new Event('change', { bubbles: true }));
        }

        // Populate content fields based on type
        setTimeout(() => {
            switch (lessonData.content_type) {
                case 'text_html':
                    const contentTextarea = form.querySelector('[id^="kt_lesson_content_textarea_"]');
                    const contentEditor = form.querySelector('[id^="kt_lesson_content_editor_"]');
                    const contentField = form.querySelector('.content-field[data-type="text_html"]');

                    if (contentTextarea && contentEditor && contentField) {
                        // Ensure field is visible
                        contentField.style.display = 'block';

                        // Set textarea value
                        contentTextarea.value = lessonData.content || '';

                        // Wait for CKEditor 5 to be initialized, then set content
                        setTimeout(() => {
                            const editorInstance = window.lessonCKEditors?.get(contentEditor.id);
                            if (editorInstance && editorInstance.ckeditor) {
                                editorInstance.ckeditor.setData(lessonData.content || '');
                            }
                        }, 200);
                    }
                    break;

                case 'video':
                case 'external_link':
                    const urlInput = form.querySelector('.content-field[data-type="' + lessonData.content_type + '"] [name="content_url"]');
                    if (urlInput && lessonData.content_url) {
                        urlInput.value = lessonData.content_url;

                        // For video type, generate preview immediately
                        if (lessonData.content_type === 'video') {
                            const container = urlInput.closest('.content-field') || urlInput.closest('.fv-row');
                            const previewContainer = container?.querySelector('[id*="video_preview"], .video-preview');

                            if (previewContainer && typeof KTCourseModuleMain !== 'undefined' && KTCourseModuleMain.generateVideoEmbed) {
                                const embedHtml = KTCourseModuleMain.generateVideoEmbed(lessonData.content_url);
                                if (embedHtml) {
                                    previewContainer.innerHTML = embedHtml;
                                    previewContainer.style.display = 'block';
                                }
                            } else {
                                // Fallback: trigger blur event to use existing preview logic
                                urlInput.dispatchEvent(new Event('blur', { bubbles: true }));
                            }
                        } else {
                            // For external_link, just trigger blur
                            urlInput.dispatchEvent(new Event('blur', { bubbles: true }));
                        }
                    }

                    if (lessonData.content_type === 'external_link') {
                        const newTabCheckbox = form.querySelector('[name="open_new_tab"]');
                        if (newTabCheckbox) newTabCheckbox.checked = lessonData.open_new_tab;
                    }
                    break;

                case 'video_upload':
                    // Show current video file info if exists
                    const videoUploadPreview = form.querySelector('.content-field[data-type="video_upload"] .video-preview, .content-field[data-type="video_upload"] .alert');
                    if (videoUploadPreview && lessonData.content_file_name) {
                        // The preview is already shown in the Blade template, just ensure it's visible
                        const videoUploadField = form.querySelector('.content-field[data-type="video_upload"]');
                        if (videoUploadField) {
                            videoUploadField.style.display = 'block';
                        }
                    }
                    break;
                case 'pdf':
                    // Show current file info if exists
                    const pdfPreview = form.querySelector('[id*="pdf_preview"], .pdf-preview');
                    if (pdfPreview && lessonData.content_file_name) {
                        pdfPreview.innerHTML = `
                            <div class="d-flex align-items-center p-5 bg-light-primary rounded">
                                <i class="ki-duotone ki-file-added fs-3x text-primary me-4">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                                <div class="flex-grow-1">
                                    <span class="fw-bold text-gray-800 d-block">Current: ${lessonData.content_file_name}</span>
                                    <span class="text-muted fs-7">Upload a new file to replace</span>
                                </div>
                            </div>
                        `;
                        pdfPreview.style.display = 'block';
                    }
                    break;
            }
        }, 100);
    };

    /**
     * Reset lesson modal to default state
     *
     * @param {string} modalId
     */
    var resetModal = (modalId) => {
        const modal = document.getElementById(modalId);
        if (!modal) return;

        delete modal.dataset.originalContentType;

        const form = modal.querySelector('form');
        if (form) {
            form.reset();

            // Hide all content fields
            form.querySelectorAll('.content-field').forEach(field => {
                field.style.display = 'none';
            });

            // Hide confirmation section
            const confirmationSection = modal.querySelector('.content-change-confirmation, [data-content-change-confirmation]');
            if (confirmationSection) {
                confirmationSection.style.display = 'none';
            }

            // Clear previews
            form.querySelectorAll('[id*="video_preview"], .video-preview, [id*="pdf_preview"], .pdf-preview').forEach(preview => {
                preview.innerHTML = '';
                preview.style.display = 'none';
            });

            // Clear validation errors
            if (typeof KTCourseModuleMain !== 'undefined') {
                KTCourseModuleMain.clearValidationErrors(form);
            }
        }
    };

    /**
     * Reinitialize CKEditor 5 instances after content refresh
     * This is called after module content is refreshed via AJAX
     */
    var reinitializeQuillEditors = () => {
        console.log('KTLessonModals: Reinitializing CKEditor 5 instances...');

        // Clean up all existing CKEditor 5 instances
        if (window.lessonCKEditors) {
            console.log('KTLessonModals: Cleaning up existing CKEditor 5 instances:', Array.from(window.lessonCKEditors.keys()));

            const destroyPromises = [];
            window.lessonCKEditors.forEach((instance, editorId) => {
                if (instance.ckeditor) {
                    destroyPromises.push(
                        instance.ckeditor.destroy().catch(error => {
                            console.error('KTLessonModals: Error destroying CKEditor 5 during reinitialization:', error);
                        })
                    );
                }
            });

            // Wait for all editors to be destroyed
            Promise.all(destroyPromises).then(() => {
                window.lessonCKEditors.clear();
                console.log('KTLessonModals: CKEditor 5 reinitialization complete');
            }).catch(error => {
                console.error('KTLessonModals: Error during CKEditor 5 reinitialization:', error);
                // Clear instances anyway even if there were errors
                window.lessonCKEditors.clear();
            });
        } else {
            console.log('KTLessonModals: CKEditor 5 reinitialization complete');
        }
    };

    // Public API
    return {
        init: function () {
            initLessonModals();
        },

        populateEditModal: populateEditModal,
        resetModal: resetModal,
        reinitializeQuillEditors: reinitializeQuillEditors
    };
}();

    // Initialize on DOM ready
KTUtil.onDOMContentLoaded(function () {
    KTLessonModals.init();
    
    // Preload CKEditor when page loads to avoid delays
    if (typeof ClassicEditor === 'undefined' && !window.ckeditorLoading && !window.ckeditorLoaded) {
        const script = document.createElement('script');
        script.src = '/assets/plugins/custom/ckeditor/ckeditor-classic.bundle.js';
        script.async = true;
        script.onload = function() {
            window.ckeditorLoaded = true;
            console.log('KTLessonModals: CKEditor 5 preloaded successfully');
        };
        script.onerror = function() {
            console.warn('KTLessonModals: Failed to preload CKEditor 5, will load on demand');
        };
        document.head.appendChild(script);
    }
});