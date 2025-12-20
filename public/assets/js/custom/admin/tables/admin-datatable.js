"use strict";

/**
 * AdminDataTable - Reusable DataTables wrapper for admin index pages
 *
 * Provides server-side processing with search, filtering, and pagination.
 * Works with HandlesDataTableRequests PHP trait and table components.
 *
 * Features:
 * - Automatic filter binding from select elements
 * - Built-in info display support
 * - Automatic refresh button binding
 * - Bootstrap component reinitialization
 * - Debounced search
 *
 * @example
 * new AdminDataTable('programs-table', {
 *     ajaxUrl: '/admin/programs',
 *     columns: [
 *         { data: 'name', render: ColumnRenderers.nameCell({ ... }) },
 *         { data: 'status', render: ColumnRenderers.statusBadge({ ... }) },
 *         { data: 'id', render: ColumnRenderers.dropdownActions({ ... }) }
 *     ],
 *     filters: {
 *         status: 'select[name="status"]'
 *     }
 * });
 */
class AdminDataTable {
    /**
     * Initialize AdminDataTable
     *
     * @param {string} tableId - ID of the table element (without #)
     * @param {Object} options - Configuration options
     * @param {string} options.ajaxUrl - URL for AJAX data requests
     * @param {Array} options.columns - DataTables column definitions
     * @param {string} [options.searchInput] - Selector for search input
     * @param {Object} [options.filters] - Map of filter names to selectors
     * @param {string} [options.perPageSelect] - Selector for per-page select
     * @param {number} [options.defaultPageLength] - Default rows per page
     * @param {number} [options.searchDelay] - Debounce delay for search (ms)
     * @param {Object} [options.language] - DataTables language overrides
     * @param {Function} [options.onInit] - Callback after initialization
     * @param {Function} [options.onDraw] - Callback after each draw
     * @param {Function} [options.onAjaxComplete] - Callback after AJAX completes
     * @param {string} [options.infoElementId] - ID of custom info display element
     * @param {boolean} [options.autoBindRefresh] - Auto-bind refresh button (default: true)
     * @param {Object} [options.translations] - Translation strings for info display
     */
    constructor(tableId, options) {
        this.tableId = tableId;
        this.tableElement = null;
        this.table = null;
        this.searchTimeout = null;

        // Merge options with defaults
        this.options = Object.assign({
            ajaxUrl: '',
            columns: [],
            searchInput: 'input[name="search"]',
            filters: {},
            perPageSelect: 'select[name="per_page"]',
            defaultPageLength: 15,
            searchDelay: 400,
            language: {},
            onInit: null,
            onDraw: null,
            onAjaxComplete: null,
            order: [[0, 'desc']],
            responsive: true,
            stateSave: false,
            infoElementId: 'table-info',
            autoBindRefresh: true,
            translations: {
                showing: 'Showing',
                to: 'to',
                of: 'of',
                entries: 'entries',
                filteredFrom: 'filtered from',
                total: 'total',
                noRecords: 'No matching records found'
            }
        }, options);

        this.init();
    }

