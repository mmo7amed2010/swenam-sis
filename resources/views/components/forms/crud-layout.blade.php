{{--
    CRUD Layout Component

    Two-column responsive layout for CRUD forms with main content area and sidebar.

    Usage:
    <x-forms.crud-layout>
        <x-slot name="main">
            <!-- Main form content (cards, fields, etc.) -->
        </x-slot>

        <x-slot name="sidebar">
            <!-- Sidebar content (publish settings, meta info, etc.) -->
        </x-slot>
    </x-forms.crud-layout>

    Layout:
    - Mobile: Stacked (flex-column)
    - Desktop: Side-by-side (flex-lg-row)
    - Sidebar width: 300px on desktop, full width on mobile
    - Gap: 1.75rem (gap-7) between elements

    @see resources/views/pages/apps/courses/create.blade.php Original pattern
--}}
<div class="d-flex flex-column flex-lg-row gap-7 gap-lg-10">
    {{-- Main Content Column --}}
    <div class="d-flex flex-column flex-row-fluid gap-7 gap-lg-10">
        {{ $main }}
    </div>

    {{-- Sidebar Column --}}
    @isset($sidebar)
    <div class="d-flex flex-column gap-7 gap-lg-10 w-100 w-lg-300px mb-7 me-lg-10">
        {{ $sidebar }}
    </div>
    @endisset
</div>
