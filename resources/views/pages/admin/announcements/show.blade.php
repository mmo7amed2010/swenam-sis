<x-default-layout>

    @section('title')
        {{ $announcement->title }}
    @endsection

    @section('breadcrumbs')
        {{ Breadcrumbs::render('admin.announcements.show', $announcement) }}
    @endsection

    @php
        $priorityColors = [
            'high' => 'danger',
            'medium' => 'warning',
            'low' => 'info',
        ];
        $priorityColor = $priorityColors[$announcement->priority] ?? 'primary';
    @endphp

    <div class="card">
        <div class="card-header border-0 pt-6">
            <div class="card-title flex-column align-items-start">
                <h2 class="fw-bold mb-3">{{ $announcement->title }}</h2>
                <div class="d-flex gap-2">
                    <span class="badge badge-{{ $priorityColor }}">System Announcement</span>
                    <span class="badge badge-light-{{ $priorityColor }}">{{ ucfirst($announcement->priority) }}
                        Priority</span>
                </div>
            </div>
            <div class="card-toolbar gap-2">
                <form action="{{ route('admin.announcements.destroy', $announcement) }}" method="POST"
                    class="d-inline delete-form">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-light-danger">
                        {!! getIcon('trash', 'fs-6 me-1') !!}
                        Delete
                    </button>
                </form>
            </div>
        </div>

        <div class="card-body">
            <div class="mb-8">
                <div class="text-gray-800 fs-5">
                    {!! nl2br(e($announcement->content)) !!}
                </div>
            </div>

            <div class="separator mb-6"></div>
            <div class="d-flex flex-wrap gap-5">
                <div class="d-flex align-items-center">
                    {!! getIcon('user', 'fs-5 text-gray-600 me-2') !!}
                    <div>
                        <div class="text-gray-600 fs-7">Posted by</div>
                        <div class="fw-semibold">{{ $announcement->creator->name }}</div>
                    </div>
                </div>
                <div class="d-flex align-items-center">
                    {!! getIcon('time', 'fs-5 text-gray-600 me-2') !!}
                    <div>
                        <div class="text-gray-600 fs-7">Posted on</div>
                        <div class="fw-semibold">{{ $announcement->created_at->format('M d, Y h:i A') }}</div>
                    </div>
                </div>
                <div class="d-flex align-items-center">
                    {!! getIcon('people', 'fs-5 text-gray-600 me-2') !!}
                    <div>
                        <div class="text-gray-600 fs-7">Target Audience</div>
                        <div class="fw-semibold">
                            @if($announcement->target_audience === 'all')
                                All Users
                            @elseif($announcement->target_audience === 'students')
                                Students Only
                            @elseif($announcement->target_audience === 'instructors')
                                Instructors Only
                            @elseif($announcement->target_audience === 'admins')
                                Admins Only
                            @elseif($announcement->target_audience === 'program' && $announcement->program_id)
                                @php
                                    $program = \App\Models\Program::find($announcement->program_id);
                                @endphp
                                {{ $program ? $program->name : 'Program' }}
                                @if($announcement->course_id)
                                    @php
                                        $course = \App\Models\Course::find($announcement->course_id);
                                    @endphp
                                    <span class="badge badge-light-info ms-1">{{ $course ? $course->name : 'Course' }}</span>
                                @endif
                            @else
                                {{ ucfirst($announcement->target_audience) }}
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card-footer">
            <a href="{{ route('admin.announcements.index') }}" class="btn btn-light">
                {!! getIcon('arrow-left', 'fs-6 me-1') !!}
                Back to Announcements
            </a>
        </div>
    </div>

    @push('scripts')
        <script>
            // Delete confirmation with SweetAlert2
            document.querySelector('.delete-form').addEventListener('submit', function (e) {
                e.preventDefault();

                Swal.fire({
                    title: 'Delete Announcement?',
                    text: "This action cannot be undone!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Yes, delete it!',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        this.submit();
                    }
                });
            });
        </script>
    @endpush

</x-default-layout>