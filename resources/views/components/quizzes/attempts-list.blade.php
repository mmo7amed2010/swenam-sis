{{--
 * Quiz Attempts List Component (DataTable Version)
 *
 * Displays a list of student quiz attempts with server-side pagination.
 * Shared between admin and instructor views with context-aware routing.
 *
 * @param string $context - 'admin' or 'instructor'
 * @param \App\Models\Program $program
 * @param \App\Models\Course $course
 * @param \App\Models\Quiz $quiz
 * @param array $stats - ['total' => int, 'graded' => int, 'pending' => int]
--}}

@props(['context', 'program', 'course', 'quiz', 'stats'])

@php
    $isAdmin = $context === 'admin';

    // Context-aware routes
    $routePrefix = $isAdmin ? 'admin.programs.courses' : 'instructor.courses';
    $attemptsRoute = route("{$routePrefix}.quizzes.attempts", [$program, $course, $quiz]);
    $quizRoute = route("{$routePrefix}.quizzes.show", [$program, $course, $quiz]);
@endphp

{{-- Quiz Header --}}
<div class="card mb-6">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-start">
            <div>
                <h1 class="fs-2 fw-bold mb-2">{{ $quiz->title }}</h1>
                <div class="text-muted">
                    @if($quiz->assessment_type === 'exam')
                        <span class="badge badge-light-danger me-2">{{ __('Exam') }}</span>
                    @else
                        <span class="badge badge-light-primary me-2">{{ __('Quiz') }}</span>
                    @endif
                    <span class="me-3"><i class="fas fa-question-circle me-1"></i>{{ $quiz->questions_count ?? $quiz->questions()->count() }} {{ __('questions') }}</span>
                    <span><i class="fas fa-star me-1"></i>{{ $quiz->total_points ?? 100 }} {{ __('points') }}</span>
                </div>
            </div>
            <a href="{{ $quizRoute }}" class="btn btn-light">
                <i class="fas fa-arrow-left me-1"></i>{{ __('Back to Quiz') }}
            </a>
        </div>

        {{-- Stats --}}
        <div class="d-flex flex-wrap pt-5">
            <div class="border border-gray-300 border-dashed rounded min-w-100px py-3 px-4 me-6">
                <div class="fs-2 fw-bold" data-stat="total">{{ $stats['total'] }}</div>
                <div class="fw-semibold fs-7 text-gray-500">{{ __('Total Attempts') }}</div>
            </div>
            <div class="border border-gray-300 border-dashed rounded min-w-100px py-3 px-4 me-6">
                <div class="fs-2 fw-bold text-success" data-stat="graded">{{ $stats['graded'] }}</div>
                <div class="fw-semibold fs-7 text-gray-500">{{ __('Graded') }}</div>
            </div>
            <div class="border border-gray-300 border-dashed rounded min-w-100px py-3 px-4">
                <div class="fs-2 fw-bold text-warning" data-stat="pending">{{ $stats['pending'] }}</div>
                <div class="fw-semibold fs-7 text-gray-500">{{ __('Pending') }}</div>
            </div>
        </div>
    </div>
</div>

{{-- Filters --}}
<div class="card mb-6">
    <div class="card-body">
        <div class="d-flex flex-wrap gap-3 align-items-center">
            <div>
                <select name="filter" class="form-select form-select-sm" data-filter="filter">
                    <option value="all">{{ __('All Attempts') }}</option>
                    <option value="pending">{{ __('Pending Grading') }}</option>
                    <option value="graded">{{ __('Graded') }}</option>
                    <option value="in_progress">{{ __('In Progress') }}</option>
                </select>
            </div>
            <button type="button" class="btn btn-sm btn-light-primary" data-refresh-table>
                <i class="ki-outline ki-arrows-circle fs-5 me-1"></i>{{ __('Refresh') }}
            </button>
        </div>
    </div>
</div>

{{-- Attempts DataTable --}}
<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table id="attempts-table"
                   class="table align-middle table-row-dashed fs-6 gy-5"
                   data-ajax-url="{{ $attemptsRoute }}"
                   data-page-length="15">
                <thead>
                    <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                        <th class="min-w-200px">{{ __('Student') }}</th>
                        <th class="min-w-125px">{{ __('Started') }}</th>
                        <th class="min-w-125px">{{ __('Ended') }}</th>
                        <th class="min-w-100px text-center">{{ __('Status') }}</th>
                        <th class="min-w-100px text-center">{{ __('Score') }}</th>
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
<script src="{{ asset('assets/js/custom/quizzes/attempts-table.js') }}"></script>
@endpush
