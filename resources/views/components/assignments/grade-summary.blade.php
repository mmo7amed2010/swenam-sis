{{--
 * Assignment Grade - Summary Component
 *
 * Displays assignment summary information in the grading sidebar.
 * Shared between admin and instructor views.
 *
 * @param \App\Models\Assignment $assignment
 * @param \App\Models\Grade|null $existingGrade
 * @param int $maxPoints
--}}

@props(['assignment', 'existingGrade', 'maxPoints'])

<x-cards.section
    :title="__('Assignment Summary')"
>
    <div class="d-flex flex-column gap-3">
        <div class="d-flex justify-content-between">
            <span class="text-muted">{{ __('Total Points') }}</span>
            <span class="fw-bold">{{ $maxPoints }}</span>
        </div>
        <div class="d-flex justify-content-between">
            <span class="text-muted">{{ __('Due Date') }}</span>
            <span class="fw-bold">{{ $assignment->due_date?->format('M d, Y g:i A') ?? __('No deadline') }}</span>
        </div>
        @if($existingGrade)
        <div class="separator"></div>
        <div class="d-flex justify-content-between">
            <span class="text-muted">{{ __('Current Grade') }}</span>
            <span class="fw-bold text-primary">
                {{ number_format($existingGrade->points_awarded, 2) }} / {{ number_format($existingGrade->max_points, 2) }}
            </span>
        </div>
        <div class="d-flex justify-content-between">
            <span class="text-muted">{{ __('Status') }}</span>
            <span class="badge badge-light-{{ $existingGrade->is_published ? 'success' : 'warning' }}">
                {{ $existingGrade->is_published ? __('Published') : __('Draft') }}
            </span>
        </div>
        @endif
    </div>
</x-cards.section>
