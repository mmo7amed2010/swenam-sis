{{--
 * Delete Lesson Form Component
 *
 * Shared AJAX-based confirmation modal for deleting a lesson.
 * Supports both admin and instructor contexts via the context parameter.
 *
 * @param string $context - 'admin' or 'instructor'
 * @param \App\Models\Program $program
 * @param \App\Models\Course $course
 * @param \App\Models\CourseModule $module
 * @param \App\Models\ModuleLesson $lesson
--}}

@props(['context', 'program', 'course', 'module', 'lesson'])

@php
$isAdmin = $context === 'admin';
$action = $isAdmin
    ? route('admin.programs.courses.modules.lessons.destroy', [$program, $course, $module, $lesson])
    : route('instructor.courses.modules.lessons.destroy', [$program, $course, $module, $lesson]);
$refreshUrl = $isAdmin
    ? route('admin.programs.courses.modules.content', [$program, $course, $module])
    : route('instructor.courses.modules.content', [$program, $course, $module]);
@endphp

<x-modals.ajax-form
    id="kt_modal_delete_lesson_{{ $lesson->id }}"
    title="{{ __('Delete Lesson') }}"
    :action="$action"
    method="DELETE"
    targetContainer="#module-content-{{ $module->id }}"
    :refreshUrl="$refreshUrl"
    successMessage="{{ __('Lesson deleted successfully') }}"
    submitLabel="{{ __('Delete Lesson') }}"
    submitClass="btn-danger"
    headerClass="border-0 pb-0"
    :resetOnSuccess="false"
>
    <div class="text-center">
        {{-- Warning Icon --}}
        <div class="mb-5">
            {!! getIcon('information-5', 'fs-5tx text-danger') !!}
        </div>

        {{-- Confirmation Message --}}
        <h3 class="mb-3">{{ __('Delete ":title"?', ['title' => $lesson->title]) }}</h3>
        <p class="text-gray-600 mb-5">
            {{ __('This will permanently remove the lesson and all its content from this module.') }}
            <br>
            <span class="fw-bold text-danger">{{ __('This action cannot be undone.') }}</span>
        </p>
    </div>
</x-modals.ajax-form>
