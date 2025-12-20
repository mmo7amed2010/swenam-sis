{{--
 * Course Students Index Component (DataTable Version)
 *
 * Displays a list of course students with progress and server-side pagination.
 * Shared between admin and instructor views with context-aware routing.
 *
 * @param string $context - 'admin' or 'instructor'
 * @param \App\Models\Program $program
 * @param \App\Models\Course $course
 * @param array $stats - ['total' => int, 'started' => int, 'not_started' => int]
--}}

@props(['context', 'program', 'course', 'stats'])

@php
    $isAdmin = $context === 'admin';

    // Context-aware routes
    $routePrefix = $isAdmin ? 'admin.programs.courses' : 'instructor.courses';
    $studentsRoute = route("{$routePrefix}.students.index", [$program, $course]);
    $courseRoute = $isAdmin
        ? route('admin.programs.courses.show', [$program, $course])
        : route('instructor.courses.show', [$program, $course]);
@endphp

{{-- Header --}}
<div class="card mb-6">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-start">
            <div>
                <h1 class="fs-2 fw-bold mb-2">{{ __('Course Students') }}</h1>
                <div class="text-muted">
                    {{ __('View student progress for :course', ['course' => $course->title]) }}
                </div>
            </div>
            <a href="{{ $courseRoute }}" class="btn btn-light">
                <i class="fas fa-arrow-left me-1"></i>{{ __('Back to Course') }}
            </a>
        </div>

        {{-- Stats --}}
        <div class="d-flex flex-wrap pt-5">
            <div class="border border-gray-300 border-dashed rounded min-w-100px py-3 px-4 me-6">
                <div class="fs-2 fw-bold" data-stat="total">{{ $stats['total'] }}</div>
                <div class="fw-semibold fs-7 text-gray-500">{{ __('Total Students') }}</div>
            </div>
            <div class="border border-gray-300 border-dashed rounded min-w-100px py-3 px-4 me-6">
                <div class="fs-2 fw-bold text-success" data-stat="started">{{ $stats['started'] }}</div>
                <div class="fw-semibold fs-7 text-gray-500">{{ __('Started') }}</div>
            </div>
            <div class="border border-gray-300 border-dashed rounded min-w-100px py-3 px-4">
                <div class="fs-2 fw-bold text-warning" data-stat="not_started">{{ $stats['not_started'] }}</div>
                <div class="fw-semibold fs-7 text-gray-500">{{ __('Not Started') }}</div>
            </div>
        </div>
    </div>
</div>

{{-- Filters --}}
<div class="card mb-6">
    <div class="card-body">
        <div class="d-flex flex-wrap gap-3 align-items-center">
            <button type="button" class="btn btn-sm btn-light-primary" data-refresh-table>
                <i class="ki-outline ki-arrows-circle fs-5 me-1"></i>{{ __('Refresh') }}
            </button>
        </div>
    </div>
</div>

{{-- Students DataTable --}}
<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table id="students-table"
                   class="table align-middle table-row-dashed fs-6 gy-5"
                   data-ajax-url="{{ $studentsRoute }}"
                   data-page-length="15">
                <thead>
                    <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                        <th class="min-w-200px">{{ __('Student') }}</th>
                        <th class="min-w-150px">{{ __('Content Progress') }}</th>
                        <th class="min-w-100px text-center">{{ __('Assignments') }}</th>
                        <th class="min-w-100px text-center">{{ __('Quizzes') }}</th>
                        <th class="min-w-100px text-end">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody class="text-gray-600 fw-semibold">
                    {{-- Data loaded via AJAX --}}
                </tbody>
            </table>
        </div>

        <x-tables.datatable-footer />
    </div>
</div>

@push('scripts')
<script src="{{ asset('assets/js/custom/admin/tables/admin-datatable.js') }}"></script>
<script src="{{ asset('assets/js/custom/admin/tables/column-renderers.js') }}"></script>
<script src="{{ asset('assets/js/custom/courses/students-table.js') }}"></script>
@endpush
