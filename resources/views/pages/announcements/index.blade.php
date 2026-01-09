<x-default-layout>

    @section('title')
        {{ __('Announcements') }}
    @endsection

    @section('breadcrumbs')
        {{ Breadcrumbs::render('announcements.index') }}
    @endsection

    @php
        $priorityColors = [
            'high' => 'danger',
            'medium' => 'warning',
            'low' => 'info',
        ];

        $priorityIcons = [
            'high' => 'notification-bing',
            'medium' => 'notification',
            'low' => 'information-5',
        ];
    @endphp

    <div class="row g-5 g-xl-10">
        {{-- System Announcements --}}
        <div class="col-12">
            <div class="card card-flush h-xl-100">
                <div class="card-header pt-7">
                    <h3 class="card-title align-items-start flex-column">
                        <span class="card-label fw-bold text-gray-800">
                            {!! getIcon('shield-tick', 'fs-3 me-2 text-primary') !!}
                            {{ __('System Announcements') }}
                        </span>
                        <span class="text-gray-400 mt-1 fw-semibold fs-6">{{ __('Important updates and news') }}</span>
                    </h3>
                </div>

                <div class="card-body pt-5">
                    @if($systemAnnouncements->count() > 0)
                        <div class="timeline timeline-border-dashed">
                            @foreach($systemAnnouncements as $announcement)
                                @php
                                    $priorityColor = $priorityColors[$announcement->priority] ?? 'primary';
                                    $priorityIcon = $priorityIcons[$announcement->priority] ?? 'notification';
                                    $isNew = $announcement->created_at->isToday();
                                @endphp

                                <div class="timeline-item">
                                    <div class="timeline-line"></div>
                                    <div class="timeline-icon">
                                        <span class="svg-icon svg-icon-2 svg-icon-{{ $priorityColor }}">
                                            {!! getIcon($priorityIcon, 'fs-2 text-' . $priorityColor) !!}
                                        </span>
                                    </div>
                                    <div class="timeline-content mb-8 mt-n1">
                                        <div class="pe-3 mb-3">
                                            <div class="d-flex align-items-center mb-2">
                                                <a href="{{ route('announcements.show', $announcement) }}"
                                                   class="text-gray-800 text-hover-primary fw-bold fs-5 me-2">
                                                    {{ $announcement->title }}
                                                </a>
                                                @if($isNew)
                                                    <span class="badge badge-light-success badge-sm">New</span>
                                                @endif
                                            </div>

                                            <div class="text-gray-600 fw-normal fs-6 mb-3">
                                                {{ Str::limit(strip_tags($announcement->content), 150) }}
                                            </div>

                                            <div class="d-flex align-items-center flex-wrap gap-2">
                                                <span class="badge badge-light-{{ $priorityColor }}">
                                                    {{ ucfirst($announcement->priority) }} Priority
                                                </span>
                                                <span class="text-gray-400 fs-7">
                                                    {!! getIcon('time', 'fs-7 me-1') !!}
                                                    {{ $announcement->created_at->diffForHumans() }}
                                                </span>
                                                <span class="text-gray-400 fs-7">
                                                    {!! getIcon('user', 'fs-7 me-1') !!}
                                                    {{ $announcement->creator->name ?? 'System' }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="mt-5">
                            {{ $systemAnnouncements->onEachSide(1)->links('pagination::bootstrap-5') }}
                        </div>
                    @else
                        <div class="text-center py-15">
                            <div class="symbol symbol-100px mb-5">
                                <span class="symbol-label bg-light-primary">
                                    {!! getIcon('notification', 'fs-3x text-primary') !!}
                                </span>
                            </div>
                            <div class="text-gray-800 fw-bold fs-4 mb-2">{{ __('No System Announcements') }}</div>
                            <div class="text-gray-400 fs-6">{{ __('Check back later for important updates') }}</div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <style>
        .timeline-item:hover .timeline-content {
            transform: translateX(5px);
            transition: transform 0.2s ease;
        }

        .timeline-border-dashed .timeline-line {
            border-left-style: dashed;
        }
    </style>

</x-default-layout>