    /**
     * Initialize the DataTable
     */
    init() {
        const self = this;
        this.tableElement = document.getElementById(this.tableId);

        if (!this.tableElement) {
            console.error(`AdminDataTable: Table #${this.tableId} not found`);
            return;
        }

        // Validate columns configuration
        if (!Array.isArray(this.options.columns) || this.options.columns.length === 0) {
            console.error(`AdminDataTable: No columns defined for table #${this.tableId}`);
            return;
        }

        // Read data attributes from table element
        if (this.tableElement.dataset.ajaxUrl && !this.options.ajaxUrl) {
            this.options.ajaxUrl = this.tableElement.dataset.ajaxUrl;
        }
        if (this.tableElement.dataset.pageLength) {
            this.options.defaultPageLength = parseInt(this.tableElement.dataset.pageLength, 10);
        }

        // Default language settings
        const defaultLanguage = {
            processing: '<div class="d-flex align-items-center"><span class="spinner-border spinner-border-sm me-2"></span>Loading...</div>',
            emptyTable: 'No data available',
            zeroRecords: 'No matching records found',
            info: 'Showing _START_ to _END_ of _TOTAL_ entries',
            infoEmpty: 'Showing 0 to 0 of 0 entries',
            infoFiltered: '(filtered from _MAX_ total entries)',
            lengthMenu: 'Show _MENU_ entries',
            paginate: {
                first: '<i class="ki-outline ki-double-left fs-6"></i>',
                previous: '<i class="ki-outline ki-left fs-6"></i>',
                next: '<i class="ki-outline ki-right fs-6"></i>',
                last: '<i class="ki-outline ki-double-right fs-6"></i>'
            }
        };

        // Initialize DataTable
        this.table = $(this.tableElement).DataTable({
            processing: true,
            serverSide: true,
            autoWidth: false,
            deferRender: true,
            ajax: {
                url: this.options.ajaxUrl,
                type: 'GET',
                data: (d) => {
                    // Add custom filter parameters
                    Object.keys(this.options.filters).forEach(filterName => {
                        const selector = this.options.filters[filterName];
                        const element = document.querySelector(selector);
                        if (element) {
                            d[filterName] = element.value;
                        }
                    });
                    return d;
                },
                error: function(xhr, error, thrown) {
                    console.error('DataTable AJAX error:', error, thrown);
                }
            },
            columns: this.options.columns,
            order: this.options.order,
            pageLength: this.options.defaultPageLength,
            lengthMenu: [[10, 15, 25, 50, 100], [10, 15, 25, 50, 100]],
            responsive: this.options.responsive,
            stateSave: this.options.stateSave,
            language: Object.assign(defaultLanguage, this.options.language),
            // Show table and pagination, hide built-in info (we have custom info display)
            dom: '<"row"<"col-sm-12"tr>><"row"<"col-sm-12"p>>',
            drawCallback: function() {
                // Re-initialize Bootstrap components
                self.initBootstrapComponents();

                // Call custom callback after a small delay to ensure data is processed
                if (typeof self.options.onDraw === 'function') {
                    setTimeout(() => {
                        self.options.onDraw(self.table);
                    }, 10);
                }
            },
            initComplete: function() {
                // Hide default search and length controls (we use custom ones)
                self.bindExternalControls();

                // Call custom callback
                if (typeof self.options.onInit === 'function') {
                    self.options.onInit(self.table);
                }
            }
        });

        // Listen for xhr event to update info after AJAX completes
        $(this.tableElement).on('xhr.dt', function(e, settings, json, xhr) {
            if (json) {
                // Update built-in info display
                self.updateInfoDisplay(json);

                // Call custom callback if provided
                if (typeof self.options.onAjaxComplete === 'function') {
                    self.options.onAjaxComplete(json, self.table);
                }
            }
        });

        // Bind refresh button if auto-bind is enabled
        if (this.options.autoBindRefresh) {
            this.bindRefreshButton();
        }
    }

    /**
     * Update the custom info display element
     *
     * @param {Object} json - DataTables AJAX response
     */
    updateInfoDisplay(json) {
        const infoEl = document.getElementById(this.options.infoElementId);
        if (!infoEl) return;

        const pageInfo = this.table.page.info();
        const start = pageInfo.start + 1;
        const end = Math.min(pageInfo.start + pageInfo.length, json.recordsFiltered);
        const t = this.options.translations;

        if (json.recordsFiltered > 0) {
            let infoText = `${t.showing} <span class="fw-semibold">${start}</span> ${t.to} <span class="fw-semibold">${end}</span> ${t.of} <span class="fw-semibold">${json.recordsFiltered.toLocaleString()}</span> ${t.entries}`;

            // Show filtered notice if searching
            if (json.recordsFiltered < json.recordsTotal) {
                infoText += ` <span class="text-muted">(${t.filteredFrom} ${json.recordsTotal.toLocaleString()} ${t.total})</span>`;
            }
            infoEl.innerHTML = infoText;
        } else {
            infoEl.innerHTML = `<span class="text-muted">${t.noRecords}</span>`;
        }
    }

