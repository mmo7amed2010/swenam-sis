{{--
/**
 * DataTable Footer Component
 *
 * Provides info display and per-page selector for DataTables.
 * Works with AdminDataTable JavaScript class for automatic updates.
 *
 * @param array $perPageOptions - Available page size options (default: [10, 15, 25, 50])
 * @param int $defaultPerPage - Default selected page size (default: 15)
 * @param string $infoId - ID for info element (default: 'table-info')
 *
 * @example Basic Usage
 * <x-tables.datatable-footer />
 *
 * @example Custom Options
 * <x-tables.datatable-footer
 *     :per-page-options="[5, 10, 20, 50, 100]"
 *     :default-per-page="10"
 *     info-id="custom-info"
 * />
 */
--}}

@props([
    'perPageOptions' => [10, 15, 25, 50],
    'defaultPerPage' => 15,
    'infoId' => 'table-info'
])

<div class="d-flex flex-column flex-sm-row align-items-center justify-content-between gap-3 pt-4 border-top border-gray-200">
    {{-- Info Display (updated by AdminDataTable) --}}
    <div id="{{ $infoId }}" class="text-gray-600 fs-7 order-2 order-sm-1"></div>

    {{-- Per Page Selector --}}
    <div class="d-flex align-items-center gap-4 order-1 order-sm-2">
        <div class="d-flex align-items-center gap-2">
            <label class="text-gray-600 fs-7 text-nowrap">{{ __('Per page') }}:</label>
            <select name="per_page" class="form-select form-select-sm form-select-solid w-auto">
                @foreach($perPageOptions as $option)
                    <option value="{{ $option }}" @selected($option === $defaultPerPage)>{{ $option }}</option>
                @endforeach
            </select>
        </div>
    </div>
</div>
