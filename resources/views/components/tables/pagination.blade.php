{{--
/**
 * Table Pagination Component
 *
 * Clean pagination component with info text, per-page selector, and AJAX support.
 * Always shows entry info regardless of page count.
 *
 * @param object|null $paginator - Laravel paginator instance
 * @param string $containerId - Target container ID for AJAX updates (default: 'table-container')
 * @param bool $showInfo - Show "Showing X to Y of Z entries" text (default: true)
 * @param bool $showPerPage - Show per-page selector (default: true)
 * @param array $perPageOptions - Options for per-page dropdown (default: [10, 15, 25, 50])
 * @param bool $ajax - Enable AJAX pagination (default: true)
 * @param string $variant - Style variant: default|simple|minimal (default: default)
 *
 * @example Basic Usage
 * <x-tables.pagination :paginator="$programs" />
 *
 * @example Minimal Style
 * <x-tables.pagination :paginator="$users" variant="minimal" />
 */
--}}

@props([
    'paginator' => null,
    'containerId' => 'table-container',
    'showInfo' => true,
    'showPerPage' => true,
    'perPageOptions' => [10, 15, 25, 50],
    'ajax' => true,
    'variant' => 'default',
])

@if($paginator)
    @php
        $currentPerPage = request('per_page', $perPageOptions[0] ?? 15);
        $hasPages = $paginator->hasPages();
        $total = $paginator->total();
    @endphp

    <div class="d-flex flex-column flex-sm-row align-items-center justify-content-between gap-3 pt-4 border-top border-gray-200">
        {{-- Left: Info Text - Always visible --}}
        @if($showInfo)
            <div class="text-gray-600 fs-7 order-2 order-sm-1">
                @if($total > 0)
                    {{ __('Showing') }}
                    <span class="fw-semibold text-gray-800">{{ $paginator->firstItem() }}</span>
                    {{ __('to') }}
                    <span class="fw-semibold text-gray-800">{{ $paginator->lastItem() }}</span>
                    {{ __('of') }}
                    <span class="fw-semibold text-gray-800">{{ number_format($total) }}</span>
                    {{ __('entries') }}
                @else
                    <span class="text-gray-500">{{ __('No entries found') }}</span>
                @endif
            </div>
        @endif

        {{-- Center: Pagination Links --}}
        @if($hasPages)
            <nav class="d-flex align-items-center gap-1 order-1 order-sm-2" aria-label="Pagination">
                {{-- Previous --}}
                @if($paginator->onFirstPage())
                    <span class="btn btn-sm btn-icon btn-light disabled" aria-disabled="true">
                        <i class="ki-outline ki-left fs-6"></i>
                    </span>
                @else
                    <button type="button"
                            class="btn btn-sm btn-icon btn-light btn-active-primary"
                            data-page="{{ $paginator->currentPage() - 1 }}"
                            data-pagination-link
                            aria-label="{{ __('Previous') }}">
                        <i class="ki-outline ki-left fs-6"></i>
                    </button>
                @endif

                {{-- Page Numbers --}}
                @php
                    $currentPage = $paginator->currentPage();
                    $lastPage = $paginator->lastPage();
                    $range = 2; // Pages to show around current
                @endphp

                @for($page = 1; $page <= $lastPage; $page++)
                    @if($page == 1 || $page == $lastPage || ($page >= $currentPage - $range && $page <= $currentPage + $range))
                        @if($page == $currentPage)
                            <span class="btn btn-sm btn-primary fw-semibold min-w-35px">{{ $page }}</span>
                        @else
                            <button type="button"
                                    class="btn btn-sm btn-light btn-active-primary fw-semibold min-w-35px"
                                    data-page="{{ $page }}"
                                    data-pagination-link>
                                {{ $page }}
                            </button>
                        @endif
                    @elseif($page == $currentPage - $range - 1 || $page == $currentPage + $range + 1)
                        <span class="text-gray-400 px-1">...</span>
                    @endif
                @endfor

                {{-- Next --}}
                @if($paginator->hasMorePages())
                    <button type="button"
                            class="btn btn-sm btn-icon btn-light btn-active-primary"
                            data-page="{{ $paginator->currentPage() + 1 }}"
                            data-pagination-link
                            aria-label="{{ __('Next') }}">
                        <i class="ki-outline ki-right fs-6"></i>
                    </button>
                @else
                    <span class="btn btn-sm btn-icon btn-light disabled" aria-disabled="true">
                        <i class="ki-outline ki-right fs-6"></i>
                    </span>
                @endif
            </nav>
        @endif

        {{-- Right: Per Page Selector --}}
        @if($showPerPage)
            <div class="d-flex align-items-center gap-2 order-3">
                <select class="form-select form-select-sm form-select-solid w-70px"
                        data-per-page-selector
                        aria-label="{{ __('Items per page') }}">
                    @foreach($perPageOptions as $option)
                        <option value="{{ $option }}" {{ $currentPerPage == $option ? 'selected' : '' }}>
                            {{ $option }}
                        </option>
                    @endforeach
                </select>
                <span class="text-gray-500 fs-7 d-none d-md-inline">{{ __('per page') }}</span>
            </div>
        @endif
    </div>

    {{-- AJAX Pagination JavaScript --}}
    @if($ajax)
        @once
            @push('scripts')
                <script>
                    (function() {
                        'use strict';

                        class AjaxPagination {
                            constructor(containerId, options = {}) {
                                this.containerId = containerId;
                                this.options = {
                                    loadingText: '{{ __("Loading...") }}',
                                    fadeSpeed: 100,
                                    scrollToTop: true,
                                    scrollOffset: 100,
                                    updateUrl: true,
                                    onBeforeLoad: null,
                                    onAfterLoad: null,
                                    ...options
                                };

                                this.init();
                            }

                            init() {
                                const container = document.getElementById(this.containerId);
                                if (!container) return;

                                // Bind pagination links within container
                                container.querySelectorAll('[data-pagination-link]').forEach(link => {
                                    link.addEventListener('click', (e) => {
                                        e.preventDefault();
                                        this.loadPage(link.getAttribute('data-page'));
                                    });
                                });

                                // Bind per-page selector within container
                                const perPageSelector = container.querySelector('[data-per-page-selector]');
                                if (perPageSelector) {
                                    perPageSelector.addEventListener('change', (e) => {
                                        this.loadPage(1, parseInt(e.target.value));
                                    });
                                }
                            }

                            loadPage(page, perPage = null) {
                                const url = new URL(window.location.href);
                                url.searchParams.set('page', page);

                                if (perPage) {
                                    url.searchParams.set('per_page', perPage);
                                }

                                // Collect search/filter values from page
                                document.querySelectorAll('input[type="text"][name], input[type="search"][name]').forEach(input => {
                                    if (input.value) {
                                        url.searchParams.set(input.name, input.value);
                                    } else {
                                        url.searchParams.delete(input.name);
                                    }
                                });

                                document.querySelectorAll('select[name]:not([data-per-page-selector])').forEach(select => {
                                    if (select.value) {
                                        url.searchParams.set(select.name, select.value);
                                    } else {
                                        url.searchParams.delete(select.name);
                                    }
                                });

                                if (typeof this.options.onBeforeLoad === 'function') {
                                    this.options.onBeforeLoad();
                                }

                                this.showLoading();

                                fetch(url.toString(), {
                                    headers: {
                                        'X-Requested-With': 'XMLHttpRequest',
                                        'Accept': 'text/html'
                                    }
                                })
                                .then(response => response.text())
                                .then(html => {
                                    const parser = new DOMParser();
                                    const doc = parser.parseFromString(html, 'text/html');
                                    const newContent = doc.getElementById(this.containerId);
                                    const container = document.getElementById(this.containerId);

                                    if (newContent && container) {
                                        container.style.opacity = '0.5';

                                        setTimeout(() => {
                                            container.innerHTML = newContent.innerHTML;
                                            container.style.opacity = '1';

                                            // Re-initialize
                                            new AjaxPagination(this.containerId, this.options);

                                            if (this.options.updateUrl) {
                                                window.history.pushState({}, '', url.toString());
                                            }

                                            if (this.options.scrollToTop) {
                                                const offset = container.getBoundingClientRect().top + window.pageYOffset - this.options.scrollOffset;
                                                window.scrollTo({ top: Math.max(0, offset), behavior: 'smooth' });
                                            }

                                            if (typeof this.options.onAfterLoad === 'function') {
                                                this.options.onAfterLoad();
                                            }

                                            // Reinit Bootstrap components
                                            container.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(el => {
                                                new bootstrap.Tooltip(el);
                                            });
                                            container.querySelectorAll('[data-bs-toggle="dropdown"]').forEach(el => {
                                                new bootstrap.Dropdown(el);
                                            });
                                        }, this.options.fadeSpeed);
                                    }

                                    this.hideLoading();
                                })
                                .catch(error => {
                                    console.error('Pagination error:', error);
                                    this.hideLoading();
                                });
                            }

                            showLoading() {
                                const container = document.getElementById(this.containerId);
                                if (!container) return;

                                // Remove existing overlay
                                this.hideLoading();

                                const overlay = document.createElement('div');
                                overlay.className = 'pagination-loading-overlay';
                                overlay.innerHTML = `
                                    <div class="spinner-border spinner-border-sm text-primary" role="status">
                                        <span class="visually-hidden">${this.options.loadingText}</span>
                                    </div>
                                `;
                                overlay.style.cssText = 'position:absolute;top:0;left:0;right:0;bottom:0;background:rgba(255,255,255,0.7);display:flex;align-items:center;justify-content:center;z-index:5;';

                                container.style.position = 'relative';
                                container.appendChild(overlay);
                            }

                            hideLoading() {
                                document.querySelectorAll('.pagination-loading-overlay').forEach(el => el.remove());
                            }
                        }

                        // Auto-initialize
                        document.addEventListener('DOMContentLoaded', () => {
                            const container = document.getElementById('{{ $containerId }}');
                            if (container) {
                                window.paginationInstance = new AjaxPagination('{{ $containerId }}');
                            }
                        });

                        window.AjaxPagination = AjaxPagination;
                    })();
                </script>
            @endpush
        @endonce
    @endif
@endif

