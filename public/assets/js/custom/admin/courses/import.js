"use strict";

/**
 * KTCourseImport
 *
 * Handles CSV and ZIP import functionality for course content.
 *
 * @requires Bootstrap 5
 */
var KTCourseImport = function () {

    /**
     * Initialize import functionality
     */
    var init = () => {
        initCsvImport();
        initZipImport();
    };

    /**
     * Initialize CSV import form
     */
    var initCsvImport = () => {
        const form = document.getElementById('csvImportForm');
        if (!form) return;

        const csvImportUrl = form.dataset.importUrl;
        const csvProcessUrl = form.dataset.processUrl;

        form.addEventListener('submit', async function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            const submitBtn = this.querySelector('button[type="submit"]');
            submitBtn.setAttribute('data-kt-indicator', 'on');
            submitBtn.disabled = true;

            try {
                const response = await fetch(csvImportUrl, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                });

                const data = await response.json();

                if (data.success) {
                    // Show preview
                    const previewDiv = document.getElementById('csvPreview');
                    const previewContent = document.getElementById('csvPreviewContent');
                    previewDiv.classList.remove('d-none');

                    let html = `<p><strong>Total:</strong> ${data.preview.total_modules} modules, ${data.preview.total_lessons} lessons</p>`;
                    html += '<ul class="list-group">';
                    data.preview.modules.forEach(module => {
                        html += `<li class="list-group-item"><strong>${module.title}</strong> (${module.lessons.length} lessons)</li>`;
                    });
                    html += '</ul>';
                    previewContent.innerHTML = html;
                } else {
                    alert(data.message || 'Failed to process CSV file.');
                }
            } catch (error) {
                alert('An error occurred. Please try again.');
            } finally {
                submitBtn.removeAttribute('data-kt-indicator');
                submitBtn.disabled = false;
            }
        });

        // Confirm CSV Import
        const confirmBtn = document.getElementById('confirmCsvImport');
        if (confirmBtn) {
            confirmBtn.addEventListener('click', async function() {
                const btn = this;
                btn.setAttribute('data-kt-indicator', 'on');
                btn.disabled = true;

                try {
                    const response = await fetch(csvProcessUrl, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Content-Type': 'application/json',
                        },
                    });

                    const data = await response.json();

                    if (data.success) {
                        alert(data.message);
                        location.reload();
                    } else {
                        alert(data.message || 'Failed to import content.');
                    }
                } catch (error) {
                    alert('An error occurred. Please try again.');
                } finally {
                    btn.removeAttribute('data-kt-indicator');
                    btn.disabled = false;
                }
            });
        }
    };

    /**
     * Initialize ZIP import form
     */
    var initZipImport = () => {
        const form = document.getElementById('zipImportForm');
        if (!form) return;

        const zipImportUrl = form.dataset.importUrl;
        const zipProcessUrl = form.dataset.processUrl;

        form.addEventListener('submit', async function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            const submitBtn = this.querySelector('button[type="submit"]');
            submitBtn.setAttribute('data-kt-indicator', 'on');
            submitBtn.disabled = true;

            try {
                const response = await fetch(zipImportUrl, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                });

                const data = await response.json();

                if (data.success) {
                    const previewDiv = document.getElementById('zipPreview');
                    const previewContent = document.getElementById('zipPreviewContent');
                    previewDiv.classList.remove('d-none');

                    let html = `<p><strong>Total:</strong> ${data.preview.total_modules} modules, ${data.preview.total_lessons} lessons</p>`;
                    html += '<ul class="list-group">';
                    data.preview.modules.forEach(module => {
                        html += `<li class="list-group-item"><strong>${module.title}</strong> (${module.lessons.length} lessons)</li>`;
                    });
                    html += '</ul>';
                    previewContent.innerHTML = html;
                } else {
                    alert(data.message || 'Failed to process ZIP file.');
                }
            } catch (error) {
                alert('An error occurred. Please try again.');
            } finally {
                submitBtn.removeAttribute('data-kt-indicator');
                submitBtn.disabled = false;
            }
        });

        // Confirm ZIP Import
        const confirmBtn = document.getElementById('confirmZipImport');
        if (confirmBtn) {
            confirmBtn.addEventListener('click', async function() {
                const btn = this;
                btn.setAttribute('data-kt-indicator', 'on');
                btn.disabled = true;

                try {
                    const response = await fetch(zipProcessUrl, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Content-Type': 'application/json',
                        },
                    });

                    const data = await response.json();

                    if (data.success) {
                        alert(data.message);
                        location.reload();
                    } else {
                        alert(data.message || 'Failed to import content.');
                    }
                } catch (error) {
                    alert('An error occurred. Please try again.');
                } finally {
                    btn.removeAttribute('data-kt-indicator');
                    btn.disabled = false;
                }
            });
        }
    };

    // Public API
    return {
        init: init
    };
}();

// Initialize on DOM ready
KTUtil.onDOMContentLoaded(function () {
    KTCourseImport.init();
});
