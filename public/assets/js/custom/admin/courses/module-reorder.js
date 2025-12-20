"use strict";

/**
 * KTModuleReorder
 *
 * Handles module drag-and-drop reordering functionality.
 *
 * @requires Bootstrap 5
 */
var KTModuleReorder = function () {

    var modulesList = null;
    var saveOrderBtn = null;
    var draggedElement = null;
    var originalOrder = [];

    /**
     * Initialize module reordering
     */
    var init = () => {
        modulesList = document.getElementById('modulesList');
        saveOrderBtn = document.getElementById('saveModuleOrderBtn');

        if (!modulesList) return;

        // Store original order
        document.querySelectorAll('#modulesList .module-card').forEach((item) => {
            originalOrder.push(item.dataset.moduleId);
        });

        initDragEvents();
        initSaveButton();
    };

    /**
     * Initialize drag events
     */
    var initDragEvents = () => {
        // Drag start
        modulesList.addEventListener('dragstart', function(e) {
            if (e.target.classList.contains('module-card')) {
                draggedElement = e.target;
                e.target.classList.add('dragging');

                // Show drop zones on other items
                document.querySelectorAll('#modulesList .module-card:not(.dragging)').forEach(item => {
                    item.classList.add('drop-zone');
                });
            }
        });

        // Drag end
        modulesList.addEventListener('dragend', function(e) {
            if (e.target.classList.contains('module-card')) {
                e.target.classList.remove('dragging');

                // Remove drop zones
                document.querySelectorAll('.drop-zone').forEach(item => {
                    item.classList.remove('drop-zone');
                });

                // Remove drop indicator
                const indicator = document.querySelector('.drop-indicator');
                if (indicator) indicator.remove();

                // Check if order changed
                checkOrderChanged();
            }
        });

        // Drag over
        modulesList.addEventListener('dragover', function(e) {
            e.preventDefault();

            // Remove previous indicator
            const oldIndicator = document.querySelector('.drop-indicator');
            if (oldIndicator) oldIndicator.remove();

            const afterElement = getDragAfterElement(modulesList, e.clientY);

            // Create drop indicator
            const indicator = document.createElement('div');
            indicator.className = 'drop-indicator';
            indicator.innerHTML = '<div class="drop-indicator-line"></div><div class="drop-indicator-text">Drop here</div>';

            if (afterElement == null) {
                modulesList.appendChild(indicator);
                modulesList.appendChild(draggedElement);
            } else {
                modulesList.insertBefore(indicator, afterElement);
                modulesList.insertBefore(draggedElement, afterElement);
            }

            // Update order numbers
            updateOrderNumbers();
        });
    };

    /**
     * Get element to insert dragged item after
     */
    var getDragAfterElement = (container, y) => {
        const draggableElements = [...container.querySelectorAll('.module-card:not(.dragging)')];

        return draggableElements.reduce((closest, child) => {
            const box = child.getBoundingClientRect();
            const offset = y - box.top - box.height / 2;

            if (offset < 0 && offset > closest.offset) {
                return { offset: offset, element: child };
            } else {
                return closest;
            }
        }, { offset: Number.NEGATIVE_INFINITY }).element;
    };

    /**
     * Update order number badges
     */
    var updateOrderNumbers = () => {
        document.querySelectorAll('#modulesList .module-card').forEach((item, index) => {
            const badge = item.querySelector('.order-badge span, .order-badge');
            if (badge) {
                badge.textContent = index + 1;
            }
        });
    };

    /**
     * Check if order has changed and show/hide save button
     */
    var checkOrderChanged = () => {
        const currentOrder = Array.from(document.querySelectorAll('#modulesList .module-card')).map(item => item.dataset.moduleId);
        const orderChanged = !currentOrder.every((id, index) => id === originalOrder[index]);

        if (orderChanged && saveOrderBtn) {
            saveOrderBtn.classList.remove('d-none');
        } else if (saveOrderBtn) {
            saveOrderBtn.classList.add('d-none');
        }
    };

    /**
     * Initialize save button handler
     */
    var initSaveButton = () => {
        if (!saveOrderBtn) return;

        saveOrderBtn.addEventListener('click', async function() {
            const moduleIds = Array.from(document.querySelectorAll('#modulesList .module-card')).map(item => item.dataset.moduleId);
            const reorderUrl = saveOrderBtn.dataset.reorderUrl;

            if (!reorderUrl) {
                console.error('No reorder URL found');
                return;
            }

            // Disable button and show loading
            saveOrderBtn.disabled = true;
            const originalText = saveOrderBtn.innerHTML;
            saveOrderBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Saving...';

            // Show loading overlay if available
            if (typeof showLoading === 'function') {
                showLoading('Saving new module order...');
            }

            try {
                const response = await fetch(reorderUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ module_ids: moduleIds })
                });

                const data = await response.json();

                if (typeof hideLoading === 'function') {
                    hideLoading();
                }

                if (data.success) {
                    saveOrderBtn.classList.add('d-none');
                    saveOrderBtn.innerHTML = originalText;
                    saveOrderBtn.disabled = false;
                    originalOrder = moduleIds;

                    // Show success toast
                    if (typeof showToast === 'function') {
                        showToast('Module order updated successfully!', 'success');
                    } else if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            text: 'Module order updated successfully!',
                            icon: 'success',
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 3000
                        });
                    }

                    // Add success animation
                    document.querySelectorAll('#modulesList .module-card').forEach(item => {
                        item.style.animation = 'successPulse 0.5s ease-in-out';
                        setTimeout(() => item.style.animation = '', 500);
                    });
                } else {
                    throw new Error(data.message || 'Failed to update order');
                }
            } catch (error) {
                if (typeof hideLoading === 'function') {
                    hideLoading();
                }

                console.error('Error:', error);
                saveOrderBtn.innerHTML = originalText;
                saveOrderBtn.disabled = false;

                if (typeof showToast === 'function') {
                    showToast('Failed to update module order. Please try again.', 'error');
                } else {
                    alert('Failed to update module order. Please try again.');
                }
            }
        });
    };

    // Public API
    return {
        init: init
    };
}();

// Initialize on DOM ready
KTUtil.onDOMContentLoaded(function () {
    KTModuleReorder.init();
});