    /**
     * Bind refresh button to table reload
     */
    bindRefreshButton() {
        const self = this;
        const refreshBtn = document.querySelector('[data-action="refresh"]');

        if (refreshBtn) {
            refreshBtn.addEventListener('click', function(e) {
                e.preventDefault();
                self.reload();
            });
        }
    }

    /**
     * Bind external search, filter, and per-page controls
     */
    bindExternalControls() {
        const self = this;

        // Bind search input with debounce
        const searchInput = document.querySelector(this.options.searchInput);
        if (searchInput) {
            searchInput.addEventListener('input', function() {
                clearTimeout(self.searchTimeout);
                self.searchTimeout = setTimeout(() => {
                    self.table.search(this.value).draw();
                }, self.options.searchDelay);
            });

            // Handle Enter key
            searchInput.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    clearTimeout(self.searchTimeout);
                    self.table.search(this.value).draw();
                }
            });
        }

        // Bind filter changes
        Object.keys(this.options.filters).forEach(filterName => {
            const selector = this.options.filters[filterName];
            const element = document.querySelector(selector);
            if (element) {
                element.addEventListener('change', function() {
                    self.table.draw();
                });
            }
        });

        // Bind per-page selector
        const perPageSelect = document.querySelector(this.options.perPageSelect);
        if (perPageSelect) {
            perPageSelect.addEventListener('change', function() {
                self.table.page.len(parseInt(this.value, 10)).draw();
            });

            // Sync initial value
            perPageSelect.value = this.options.defaultPageLength;
        }
    }

    /**
     * Re-initialize Bootstrap components after DataTable redraw
     */
    initBootstrapComponents() {
        // Tooltips
        const tooltipElements = document.querySelectorAll('[data-bs-toggle="tooltip"]');
        tooltipElements.forEach(el => {
            const existing = bootstrap.Tooltip.getInstance(el);
            if (existing) existing.dispose();
            new bootstrap.Tooltip(el);
        });

        // Dropdowns
        const dropdownElements = document.querySelectorAll('[data-bs-toggle="dropdown"]');
        dropdownElements.forEach(el => {
            new bootstrap.Dropdown(el);
        });

        // Popovers
        const popoverElements = document.querySelectorAll('[data-bs-toggle="popover"]');
        popoverElements.forEach(el => {
            const existing = bootstrap.Popover.getInstance(el);
            if (existing) existing.dispose();
            new bootstrap.Popover(el);
        });
    }

    /**
     * Reload table data
     *
     * @param {boolean} [resetPaging=false] - Whether to reset to first page
     */
    reload(resetPaging = false) {
        if (this.table) {
            this.table.ajax.reload(null, resetPaging);
        }
    }

    /**
     * Get the underlying DataTable instance
     *
     * @returns {Object} DataTable instance
     */
    getInstance() {
        return this.table;
    }

    /**
     * Destroy the DataTable instance
     */
    destroy() {
        if (this.table) {
            this.table.destroy();
            this.table = null;
        }
    }

    /**
     * Clear search and filters
     */
    clearFilters() {
        // Clear search input
        const searchInput = document.querySelector(this.options.searchInput);
        if (searchInput) {
            searchInput.value = '';
        }

        // Reset filters to default
        Object.keys(this.options.filters).forEach(filterName => {
            const selector = this.options.filters[filterName];
            const element = document.querySelector(selector);
            if (element) {
                element.value = '';
            }
        });

        // Reset DataTable search and reload
        if (this.table) {
            this.table.search('').draw();
        }
    }
}

// Export for global access
window.AdminDataTable = AdminDataTable;
