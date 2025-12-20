{{--
/**
 * Enhanced Table Toolbar Component (AJAX-Compatible)
 *
 * Feature-rich toolbar with search, filters, view toggles, and actions.
 * Designed to work seamlessly with DataTables AJAX operations.
 *
 * @param string|null $searchPlaceholder - Search input placeholder
 * @param string $searchName - Search input name (default: search)
 * @param string|null $searchValue - Pre-filled search value
 * @param array|null $filters - Filter options [name => [options]] - renders as <select>
 * @param array|null $activeFilters - Currently active filters for badge display
 * @param bool $showViewToggle - Show grid/list view toggle (default: false)
 * @param string $currentView - Current view mode: list|grid (default: list)
 * @param bool $showRefresh - Show refresh button (default: false)
 * @param bool $showExport - Show export button (default: false)
 * @param string|null $exportUrl - Export action URL
 * @param string $variant - Style variant: default|compact|expanded (default: default)
 * @param bool $ajaxMode - Use AJAX instead of form submit (default: true)
 *
 * @slot actions - Primary action buttons (create, import, etc.)
 * @slot filters - Custom filter dropdowns
 * @slot bulkActions - Bulk action buttons (shown when items selected)
 *
 * @example Basic Search with Actions (AJAX)
 * <x-tables.toolbar search-placeholder="Search courses...">
 *     <x-slot:actions>
 *         <a href="{{ route('courses.create') }}" class="btn btn-primary">
 *             <i class="ki-outline ki-plus fs-2"></i> Create Course
 *         </a>
 *     </x-slot:actions>
 * </x-tables.toolbar>
 *
 * @example With Filters and View Toggle
 * <x-tables.toolbar
 *     search-placeholder="Search..."
 *     :filters="['status' => ['active', 'draft', 'archived']]"
 *     :show-view-toggle="true"
 *     :show-refresh="true"
 * >
 *     <x-slot:actions>...</x-slot:actions>
 * </x-tables.toolbar>
 */
--}}

@props([
    'searchPlaceholder' => null,
    'searchName' => 'search',
    'searchValue' => null,
    'filters' => null,
    'activeFilters' => null,
    'showViewToggle' => false,
    'currentView' => 'list',
    'showRefresh' => false,
    'showExport' => false,
    'exportUrl' => null,
    'variant' => 'default',
    'ajaxMode' => true,
])

@php
    $searchValue = $searchValue ?? request($searchName);
    $activeFilterCount = $activeFilters ? count(array_filter($activeFilters)) : 0;

    $variants = [
        'default' => ['gap' => 'gap-3', 'searchWidth' => 'w-250px'],
        'compact' => ['gap' => 'gap-2', 'searchWidth' => 'w-200px'],
        'expanded' => ['gap' => 'gap-4', 'searchWidth' => 'w-300px'],
    ];
    $variantConfig = $variants[$variant] ?? $variants['default'];
@endphp

