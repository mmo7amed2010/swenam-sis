{{--
/**
 * Table Row Actions Component
 *
 * Standardized action buttons/dropdown for table rows with permission support.
 * Supports both inline buttons and dropdown menu modes.
 *
 * @param string|null $viewUrl - View action URL
 * @param string|null $editUrl - Edit action URL
 * @param string|null $deleteUrl - Delete action URL (triggers modal)
 * @param string|null $deleteModal - Delete modal ID (default: auto-generated)
 * @param string|null $viewPermission - Permission required for view
 * @param string|null $editPermission - Permission required for edit
 * @param string|null $deletePermission - Permission required for delete
 * @param string $mode - Display mode: inline|dropdown (default: inline)
 * @param string $size - Button size: sm|md (default: sm)
 * @param bool $confirmDelete - Show delete confirmation (default: true)
 * @param string|null $deleteMessage - Custom delete confirmation message
 * @param string|null $itemName - Item name for delete confirmation
 *
 * @slot default - Additional custom actions
 * @slot dropdownItems - Additional dropdown menu items
 *
 * @example Inline Mode (Default)
 * <x-tables.row-actions
 *     :viewUrl="route('courses.show', $course)"
 *     :editUrl="route('courses.edit', $course)"
 *     :deleteUrl="route('courses.destroy', $course)"
 *     editPermission="manage courses"
 *     deletePermission="manage courses"
 * />
 *
 * @example Dropdown Mode
 * <x-tables.row-actions
 *     :viewUrl="route('courses.show', $course)"
 *     :editUrl="route('courses.edit', $course)"
 *     :deleteUrl="route('courses.destroy', $course)"
 *     mode="dropdown"
 *     :itemName="$course->name"
 * >
 *     <x-slot:dropdownItems>
 *         <li><a class="dropdown-item" href="#">Duplicate</a></li>
 *         <li><a class="dropdown-item" href="#">Archive</a></li>
 *     </x-slot:dropdownItems>
 * </x-tables.row-actions>
 *
 * @example With Custom Actions
 * <x-tables.row-actions :viewUrl="route('courses.show', $course)">
 *     <a href="#" class="btn btn-sm btn-icon btn-light-info" title="Preview">
 *         <i class="ki-outline ki-eye fs-4"></i>
 *     </a>
 * </x-tables.row-actions>
 */
--}}

@props([
    'viewUrl' => null,
    'editUrl' => null,
    'deleteUrl' => null,
    'deleteModal' => null,
    'viewPermission' => null,
    'editPermission' => null,
    'deletePermission' => null,
    'mode' => 'inline',
    'size' => 'sm',
    'confirmDelete' => true,
    'deleteMessage' => null,
    'itemName' => null,
])

@php
    $uniqueId = 'delete_' . uniqid();
    $deleteModalId = $deleteModal ?? $uniqueId;

    $canView = $viewPermission ? auth()->check() && auth()->user()->can($viewPermission) : true;
    $canEdit = $editPermission ? auth()->check() && auth()->user()->can($editPermission) : true;
    $canDelete = $deletePermission ? auth()->check() && auth()->user()->can($deletePermission) : true;

    $btnSizeClass = $size === 'sm' ? 'btn-sm btn-icon' : 'btn-icon';
    $iconSize = $size === 'sm' ? 'fs-5' : 'fs-4';
@endphp

