{{--
    Tasks List Component - Metronic Design System Aligned

    A list of today's tasks with icons, status badges, and links.

    @props
    - tasks: Collection - Collection of task items with keys:
        id, title, type (assignment|quiz), course_id, course_code,
        due_time, status_color, icon, is_overdue
    - limit: int - Maximum number of tasks to display

    @example
    <x-student.tasks-list :tasks="$todaysTasks" :limit="5" />
--}}

@props([
    'tasks',
    'limit' => 5,
])

@php
    $displayTasks = $tasks->take($limit);
    $hasMore = $tasks->count() > $limit;
@endphp

<div class="card card-flush h-xl-100">
    <div class="card-header border-0 pt-5">
        <h3 class="card-title align-items-start flex-column">
            <span class="card-label fw-bold text-gray-900">{{ __("Today's Tasks") }}</span>
            <span class="text-muted mt-1 fw-semibold fs-7">{{ __('Your focus for today') }}</span>
        </h3>
        <div class="card-toolbar">
            <span class="badge badge-light-{{ $tasks->count() > 0 ? 'danger' : 'success' }}">
                {{ $tasks->count() }} {{ __('pending') }}
            </span>
        </div>
    </div>

    <div class="card-body pt-5">
        @if($displayTasks->count() > 0)
            <div class="d-flex flex-column gap-4" role="list" aria-label="{{ __('Today\'s tasks') }}">
                @foreach($displayTasks as $task)
                    <div class="d-flex align-items-start" role="listitem">
                        {{-- Icon --}}
                        <div class="symbol symbol-40px me-4">
                            <span class="symbol-label bg-light-{{ $task['status_color'] }}">
                                {!! getIcon($task['icon'], 'fs-3 text-' . $task['status_color']) !!}
                            </span>
                        </div>

                        {{-- Content --}}
                        <div class="flex-grow-1">
                            @php
                                $taskUrl = $task['type'] === 'assignment'
                                    ? route('student.assignments.show', $task['id'])
                                    : route('student.courses.quizzes.show', ['course' => $task['course_id'] ?? 0, 'quiz' => $task['id']]);
                            @endphp
                            <a href="{{ $taskUrl }}"
                               class="text-gray-800 text-hover-primary fw-bold fs-6 d-block">
                                {{ Str::limit($task['title'], 30) }}
                            </a>
                            <span class="text-muted fs-7">
                                {{ $task['course_code'] }} - {{ __('Due') }} {{ $task['due_time'] }}
                            </span>
                        </div>

                        {{-- Status Badge --}}
                        <span class="badge badge-light-{{ $task['status_color'] }} ms-2">
                            {{ $task['is_overdue'] ? __('Overdue') : __('Today') }}
                        </span>
                    </div>

                    @if(!$loop->last)
                        <div class="separator separator-dashed"></div>
                    @endif
                @endforeach

                {{-- View All Link --}}
                @if($hasMore)
                    <div class="text-center pt-3">
                        <a href="{{ route('student.program.index') }}" class="btn btn-sm btn-light-primary">
                            {{ __('View All Courses') }}
                            {!! getIcon('arrow-right', 'fs-5 ms-1') !!}
                        </a>
                    </div>
                @endif
            </div>
        @else
            {{-- Empty State --}}
            <div class="text-center py-10">
                <div class="symbol symbol-80px mb-5">
                    <span class="symbol-label bg-light-success">
                        {!! getIcon('check-circle', 'fs-3x text-success') !!}
                    </span>
                </div>
                <h4 class="fw-bold text-gray-800">{{ __('All Caught Up!') }}</h4>
                <p class="text-muted fs-6">{{ __('You have no pending tasks for today') }}</p>
            </div>
        @endif
    </div>
</div>
