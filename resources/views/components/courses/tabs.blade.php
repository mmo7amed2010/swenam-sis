{{--
 * Course Tabs Navigation Component
 *
 * Shared tabs navigation for course show pages (admin & instructor).
 *
 * @param \App\Models\Course $course
 * @param \App\Models\Program $program
 * @param array $stats - Course statistics array with 'module_count'
--}}

@props([
    'course',
    'program',
    'stats',
])

<!--begin::Tabs Navigation-->
<ul class="nav nav-stretch nav-line-tabs nav-line-tabs-2x border-transparent fs-5 fw-bold mb-5" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#course_details_tab" type="button" role="tab">
            {!! getIcon('profile-circle', 'fs-4 me-2') !!}
            {{ __('Course Details') }}
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#modules_tab" type="button" role="tab">
            {!! getIcon('element-11', 'fs-4 me-2') !!}
            {{ __('Course Content') }}
            <span class="badge badge-light-primary ms-2">{{ $stats['module_count'] ?? 0 }}</span>
        </button>
    </li>
    @if(auth()->user()->user_type === 'instructor')
        <li class="nav-item" role="presentation">
            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#announcements_tab" type="button" role="tab">
                {!! getIcon('notification-bing', 'fs-4 me-2') !!}
                {{ __('Announcements') }}
                @if($course->announcements()->published()->count() > 0)
                    <span class="badge badge-light-success ms-2">{{ $course->announcements()->published()->count() }}</span>
                @endif
            </button>
        </li>
    @endif
</ul>
<!--end::Tabs Navigation-->
