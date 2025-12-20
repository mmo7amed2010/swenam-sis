{{--
 * Assignment Grade - Previous Submissions Component
 *
 * Displays table of previous submission attempts for the same student.
 * Shared between admin and instructor views.
 *
 * @param \Illuminate\Support\Collection $previousSubmissions
--}}

@props(['previousSubmissions'])

@if($previousSubmissions->count() > 0)
<x-cards.section
    :title="__('Previous Submissions')"
    class="mb-5 mb-xl-10"
>
    <div class="table-responsive">
        <table class="table table-row-bordered table-row-gray-100 align-middle gs-0 gy-3">
            <thead>
                <tr class="fw-bold text-muted">
                    <th class="min-w-120px">{{ __('Submitted') }}</th>
                    <th class="min-w-100px text-center">{{ __('Status') }}</th>
                    <th class="min-w-120px text-center">{{ __('Grade') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($previousSubmissions as $prevSubmission)
                <tr>
                    <td>
                        <span class="text-gray-700 fw-semibold">
                            {{ $prevSubmission->submitted_at?->format('M d, Y g:i A') ?? __('N/A') }}
                        </span>
                    </td>
                    <td class="text-center">
                        <span class="badge badge-light-{{ $prevSubmission->status === 'graded' ? 'success' : 'primary' }}">
                            {{ ucfirst($prevSubmission->status) }}
                        </span>
                    </td>
                    <td class="text-center">
                        @php
                            $prevGrade = $prevSubmission->grades->where('is_published', true)->sortByDesc('version')->first();
                        @endphp
                        @if($prevGrade)
                            <span class="fw-bold text-primary">
                                {{ number_format($prevGrade->points_awarded, 1) }} / {{ number_format($prevGrade->max_points, 1) }}
                            </span>
                        @else
                            <span class="text-muted">{{ __('Not Graded') }}</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</x-cards.section>
@endif
