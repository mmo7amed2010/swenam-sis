<x-default-layout>

    @section('title')
        {{ __('Grade Submission') }} - {{ $assignment->title }}
    @endsection

    @section('breadcrumbs')
        <x-breadcrumb :items="[
            ['title' => __('Programs'), 'url' => route('admin.programs.index')],
            ['title' => $program->name, 'url' => route('admin.programs.show', $program)],
            ['title' => $course->course_code, 'url' => route('admin.programs.courses.show', [$program, $course])],
            ['title' => $assignment->title, 'url' => route('admin.programs.courses.assignments.show', [$program, $course, $assignment])],
            ['title' => __('Submissions'), 'url' => route('admin.programs.courses.assignments.submissions', [$program, $course, $assignment])],
            ['title' => __('Grade')]
        ]" />
    @endsection

    <!--begin::Toolbar-->
    <div class="d-flex flex-wrap flex-stack pb-7">
        <div class="d-flex flex-wrap align-items-center my-1">
            <h3 class="fw-bold me-5 my-1">{{ __('Grade Submission') }}</h3>
            <span class="text-muted fs-6">{{ $assignment->title }}</span>
        </div>
        <x-assignments.grade-navigation
            :previousSubmissionId="$previousSubmissionId"
            :nextSubmissionId="$nextSubmissionId"
            :program="$program"
            :course="$course"
            :assignment="$assignment"
            context="admin"
        />
    </div>
    <!--end::Toolbar-->

    <div class="row g-5 g-xl-10">
        <!--begin::Left Column - Student Info & Submission-->
        <div class="col-xl-8">
            <!--begin::Student Info-->
            <x-assignments.grade-student-info :submission="$submission" />
            <!--end::Student Info-->

            <!--begin::Assignment Instructions-->
            @if($assignment->instructions)
            <x-cards.section
                :title="__('Assignment Instructions')"
                class="mb-5 mb-xl-10"
            >
                <div class="text-gray-700">{!! nl2br(e($assignment->instructions)) !!}</div>
            </x-cards.section>
            @endif
            <!--end::Assignment Instructions-->

            <!--begin::Submission Content-->
            <x-assignments.grade-submission-content
                :submission="$submission"
                :assignment="$assignment"
                :program="$program"
                :course="$course"
                context="admin"
            />
            <!--end::Submission Content-->

            <!--begin::Previous Submissions-->
            <x-assignments.grade-previous-submissions :previousSubmissions="$previousSubmissions" />
            <!--end::Previous Submissions-->
        </div>
        <!--end::Left Column-->

        <!--begin::Right Column - Grading Form-->
        <div class="col-xl-4">
            <!--begin::Grading Form-->
            <x-assignments.grade-form
                :submission="$submission"
                :assignment="$assignment"
                :existingGrade="$existingGrade"
                :maxPoints="$maxPoints"
                :maxPointsAfterPenalty="$maxPointsAfterPenalty"
                :latePenalty="$latePenalty"
                :program="$program"
                :course="$course"
                context="admin"
            />
            <!--end::Grading Form-->

            <!--begin::Assignment Summary-->
            <x-assignments.grade-summary
                :assignment="$assignment"
                :existingGrade="$existingGrade"
                :maxPoints="$maxPoints"
            />
            <!--end::Assignment Summary-->
        </div>
        <!--end::Right Column-->
    </div>

    <!--begin::Preview Modal-->
    <div class="modal fade" id="previewModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="fw-bold" id="previewTitle">{{ __('File Preview') }}</h2>
                    <div class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal">
                        <i class="ki-duotone ki-cross fs-1">
                            <span class="path1"></span>
                            <span class="path2"></span>
                        </i>
                    </div>
                </div>
                <div class="modal-body" id="previewBody">
                    <!-- Dynamic content loaded here -->
                </div>
            </div>
        </div>
    </div>
    <!--end::Preview Modal-->

    @push('scripts')
    <script>
        function previewFile(url, fileName) {
            const modal = new bootstrap.Modal(document.getElementById('previewModal'));
            const titleEl = document.getElementById('previewTitle');
            const bodyEl = document.getElementById('previewBody');

            titleEl.textContent = fileName + ' - {{ __('Preview') }}';

            // Check file type
            const isPdf = url.toLowerCase().endsWith('.pdf') || fileName.toLowerCase().endsWith('.pdf');
            const isImage = /\.(jpg|jpeg|png|gif|webp)$/i.test(fileName);

            if (isPdf) {
                bodyEl.innerHTML = `<embed src="${url}" type="application/pdf" width="100%" height="700px" style="border: none;">`;
            } else if (isImage) {
                bodyEl.innerHTML = `<img src="${url}" class="img-fluid" alt="${fileName}" style="max-width: 100%; height: auto;">`;
            } else {
                bodyEl.innerHTML = `<div class="alert alert-info">{{ __('Preview not available for this file type. Please download to view.') }}</div>`;
            }

            modal.show();
        }
    </script>
    @endpush

</x-default-layout>
