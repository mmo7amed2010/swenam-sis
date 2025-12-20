{{--
 * Assignment Grade - Student Information Component
 *
 * Displays student information for grading view.
 * Shared between admin and instructor views.
 *
 * @param \App\Models\Submission $submission
--}}

@props(['submission'])

<x-cards.section
    :title="__('Student Information')"
    class="mb-5 mb-xl-10"
>
    <div class="row g-5">
        <div class="col-md-6">
            <div class="d-flex flex-column">
                <span class="text-muted fw-semibold mb-2">{{ __('Student Name') }}</span>
                <span class="text-gray-800 fw-bold">{{ $submission->student->name ?? 'N/A' }}</span>
            </div>
        </div>
        <div class="col-md-6">
            <div class="d-flex flex-column">
                <span class="text-muted fw-semibold mb-2">{{ __('Email') }}</span>
                <span class="text-gray-800 fw-bold">{{ $submission->student->email ?? 'N/A' }}</span>
            </div>
        </div>
        <div class="col-md-6">
            <div class="d-flex flex-column">
                <span class="text-muted fw-semibold mb-2">{{ __('Submission Date') }}</span>
                <span class="text-gray-800 fw-bold">
                    {{ $submission->submitted_at?->format('F d, Y g:i A') ?? __('N/A') }}
                </span>
                @if($submission->is_late)
                    <span class="badge badge-light-danger mt-2">{{ __('Late') }} ({{ $submission->late_days }} {{ __('days') }})</span>
                @endif
            </div>
        </div>
        <div class="col-md-6">
            <div class="d-flex flex-column">
                <span class="text-muted fw-semibold mb-2">{{ __('Attempt Number') }}</span>
                <span class="text-gray-800 fw-bold">{{ $submission->attempt_number }}</span>
            </div>
        </div>
    </div>
</x-cards.section>