<div class="d-flex flex-column flex-lg-row align-items-stretch align-items-lg-center justify-content-between {{ $variantConfig['gap'] }} w-100">
    {{-- Left Side: Search & Filters --}}
    <div class="d-flex flex-column flex-sm-row align-items-stretch align-items-sm-center {{ $variantConfig['gap'] }}">
        {{-- Search Input --}}
        @if ($searchPlaceholder)
            <div class="d-flex align-items-center position-relative">
                <i class="ki-outline ki-magnifier fs-3 position-absolute ms-4 text-gray-500">
                    <span class="path1"></span>
                    <span class="path2"></span>
                </i>
                <input type="text"
                       name="{{ $searchName }}"
                       class="form-control form-control-solid {{ $variantConfig['searchWidth'] }} ps-12 pe-10"
                       placeholder="{{ $searchPlaceholder }}"
                       value="{{ $searchValue }}"
                       data-kt-search="true"
                       autocomplete="off" />
                {{-- Clear Button (visible when search has value) --}}
                <button type="button"
                        class="btn btn-icon btn-sm btn-active-light-primary position-absolute end-0 me-2 {{ $searchValue ? '' : 'd-none' }}"
                        data-action="clear-search"
                        title="Clear search">
                    <i class="ki-outline ki-cross fs-4"></i>
                </button>
            </div>
        @endif

        {{-- Filter Controls --}}
        @if (isset($filtersSlot))
            {{ $filtersSlot }}
        @elseif ($filters)
            @foreach ($filters as $filterName => $filterOptions)
                <select name="{{ $filterName }}"
                        class="form-select form-select-sm form-select-solid w-auto"
                        data-filter="{{ $filterName }}">
                    <option value="">{{ ucfirst($filterName) }}: {{ __('All') }}</option>
                    @foreach ($filterOptions as $option)
                        <option value="{{ $option }}" @selected(request($filterName) === $option)>
                            {{ ucfirst($option) }}
                        </option>
                    @endforeach
                </select>
            @endforeach
        @endif

        {{-- Active Filter Badges (for non-AJAX pages) --}}
        @if ($activeFilterCount > 0 && !$ajaxMode)
            <div class="d-flex align-items-center gap-2 ms-2">
                @foreach ($activeFilters as $key => $value)
                    @if ($value)
                        <span class="badge badge-light-primary d-flex align-items-center gap-1 py-2 px-3">
                            {{ ucfirst($key) }}: {{ $value }}
                            <a href="{{ request()->fullUrlWithQuery([$key => null]) }}"
                               class="ms-1 text-primary text-hover-dark">
                                <i class="ki-outline ki-cross fs-7"></i>
                            </a>
                        </span>
                    @endif
                @endforeach
                <a href="{{ url()->current() }}" class="btn btn-sm btn-link text-danger">
                    Clear all
                </a>
            </div>
        @endif
    </div>

    {{-- Right Side: View Toggle, Refresh, Export & Actions --}}
    <div class="d-flex align-items-center {{ $variantConfig['gap'] }}">
        {{-- Bulk Actions (hidden by default, shown via JS when items selected) --}}
        @if (isset($bulkActions))
            <div class="bulk-actions d-none align-items-center gap-2" data-bulk-actions>
                <span class="text-gray-600 fs-7 fw-semibold me-2">
                    <span data-selected-count>0</span> selected
                </span>
                {{ $bulkActions }}
                <div class="separator separator-content border-gray-300 mx-3" style="height: 24px;"></div>
            </div>
        @endif

        {{-- View Toggle --}}
        @if ($showViewToggle)
            <div class="btn-group" role="group" aria-label="View toggle">
                <button type="button"
                        class="btn btn-sm btn-icon {{ $currentView === 'list' ? 'btn-primary' : 'btn-light' }}"
                        data-view="list"
                        title="List view">
                    <i class="ki-outline ki-row-horizontal fs-4"></i>
                </button>
                <button type="button"
                        class="btn btn-sm btn-icon {{ $currentView === 'grid' ? 'btn-primary' : 'btn-light' }}"
                        data-view="grid"
                        title="Grid view">
                    <i class="ki-outline ki-element-11 fs-4"></i>
                </button>
            </div>
        @endif

        {{-- Refresh Button --}}
        @if ($showRefresh)
            <button type="button"
                    class="btn btn-sm btn-icon btn-light-primary"
                    data-action="refresh"
                    title="{{ __('Refresh') }}">
                <i class="ki-outline ki-arrows-circle fs-4"></i>
            </button>
        @endif

        {{-- Export Button --}}
        @if ($showExport && $exportUrl)
            <a href="{{ $exportUrl }}"
               class="btn btn-sm btn-light-success d-flex align-items-center"
               title="Export data">
                <i class="ki-outline ki-exit-up fs-4 me-1"></i>
                <span class="d-none d-sm-inline">Export</span>
            </a>
        @endif

        {{-- Primary Actions --}}
        @if (isset($actions))
            <div class="d-flex align-items-center gap-2">
                {{ $actions }}
            </div>
        @endif
    </div>
</div>

{{-- Toolbar Scripts --}}
@once
    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Focus search on "/" key press
                document.addEventListener('keydown', function(e) {
                    if (e.key === '/' && !['INPUT', 'TEXTAREA'].includes(document.activeElement.tagName)) {
                        e.preventDefault();
                        document.querySelector('[data-kt-search="true"]')?.focus();
                    }
                });

                // Clear search button handler
                document.querySelectorAll('[data-action="clear-search"]').forEach(btn => {
                    btn.addEventListener('click', function() {
                        const wrapper = this.closest('.position-relative');
                        const input = wrapper?.querySelector('input[name]');
                        if (input) {
                            input.value = '';
                            input.dispatchEvent(new Event('input', { bubbles: true }));
                            this.classList.add('d-none');
                        }
                    });
                });

                // Show/hide clear button based on input value
                document.querySelectorAll('[data-kt-search="true"]').forEach(input => {
                    const clearBtn = input.parentElement?.querySelector('[data-action="clear-search"]');
                    if (clearBtn) {
                        input.addEventListener('input', function() {
                            clearBtn.classList.toggle('d-none', !this.value);
                        });
                    }
                });
            });
        </script>
    @endpush
@endonce
