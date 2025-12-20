{{--
 * Assignment Grade - Grading Form Component
 *
 * Displays the grading form with points, feedback, rubric, and late penalty override.
 * Shared between admin and instructor views with context-aware routing.
 *
 * @param \App\Models\Submission $submission
 * @param \App\Models\Assignment $assignment
 * @param \App\Models\Grade|null $existingGrade
 * @param int $maxPoints
 * @param float $maxPointsAfterPenalty
 * @param float $latePenalty
 * @param \App\Models\Program $program
 * @param \App\Models\Course $course
 * @param string $context - 'admin' or 'instructor'
--}}

@props(['submission', 'assignment', 'existingGrade', 'maxPoints', 'maxPointsAfterPenalty', 'latePenalty', 'program', 'course', 'context'])

@php
    $isAdmin = $context === 'admin';
    $formAction = $isAdmin
        ? route('admin.programs.courses.assignments.grade.store', [$program, $course, $assignment, $submission])
        : route('instructor.courses.assignments.grade.store', [$program, $course, $assignment, $submission]);
@endphp

<x-forms.section
    :title="__('Grading')"
    class="mb-5 mb-xl-10"
>
    <form id="grade-form" action="{{ $formAction }}" method="POST">
        @csrf

        <!--begin::Points-->
        <div class="mb-7">
            <label class="form-label fw-bold required">{{ __('Points Awarded') }}</label>
            <div class="input-group">
                <input type="number"
                       name="points_awarded"
                       id="points-awarded"
                       class="form-control"
                       step="0.01"
                       min="0"
                       max="{{ $latePenalty > 0 ? $maxPointsAfterPenalty : $maxPoints }}"
                       value="{{ old('points_awarded', $existingGrade->points_awarded ?? '') }}"
                       required>
                <span class="input-group-text">/ {{ $maxPoints }}</span>
            </div>
            <div class="form-text">
                {{ __('Maximum: :max points', ['max' => $maxPoints]) }}
                @if($latePenalty > 0)
                    <br><span class="text-warning">{{ __('After late penalty: :max points', ['max' => number_format($maxPointsAfterPenalty, 2)]) }}</span>
                @endif
            </div>
            @error('points_awarded')
                <div class="text-danger mt-2">{{ $message }}</div>
            @enderror
        </div>
        <!--end::Points-->

        <!--begin::Late Penalty Override-->
        @if($submission->is_late && $assignment->late_policy === 'penalty')
        <div class="mb-7">
            <label class="form-label fw-bold">{{ __('Late Penalty') }}</label>
            <div class="alert alert-warning d-flex align-items-center p-4">
                <i class="ki-duotone ki-information-5 fs-2x text-warning me-4">
                    <span class="path1"></span>
                    <span class="path2"></span>
                    <span class="path3"></span>
                </i>
                <div class="flex-grow-1">
                    <div class="fw-bold mb-1">{{ __('Auto-calculated:') }} {{ number_format($latePenalty, 2) }}%</div>
                    <div class="text-muted fs-7">{{ __('Late by :days day(s)', ['days' => $submission->late_days]) }}</div>
                </div>
            </div>
            <label class="form-label">{{ __('Override Late Penalty (%)') }}</label>
            <input type="number"
                   name="late_penalty_override"
                   id="late-penalty-override"
                   class="form-control"
                   step="0.01"
                   min="0"
                   max="100"
                   value="{{ old('late_penalty_override', $existingGrade->late_penalty_override ?? '') }}"
                   placeholder="{{ __('Leave empty to use auto-calculated') }}">
            <div class="form-text">{{ __('Optional: Override the auto-calculated late penalty percentage') }}</div>
            @error('late_penalty_override')
                <div class="text-danger mt-2">{{ $message }}</div>
            @enderror
        </div>
        @endif
        <!--end::Late Penalty Override-->

        <!--begin::Feedback-->
        <div class="mb-7">
            <label class="form-label fw-bold">{{ __('Feedback') }}</label>
            <textarea name="feedback" id="feedback" class="form-control" rows="10" placeholder="{{ __('Enter feedback for the student...') }}">{{ old('feedback', $existingGrade->feedback ?? '') }}</textarea>
            @error('feedback')
                <div class="text-danger mt-2">{{ $message }}</div>
            @enderror
        </div>
        <!--end::Feedback-->

        <!--begin::Rubric-->
        @if($assignment->rubric && is_array($assignment->rubric))
        <div class="mb-7">
            <label class="form-label fw-bold">{{ __('Rubric Scores') }}</label>
            <div class="border rounded p-4">
                @foreach($assignment->rubric as $criterion)
                <div class="mb-4">
                    <div class="fw-bold mb-2">{{ $criterion['name'] ?? 'Criterion' }}</div>
                    <input type="number"
                           name="rubric_scores[{{ $loop->index }}]"
                           class="form-control"
                           step="0.01"
                           min="0"
                           max="{{ $criterion['max_points'] ?? 10 }}"
                           value="{{ old("rubric_scores.{$loop->index}", $existingGrade->rubric_scores[$loop->index] ?? '') }}"
                           placeholder="0 - {{ $criterion['max_points'] ?? 10 }}">
                </div>
                @endforeach
            </div>
        </div>
        @endif
        <!--end::Rubric-->

        <!--begin::Action Buttons-->
        <div class="d-flex flex-column gap-3">
            <button type="submit" name="action" value="draft" class="btn btn-light-primary">
                <i class="ki-duotone ki-file fs-3 me-2">
                    <span class="path1"></span>
                    <span class="path2"></span>
                </i>
                {{ __('Save Draft') }}
            </button>
            <button type="submit" name="action" value="publish" class="btn btn-primary">
                <i class="ki-duotone ki-check fs-3 me-2">
                    <span class="path1"></span>
                    <span class="path2"></span>
                </i>
                {{ __('Publish Grade') }}
            </button>
        </div>
        <!--end::Action Buttons-->
    </form>
</x-forms.section>

@push('scripts')
<script>
    // Initialize TinyMCE for feedback
    @if(config('app.tinymce_enabled', false))
    if (typeof tinymce !== 'undefined') {
        tinymce.init({
            selector: '#feedback',
            height: 300,
            menubar: false,
            plugins: 'lists link image code',
            toolbar: 'undo redo | formatselect | bold italic | alignleft aligncenter alignright | bullist numlist | link image | code',
            content_style: 'body { font-family: Arial, sans-serif; font-size: 14px; }'
        });
    }
    @endif

    // Auto-adjust points if late penalty override changes
    document.addEventListener('DOMContentLoaded', function() {
        const latePenaltyOverride = document.getElementById('late-penalty-override');
        const pointsAwarded = document.getElementById('points-awarded');
        const maxPoints = {{ $maxPoints }};
        const maxPointsAfterPenalty = {{ $maxPointsAfterPenalty }};

        if (latePenaltyOverride) {
            latePenaltyOverride.addEventListener('change', function() {
                const override = parseFloat(this.value) || 0;
                const newMax = override > 0 ? maxPoints * (1 - (override / 100)) : maxPoints;

                // Update max attribute
                pointsAwarded.setAttribute('max', newMax);

                // Adjust current value if it exceeds new max
                if (parseFloat(pointsAwarded.value) > newMax) {
                    pointsAwarded.value = newMax.toFixed(2);
                }
            });
        }
    });
</script>
@endpush
