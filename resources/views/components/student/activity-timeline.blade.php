{{--
    Activity Timeline Component - Metronic Design System Aligned

    A timeline showing recent activity, grades, and achievements.

    @props
    - activities: Collection - Collection of activity items with keys:
        date (Carbon), title, description, course_code, color, icon
    - title: string - Section title
    - subtitle: string - Section subtitle

    @example
    <x-student.activity-timeline
        :activities="$recentActivity"
        :title="__('Recent Activity')"
        :subtitle="__('Your latest grades and achievements')"
    />
--}}

@props([
    'activities',
    'title' => null,
    'subtitle' => null,
])

<div class="card card-flush mb-5 mb-xl-10">
    <div class="card-header border-0 pt-5">
        <h3 class="card-title align-items-start flex-column">
            <span class="card-label fw-bold text-gray-900">{{ $title ?? __('Recent Activity') }}</span>
            @if($subtitle)
                <span class="text-muted mt-1 fw-semibold fs-7">{{ $subtitle }}</span>
            @endif
        </h3>
    </div>

    <div class="card-body py-5">
        @if($activities->count() > 0)
            <div class="timeline-label" role="list" aria-label="{{ $title ?? __('Recent Activity') }}">
                @foreach($activities as $activity)
                    <div class="timeline-item" role="listitem">
                        {{-- Date --}}
                        <div class="timeline-label fw-bold text-gray-800 fs-6">
                            {{ $activity['date']->format('M j') }}
                        </div>

                        {{-- Badge/Icon --}}
                        <div class="timeline-badge">
                            <div class="symbol symbol-35px">
                                <span class="symbol-label bg-light-{{ $activity['color'] }}">
                                    {!! getIcon($activity['icon'], 'fs-4 text-' . $activity['color']) !!}
                                </span>
                            </div>
                        </div>

                        {{-- Content --}}
                        <div class="timeline-content ps-3">
                            <span class="fw-bold text-gray-800 d-block">{{ $activity['title'] }}</span>
                            <span class="text-muted fs-7">
                                {{ $activity['description'] }} - {{ $activity['course_code'] }}
                            </span>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            {{-- Empty State --}}
            <div class="text-center py-10">
                <div class="symbol symbol-80px mb-5">
                    <span class="symbol-label bg-light-secondary">
                        {!! getIcon('chart-simple', 'fs-3x text-gray-400') !!}
                    </span>
                </div>
                <h4 class="fw-bold text-gray-800">{{ __('No Activity Yet') }}</h4>
                <p class="text-muted fs-6">{{ __('Your grades and achievements will appear here') }}</p>
            </div>
        @endif
    </div>
</div>
