{{--
/**
 * Reusable Export Dropdown Component
 *
 * Renders a Bootstrap dropdown button with Excel/CSV export options.
 * Dynamically builds the export URL from the current filter panel values.
 *
 * @param string $exportUrl - Base export URL (route without query params)
 * @param string $filterId - ID of the collapsible filter panel to read values from (default: 'applications-filters')
 * @param array  $extraFields - Additional hidden input name selectors to include (e.g. ['agent_id'])
 *
 * @example Basic usage
 * <x-tables.export-dropdown :export-url="route('admin.applications.export')" />
 *
 * @example With extra hidden fields
 * <x-tables.export-dropdown
 *     :export-url="route('admin.applications.export')"
 *     :extra-fields="['agent_id']"
 * />
 */
--}}

@props([
    'exportUrl',
    'filterId' => 'applications-filters',
    'extraFields' => [],
])

@php $uid = 'export-' . Str::random(6); @endphp

<div class="dropdown">
    <button type="button"
            class="btn btn-sm btn-light-success d-flex align-items-center gap-2"
            data-bs-toggle="dropdown"
            aria-expanded="false">
        <i class="ki-outline ki-file-down fs-4"></i>
        {{ __('Export') }}
    </button>
    <ul class="dropdown-menu dropdown-menu-end">
        <li>
            <a class="dropdown-item" href="#" data-export-format="xlsx" data-export-uid="{{ $uid }}">
                <i class="ki-outline ki-file fs-4 me-2"></i>
                {{ __('Export Excel (.xlsx)') }}
            </a>
        </li>
        <li>
            <a class="dropdown-item" href="#" data-export-format="csv" data-export-uid="{{ $uid }}">
                <i class="ki-outline ki-file fs-4 me-2"></i>
                {{ __('Export CSV (.csv)') }}
            </a>
        </li>
    </ul>
</div>

@once
    @push('scripts')
        <script>
            document.addEventListener('click', function(e) {
                var link = e.target.closest('[data-export-format]');
                if (!link) return;
                e.preventDefault();

                var format = link.getAttribute('data-export-format');
                var uid = link.getAttribute('data-export-uid');
                var container = document.querySelector('[data-export-container="' + uid + '"]');
                if (!container) return;

                var baseUrl = container.getAttribute('data-export-url');
                var filterId = container.getAttribute('data-filter-id');
                var extraFields = JSON.parse(container.getAttribute('data-extra-fields') || '[]');

                var params = new URLSearchParams();
                params.set('format', format);

                var filtersPanel = document.getElementById(filterId);
                if (filtersPanel) {
                    filtersPanel.querySelectorAll('select').forEach(function(s) {
                        if (s.name && s.value && s.value !== 'all') {
                            params.set(s.name, s.value);
                        }
                    });
                    filtersPanel.querySelectorAll('input[type="date"]').forEach(function(d) {
                        if (d.name && d.value) {
                            params.set(d.name, d.value);
                        }
                    });
                }

                extraFields.forEach(function(fieldName) {
                    var input = document.querySelector('input[name="' + fieldName + '"]');
                    if (input && input.value) {
                        params.set(fieldName, input.value);
                    }
                });

                window.location.href = baseUrl + '?' + params.toString();
            });
        </script>
    @endpush
@endonce

{{-- Hidden data container for JS --}}
<div class="d-none"
     data-export-container="{{ $uid }}"
     data-export-url="{{ $exportUrl }}"
     data-filter-id="{{ $filterId }}"
     data-extra-fields="{{ json_encode($extraFields) }}"></div>
