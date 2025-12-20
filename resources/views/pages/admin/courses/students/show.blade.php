<x-default-layout>

    @section('title')
        {{ $student->name }} - {{ __('Student Progress') }}
    @endsection

    @section('breadcrumbs')
        <x-breadcrumb :items="[
            ['title' => __('Programs'), 'url' => route('admin.programs.index')],
            ['title' => $program->name, 'url' => route('admin.programs.show', $program)],
            ['title' => $course->course_code, 'url' => route('admin.programs.courses.show', [$program, $course])],
            ['title' => __('Students'), 'url' => route('admin.programs.courses.students.index', [$program, $course])],
            ['title' => $student->name]
        ]" />
    @endsection

    {{-- Student Header --}}
    <div class="card mb-6">
        <div class="card-body">
            <div class="d-flex align-items-center">
                <div class="symbol symbol-circle symbol-80px me-5">
                    <span class="symbol-label bg-light-primary text-primary fs-1 fw-bold">
                        {{ strtoupper(substr($student->name, 0, 1)) }}
                    </span>
                </div>
                <div class="flex-grow-1">
                    <h1 class="fs-2 fw-bold mb-1">{{ $student->name }}</h1>
                    <div class="text-muted">
                        <span class="me-3"><i class="fas fa-envelope me-1"></i>{{ $student->email }}</span>
                        @if($student->student_id)
                            <span><i class="fas fa-id-card me-1"></i>{{ $student->student_id }}</span>
                        @endif
                    </div>
                    <div class="mt-2">
                        <span class="text-muted">{{ __('Joined') }}: {{ $student->created_at ? $student->created_at->format('M d, Y') : __('N/A') }}</span>
                    </div>
                </div>
                @if($averageGrade !== null)
                <div class="text-end">
                    <div class="fs-2x fw-bold {{ $averageGrade >= 70 ? 'text-success' : ($averageGrade >= 50 ? 'text-warning' : 'text-danger') }}">{{ $averageGrade }}%</div>
                    <div class="fw-semibold text-gray-500">{{ __('Average Grade') }}</div>
                </div>
                @endif
            </div>
        </div>
    </div>

    <div class="row g-6">
        {{-- Content Progress --}}
        <div class="col-lg-8">
            <div class="card mb-6">
                <div class="card-header border-0 pt-6">
                    <h3 class="card-title">{{ __('Content Progress') }}</h3>
                </div>
                <div class="card-body py-4">
                    @foreach($course->modules as $module)
                    <div class="mb-6">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h5 class="mb-0">{{ $module->title }}</h5>
                            @php
                                $moduleItems = $module->items;
                                $completedModuleItems = $moduleItems->filter(fn($item) => in_array($item->id, $itemProgressIds))->count();
                                $moduleProgress = $moduleItems->count() > 0 ? round(($completedModuleItems / $moduleItems->count()) * 100) : 0;
                            @endphp
                            <span class="badge badge-light-{{ $moduleProgress === 100 ? 'success' : 'primary' }}">{{ $completedModuleItems }}/{{ $moduleItems->count() }}</span>
                        </div>
                        <div class="progress h-8px">
                            <div class="progress-bar bg-{{ $moduleProgress === 100 ? 'success' : 'primary' }}" role="progressbar" style="width: {{ $moduleProgress }}%"></div>
                        </div>
                        @if($moduleItems->count() > 0)
                        <div class="mt-3">
                            @foreach($moduleItems as $item)
                            <div class="d-flex align-items-center py-2 {{ !$loop->last ? 'border-bottom' : '' }}">
                                @if(in_array($item->id, $itemProgressIds))
                                    <i class="fas fa-check-circle text-success me-2"></i>
                                @else
                                    <i class="far fa-circle text-muted me-2"></i>
                                @endif
                                <span class="{{ in_array($item->id, $itemProgressIds) ? 'text-gray-800' : 'text-muted' }}">{{ $item->title }}</span>
                            </div>
                            @endforeach
                        </div>
                        @endif
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Grades Summary --}}
        <div class="col-lg-4">
            {{-- Assignment Grades --}}
            <div class="card mb-6">
                <div class="card-header border-0 pt-6">
                    <h3 class="card-title">{{ __('Assignment Grades') }}</h3>
                </div>
                <div class="card-body py-4">
                    @if($submissions->count() > 0)
                        @foreach($submissions as $submission)
                        <div class="d-flex justify-content-between align-items-center py-2 {{ !$loop->last ? 'border-bottom' : '' }}">
                            <div>
                                <span class="fw-semibold">{{ $submission->assignment->title }}</span>
                                <div class="text-muted fs-7">{{ $submission->submitted_at ? $submission->submitted_at->format('M d, Y') : '' }}</div>
                            </div>
                            <div class="text-end">
                                @if($submission->grades->first())
                                    <span class="fw-bold">{{ $submission->grades->first()->points_earned }}/{{ $submission->assignment->total_points ?? 100 }}</span>
                                @else
                                    <span class="badge badge-light-warning">{{ __('Pending') }}</span>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    @else
                        <div class="text-center py-5">
                            <p class="text-muted mb-0">{{ __('No assignment submissions') }}</p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Quiz Grades --}}
            <div class="card">
                <div class="card-header border-0 pt-6">
                    <h3 class="card-title">{{ __('Quiz Scores') }}</h3>
                </div>
                <div class="card-body py-4">
                    @if($quizAttempts->count() > 0)
                        @foreach($quizAttempts as $attempt)
                        <div class="d-flex justify-content-between align-items-center py-2 {{ !$loop->last ? 'border-bottom' : '' }}">
                            <div>
                                <span class="fw-semibold">{{ $attempt->quiz->title }}</span>
                                <div class="text-muted fs-7">{{ $attempt->start_time ? $attempt->start_time->format('M d, Y') : '' }}</div>
                            </div>
                            <div class="text-end">
                                @if($attempt->score !== null)
                                    <span class="fw-bold">{{ $attempt->score }}/{{ $attempt->quiz->total_points ?? 100 }}</span>
                                @elseif($attempt->status === 'completed')
                                    <span class="badge badge-light-warning">{{ __('Pending') }}</span>
                                @else
                                    <span class="badge badge-light-secondary">{{ ucfirst($attempt->status) }}</span>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    @else
                        <div class="text-center py-5">
                            <p class="text-muted mb-0">{{ __('No quiz attempts') }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

</x-default-layout>
