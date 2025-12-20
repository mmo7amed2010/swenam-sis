{{--
 * Assignment Show - Details Component
 *
 * Displays assignment details: description, due date, points, submission type, etc.
 * Shared between admin and instructor views.
 *
 * @param \App\Models\Assignment $assignment
--}}

@props(['assignment'])

<x-cards.section
    :title="__('Assignment Details')"
    class="mb-5 mb-xl-10"
>
    @if($assignment->description)
    <div class="mb-7">
        <div class="text-gray-600 fw-bold mb-2">{{ __('Description') }}</div>
        <div class="text-gray-900">{!! $assignment->description !!}</div>
    </div>
    @endif

    <div class="d-flex flex-column gap-5">
        <div class="d-flex flex-stack">
            <div class="text-gray-600">{{ __('Due Date') }}</div>
            <div class="text-gray-900 fw-bold">{{ $assignment->due_date?->format('M d, Y H:i') ?? __('No deadline') }}</div>
        </div>
        <div class="separator"></div>
        <div class="d-flex flex-stack">
            <div class="text-gray-600">{{ __('Total Points') }}</div>
            <div class="text-gray-900 fw-bold">{{ $assignment->total_points ?? $assignment->max_points ?? 0 }}</div>
        </div>
        <div class="separator"></div>
        <div class="d-flex flex-stack">
            <div class="text-gray-600">{{ __('Submission Type') }}</div>
            <div class="text-gray-900 fw-bold">{{ ucfirst(str_replace('_', ' ', $assignment->submission_type ?? 'file_upload')) }}</div>
        </div>
        @if($assignment->submission_type === 'file_upload' || $assignment->submission_type === 'multiple')
        <div class="separator"></div>
        <div class="d-flex flex-stack">
            <div class="text-gray-600">{{ __('Max File Size') }}</div>
            <div class="text-gray-900 fw-bold">{{ $assignment->max_file_size_mb ?? 10 }} MB</div>
        </div>
        @if($assignment->allowed_file_types)
        <div class="separator"></div>
        <div class="d-flex flex-stack">
            <div class="text-gray-600">{{ __('Allowed File Types') }}</div>
            <div class="text-gray-900 fw-bold">{{ implode(', ', array_map('strtoupper', $assignment->allowed_file_types)) }}</div>
        </div>
        @endif
        @endif
        <div class="separator"></div>
        <div class="d-flex flex-stack">
            <div class="text-gray-600">{{ __('Late Submission Policy') }}</div>
            <div class="text-gray-900 fw-bold">
                @if($assignment->late_policy === 'not_allowed')
                {{ __('Not Allowed') }}
                @elseif($assignment->late_policy === 'penalty')
                {{ __('Allowed with Penalty') }} ({{ $assignment->late_penalty_per_day ?? 0 }}% per day)
                @else
                {{ __('Allowed No Penalty') }}
                @endif
            </div>
        </div>
    </div>
</x-cards.section>