@if ($mode === 'dropdown')
    {{-- Dropdown Mode --}}
    <div class="dropdown" style="position: static;">
        <button class="btn btn-{{ $size }} btn-icon btn-light btn-active-light-primary"
                type="button"
                data-bs-toggle="dropdown"
                data-bs-boundary="viewport"
                aria-expanded="false">
            <i class="ki-outline ki-dots-vertical {{ $iconSize }}"></i>
        </button>
        <ul class="dropdown-menu dropdown-menu-end shadow-sm py-2" style="z-index: 1050;">
            {{-- View --}}
            @if ($viewUrl && $canView)
                <li>
                    <a class="dropdown-item d-flex align-items-center py-2" href="{{ $viewUrl }}">
                        <i class="ki-outline ki-eye fs-5 me-2 text-gray-500"></i>
                        {{ __('View') }}
                    </a>
                </li>
            @endif

            {{-- Edit --}}
            @if ($editUrl && $canEdit)
                <li>
                    <a class="dropdown-item d-flex align-items-center py-2" href="{{ $editUrl }}">
                        <i class="ki-outline ki-pencil fs-5 me-2 text-gray-500"></i>
                        {{ __('Edit') }}
                    </a>
                </li>
            @endif

            {{-- Custom Dropdown Items --}}
            @if (isset($dropdownItems))
                <li><hr class="dropdown-divider my-2"></li>
                {{ $dropdownItems }}
            @endif

            {{-- Delete --}}
            @if ($deleteUrl && $canDelete)
                <li><hr class="dropdown-divider my-2"></li>
                <li>
                    @if ($confirmDelete)
                        <button type="button"
                                class="dropdown-item d-flex align-items-center py-2 text-danger"
                                data-bs-toggle="modal"
                                data-bs-target="#{{ $deleteModalId }}">
                            <i class="ki-outline ki-trash fs-5 me-2"></i>
                            {{ __('Delete') }}
                        </button>
                    @else
                        <form action="{{ $deleteUrl }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="dropdown-item d-flex align-items-center py-2 text-danger">
                                <i class="ki-outline ki-trash fs-5 me-2"></i>
                                {{ __('Delete') }}
                            </button>
                        </form>
                    @endif
                </li>
            @endif
        </ul>
    </div>
@else
    {{-- Inline Mode --}}
    <div class="d-flex align-items-center gap-1 justify-content-end">
        {{-- Custom Actions (prepended) --}}
        {{ $slot }}

        {{-- View --}}
        @if ($viewUrl && $canView)
            <a href="{{ $viewUrl }}"
               class="btn {{ $btnSizeClass }} btn-light-primary"
               data-bs-toggle="tooltip"
               title="{{ __('View') }}">
                <i class="ki-outline ki-eye {{ $iconSize }}"></i>
            </a>
        @endif

        {{-- Edit --}}
        @if ($editUrl && $canEdit)
            <a href="{{ $editUrl }}"
               class="btn {{ $btnSizeClass }} btn-light-warning"
               data-bs-toggle="tooltip"
               title="{{ __('Edit') }}">
                <i class="ki-outline ki-pencil {{ $iconSize }}"></i>
            </a>
        @endif

        {{-- Delete --}}
        @if ($deleteUrl && $canDelete)
            @if ($confirmDelete)
                <button type="button"
                        class="btn {{ $btnSizeClass }} btn-light-danger"
                        data-bs-toggle="modal"
                        data-bs-target="#{{ $deleteModalId }}"
                        title="{{ __('Delete') }}">
                    <i class="ki-outline ki-trash {{ $iconSize }}"></i>
                </button>
            @else
                <form action="{{ $deleteUrl }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                            class="btn {{ $btnSizeClass }} btn-light-danger"
                            data-bs-toggle="tooltip"
                            title="{{ __('Delete') }}">
                        <i class="ki-outline ki-trash {{ $iconSize }}"></i>
                    </button>
                </form>
            @endif
        @endif
    </div>
@endif

{{-- Delete Confirmation Modal --}}
@if ($deleteUrl && $canDelete && $confirmDelete)
    <div class="modal fade" id="{{ $deleteModalId }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered mw-400px">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-body text-center py-10 px-10">
                    {{-- Icon --}}
                    <div class="symbol symbol-80px mb-5">
                        <span class="symbol-label bg-light-danger">
                            <i class="ki-outline ki-trash fs-2x text-danger"></i>
                        </span>
                    </div>

                    {{-- Title --}}
                    <h4 class="text-gray-800 fw-bold mb-3">{{ __('Delete Confirmation') }}</h4>

                    {{-- Message --}}
                    <p class="text-gray-600 fs-6 mb-8">
                        @if ($deleteMessage)
                            {{ $deleteMessage }}
                        @elseif ($itemName)
                            {{ __('Are you sure you want to delete') }}
                            <strong class="text-gray-800">"{{ $itemName }}"</strong>?
                            {{ __('This action cannot be undone.') }}
                        @else
                            {{ __('Are you sure you want to delete this item? This action cannot be undone.') }}
                        @endif
                    </p>

                    {{-- Actions --}}
                    <div class="d-flex justify-content-center gap-3">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                            {{ __('Cancel') }}
                        </button>
                        <form action="{{ $deleteUrl }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">
                                <i class="ki-outline ki-trash fs-5 me-1"></i>
                                {{ __('Delete') }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif
