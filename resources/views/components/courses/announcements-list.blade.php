{{--
 * Announcements List Component
 *
 * Displays the list of announcements (used for AJAX refresh)
 *
 * @param \Illuminate\Database\Eloquent\Collection $announcements
 * @param \App\Models\Program $program
 * @param \App\Models\Course $course
 * @param string $context - 'instructor' or 'admin'
--}}

@props([
    'announcements',
    'program',
    'course',
    'context' => 'instructor',
])

@if($announcements->count() > 0)
    <!--begin::Announcements list-->
    @foreach($announcements as $announcement)
        @php
            $priorityColors = [
                'high' => 'danger',
                'medium' => 'warning',
                'low' => 'info',
            ];
            $priorityColor = $priorityColors[$announcement->priority] ?? 'primary';
        @endphp

        <div class="card card-flush mb-5 border border-{{ $priorityColor }}">
            <div class="card-header">
                <div class="card-title">
                    <h3 class="fw-bold mb-0">{{ $announcement->title }}</h3>
                </div>
                <div class="card-toolbar">
                    <span class="badge badge-light-{{ $priorityColor }} me-2">{{ ucfirst($announcement->priority) }}
                        Priority</span>
                    @if($announcement->is_published)
                        <span class="badge badge-light-success">Published</span>
                    @else
                        <span class="badge badge-light-warning">Draft</span>
                    @endif
                </div>
            </div>
            <div class="card-body pt-0">
                <div class="text-gray-700 mb-4">
                    {!! $announcement->content !!}
                </div>
                <div class="d-flex align-items-center text-gray-600 fs-7 mb-3">
                    {!! getIcon('user', 'fs-7 me-1') !!}
                    {{ $announcement->creator->name }}
                    <span class="mx-2">•</span>
                    {!! getIcon('time', 'fs-7 me-1') !!}
                    {{ $announcement->created_at->format('M d, Y h:i A') }}
                    @if($announcement->send_email)
                        <span class="mx-2">•</span>
                        {!! getIcon('sms', 'fs-7 me-1') !!}
                        Email sent
                    @endif
                </div>
                <div class="d-flex gap-2">
                    <button type="button"
                        class="btn btn-sm btn-light"
                        data-announcement-edit-trigger
                        data-announcement-id="{{ $announcement->id }}"
                        data-announcement-title="{{ $announcement->title }}">
                        {!! getIcon('pencil', 'fs-6 me-1') !!}
                        Edit
                    </button>
                    <button type="button"
                        class="btn btn-sm btn-light-danger"
                        data-announcement-delete-trigger
                        data-announcement-id="{{ $announcement->id }}"
                        data-announcement-title="{{ $announcement->title }}"
                        data-delete-url="{{ route($context . '.announcements.destroy', [$program, $course, $announcement]) }}">
                        {!! getIcon('trash', 'fs-6 me-1') !!}
                        Delete
                    </button>
                </div>
            </div>
        </div>
    @endforeach
    <!--end::Announcements list-->
@else
    <!--begin::Empty state-->
    <div class="text-center py-20">
        {!! getIcon('notification', 'fs-3x text-gray-300 mb-5') !!}
        <h3 class="text-gray-600 fw-semibold mb-2">No Announcements Yet</h3>
        <p class="text-gray-400 mb-5">Create your first announcement to notify students</p>
        <button type="button"
            class="btn btn-primary"
            data-bs-toggle="modal"
            data-bs-target="#kt_modal_add_announcement">
            {!! getIcon('plus', 'fs-6 me-1') !!}
            Create Announcement
        </button>
    </div>
    <!--end::Empty state-->
@endif
