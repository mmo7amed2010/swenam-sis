{{--
 * Assignment Show - Submissions Table Component
 *
 * Displays inline table of submissions on the show page.
 * Shared between admin and instructor views with context-aware routing.
 *
 * @param \Illuminate\Support\Collection $submissions
 * @param \App\Models\Program $program
 * @param \App\Models\Course $course
 * @param \App\Models\Assignment $assignment
 * @param string $context - 'admin' or 'instructor'
--}}

@props(['submissions', 'program', 'course', 'assignment', 'context'])

@php
    $isAdmin = $context === 'admin';

    $gradeRoutePrefix = $isAdmin
        ? 'admin.programs.courses.assignments.grade'
        : 'instructor.courses.assignments.grade';
@endphp

<x-cards.section :title="__('Submissions')">
    <div class="text-muted fs-7 mb-5">{{ __('Student submissions for this assignment') }}</div>
    @if($submissions->count() > 0)
        <div class="table-responsive">
            <table class="table table-row-dashed table-row-gray-300 align-middle gs-0 gy-4">
                <thead>
                    <tr>
                        <th>{{ __('Student') }}</th>
                        <th>{{ __('Submitted') }}</th>
                        <th>{{ __('Status') }}</th>
                        <th>{{ __('Grade') }}</th>
                        <th>{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($submissions as $submission)
                    <tr>
                        <td>{{ $submission->student->name ?? 'N/A' }}</td>
                        <td>{{ $submission->submitted_at ? $submission->submitted_at->format('M d, Y H:i') : '-' }}</td>
                        <td>
                            @if($submission->status === 'submitted')
                            <span class="badge badge-light-info">{{ __('Submitted') }}</span>
                            @elseif($submission->status === 'graded')
                            <span class="badge badge-light-success">{{ __('Graded') }}</span>
                            @else
                            <span class="badge badge-light-secondary">{{ __('Draft') }}</span>
                            @endif
                        </td>
                        <td>
                            @if($submission->publishedGrade())
                            {{ $submission->publishedGrade()->points_awarded }} / {{ $submission->publishedGrade()->max_points }}
                            @else
                            -
                            @endif
                        </td>
                        <td>
                            <a href="{{ route($gradeRoutePrefix, [$program, $course, $assignment, $submission]) }}" class="btn btn-sm btn-light-primary">
                                {{ $submission->publishedGrade() ? __('View/Edit') : __('Grade') }}
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <x-tables.empty-state
            icon="file-text"
            title="{{ __('No Submissions Yet') }}"
            message="{{ __('No students have submitted this assignment yet.') }}"
            bgColor="ffffff"
        />
    @endif
</x-cards.section>
