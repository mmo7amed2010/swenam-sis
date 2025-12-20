{{--
 * Assignment Submissions - Stats Component
 *
 * Displays submission status count.
 * Shared between admin and instructor views.
 *
 * @param int $submittedCount
 * @param int $totalStudents
--}}

@props(['submittedCount', 'totalStudents'])

<!--begin::Submission Count-->
<x-cards.section class="mb-5 mb-xl-10">
    <div class="d-flex align-items-center">
        <div class="symbol symbol-50px me-5">
            <div class="symbol-label bg-light-primary">
                <i class="ki-duotone ki-file fs-2x text-primary">
                    <span class="path1"></span>
                    <span class="path2"></span>
                </i>
            </div>
        </div>
        <div class="flex-grow-1">
            <h4 class="fw-bold text-gray-900 mb-1">{{ __('Submission Status') }}</h4>
            <p class="text-gray-600 mb-0">
                {{ __(':submitted of :total program students submitted', ['submitted' => $submittedCount, 'total' => $totalStudents]) }}
            </p>
        </div>
    </div>
</x-cards.section>
<!--end::Submission Count-->
