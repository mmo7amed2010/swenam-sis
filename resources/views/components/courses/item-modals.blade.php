{{--
 * Item Modals Component
 *
 * Includes edit/delete modals based on item type.
 * Supports both admin and instructor contexts via the context parameter.
 *
 * @param string $context - 'admin' or 'instructor'
 * @param \App\Models\Program $program
 * @param \App\Models\Course $course
 * @param \App\Models\CourseModule $module
 * @param \App\Models\CourseModuleItem $item
--}}

@props(['context', 'program', 'course', 'module', 'item'])

@php $itemable = $item->itemable; @endphp

@if($itemable)
    @switch($item->itemable_type)
        @case('App\\Models\\ModuleLesson')
            <x-modals.edit-lesson-form
                :context="$context"
                :program="$program"
                :course="$course"
                :module="$module"
                :lesson="$itemable"
            />
            <x-modals.delete-lesson-form
                :context="$context"
                :program="$program"
                :course="$course"
                :module="$module"
                :lesson="$itemable"
            />
            @break

        @case('App\\Models\\Quiz')
            <x-modals.edit-quiz-form
                :context="$context"
                :program="$program"
                :course="$course"
                :quiz="$itemable"
                :module="$itemable->module"
            />
            @break

        @case('App\\Models\\Assignment')
            <x-modals.edit-assignment-form
                :context="$context"
                :program="$program"
                :course="$course"
                :assignment="$itemable"
                :module="$module"
            />
            @break
    @endswitch
@endif
