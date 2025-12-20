{{--
    Deadlines Table Component - Metronic Design System Aligned

    A table displaying upcoming deadlines for assignments and quizzes.

    @props
    - deadlines: Collection - Collection of deadline items with keys:
        title, course_code, type (assignment|quiz), color, icon, points, due_date
    - title: string - Table title
    - subtitle: string - Table subtitle badge text

    @example
    <x-student.deadlines-table
        :deadlines="$upcomingDeadlines"
        :title="__('Upcoming Deadlines')"
        :subtitle="__('Next 14 days')"
    />
--}}

@props([
    'deadlines',
    'title' => null,
    'subtitle' => null,
])

@if($deadlines->count() > 0)
    <div class="card card-flush mb-5 mb-xl-10">
        <div class="card-header border-0 pt-6">
            <h3 class="card-title">
                {!! getIcon('calendar', 'fs-2 me-2 text-primary') !!}
                {{ $title ?? __('Upcoming Deadlines') }}
            </h3>
            @if($subtitle)
                <div class="card-toolbar">
                    <span class="badge badge-light-primary fs-7">{{ $subtitle }}</span>
                </div>
            @endif
        </div>
        <div class="card-body pt-0">
            <div class="table-responsive">
                <table class="table table-row-dashed table-row-gray-300 align-middle gs-0 gy-4">
                    <thead>
                        <tr class="fw-bold text-muted">
                            <th class="min-w-150px">{{ __('Item') }}</th>
                            <th class="min-w-100px">{{ __('Course') }}</th>
                            <th class="min-w-80px text-center">{{ __('Type') }}</th>
                            <th class="min-w-80px text-center">{{ __('Points') }}</th>
                            <th class="min-w-100px text-end">{{ __('Due Date') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($deadlines as $deadline)
                            <tr>
                                {{-- Item Title --}}
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="symbol symbol-40px me-3">
                                            <span class="symbol-label bg-light-{{ $deadline['color'] }}">
                                                {!! getIcon($deadline['icon'], 'fs-3 text-' . $deadline['color']) !!}
                                            </span>
                                        </div>
                                        <span class="text-gray-800 fw-bold">{{ $deadline['title'] }}</span>
                                    </div>
                                </td>

                                {{-- Course Code --}}
                                <td>
                                    <span class="badge badge-light-primary">{{ $deadline['course_code'] }}</span>
                                </td>

                                {{-- Type --}}
                                <td class="text-center">
                                    <span class="badge badge-light-{{ $deadline['type'] === 'assignment' ? 'success' : 'info' }}">
                                        {{ ucfirst($deadline['type']) }}
                                    </span>
                                </td>

                                {{-- Points --}}
                                <td class="text-center">
                                    <span class="text-gray-800 fw-bold">{{ $deadline['points'] ?? '-' }}</span>
                                </td>

                                {{-- Due Date --}}
                                <td class="text-end">
                                    <span class="text-gray-800 fw-semibold">
                                        {{ $deadline['due_date']->format('M j') }}
                                    </span>
                                    <span class="text-muted fs-7 d-block">
                                        {{ $deadline['due_date']->format('g:i A') }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endif
