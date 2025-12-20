{{--
 * Modules Tab Content Component
 *
 * Displays the modules content section for the course view.
 * This component is used for both initial render and AJAX refresh.
 * Supports both admin and instructor contexts via the context parameter.
 *
 * @param string $context - 'admin' or 'instructor'
 * @param \App\Models\Program $program
 * @param \App\Models\Course $course
 * @param \Illuminate\Support\Collection $modules
 * @param array $contentTotals - Array with keys: lessons, quizzes, assignments
--}}

@props(['context', 'program', 'course', 'modules', 'contentTotals'])

<x-courses.content-stats
    :context="$context"
    :program="$program"
    :course="$course"
    :modules="$modules"
    :contentTotals="$contentTotals"
/>

@if($modules->count() > 0)
    <div class="d-flex flex-column gap-4" id="modulesList">
        @foreach($modules as $module)
            <x-courses.module-card
                :context="$context"
                :program="$program"
                :course="$course"
                :module="$module"
                :iteration="$loop->iteration"
                :isFirst="$loop->first"
            />
        @endforeach
    </div>
@else
    <x-tables.empty-state
        icon="book-square"
        title="{{ __('No Modules Created') }}"
        message="{{ __('This course does not have any modules yet. Create your first module to start building course content.') }}"
        variant="gradient"
        color="primary"
    />
@endif
