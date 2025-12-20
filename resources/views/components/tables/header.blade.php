{{--
/**
 * Table Header Component
 *
 * Standardized table header row with consistent styling, sorting indicators,
 * and responsive column visibility options.
 *
 * @param array $columns - Column definitions [key => label] or [key => ['label' => '', 'sortable' => bool, 'class' => '']]
 * @param string|null $sortBy - Current sort column
 * @param string $sortDir - Current sort direction: asc|desc (default: asc)
 * @param bool $selectable - Show select-all checkbox (default: false)
 * @param string $variant - Style variant: default|primary|dark|minimal (default: default)
 *
 * @example Basic Header
 * <x-tables.header :columns="[
 *     'name' => 'Name',
 *     'email' => 'Email',
 *     'status' => 'Status',
 *     'actions' => ['label' => 'Actions', 'class' => 'text-end'],
 * ]" />
 *
 * @example With Sorting
 * <x-tables.header
 *     :columns="[
 *         'name' => ['label' => 'Name', 'sortable' => true],
 *         'created_at' => ['label' => 'Created', 'sortable' => true],
 *     ]"
 *     sort-by="name"
 *     sort-dir="asc"
 * />
 *
 * @example Selectable with Primary Variant
 * <x-tables.header
 *     :columns="$columns"
 *     :selectable="true"
 *     variant="primary"
 * />
 */
--}}

@props([
    'columns' => [],
    'sortBy' => null,
    'sortDir' => 'asc',
    'selectable' => false,
    'variant' => 'default',
])

@php
    $variants = [
        'default' => 'text-gray-500 fw-bold fs-7 text-uppercase',
        'primary' => 'bg-light-primary text-primary fw-bold fs-7 text-uppercase',
        'dark' => 'bg-gray-900 text-white fw-bold fs-7 text-uppercase',
        'minimal' => 'text-gray-400 fw-semibold fs-8 text-uppercase border-bottom border-gray-200',
    ];
    $headerClass = $variants[$variant] ?? $variants['default'];

    // Add rounded corners for primary/dark variants
    $roundedClass = in_array($variant, ['primary', 'dark']) ? 'rounded' : '';
@endphp

<thead>
    <tr class="{{ $headerClass }} {{ $roundedClass }} gs-0">
        {{-- Select All Checkbox --}}
        @if ($selectable)
            <th class="w-25px ps-4 {{ $variant === 'primary' ? 'rounded-start' : '' }}">
                <div class="form-check form-check-sm form-check-custom form-check-solid">
                    <input class="form-check-input" type="checkbox" data-select-all />
                </div>
            </th>
        @endif

        {{-- Column Headers --}}
        @foreach ($columns as $key => $column)
            @php
                // Normalize column config
                $label = is_array($column) ? ($column['label'] ?? $key) : $column;
                $sortable = is_array($column) ? ($column['sortable'] ?? false) : false;
                $class = is_array($column) ? ($column['class'] ?? '') : '';
                $width = is_array($column) ? ($column['width'] ?? null) : null;
                $hidden = is_array($column) ? ($column['hidden'] ?? null) : null;

                // Sorting state
                $isSorted = $sortBy === $key;
                $nextDir = $isSorted && $sortDir === 'asc' ? 'desc' : 'asc';

                // Build th classes
                $thClasses = $class;
                if ($loop->first && ! $selectable && $variant === 'primary') {
                    $thClasses .= ' rounded-start ps-4';
                }
                if ($loop->last && $variant === 'primary') {
                    $thClasses .= ' rounded-end pe-4';
                }
                if ($hidden) {
                    $thClasses .= " d-none d-{$hidden}-table-cell";
                }
            @endphp

            <th class="{{ $thClasses }}" @if($width) style="width: {{ $width }};" @endif>
                @if ($sortable)
                    <a href="{{ request()->fullUrlWithQuery(['sort' => $key, 'dir' => $nextDir]) }}"
                       class="d-flex align-items-center text-hover-primary {{ $isSorted ? 'text-primary' : '' }}"
                       style="text-decoration: none;">
                        {{ $label }}
                        <span class="ms-1">
                            @if ($isSorted)
                                @if ($sortDir === 'asc')
                                    <i class="ki-outline ki-arrow-up fs-7"></i>
                                @else
                                    <i class="ki-outline ki-arrow-down fs-7"></i>
                                @endif
                            @else
                                <i class="ki-outline ki-arrow-up-down fs-8 text-gray-400"></i>
                            @endif
                        </span>
                    </a>
                @else
                    {{ $label }}
                @endif
            </th>
        @endforeach
    </tr>
</thead>

{{-- JavaScript for Select All --}}
@if ($selectable)
    @once
        @push('scripts')
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const selectAll = document.querySelector('[data-select-all]');
                    if (selectAll) {
                        selectAll.addEventListener('change', function() {
                            const checkboxes = document.querySelectorAll('[data-select-row]');
                            checkboxes.forEach(cb => cb.checked = this.checked);

                            // Trigger bulk actions visibility
                            const bulkActions = document.querySelector('[data-bulk-actions]');
                            if (bulkActions) {
                                const selectedCount = document.querySelectorAll('[data-select-row]:checked').length;
                                bulkActions.classList.toggle('d-none', selectedCount === 0);
                                bulkActions.classList.toggle('d-flex', selectedCount > 0);
                                bulkActions.querySelector('[data-selected-count]').textContent = selectedCount;
                            }
                        });
                    }
                });
            </script>
        @endpush
    @endonce
@endif
