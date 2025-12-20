{{--
 * Content Viewer Back Link Component
 *
 * Fixed "Back to Course" link at the top of content viewers.
 *
 * @param string $courseUrl - URL to course overview page
--}}

@props(['courseUrl'])

<div class="content-viewer-back-link px-6 py-4">
    <a href="{{ $courseUrl }}" class="text-gray-600 text-hover-primary d-inline-flex align-items-center gap-2 fs-6 fw-medium">
        {!! getIcon('arrow-left', 'fs-5') !!}
        {{ __('Back to Course') }}
    </a>
</div>
