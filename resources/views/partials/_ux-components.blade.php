{{-- Loading Overlay Component --}}
<div class="loading-overlay" id="loadingOverlay" style="display: none;">
    <div class="d-flex flex-column align-items-center justify-content-center">
        <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;">
            <span class="visually-hidden">Loading...</span>
        </div>
        <p class="mt-3 text-gray-700 fw-semibold" id="loadingMessage">Processing your request...</p>
    </div>
</div>

{{-- Toast Notification Container --}}
<div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 11000;">
    {{-- Success Toast --}}
    <div id="successToast" class="toast align-items-center text-bg-success border-0" role="alert" aria-live="polite" aria-atomic="true">
        <div class="d-flex">
            <div class="toast-body">
                <i class="bi bi-check-circle-fill me-2"></i>
                <span id="successMessage"></span>
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
    </div>

    {{-- Error Toast --}}
    <div id="errorToast" class="toast align-items-center text-bg-danger border-0" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex">
            <div class="toast-body">
                <i class="bi bi-exclamation-circle-fill me-2"></i>
                <span id="errorMessage"></span>
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
    </div>

    {{-- Warning Toast --}}
    <div id="warningToast" class="toast align-items-center text-bg-warning border-0" role="alert" aria-live="polite" aria-atomic="true">
        <div class="d-flex">
            <div class="toast-body">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                <span id="warningMessage"></span>
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
    </div>

    {{-- Info Toast --}}
    <div id="infoToast" class="toast align-items-center text-bg-info border-0" role="alert" aria-live="polite" aria-atomic="true">
        <div class="d-flex">
            <div class="toast-body">
                <i class="bi bi-info-circle-fill me-2"></i>
                <span id="infoMessage"></span>
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
    </div>
</div>

{{-- Convert Laravel flash messages to toasts --}}
@if(session('success'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            showToast('{{ session('success') }}', 'success');
        });
    </script>
@endif

@if(session('error'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            showToast('{{ session('error') }}', 'error');
        });
    </script>
@endif

@if(session('warning'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            showToast('{{ session('warning') }}', 'warning');
        });
    </script>
@endif

@if(session('info'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            showToast('{{ session('info') }}', 'info');
        });
    </script>
@endif

@push('styles')
<style>
/* Loading Overlay Styles */
.loading-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(255, 255, 255, 0.95);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 10000;
    backdrop-filter: blur(2px);
}

/* Empty State Styles */
.empty-state {
    padding: 60px 20px;
    background: #f9fafb;
    border-radius: 12px;
    text-align: center;
}

.empty-state-icon {
    display: inline-block;
    padding: 30px;
    background: white;
    border-radius: 50%;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
    margin-bottom: 20px;
}

.empty-state h3 {
    color: #374151;
    font-weight: 600;
    margin-bottom: 12px;
}

.empty-state p {
    color: #6b7280;
    font-size: 1.05rem;
    line-height: 1.6;
}

/* Skeleton Loader */
.skeleton-loader {
    padding: 20px 0;
}

.skeleton-row {
    height: 50px;
    background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
    background-size: 200% 100%;
    animation: loading 1.5s infinite;
    margin-bottom: 10px;
    border-radius: 4px;
}

@keyframes loading {
    0% { background-position: 200% 0; }
    100% { background-position: -200% 0; }
}

/* Toast Enhancements */
.toast {
    min-width: 300px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

.toast-body {
    font-size: 0.95rem;
    padding: 12px 16px;
}

.toast .bi {
    font-size: 1.1rem;
}
</style>
@endpush

@push('scripts')
<script>
/**
 * Show toast notification
 * @param {string} message - Message to display
 * @param {string} type - Toast type: success, error, warning, info
 */
function showToast(message, type = 'success') {
    const toastEl = document.getElementById(type + 'Toast');
    const messageEl = document.getElementById(type + 'Message');

    if (!toastEl || !messageEl) {
        console.error('Toast element not found:', type);
        return;
    }

    messageEl.textContent = message;

    const toast = new bootstrap.Toast(toastEl, {
        animation: true,
        autohide: true,
        delay: 5000
    });

    toast.show();
}

/**
 * Show loading overlay
 * @param {string} message - Optional custom loading message
 */
function showLoading(message = 'Processing your request...') {
    const overlay = document.getElementById('loadingOverlay');
    const messageEl = document.getElementById('loadingMessage');

    if (overlay && messageEl) {
        messageEl.textContent = message;
        overlay.style.display = 'flex';
    }
}

/**
 * Hide loading overlay
 */
function hideLoading() {
    const overlay = document.getElementById('loadingOverlay');
    if (overlay) {
        overlay.style.display = 'none';
    }
}

/**
 * Enhanced form submission with loading state
 */
document.addEventListener('DOMContentLoaded', function() {
    // Add loading state to all forms with class 'loading-form'
    document.querySelectorAll('form.loading-form').forEach(form => {
        form.addEventListener('submit', function(e) {
            const submitBtn = this.querySelector('button[type="submit"]');
            if (submitBtn) {
                submitBtn.setAttribute('data-kt-indicator', 'on');
                submitBtn.disabled = true;
            }
            showLoading();
        });
    });

    // Add loading state to all delete forms
    document.querySelectorAll('form[method="POST"]').forEach(form => {
        if (form.querySelector('input[name="_method"][value="DELETE"]')) {
            form.addEventListener('submit', function(e) {
                // AJAX modal forms handle submit via JS (no navigation),
                // so the global overlay would get stuck. Skip these.
                if (form.classList.contains('ajax-modal-form') || form.closest('[data-ajax-modal="true"]')) {
                    return;
                }

                if (confirm('Are you sure you want to delete this item?')) {
                    showLoading('Deleting...');
                } else {
                    e.preventDefault();
                }
            });
        }
    });
});
</script>
@endpush
