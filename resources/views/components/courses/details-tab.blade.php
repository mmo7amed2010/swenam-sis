{{--
 * Course Details Tab Content Component
 *
 * Shared details tab for course show pages (admin & instructor).
 * Displays course metadata, details and description.
 *
 * @param \App\Models\Program $program
 * @param \App\Models\Course $course
 * @param string $context - 'admin' or 'instructor'
--}}

@props([
    'program',
    'course',
    'context' => 'admin',
])

@php
    $isAdmin = $context === 'admin';
    $isInstructor = $context === 'instructor';
    // Ensure $program is an object - handle edge cases where it might be passed as a string
    $programName = is_object($program) ? $program->name : $program;
    $programModel = is_object($program) ? $program : null;
@endphp

<!--begin::Course Details Tab-->
<div class="tab-pane fade show active" id="course_details_tab" role="tabpanel">
    <x-cards.section :title="__('Course Details')" class="mb-5 mb-xl-10">
        <div class="row row-cols-1 row-cols-md-2 g-4">


            <x-courses.detail-item
                icon="book-open"
                color="primary"
                :label="__('Program')"
                :value="$programName"
                :href="$isAdmin && $programModel ? route('admin.programs.show', $programModel) : null"
            />

            <x-courses.detail-item
                icon="medal-star"
                color="info"
                :label="__('Credits')"
                :value="$course->credits"
            />

            @if($isInstructor)
                <x-courses.detail-item
                    icon="calendar"
                    color="warning"
                    :label="__('Created')"
                    :value="$course->created_at->format('M d, Y')"
                />
            @endif
        </div>

        @if($course->description)
            <div class="mt-5">
                <div class="p-4 border rounded-3 bg-white shadow-sm">
                    <span class="text-gray-500 fs-8 text-uppercase d-block mb-2">{{ __('Description') }}</span>
                    <p class="text-gray-800 fs-7 mb-0">{!! nl2br(e(Str::limit($course->description, 300))) !!}</p>
                    @if(strlen($course->description) > 300)
                        <a href="#" class="text-primary fs-8 mt-2 d-inline-block" data-bs-toggle="modal" data-bs-target="#kt_modal_full_description">
                            {{ __('Read more') }}
                        </a>
                    @endif
                </div>
            </div>
        @endif
    </x-cards.section>
</div>
<!--end::Course Details Tab-->
