<x-default-layout>

    @section('title')
        {{ __('Teaching Dashboard') }}
    @endsection

    @section('breadcrumbs')
        {{ Breadcrumbs::render('dashboard') }}
    @endsection

    {{-- KPI Cards --}}
    <div class="row g-5 mb-6">
        <x-stat-card
            icon="book"
            :label="__('Active Courses')"
            :value="number_format($teaching_stats['total_courses'] ?? 0)"
            color="primary"
            col-class="col-md-6 col-xl-3"
        />
        <x-stat-card
            icon="people"
            :label="__('Total Students')"
            :value="number_format($teaching_stats['total_students'] ?? 0)"
            color="success"
            col-class="col-md-6 col-xl-3"
        />
        <x-stat-card
            icon="pencil"
            :label="__('To Grade')"
            :value="number_format($teaching_stats['pending_grades'] ?? 0)"
            color="warning"
            col-class="col-md-6 col-xl-3"
        />
        <x-stat-card
            icon="calendar"
            :label="__('Deadlines')"
            :value="number_format($upcoming_deadlines?->count() ?? 0)"
            color="info"
            col-class="col-md-6 col-xl-3"
        />
    </div>

    <div class="row g-5 g-xl-10 mb-5 mb-xl-10">
        {{-- Priority Actions --}}
        <div class="col-xl-8">
            {{-- Pending Grading Queue --}}
            <x-cards.section
                :title="__('Grading Queue')"
                :subtitle="__('Submissions awaiting your review')"
                flush="true"
                class="mb-5 mb-xl-10"
            >
                <x-slot:toolbar>
                    @if($my_courses->isNotEmpty())
                        <a href="{{ route('instructor.courses.index') }}" class="btn btn-sm btn-light-warning">
                            {{ __('View All Courses') }}
                            <i class="ki-duotone ki-arrow-right fs-3 ms-1">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                        </a>
                    @endif
                </x-slot:toolbar>

                <div class="pt-5">
                    @if($grading_queue->isNotEmpty())
                        <div class="d-flex flex-column gap-5">
                            @foreach($grading_queue as $submission)
                            <div class="d-flex align-items-center bg-light-info rounded p-5">
                                <div class="symbol symbol-50px me-5">
                                    <div class="symbol-label" style="background-color: #06b6d4;">
                                        <span class="text-white fw-bold fs-4">{{ strtoupper(substr($submission->student->name ?? 'S', 0, 1)) }}</span>
                                    </div>
                                </div>
                                <div class="flex-grow-1">
                                    <span class="text-gray-900 fw-bold fs-5">{{ $submission->assignment->title ?? 'Assignment' }}</span>
                                    <div class="text-muted fw-semibold mt-1">
                                        {{ $submission->student->name ?? 'Student' }} &bull; {{ $submission->assignment->course->course_code ?? 'Course' }}
                                    </div>
                                </div>
                                <div class="text-end me-5">
                                    <span class="badge badge-light-primary">{{ __('Submitted') }}</span>
                                    <div class="text-muted fs-7 mt-1">{{ $submission->submitted_at?->diffForHumans() ?? 'Recently' }}</div>
                                </div>
                                <a href="{{ route('instructor.courses.assignments.grade', [
                                    $submission->assignment->course->program_id ?? 1,
                                    $submission->assignment->course_id,
                                    $submission->assignment_id,
                                    $submission->id
                                ]) }}" class="btn btn-sm btn-info">
                                    <i class="ki-duotone ki-pencil fs-4">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                    {{ __('Grade') }}
                                </a>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <x-lists.empty
                            icon="check-circle"
                            icon-size="5x"
                            :title="__('All Caught Up!')"
                            :message="__('No pending submissions to grade')"
                        />
                    @endif
                </div>
            </x-cards.section>

            {{-- Course Management Grid --}}
            <x-cards.section
                :title="__('My Courses')"
                :subtitle="__('Manage your active courses')"
                flush="true"
            >
                <x-slot:toolbar>
                    <a href="{{ route('instructor.courses.index') }}" class="btn btn-sm btn-primary">
                        <i class="ki-duotone ki-eye fs-3 me-1">
                            <span class="path1"></span>
                            <span class="path2"></span>
                            <span class="path3"></span>
                        </i>
                        {{ __('View All') }}
                    </a>
                </x-slot:toolbar>

                <div class="pt-5">
                    @if($my_courses->isNotEmpty())
                        <div class="row g-5">
                            @foreach($my_courses as $course)
                            <div class="col-md-6">
                                <div class="card card-bordered h-100">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center mb-5">
                                            <div class="symbol symbol-50px me-4">
                                                <div class="symbol-label bg-primary">
                                                    <i class="ki-duotone ki-book fs-2x text-white">
                                                        <span class="path1"></span>
                                                        <span class="path2"></span>
                                                        <span class="path3"></span>
                                                    </i>
                                                </div>
                                            </div>
                                            <div class="flex-grow-1">
                                                <a href="{{ route('instructor.courses.show', [$course->program_id, $course->id]) }}" class="text-gray-900 fw-bold text-hover-primary fs-5">{{ $course->name }}</a>
                                                <div class="text-muted fw-semibold fs-7">{{ $course->course_code }}</div>
                                            </div>
                                            @if($course->pending_count > 0)
                                                <span class="badge badge-light-warning">{{ $course->pending_count }} {{ __('pending') }}</span>
                                            @else
                                                <span class="badge badge-light-success">{{ __('Active') }}</span>
                                            @endif
                                        </div>

                                        <div class="d-flex justify-content-between mb-3">
                                            <div>
                                                <i class="ki-duotone ki-people fs-4 text-primary me-1">
                                                    <span class="path1"></span>
                                                    <span class="path2"></span>
                                                    <span class="path3"></span>
                                                </i>
                                                <span class="fw-semibold">{{ $course->student_count ?? 0 }} {{ __('Students') }}</span>
                                            </div>
                                            <div>
                                                <i class="ki-duotone ki-book fs-4 text-info me-1">
                                                    <span class="path1"></span>
                                                    <span class="path2"></span>
                                                </i>
                                                <span class="fw-semibold">{{ $course->modules_count ?? 0 }} {{ __('Modules') }}</span>
                                            </div>
                                        </div>

                                        <div class="separator separator-dashed mb-3"></div>

                                        <div class="d-flex gap-2">
                                            <a href="{{ route('instructor.courses.show', [$course->program_id, $course->id]) }}" class="btn btn-sm btn-light-primary flex-grow-1">
                                                <i class="ki-duotone ki-eye fs-5">
                                                    <span class="path1"></span>
                                                    <span class="path2"></span>
                                                    <span class="path3"></span>
                                                </i>
                                                {{ __('View') }}
                                            </a>
                                            <a href="{{ route('instructor.courses.assignments.index', [$course->program_id, $course->id]) }}" class="btn btn-sm btn-light-info flex-grow-1">
                                                <i class="ki-duotone ki-document fs-5">
                                                    <span class="path1"></span>
                                                    <span class="path2"></span>
                                                </i>
                                                {{ __('Assignments') }}
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <x-lists.empty
                            icon="book-open"
                            icon-size="5x"
                            :title="__('No Courses Assigned')"
                        >
                            <p class="text-muted fs-6 mb-7">{{ __('Contact an administrator to be assigned to courses') }}</p>
                        </x-lists.empty>
                    @endif
                </div>
            </x-cards.section>
        </div>

        {{-- Sidebar: Tools & Activity --}}
        <div class="col-xl-4">
            {{-- Upcoming Deadlines --}}
            @if($upcoming_deadlines->isNotEmpty())
            <x-cards.section
                :title="__('Upcoming Deadlines')"
                :subtitle="__('Next 14 days')"
                flush="true"
                class="mb-5 mb-xl-10"
            >
                <div class="pt-5">
                    <div class="d-flex flex-column gap-4">
                        @foreach($upcoming_deadlines as $deadline)
                        <div class="d-flex align-items-center">
                            <div class="symbol symbol-40px me-4">
                                <div class="symbol-label bg-light-danger">
                                    <i class="ki-duotone ki-calendar fs-3 text-danger">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                </div>
                            </div>
                            <div class="flex-grow-1">
                                <span class="text-gray-800 fw-bold d-block">{{ $deadline->title }}</span>
                                <span class="text-muted fs-7">{{ $deadline->course->course_code ?? 'Course' }}</span>
                            </div>
                            <div class="text-end">
                                <span class="badge badge-light-{{ $deadline->due_date->diffInDays(now()) <= 3 ? 'danger' : 'warning' }}">
                                    {{ $deadline->due_date->diffForHumans() }}
                                </span>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </x-cards.section>
            @endif

            {{-- Quick Actions --}}
            <x-cards.section
                :title="__('Quick Tools')"
                flush="true"
                class="mb-5 mb-xl-10"
            >
                <div class="pt-5">
                    <div class="d-grid gap-3">
                        <a href="{{ route('instructor.courses.index') }}" class="btn btn-flex btn-light-primary justify-content-start">
                            <i class="ki-duotone ki-book fs-2 me-3">
                                <span class="path1"></span>
                                <span class="path2"></span>
                                <span class="path3"></span>
                            </i>
                            <span class="d-flex flex-column align-items-start">
                                <span class="fw-bold">{{ __('My Courses') }}</span>
                                <span class="fs-7 text-muted">{{ __('View all your courses') }}</span>
                            </span>
                        </a>

                        @if($my_courses->first())
                        <a href="{{ route('instructor.courses.students.index', [$my_courses->first()->program_id, $my_courses->first()->id]) }}" class="btn btn-flex btn-light-success justify-content-start">
                            <i class="ki-duotone ki-people fs-2 me-3">
                                <span class="path1"></span>
                                <span class="path2"></span>
                                <span class="path3"></span>
                                <span class="path4"></span>
                                <span class="path5"></span>
                            </i>
                            <span class="d-flex flex-column align-items-start">
                                <span class="fw-bold">{{ __('Students') }}</span>
                                <span class="fs-7 text-muted">{{ __('View enrolled students') }}</span>
                            </span>
                        </a>

                        <a href="{{ route('instructor.courses.quizzes.index', [$my_courses->first()->program_id, $my_courses->first()->id]) }}" class="btn btn-flex btn-light-warning justify-content-start">
                            <i class="ki-duotone ki-question-2 fs-2 me-3">
                                <span class="path1"></span>
                                <span class="path2"></span>
                                <span class="path3"></span>
                            </i>
                            <span class="d-flex flex-column align-items-start">
                                <span class="fw-bold">{{ __('Quizzes') }}</span>
                                <span class="fs-7 text-muted">{{ __('View course quizzes') }}</span>
                            </span>
                        </a>
                        @endif
                    </div>
                </div>
            </x-cards.section>

            {{-- Recent Activity --}}
            <x-cards.section
                :title="__('Recent Activity')"
                flush="true"
            >
                <div class="pt-5">
                    @if($recent_activity->isNotEmpty())
                        <div class="timeline-label">
                            @foreach($recent_activity as $activity)
                            <div class="timeline-item">
                                <div class="timeline-label text-muted fs-7">{{ $activity->submitted_at?->diffForHumans(short: true) ?? '?' }}</div>
                                <div class="timeline-badge">
                                    <i class="ki-duotone ki-send text-{{ $activity->status === 'graded' ? 'success' : 'primary' }} fs-2">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                </div>
                                <div class="timeline-content">
                                    <div class="fw-bold text-gray-800">{{ $activity->student->name ?? 'Student' }}</div>
                                    <div class="text-muted fs-7">{{ __('submitted') }} {{ $activity->assignment->title ?? 'assignment' }}</div>
                                    <div class="text-muted fs-8">{{ $activity->assignment->course->course_code ?? '' }}</div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <x-lists.empty
                            icon="information-2"
                            icon-size="3x"
                            :message="__('No recent activity')"
                        />
                    @endif
                </div>
            </x-cards.section>

            {{-- Announcements Widget --}}
            <x-dashboard.announcements-widget />
        </div>
    </div>

</x-default-layout>
