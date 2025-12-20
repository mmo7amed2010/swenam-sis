"use strict";

/**
 * ColumnRenderers - Reusable DataTables column rendering functions
 *
 * Provides configurable renderer factories for common column patterns.
 * Each renderer returns a function compatible with DataTables render callback.
 *
 * @example
 * new AdminDataTable('table-id', {
 *     columns: [
 *         {
 *             data: 'name',
 *             render: ColumnRenderers.nameCell({
 *                 icon: 'abstract-26',
 *                 nameField: 'name',
 *                 urlField: 'show_url',
 *                 subtitleField: 'created_at',
 *                 statusField: 'is_active'
 *             })
 *         }
 *     ]
 * });
 */
window.ColumnRenderers = {

    /**
     * Name cell with icon, link, and optional subtitle
     *
     * @param {Object} config - Renderer configuration
     * @param {string} config.icon - Keenicons icon name (default: 'abstract-26')
     * @param {string} config.nameField - Field containing the display name
     * @param {string} config.urlField - Field containing the link URL
     * @param {string} [config.subtitleField] - Optional field for subtitle text
     * @param {string} [config.statusField] - Optional boolean field for status-based coloring
     * @param {string} [config.subtitlePrefix] - Optional prefix for subtitle (e.g., 'Created')
     * @returns {Function} DataTables render function
     */
    nameCell: function(config) {
        return function(data, type, row) {
            const icon = config.icon || 'abstract-26';
            const name = row[config.nameField] || '';
            const url = row[config.urlField] || '#';
            const subtitle = config.subtitleField ? row[config.subtitleField] : '';
            const subtitlePrefix = config.subtitlePrefix || '';

            // Determine color based on status field or default to primary
            let statusClass = 'primary';
            if (config.statusField !== undefined) {
                statusClass = row[config.statusField] ? 'success' : 'secondary';
            }

            // Build subtitle HTML if provided
            let subtitleHtml = '';
            if (subtitle) {
                subtitleHtml = `<span class="text-gray-500 fs-7">${subtitlePrefix ? subtitlePrefix + ' ' : ''}${subtitle}</span>`;
            }

            return `<div class="d-flex align-items-center">
                <div class="symbol symbol-45px me-3">
                    <span class="symbol-label bg-light-${statusClass}">
                        <i class="ki-outline ki-${icon} fs-4 text-${statusClass}"></i>
                    </span>
                </div>
                <div class="d-flex flex-column">
                    <a href="${url}" class="text-gray-800 text-hover-primary fs-6 fw-bold mb-1">
                        ${ColumnRenderers._escapeHtml(name)}
                    </a>
                    ${subtitleHtml}
                </div>
            </div>`;
        };
    },

    /**
     * Badge displaying a count with optional link
     *
     * @param {Object} config - Renderer configuration
     * @param {string} config.countField - Field containing the count value
     * @param {string} [config.urlField] - Optional field for link URL
     * @param {string} [config.icon] - Keenicons icon name (default: 'document')
     * @param {string} [config.label] - Label below the badge (default: 'items')
     * @param {string} [config.emptyLabel] - Text when count is 0 (default: 'None')
     * @param {string} [config.color] - Badge color variant (default: 'primary')
     * @returns {Function} DataTables render function
     */
    countBadge: function(config) {
        return function(data, type, row) {
            const count = parseInt(row[config.countField], 10) || 0;
            const icon = config.icon || 'document';
            const label = config.label || 'items';
            const emptyLabel = config.emptyLabel || 'None';
            const color = config.color || 'primary';

            if (count > 0) {
                const url = config.urlField ? row[config.urlField] : null;

                if (url) {
                    return `<div class="d-flex flex-column align-items-center">
                        <a href="${url}" class="badge badge-light-${color} fs-6 fw-bold py-2 px-4 mb-1">
                            <i class="ki-outline ki-${icon} fs-7 me-1"></i>${count.toLocaleString()}
                        </a>
                        <span class="text-gray-500 fs-8">${label}</span>
                    </div>`;
                }

                return `<div class="d-flex flex-column align-items-center">
                    <span class="badge badge-light-${color} fs-6 fw-bold py-2 px-4 mb-1">
                        <i class="ki-outline ki-${icon} fs-7 me-1"></i>${count.toLocaleString()}
                    </span>
                    <span class="text-gray-500 fs-8">${label}</span>
                </div>`;
            }

            return `<span class="badge badge-light-secondary fs-7 py-2 px-3">${emptyLabel}</span>`;
        };
    },

    /**
     * Status badge for boolean or string-based status fields
     *
     * @param {Object} config - Renderer configuration
     * @param {string} config.field - Field name containing status value
     * @param {Object} [config.statusMap] - Map of status values to config objects
     *   e.g., { 'active': { label: 'Active', color: 'success', icon: 'check-circle' } }
     * @param {string} [config.activeLabel] - Label for true state (default: 'Active') - used when statusMap not provided
     * @param {string} [config.inactiveLabel] - Label for false state (default: 'Inactive') - used when statusMap not provided
     * @param {string} [config.activeColor] - Color for true state (default: 'success') - used when statusMap not provided
     * @param {string} [config.inactiveColor] - Color for false state (default: 'secondary') - used when statusMap not provided
     * @param {string} [config.activeIcon] - Icon for true state (default: 'check-circle') - used when statusMap not provided
     * @param {string} [config.inactiveIcon] - Icon for false state (default: 'cross-circle') - used when statusMap not provided
     * @returns {Function} DataTables render function
     */
    statusBadge: function(config) {
        return function(data, type, row) {
            const statusValue = row[config.field];

            // If statusMap is provided, use it for string-based statuses
            if (config.statusMap && typeof statusValue === 'string') {
                const statusConfig = config.statusMap[statusValue];
                if (statusConfig) {
                    const label = statusConfig.label || statusValue;
                    const color = statusConfig.color || 'secondary';
                    const icon = statusConfig.icon || 'circle';

                    return `<span class="badge badge-light-${color} px-3 py-2 fs-7 fw-semibold">
                        <i class="ki-outline ki-${icon} fs-7 me-1"></i>${label}
                    </span>`;
                }
            }

            // Fall back to boolean behavior for backward compatibility
            const isActive = Boolean(statusValue);
            const activeLabel = config.activeLabel || 'Active';
            const inactiveLabel = config.inactiveLabel || 'Inactive';
            const activeColor = config.activeColor || 'success';
            const inactiveColor = config.inactiveColor || 'secondary';
            const activeIcon = config.activeIcon || 'check-circle';
            const inactiveIcon = config.inactiveIcon || 'cross-circle';

            const color = isActive ? activeColor : inactiveColor;
            const icon = isActive ? activeIcon : inactiveIcon;
            const label = isActive ? activeLabel : inactiveLabel;

            return `<span class="badge badge-light-${color} px-3 py-2 fs-7 fw-semibold">
                <i class="ki-outline ki-${icon} fs-7 me-1"></i>${label}
            </span>`;
        };
    },

    /**
     * Dropdown menu with action items
     *
     * @param {Object} config - Renderer configuration
     * @param {Array} config.items - Array of action item configurations
     * @param {string} [config.items[].type] - Item type: undefined (link), 'divider', or 'delete'
     * @param {string} [config.items[].icon] - Keenicons icon name
     * @param {string} [config.items[].label] - Menu item label
     * @param {string} [config.items[].urlField] - Field containing the action URL
     * @param {string} [config.items[].countField] - Optional field for badge count
     * @param {string} [config.items[].confirmMessage] - Custom confirmation message for delete
     * @returns {Function} DataTables render function
     */
    dropdownActions: function(config) {
        return function(data, type, row) {
            let items = '';

            config.items.forEach(item => {
                if (item.type === 'divider') {
                    items += '<li><hr class="dropdown-divider"></li>';
                    return;
                }

                const url = item.urlField ? row[item.urlField] : null;
                const dataAttrs = typeof item.dataAttributes === 'function'
                    ? item.dataAttributes(row)
                    : (item.dataAttributes || '');

                // Skip items without URL (except dividers)
                if (!url && item.type !== 'divider') {
                    // For delete items without URL, show disabled state
                    if (item.type === 'delete') {
                        items += `<li>
                            <span class="dropdown-item d-flex align-items-center py-2 text-muted"
                                  data-bs-toggle="tooltip"
                                  title="Cannot delete - has dependencies">
                                <i class="ki-outline ki-trash fs-5 me-2 text-gray-400"></i>
                                ${item.label || 'Delete'}
                                <i class="ki-outline ki-lock fs-7 ms-auto text-gray-400"></i>
                            </span>
                        </li>`;
                    }
                    return;
                }

                if (item.type === 'delete') {
                    const confirmMsg = item.confirmMessage || 'Are you sure you want to delete this item?';
                    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';

                    items += `<li>
                        <a class="dropdown-item d-flex align-items-center py-2 text-danger" href="#"
                           onclick="if(confirm('${ColumnRenderers._escapeJs(confirmMsg)}')) { document.getElementById('delete-form-${row.id}').submit(); } return false;">
                            <i class="ki-outline ki-trash fs-5 me-2"></i>
                            ${item.label || 'Delete'}
                        </a>
                        <form id="delete-form-${row.id}" action="${url}" method="POST" style="display:none;">
                            <input type="hidden" name="_token" value="${csrfToken}">
                            <input type="hidden" name="_method" value="DELETE">
                        </form>
                    </li>`;
                    return;
                }

                // Regular link item
                let badgeHtml = '';
                if (item.countField && row[item.countField] > 0) {
                    badgeHtml = `<span class="badge badge-light-primary ms-auto">${row[item.countField]}</span>`;
                }

                const href = item.preventNavigate ? '#' : (url || '#');

                items += `<li>
                    <a class="dropdown-item d-flex align-items-center py-2" href="${href}" ${dataAttrs}>
                        <i class="ki-outline ki-${item.icon || 'arrow-right'} fs-5 me-2 text-gray-500"></i>
                        ${item.label || 'Action'}
                        ${badgeHtml}
                    </a>
                </li>`;
            });

            return `<div class="dropdown" style="position: static;">
                <button class="btn btn-sm btn-icon btn-light btn-active-light-primary"
                        type="button"
                        data-bs-toggle="dropdown"
                        data-bs-boundary="viewport"
                        aria-expanded="false">
                    <i class="ki-outline ki-dots-vertical fs-5"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow-sm py-2" style="z-index: 1050;">
                    ${items}
                </ul>
            </div>`;
        };
    },

    /**
     * Date formatter with relative time option
     *
     * @param {Object} config - Renderer configuration
     * @param {string} [config.field] - Date field name (uses column data if not specified)
     * @param {string} [config.format] - 'relative', 'date', 'datetime' (default: 'date')
     * @param {string} [config.emptyText] - Text for null/empty values (default: '-')
     * @returns {Function} DataTables render function
     */
    dateFormat: function(config) {
        return function(data, type, row) {
            const value = config.field ? row[config.field] : data;
            const format = config.format || 'date';
            const emptyText = config.emptyText || '-';

            if (!value) {
                return `<span class="text-gray-400">${emptyText}</span>`;
            }

            // If already formatted as relative (from server), return as-is
            if (format === 'relative' && typeof value === 'string') {
                return `<span class="text-gray-600 fs-7">${value}</span>`;
            }

            try {
                const date = new Date(value);
                if (isNaN(date.getTime())) {
                    return `<span class="text-gray-600 fs-7">${value}</span>`;
                }

                const formatted = date.toLocaleDateString('en-US', {
                    year: 'numeric',
                    month: 'short',
                    day: 'numeric',
                    ...(format === 'datetime' ? { hour: '2-digit', minute: '2-digit' } : {})
                });

                return `<span class="text-gray-600 fs-7">${formatted}</span>`;
            } catch (e) {
                return `<span class="text-gray-600 fs-7">${value}</span>`;
            }
        };
    },

    /**
     * Progress bar with percentage
     *
     * @param {Object} config - Renderer configuration
     * @param {string} config.field - Field containing percentage value (0-100)
     * @param {boolean} [config.showLabel] - Show percentage label (default: true)
     * @param {string} [config.height] - Progress bar height (default: '6px')
     * @returns {Function} DataTables render function
     */
    progressBar: function(config) {
        return function(data, type, row) {
            const value = parseInt(row[config.field], 10) || 0;
            const showLabel = config.showLabel !== false;
            const height = config.height || '6px';

            // Determine color based on progress
            let color = 'primary';
            if (value >= 100) color = 'success';
            else if (value >= 75) color = 'info';
            else if (value >= 50) color = 'warning';
            else if (value > 0) color = 'danger';

            return `<div class="d-flex flex-column w-100">
                ${showLabel ? `<span class="text-gray-700 fs-7 fw-semibold mb-1">${value}%</span>` : ''}
                <div class="progress" style="height: ${height};">
                    <div class="progress-bar bg-${color}" role="progressbar" style="width: ${value}%"></div>
                </div>
            </div>`;
        };
    },

    /**
     * Avatar with optional name and subtitle
     *
     * @param {Object} config - Renderer configuration
     * @param {string} [config.avatarField] - Field containing avatar URL
     * @param {string} config.nameField - Field containing the name
     * @param {string} [config.urlField] - Field containing profile link
     * @param {string} [config.subtitleField] - Field for subtitle (e.g., email)
     * @param {string} [config.size] - Avatar size: sm, md, lg (default: 'md')
     * @returns {Function} DataTables render function
     */
    avatar: function(config) {
        return function(data, type, row) {
            const name = row[config.nameField] || '';
            const avatar = config.avatarField ? row[config.avatarField] : null;
            const url = config.urlField ? row[config.urlField] : null;
            const subtitle = config.subtitleField ? row[config.subtitleField] : '';

            const sizeClass = {
                'sm': 'symbol-35px',
                'md': 'symbol-45px',
                'lg': 'symbol-50px'
            }[config.size || 'md'] || 'symbol-45px';

            // Generate initials from name
            const initials = name.split(' ').map(n => n.charAt(0).toUpperCase()).slice(0, 2).join('');

            let avatarHtml;
            if (avatar) {
                avatarHtml = `<div class="symbol ${sizeClass} me-3">
                    <img src="${avatar}" alt="${ColumnRenderers._escapeHtml(name)}" />
                </div>`;
            } else {
                avatarHtml = `<div class="symbol ${sizeClass} me-3">
                    <span class="symbol-label bg-light-primary text-primary fs-6 fw-bold">
                        ${initials}
                    </span>
                </div>`;
            }

            const nameHtml = url
                ? `<a href="${url}" class="text-gray-800 text-hover-primary fs-6 fw-bold">${ColumnRenderers._escapeHtml(name)}</a>`
                : `<span class="text-gray-800 fs-6 fw-bold">${ColumnRenderers._escapeHtml(name)}</span>`;

            const subtitleHtml = subtitle
                ? `<span class="text-gray-500 fs-7">${ColumnRenderers._escapeHtml(subtitle)}</span>`
                : '';

            return `<div class="d-flex align-items-center">
                ${avatarHtml}
                <div class="d-flex flex-column">
                    ${nameHtml}
                    ${subtitleHtml}
                </div>
            </div>`;
        };
    },

    /**
     * Simple text with optional truncation
     *
     * @param {Object} config - Renderer configuration
     * @param {string} [config.field] - Field name (uses column data if not specified)
     * @param {number} [config.maxLength] - Maximum characters before truncation
     * @param {string} [config.emptyText] - Text for empty values (default: '-')
     * @param {string} [config.className] - Additional CSS classes
     * @returns {Function} DataTables render function
     */
    text: function(config) {
        return function(data, type, row) {
            let value = config.field ? row[config.field] : data;
            const maxLength = config.maxLength;
            const emptyText = config.emptyText || '-';
            const className = config.className || 'text-gray-700';

            if (!value && value !== 0) {
                return `<span class="text-gray-400">${emptyText}</span>`;
            }

            value = String(value);
            let displayValue = ColumnRenderers._escapeHtml(value);

            if (maxLength && value.length > maxLength) {
                displayValue = ColumnRenderers._escapeHtml(value.substring(0, maxLength)) + '...';
                return `<span class="${className}" title="${ColumnRenderers._escapeHtml(value)}">${displayValue}</span>`;
            }

            return `<span class="${className}">${displayValue}</span>`;
        };
    },

    // ========================================
    // Utility Methods
    // ========================================

    /**
     * Escape HTML entities
     * @param {string} str - String to escape
     * @returns {string} Escaped string
     */
    _escapeHtml: function(str) {
        if (!str) return '';
        const div = document.createElement('div');
        div.textContent = str;
        return div.innerHTML;
    },

    /**
     * Escape string for JavaScript context
     * @param {string} str - String to escape
     * @returns {string} Escaped string
     */
    _escapeJs: function(str) {
        if (!str) return '';
        return str.replace(/'/g, "\\'").replace(/"/g, '\\"');
    }
};
