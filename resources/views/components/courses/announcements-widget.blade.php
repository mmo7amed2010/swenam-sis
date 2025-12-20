{{--
* Course Announcements Widget
*
* Displays recent announcements for a course on the course home page.
*
* @param \App\Models\Course $course
--}}

@props(['course'])

@php
    $announcements = $course->announcements()
        ->published()
        ->latest()
        ->limit(5)
        ->get();
@endphp

@if($announcements->count() > 0)
    <div class="card mb-5">
        <div class="card-header">
            <h3 class="card-title">
                {!! getIcon('notification', 'fs-3 me-2') !!}
                Course Announcements
            </h3>
        </div>
        <div class="card-body py-3">
            @foreach($announcements as $announcement)
                @php
                    $priorityColors = [
                        'high' => 'danger',
                        'medium' => 'warning',
                        'low' => 'info',
                    ];
                    $priorityColor = $priorityColors[$announcement->priority] ?? 'primary';
                @endphp

                <div class="d-flex align-items-start mb-5 {{ !$loop->last ? 'border-bottom pb-5' : '' }}">
                    <div class="symbol symbol-40px me-4">
                        <span class="symbol-label bg-light-{{ $priorityColor }}">
                            {!! getIcon('notification-bing', 'fs-2 text-' . $priorityColor) !!}
                        </span>
                    </div>
                    <div class="flex-grow-1">
                        <div class="d-flex align-items-center mb-2">
                            <h4 class="mb-0 me-2">{{ $announcement->title }}</h4>
                            <span
                                class="badge badge-light-{{ $priorityColor }} badge-sm">{{ ucfirst($announcement->priority) }}</span>
                        </div>
                        <div class="text-gray-700 mb-2">
                            {{ Str::limit(strip_tags($announcement->content), 150) }}
                        </div>
                        <div class="d-flex align-items-center text-gray-600 fs-7">
                            {!! getIcon('user', 'fs-7 me-1') !!}
                            {{ $announcement->creator->name }}
                            <span class="mx-2">•</span>
                            {!! getIcon('time', 'fs-7 me-1') !!}
                            {{ $announcement->created_at->diffForHumans() }}
                        </div>
                    </div>
                </div>
            @endforeach

            @if($course->announcements()->published()->count() > 5)
                <div class="text-center mt-3">
                    <a href="#" class="btn btn-sm btn-light-primary">
                        View All Announcements
                        {!! getIcon('arrow-right', 'fs-6 ms-1') !!}
                    </a>
                </div>
            @endif
        </div>
    </div>
@endif