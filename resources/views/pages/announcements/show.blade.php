<x-default-layout>

    @section('title')
        {{ $announcement->title }}
    @endsection

    @section('breadcrumbs')
        {{ Breadcrumbs::render('announcements.show', $announcement) }}
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
                    @if($announcement->type === 'system')
                        <span class="badge badge-primary">System Announcement</span>
                    @else
                        <span class="badge badge-info">Course Announcement</span>
                    @endif
                    <span class="badge badge-light-{{ $priorityColor }}">{{ ucfirst($announcement->priority) }}
                        Priority</span>
                </div>
            </div>
        </div>

        <div class="card-body">
            <!--begin::Content-->
            <div class="mb-8">
                <div class="text-gray-800 fs-5" style="white-space: pre-wrap;">{{ $announcement->content }}</div>
            </div>
            <!--end::Content-->

            <!--begin::Meta-->
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
                @if($announcement->type === 'system')
                    <div class="d-flex align-items-center">
                        {!! getIcon('people', 'fs-5 text-gray-600 me-2') !!}
                        <div>
                            <div class="text-gray-600 fs-7">Target Audience</div>
                            <div class="fw-semibold">
                                @if($announcement->target_audience === 'all')
                                    All Users
                                @elseif($announcement->target_audience === 'students')
                                    Students
                                @elseif($announcement->target_audience === 'instructors')
                                    Instructors
                                @elseif($announcement->target_audience === 'admins')
                                    Admins
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
                                @endif
                            </div>
                        </div>
                    </div>
                @endif
                @if($announcement->type === 'course' && $announcement->course)
                    <div class="d-flex align-items-center">
                        {!! getIcon('book', 'fs-5 text-gray-600 me-2') !!}
                        <div>
                            <div class="text-gray-600 fs-7">Course</div>
                            <div class="fw-semibold">{{ $announcement->course->name }}</div>
                        </div>
                    </div>
                @endif
            </div>
            <!--end::Meta-->
        </div>

        <div class="card-footer">
            <a href="{{ route('announcements.index') }}" class="btn btn-light">
                {!! getIcon('arrow-left', 'fs-6 me-1') !!}
                Back to Announcements
            </a>
        </div>
    </div>

</x-default-layout>