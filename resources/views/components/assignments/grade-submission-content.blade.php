{{--
 * Assignment Grade - Submission Content Component
 *
 * Displays submission content with file preview/download for grading view.
 * Shared between admin and instructor views with context-aware routing.
 *
 * @param \App\Models\Submission $submission
 * @param \App\Models\Assignment $assignment
 * @param \App\Models\Program $program
 * @param \App\Models\Course $course
 * @param string $context - 'admin' or 'instructor'
--}}

@props(['submission', 'assignment', 'program', 'course', 'context'])

@php
    $isAdmin = $context === 'admin';
    $previewRoute = $isAdmin
        ? route('admin.programs.courses.assignments.submissions.preview', [$program, $course, $assignment, $submission])
        : route('instructor.courses.assignments.submissions.preview', [$program, $course, $assignment, $submission]);
    $downloadRoute = $isAdmin
        ? route('admin.programs.courses.assignments.submissions.download', [$program, $course, $assignment, $submission])
        : route('instructor.courses.assignments.submissions.download', [$program, $course, $assignment, $submission]);
@endphp

<x-cards.section
    :title="__('Submission Content')"
    class="mb-5 mb-xl-10"
>
    @if($submission->submission_type === 'file' && $submission->file_path)
        <!--begin::File Preview-->
        <div class="mb-5">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <div>
                    <span class="fw-bold text-gray-800">{{ $submission->file_name ?? 'Submission File' }}</span>
                    <span class="text-muted ms-2">({{ $submission->file_size_human }})</span>
                </div>
                <div>
                    @php
                        $fileExtension = strtolower(pathinfo($submission->file_name ?? '', PATHINFO_EXTENSION));
                        $isPdf = $fileExtension === 'pdf';
                        $isImage = in_array($fileExtension, ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                        $isDocx = $fileExtension === 'docx';
                    @endphp
                    @if($isPdf || $isImage)
                        <button type="button" class="btn btn-sm btn-light-info me-2" onclick="previewFile('{{ $previewRoute }}', '{{ $submission->file_name }}')">
                            <i class="ki-duotone ki-eye fs-3">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                            {{ __('Preview') }}
                        </button>
                    @endif
                    <a href="{{ $downloadRoute }}" class="btn btn-sm btn-light-primary">
                        <i class="ki-duotone ki-file-down fs-3">
                            <span class="path1"></span>
                            <span class="path2"></span>
                        </i>
                        {{ __('Download') }}
                    </a>
                </div>
            </div>
            @if($isPdf)
                <div id="pdf-preview" class="border rounded p-3" style="min-height: 400px;">
                    <embed src="{{ $previewRoute }}"
                           type="application/pdf"
                           width="100%"
                           height="600px"
                           style="border: none;">
                </div>
            @elseif($isImage)
                <div class="text-center">
                    <img src="{{ $previewRoute }}"
                         class="img-fluid rounded"
                         alt="{{ $submission->file_name }}"
                         style="max-width: 100%; max-height: 600px;">
                </div>
            @elseif($isDocx)
                <div class="alert alert-info">
                    <i class="ki-duotone ki-information-5 fs-2x me-3">
                        <span class="path1"></span>
                        <span class="path2"></span>
                        <span class="path3"></span>
                    </i>
                    {{ __('DOCX files cannot be previewed in the browser. Please download to view.') }}
                </div>
            @endif
        </div>
        <!--end::File Preview-->
    @elseif($submission->submission_type === 'text' && $submission->text_content)
        <!--begin::Text Content-->
        <div class="border rounded p-5">
            <div class="text-gray-700">{!! nl2br(e($submission->text_content)) !!}</div>
        </div>
        <!--end::Text Content-->
    @elseif($submission->submission_type === 'link' && $submission->external_url)
        <!--begin::External URL-->
        <div class="border rounded p-5">
            <a href="{{ $submission->external_url }}" target="_blank" class="text-primary fw-bold">
                {{ $submission->external_url }}
                <i class="ki-duotone ki-arrow-top-right fs-5 ms-2">
                    <span class="path1"></span>
                    <span class="path2"></span>
                </i>
            </a>
        </div>
        <!--end::External URL-->
    @else
        <div class="alert alert-info">
            {{ __('No submission content available.') }}
        </div>
    @endif
</x-cards.section>
