{{--
 * Assignment Submissions - Table Component
 *
 * Displays sortable table of submissions with student info, date, status, grade, actions.
 * Shared between admin and instructor views with context-aware routing.
 *
 * @param \Illuminate\Support\Collection $submissions
 * @param string $filter
 * @param string $sort
 * @param string $dir
 * @param \App\Models\Program $program
 * @param \App\Models\Course $course
 * @param \App\Models\Assignment $assignment
 * @param string $context - 'admin' or 'instructor'
--}}

@props(['submissions', 'filter', 'sort', 'dir', 'program', 'course', 'assignment', 'context'])

@php
    $isAdmin = $context === 'admin';

    $baseRoute = $isAdmin
        ? 'admin.programs.courses.assignments.submissions'
        : 'instructor.courses.assignments.submissions';

    $gradeRoutePrefix = $isAdmin
        ? 'admin.programs.courses.assignments.grade'
        : 'instructor.courses.assignments.grade';

    $routeParams = [$program, $course, $assignment];
@endphp

<!--begin::Submissions Table-->
<x-tables.card-wrapper>
    <x-slot:toolbar>
        <h3 class="card-title align-items-start flex-column">
            <span class="card-label fw-bold fs-3 mb-1">{{ __('Student Submissions') }}</span>
            <span class="text-muted mt-1 fw-semibold fs-7">{{ __('View and manage all submissions') }}</span>
        </h3>
    </x-slot:toolbar>

    @if($submissions->count() > 0)
        <div class="table-responsive">
            <table class="table table-row-bordered table-row-gray-100 align-middle gs-0 gy-3">
                <thead>
                    <tr class="fw-bold text-muted">
                        <th class="min-w-150px">
                            <a href="{{ route($baseRoute, array_merge($routeParams, ['filter' => $filter, 'sort' => 'student_name', 'dir' => $sort === 'student_name' && $dir === 'asc' ? 'desc' : 'asc'])) }}"
                               class="text-gray-700 text-hover-primary">
                                {{ __('Student Name') }}
                                @if($sort === 'student_name')
                                    <i class="ki-duotone ki-arrow-{{ $dir === 'asc' ? 'up' : 'down' }} fs-5">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                @endif
                            </a>
                        </th>
                        <th class="min-w-150px">
                            <a href="{{ route($baseRoute, array_merge($routeParams, ['filter' => $filter, 'sort' => 'submitted_at', 'dir' => $sort === 'submitted_at' && $dir === 'asc' ? 'desc' : 'asc'])) }}"
                               class="text-gray-700 text-hover-primary">
                                {{ __('Submission Date') }}
                                @if($sort === 'submitted_at')
                                    <i class="ki-duotone ki-arrow-{{ $dir === 'asc' ? 'up' : 'down' }} fs-5">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                @endif
                            </a>
                        </th>
                        <th class="min-w-100px text-center">{{ __('Status') }}</th>
                        <th class="min-w-120px text-center">
                            <a href="{{ route($baseRoute, array_merge($routeParams, ['filter' => $filter, 'sort' => 'grade', 'dir' => $sort === 'grade' && $dir === 'asc' ? 'desc' : 'asc'])) }}"
                               class="text-gray-700 text-hover-primary">
                                {{ __('Grade') }}
                                @if($sort === 'grade')
                                    <i class="ki-duotone ki-arrow-{{ $dir === 'asc' ? 'up' : 'down' }} fs-5">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                @endif
                            </a>
                        </th>
                        <th class="min-w-100px text-end">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($submissions as $submission)
                    <tr>
                        <td>
                            <span class="text-gray-800 fw-bold">
                                {{ $submission->student->name ?? 'N/A' }}
                            </span>
                            <div class="text-muted fs-7">{{ $submission->student->email ?? 'N/A' }}</div>
                        </td>
                        <td>
                            <span class="text-gray-700 fw-semibold">
                                {{ $submission->submitted_at?->format('M d, Y g:i A') ?? __('N/A') }}
                            </span>
                        </td>
                        <td class="text-center">
                            @php
                                $statusColors = [
                                    'submitted' => 'primary',
                                    'graded' => 'success',
                                ];
                                $status = $submission->status;
                                $color = $statusColors[$status] ?? 'secondary';
                            @endphp
                            <span class="badge badge-light-{{ $color }}">{{ ucfirst($status) }}</span>
                        </td>
                        <td class="text-center">
                            @if($submission->publishedGrade())
                                @php
                                    $grade = $submission->publishedGrade();
                                @endphp
                                <span class="fw-bold text-primary">
                                    {{ number_format($grade->points_awarded, 1) }} / {{ number_format($grade->max_points, 1) }}
                                </span>
                                <div class="text-muted fs-7">{{ number_format($grade->percentage, 1) }}%</div>
                            @else
                                <span class="text-muted">{{ __('Not Graded') }}</span>
                            @endif
                        </td>
                        <td class="text-end">
                            <a href="{{ route($gradeRoutePrefix, array_merge($routeParams, [$submission])) }}"
                               class="btn btn-sm btn-primary">
                                {{ $submission->publishedGrade() ? __('View/Edit Grade') : __('Grade') }}
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <x-lists.empty
            :title="__('No Submissions Found')"
            icon="information-5"
            icon-size="3x"
            :message="$filter === 'all' ? __('No students have submitted this assignment yet.') : __('No submissions match the selected filter.')"
        />
    @endif
</x-tables.card-wrapper>
<!--end::Submissions Table-->
