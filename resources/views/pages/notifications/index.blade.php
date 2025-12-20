<x-default-layout>

    @section('title')
        Notifications
    @endsection

    @section('breadcrumbs')
        {{ Breadcrumbs::render('notifications.index') }}
    @endsection

    <!--begin::Toolbar-->
    <div class="d-flex flex-wrap flex-stack mb-6">
        <h3 class="fw-bold my-2">
            {!! getIcon('notification-bing', 'fs-2 me-2') !!}
            My Notifications
            @if(auth()->user()->unreadNotifications->count() > 0)
                <span
                    class="badge badge-circle badge-primary ms-2">{{ auth()->user()->unreadNotifications->count() }}</span>
            @endif
        </h3>

        <div class="d-flex align-items-center gap-2 my-2">
            <!--begin::Filter-->
            <select id="notification-filter" class="form-select form-select-solid w-150px">
                <option value="all" {{ $filter === 'all' ? 'selected' : '' }}>All</option>
                <option value="unread" {{ $filter === 'unread' ? 'selected' : '' }}>Unread</option>
                <option value="read" {{ $filter === 'read' ? 'selected' : '' }}>Read</option>
            </select>
            <!--end::Filter-->

            @if(auth()->user()->unreadNotifications->count() > 0)
                <form action="{{ route('notifications.mark-all-read') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-light-primary">
                        {!! getIcon('check-circle', 'fs-6 me-1') !!}
                        Mark All Read
                    </button>
                </form>
            @endif

            <a href="{{ route('settings.notifications') }}" class="btn btn-light">
                {!! getIcon('setting-2', 'fs-6 me-1') !!}
                Settings
            </a>
        </div>
    </div>
    <!--end::Toolbar-->

    @if($notifications->count() > 0)
        <!--begin::Notifications-->
        <div class="row g-6 g-xl-9">
            @foreach($notifications as $notification)
                @php
                    $data = $notification->data;
                    $isUnread = is_null($notification->read_at);
                    $icon = $data['icon'] ?? 'notification';
                    $priority = $data['priority'] ?? 'medium';
                    $title = $data['title'] ?? 'Notification';
                    $message = $data['content'] ?? $data['message'] ?? '';
                    $fullContent = $data['full_content'] ?? $message;

                    $priorityColors = [
                        'high' => 'danger',
                        'medium' => 'warning',
                        'low' => 'info',
                    ];
                    $priorityColor = $priorityColors[$priority] ?? 'primary';
                @endphp

                <div class="col-12">
                    <div class="card {{ $isUnread ? 'border border-primary' : '' }} shadow-sm hover-elevate-up">
                        <div class="card-body p-6">
                            <div class="d-flex align-items-start">
                                <!--begin::Icon-->
                                <div class="symbol symbol-50px me-5">
                                    <span class="symbol-label bg-light-{{ $priorityColor }}">
                                        {!! getIcon($icon, 'fs-2x text-' . $priorityColor) !!}
                                    </span>
                                </div>
                                <!--end::Icon-->

                                <!--begin::Content-->
                                <div class="flex-grow-1">
                                    <!--begin::Title-->
                                    <div class="d-flex align-items-center mb-2">
                                        <h4 class="fw-bold text-gray-800 mb-0 me-2">{{ $title }}</h4>
                                        @if($isUnread)
                                            <span class="badge badge-light-primary">New</span>
                                        @endif
                                    </div>
                                    <!--end::Title-->

                                    <!--begin::Message-->
                                    <div class="text-gray-700 fs-6 fw-normal mb-4"
                                        style="white-space: pre-wrap; line-height: 1.6;">
                                        {{ $fullContent }}
                                    </div>
                                    <!--end::Message-->

                                    <!--begin::Meta-->
                                    <div class="d-flex flex-wrap align-items-center gap-3">
                                        <span class="badge badge-light-{{ $priorityColor }}">
                                            {{ ucfirst($priority) }} Priority
                                        </span>
                                        <span class="text-gray-400 fs-7">
                                            {!! getIcon('time', 'fs-7 me-1') !!}
                                            {{ $notification->created_at->format('M d, Y h:i A') }}
                                            <span class="text-muted">({{ $notification->created_at->diffForHumans() }})</span>
                                        </span>
                                    </div>
                                    <!--end::Meta-->
                                </div>
                                <!--end::Content-->

                                <!--begin::Actions-->
                                <div class="d-flex flex-column gap-2 ms-3">
                                    @if($isUnread)
                                        <form action="{{ route('notifications.read', $notification->id) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-icon btn-light-primary" title="Mark as read"
                                                data-bs-toggle="tooltip">
                                                {!! getIcon('check', 'fs-4') !!}
                                            </button>
                                        </form>
                                    @endif

                                    <form action="{{ route('notifications.destroy', $notification->id) }}" method="POST"
                                        onsubmit="return confirm('Delete this notification?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-icon btn-light-danger" title="Delete"
                                            data-bs-toggle="tooltip">
                                            {!! getIcon('trash', 'fs-4') !!}
                                        </button>
                                    </form>
                                </div>
                                <!--end::Actions-->
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        <!--end::Notifications-->

        <!--begin::Pagination-->
        <div class="d-flex flex-stack flex-wrap pt-10">
            <div class="fs-6 fw-semibold text-gray-700">
                Showing {{ $notifications->firstItem() }} to {{ $notifications->lastItem() }} of
                {{ $notifications->total() }} notifications
            </div>
            {{ $notifications->links() }}
        </div>
        <!--end::Pagination-->
    @else
        <!--begin::Empty state-->
        <div class="card shadow-sm">
            <div class="card-body p-20">
                <div class="text-center">
                    <div class="mb-7">
                        <div class="symbol symbol-150px mb-5">
                            <span class="symbol-label bg-light-primary">
                                {!! getIcon('notification', 'fs-4x text-primary') !!}
                            </span>
                        </div>
                    </div>
                    <h2 class="text-gray-800 fw-bold mb-3">
                        @if($filter === 'unread')
                            All Caught Up!
                        @else
                            No Notifications Found
                        @endif
                    </h2>
                    <p class="text-gray-600 fs-5 mb-7">
                        @if($filter === 'unread')
                            You're all caught up! No unread notifications at the moment.
                        @elseif($filter === 'read')
                            No read notifications found.
                        @elseif($search)
                            No notifications match your search criteria.
                        @else
                            You don't have any notifications yet. They'll appear here when you receive them.
                        @endif
                    </p>
                    <a href="{{ route('dashboard') }}" class="btn btn-primary">
                        {!! getIcon('home', 'fs-6 me-1') !!}
                        Back to Dashboard
                    </a>
                </div>
            </div>
        </div>
        <!--end::Empty state-->
    @endif

    @push('scripts')
        <script>
            // Filter functionality
            const filterSelect = document.getElementById('notification-filter');

            filterSelect.addEventListener('change', function () {
                const filter = this.value;
                const url = new URL(window.location.href);
                url.searchParams.set('filter', filter);
                window.location.href = url.toString();
            });

            // Initialize tooltips
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        </script>
    @endpush

    <style>
        .hover-elevate-up {
            transition: all 0.3s ease;
        }

        .hover-elevate-up:hover {
            transform: translateY(-5px);
            box-shadow: 0 0.5rem 1.5rem 0.5rem rgba(0, 0, 0, 0.075) !important;
        }
    </style>

</x-default-layout>