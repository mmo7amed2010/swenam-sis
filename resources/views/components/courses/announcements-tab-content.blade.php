{{--
 * Course Announcements Tab Content Component
 *
 * Displays announcements list within the course details tab.
 *
 * @param \App\Models\Course $course
 * @param \App\Models\Program $program
 * @param \Illuminate\Database\Eloquent\Collection $announcements
 * @param string $context - 'instructor' or 'admin'
--}}

@props([
    'course',
    'program',
    'announcements',
    'context' => 'instructor',
])

<div class="card">
    <!--begin::Card header-->
    <div class="card-header border-0 pt-6">
        <h3 class="card-title align-items-start flex-column">
            <span class="card-label fw-bold fs-3 mb-1">Course Announcements</span>
            <span class="text-muted mt-1 fw-semibold fs-7">{{ $course->course_code }} - {{ $course->name }}</span>
        </h3>

        <div class="card-toolbar">
            <button type="button"
                class="btn btn-sm btn-primary"
                data-bs-toggle="modal"
                data-bs-target="#kt_modal_add_announcement">
                {!! getIcon('plus', 'fs-6 me-1') !!}
                New Announcement
            </button>
        </div>
    </div>
    <!--end::Card header-->

    <!--begin::Card body-->
    <div class="card-body py-4">
        <div id="announcementsListContainer">
            <x-courses.announcements-list
                :announcements="$announcements"
                :program="$program"
                :course="$course"
                :context="$context"
            />
        </div>
    </div>
    <!--end::Card body-->
</div>
