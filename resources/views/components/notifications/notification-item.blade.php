{{--
* Notification Item Component
*
* Single notification display for dropdown and list views.
*
* @param \Illuminate\Notifications\DatabaseNotification $notification
--}}

@props(['notification'])

@php
    $data = $notification->data;
    $isUnread = is_null($notification->read_at);
    $icon = $data['icon'] ?? 'notification';
    $priority = $data['priority'] ?? 'medium';
    $title = $data['title'] ?? 'Notification';
    $message = $data['message'] ?? '';
    $actionUrl = $data['action_url'] ?? route('notifications.show', $notification->id);

    // Priority colors
    $priorityColors = [
        'high' => 'danger',
        'medium' => 'warning',
        'low' => 'info',
    ];
    $priorityColor = $priorityColors[$priority] ?? 'primary';
@endphp

<div class="d-flex flex-stack py-4 {{ !$isUnread ? 'opacity-75' : '' }}">
    <!--begin::Section-->
    <div class="d-flex align-items-center">
        <!--begin::Symbol-->
        <div class="symbol symbol-35px me-4">
            <span class="symbol-label bg-light-{{ $priorityColor }}">
                {!! getIcon($icon, 'fs-2 text-' . $priorityColor) !!}
            </span>
        </div>
        <!--end::Symbol-->

        <!--begin::Title-->
        <div class="mb-0 me-2">
            <a href="{{ $actionUrl }}" class="fs-6 text-gray-800 text-hover-primary fw-bold">
                {{ $title }}
                @if($isUnread)
                    <span class="badge badge-circle badge-primary ms-2" style="width: 8px; height: 8px; padding: 0;"></span>
                @endif
            </a>
            <div class="text-gray-500 fs-7">{{ Str::limit($message, 80) }}</div>
            <div class="text-gray-400 fs-8 mt-1">
                {!! getIcon('time', 'fs-8 me-1') !!}
                {{ $notification->created_at->diffForHumans() }}
            </div>
        </div>
        <!--end::Title-->
    </div>
    <!--end::Section-->

    <!--begin::Actions-->
    <div class="d-flex flex-column align-items-end">
        @if($isUnread)
            <form action="{{ route('notifications.read', $notification->id) }}" method="POST" class="mb-1">
                @csrf
                <button type="submit" class="btn btn-sm btn-icon btn-active-light-primary" title="Mark as read">
                    {!! getIcon('check', 'fs-6') !!}
                </button>
            </form>
        @endif

        <form action="{{ route('notifications.destroy', $notification->id) }}" method="POST">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-sm btn-icon btn-active-light-danger" title="Delete">
                {!! getIcon('trash', 'fs-6') !!}
            </button>
        </form>
    </div>
    <!--end::Actions-->
</div>

<div class="separator separator-dashed"></div>