"use strict";

/**
 * KTContentReorder
 *
 * Handles content item drag-and-drop reordering within modules and toggle required functionality.
 *
 * @requires Bootstrap 5
 * @requires SweetAlert2
 */
var KTContentReorder = function () {

    /**
     * Initialize content reordering
     */
    var init = () => {
        initModuleContentReorder();
        initToggleRequiredButtons();
    };

    /**
     * Initialize drag-drop for module content lists
     */
    var initModuleContentReorder = () => {
        document.querySelectorAll('.module-content-list').forEach((list) => {
            const reorderUrl = list.dataset.reorderUrl;
            if (!reorderUrl) {
                return;
            }

            list.querySelectorAll('.content-item').forEach((item) => {
                item.setAttribute('draggable', true);
            });

            let draggedItem = null;

            list.addEventListener('dragstart', (event) => {
                if (!event.target.classList.contains('content-item')) {
                    return;
                }
                draggedItem = event.target;
                event.target.classList.add('dragging');
            });

            list.addEventListener('dragover', (event) => {
                event.preventDefault();
                const afterElement = getDragAfterContentItem(list, event.clientY);
                if (!draggedItem) {
                    return;
                }
                if (afterElement == null) {
                    list.appendChild(draggedItem);
                } else {
                    list.insertBefore(draggedItem, afterElement);
                }
            });

            list.addEventListener('dragend', () => {
                if (!draggedItem) {
                    return;
                }
                draggedItem.classList.remove('dragging');
                draggedItem = null;
                persistModuleContentOrder(list, reorderUrl);
            });
        });
    };

    /**
     * Get element to insert dragged item after
     */
    var getDragAfterContentItem = (container, y) => {
        const draggableElements = [...container.querySelectorAll('.content-item:not(.dragging)')];

        return draggableElements.reduce((closest, child) => {
            const box = child.getBoundingClientRect();
            const offset = y - box.top - box.height / 2;

            if (offset < 0 && offset > closest.offset) {
                return { offset, element: child };
            }

            return closest;
        }, { offset: Number.NEGATIVE_INFINITY }).element;
    };

    /**
     * Persist content order to server
     */
    var persistModuleContentOrder = async (list, reorderUrl) => {
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        if (!csrfToken) {
            return;
        }

        const payload = {
            items: Array.from(list.querySelectorAll('.content-item')).map((item, index) => ({
                id: item.dataset.itemId,
                order: index,
            })),
        };

        try {
            const response = await fetch(reorderUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                },
                body: JSON.stringify(payload),
            });

            const data = await response.json();
            if (data.success) {
                showToastMessage(data.message ?? 'Items reordered successfully', 'success');
            } else {
                throw new Error(data.message || 'Failed to reorder.');
            }
        } catch (error) {
            console.error(error);
            showToastMessage('Failed to reorder items. Please refresh the page.', 'error');
        }
    };

    /**
     * Initialize toggle required buttons using event delegation
     */
    var initToggleRequiredButtons = () => {
        document.addEventListener('click', async function(e) {
            const button = e.target.closest('.btn-toggle-required');
            if (!button) return;

            const url = button.dataset.toggleUrl;
            if (!url) return;

            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            if (!csrfToken) return;

            // Show loading state
            button.disabled = true;
            button.setAttribute('data-kt-indicator', 'on');

            try {
                const response = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                    },
                });
                const data = await response.json();

                if (data.success) {
                    button.dataset.required = data.is_required ? 1 : 0;
                    const contentItem = button.closest('.content-item');
                    const badge = contentItem?.querySelector('.required-badge');
                    if (badge) {
                        badge.classList.toggle('d-none', !data.is_required);
                    }
                    button.title = data.is_required ? 'Mark optional' : 'Mark required';
                    showToastMessage(data.message ?? 'Item updated successfully', 'success');
                } else {
                    throw new Error(data.message || 'Failed to update.');
                }
            } catch (error) {
                console.error(error);
                showToastMessage('Failed to update item. Please try again.', 'error');
            } finally {
                button.disabled = false;
                button.removeAttribute('data-kt-indicator');
            }
        });
    };

    /**
     * Update module item count badge
     */
    var updateModuleItemCount = (list) => {
        const count = list.querySelectorAll('.content-item').length;
        const badge = list.closest('.card')?.querySelector('.badge-light-primary');
        if (badge) {
            badge.textContent = count;
        }
        refreshSummaryCountsFromDom();
    };

    /**
     * Refresh summary counts from DOM
     */
    var refreshSummaryCountsFromDom = () => {
        const lessons = document.querySelectorAll('.content-item[data-type="ModuleLesson"]').length;
        const quizzes = document.querySelectorAll('.content-item[data-type="Quiz"]').length;
        const assignments = document.querySelectorAll('.content-item[data-type="Assignment"]').length;

        const lessonsEl = document.getElementById('totalLessonsCount');
        const quizzesEl = document.getElementById('totalQuizzesCount');
        const assignmentsEl = document.getElementById('totalAssignmentsCount');

        if (lessonsEl) lessonsEl.textContent = lessons;
        if (quizzesEl) quizzesEl.textContent = quizzes;
        if (assignmentsEl) assignmentsEl.textContent = assignments;
    };

    /**
     * Update summary counts from document
     */
    var updateSummaryCountsFromDoc = (doc) => {
        const ids = ['totalModulesCount', 'totalLessonsCount', 'totalQuizzesCount', 'totalAssignmentsCount'];
        ids.forEach(id => {
            const source = doc.getElementById(id);
            const target = document.getElementById(id);
            if (source && target) {
                target.textContent = source.textContent;
            }
        });
    };

    /**
     * Show toast message
     */
    var showToastMessage = (message, type = 'info') => {
        if (typeof window.showToast === 'function') {
            window.showToast(message, type);
        } else if (typeof Swal !== 'undefined') {
            Swal.fire({
                text: message,
                icon: type === 'error' ? 'error' : 'success',
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000
            });
        } else {
            alert(message);
        }
    };

    // Public API
    return {
        init: init,
        initModuleContentReorder: initModuleContentReorder,
        updateModuleItemCount: updateModuleItemCount,
        updateSummaryCountsFromDoc: updateSummaryCountsFromDoc,
        refreshSummaryCountsFromDom: refreshSummaryCountsFromDom
    };
}();

// Initialize on DOM ready
KTUtil.onDOMContentLoaded(function () {
    KTContentReorder.init();
});

// Make functions globally available for AJAX refresh callbacks
window.initModuleContentReorder = KTContentReorder.initModuleContentReorder;
window.initModuleContentActions = KTContentReorder.init;
window.updateSummaryCountsFromDoc = KTContentReorder.updateSummaryCountsFromDoc;
