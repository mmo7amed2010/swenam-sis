<x-default-layout>

    @section('title')
        {{ $module->title }}
    @endsection

    @section('breadcrumbs')
        <x-breadcrumb :items="[
            ['title' => __('Programs'), 'url' => route('admin.programs.index')],
            ['title' => $program->name, 'url' => route('admin.programs.show', $program)],
            ['title' => $course->course_code, 'url' => route('admin.programs.courses.show', [$program, $course])],
            ['title' => Str::limit($module->title, 40)]
        ]" />
    @endsection

    <!--begin::Toolbar-->
    <div class="d-flex flex-wrap flex-stack pb-7">
        <div class="d-flex flex-wrap align-items-center my-1">
            <span class="badge badge-circle badge-light-primary me-3 fs-2 fw-bold">{{ $module->order_index }}</span>
            <h3 class="fw-bold me-5 my-1">{{ $module->title }}</h3>
            @if($module->release_date && $module->release_date->isFuture())
            <span class="badge badge-light-warning fs-7 fw-bold my-1">{{ __('Scheduled') }} - {{ $module->release_date->format('M d, Y') }}</span>
            @else
            <x-tables.status-badge :status="$module->status" size="sm" />
            @endif
        </div>
        <div class="d-flex my-1">
            <a href="{{ route('admin.programs.courses.show', [$program, $course]) }}" class="btn btn-sm btn-light me-3">{{ __('Back to Course') }}</a>
            {{-- Edit Module - Modal not yet implemented, link removed --}}
            <button class="btn btn-sm btn-light" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
                {{ __('Actions') }}
                {!! getIcon('down', 'fs-5 ms-1') !!}
            </button>
            <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-semibold fs-7 w-200px py-4" data-kt-menu="true">
                <div class="menu-item px-3">
                    <form action="{{ route('admin.programs.courses.modules.toggle', [$program, $course, $module]) }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="menu-link px-3 btn btn-link text-start w-100 p-0">
                            {{ $module->status === 'published' ? __('Set to Draft') : __('Publish Module') }}
                        </button>
                    </form>
                </div>
                <div class="separator my-2"></div>
                <div class="menu-item px-3">
                    <form action="{{ route('admin.programs.courses.modules.destroy', [$program, $course, $module]) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="menu-link px-3 btn btn-link text-danger text-start w-100 p-0" onclick="return confirm('{{ __('Delete this module permanently?') }}')">
                            {{ __('Delete Module') }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!--end::Toolbar-->

    <div class="row g-5 g-xl-10">
        <!--begin::Col-->
        <div class="col-xl-8">
            <!--begin::Module details-->
            <x-cards.section
                :title="__('Module Details')"
                class="mb-5 mb-xl-10"
            >
                    @if($module->description)
                    <div class="mb-7">
                        <div class="text-gray-600 fw-bold mb-2">{{ __('Description') }}</div>
                        <div class="text-gray-900">{{ $module->description }}</div>
                    </div>
                    @endif

                    @if($module->content)
                    <div class="separator my-5"></div>
                    <div class="mb-7">
                        <div class="text-gray-600 fw-bold mb-2">{{ __('Content') }}</div>
                        <div class="text-gray-900">{!! nl2br(e($module->content)) !!}</div>
                    </div>
                    @endif

                    @if(!$module->description && !$module->content)
                    <div class="text-center text-gray-500 py-5">
                        {!! getIcon('file-text', 'fs-3x text-gray-400 mb-3') !!}
                        <div>{{ __('No content available for this module') }}</div>
                    </div>
                    @endif
            </x-cards.section>
            <!--end::Module details-->

            <!--begin::Exam Section (Story 4.13)-->
            <x-cards.section
                :title="__('Module Exam')"
                class="mb-5 mb-xl-10"
            >
                <x-slot:toolbar>
                    @if($module->exam)
                        <a href="{{ route('admin.programs.courses.quizzes.show', [$program, $course, $module->exam]) }}"
                           class="btn btn-info me-2">
                            {!! getIcon('question-circle', 'fs-2') !!}
                            {{ __('Manage Questions') }}
                        </a>
                        <button type="button" class="btn btn-primary"
                                data-bs-toggle="modal" data-bs-target="#kt_modal_edit_quiz_{{ $module->exam->id }}">
                            {!! getIcon('edit', 'fs-2') !!}
                            {{ __('Edit Exam') }}
                        </button>
                    @else
                        <button type="button"
                                class="btn btn-success"
                                data-bs-toggle="modal"
                                data-bs-target="#kt_modal_add_quiz_{{ $module->id }}">
                            {!! getIcon('plus-circle', 'fs-2') !!}
                            {{ __('Create Exam') }}
                        </button>
                    @endif
                </x-slot:toolbar>

                @if($module->exam)
                    <div class="alert alert-{{ $module->exam->published ? 'success' : 'warning' }} border-2 mb-4">
                        <div class="d-flex align-items-center">
                            <div class="symbol symbol-40px me-4">
                                <div class="symbol-label bg-{{ $module->exam->published ? 'success' : 'warning' }} text-white">
                                    <i class="fas fa-{{ $module->exam->published ? 'check' : 'clock' }}"></i>
                                </div>
                            </div>
                            <div class="d-flex flex-column">
                                <h6 class="mb-1 fw-bold">{{ __('Exam Status') }}: {{ $module->exam->published ? __('Published') : __('Draft') }}</h6>
                                <div class="d-flex align-items-center flex-wrap gap-4">
                                    <span><strong>{{ __('Title') }}:</strong> {{ $module->exam->title }}</span>
                                    <span><strong>{{ __('Passing Score') }}:</strong> {{ $module->exam->passing_score }}%</span>
                                    <span><strong>{{ __('Attempts') }}:</strong> {{ $module->exam->max_attempts }}</span>
                                    @if($module->exam->due_date)
                                    <span><strong>{{ __('Due Date') }}:</strong> {{ $module->exam->due_date->format('M d, Y') }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    @if($module->exam->questions->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-row-dashed align-middle g-5">
                            <thead>
                                <tr>
                                    <th>{{ __('Question') }}</th>
                                    <th>{{ __('Type') }}</th>
                                    <th>{{ __('Points') }}</th>
                                    <th class="text-center">{{ __('Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($module->exam->questions->sortBy('order_number') as $question)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <span class="badge badge-circle badge-light-primary me-3">{{ $question->order_number }}</span>
                                            <span>{{ Str::limit(strip_tags($question->question_text), 80) }}</span>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge badge-light-{{ $question->question_type === 'mcq' ? 'info' : 'warning' }}">
                                            {{ $question->question_type === 'mcq' ? __('Multiple Choice') : __('True/False') }}
                                        </span>
                                    </td>
                                    <td>{{ $question->points }}</td>
                                    <td class="text-center">
                                        <a href="{{ route('admin.programs.courses.quizzes.questions.edit', [$program, $course, $module->exam, $question]) }}"
                                           class="btn btn-sm btn-light-primary">
                                            {{ __('Edit') }}
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="text-center mt-4">
                        <small class="text-muted">
                            {{ __('Total Points') }}: {{ $module->exam->questions->sum('points') }} |
                            {{ __('Questions') }}: {{ $module->exam->questions->count() }}
                        </small>
                    </div>
                    @else
                    <div class="text-center py-5">
                        <i class="fas fa-question-circle fs-3x text-gray-400 mb-3"></i>
                        <div class="text-gray-600 mb-3">{{ __('No questions added yet') }}</div>
                        <a href="{{ route('admin.programs.courses.quizzes.questions.create', [$program, $course, $module->exam]) }}"
                           class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i>{{ __('Add First Question') }}
                        </a>
                    </div>
                    @endif
                @else
                    <div class="text-center py-8">
                        <div class="symbol symbol-75px mb-5">
                            <div class="symbol-label bg-light">
                                <i class="fas fa-exclamation-triangle fs-2 text-warning"></i>
                            </div>
                        </div>
                        <h4 class="text-gray-800 mb-3">{{ __('No Exam Created') }}</h4>
                        <p class="text-gray-600 mb-5">
                            {{ __('This module doesn\'t have an exam yet. Create a module exam to assess student understanding of all module content.') }}
                        </p>
                        <div class="d-flex justify-content-center gap-3">
                            <button type="button"
                                    class="btn btn-success btn-lg"
                                    data-bs-toggle="modal"
                                    data-bs-target="#kt_modal_add_quiz_{{ $module->id }}">
                                <i class="fas fa-plus me-2"></i>{{ __('Create Module Exam') }}
                            </button>
                        </div>
                    </div>
                @endif
            </x-cards.section>
            <!--end::Exam Section-->

            <!--begin::Lessons Section (Story 2.7, 2.8)-->
            <x-cards.section
                :title="__('Module Lessons')"
                class="mb-5 mb-xl-10"
            >
                <x-slot:toolbar>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#kt_modal_add_lesson_{{ $module->id }}">
                        {!! getIcon('plus', 'fs-2') !!}
                        {{ __('Add Lesson') }}
                    </button>
                </x-slot:toolbar>
                    @if($module->lessons->count() > 0)
                        <div class="d-flex flex-column gap-3" id="lessonsList_{{ $module->id }}">
                            @foreach($module->lessons->sortBy('order_number') as $lesson)
                            <div class="card card-bordered lesson-item" data-lesson-id="{{ $lesson->id }}">
                                <div class="card-body p-5">
                                    <div class="d-flex align-items-start justify-content-between">
                                        <div class="d-flex flex-column flex-grow-1">
                                            <div class="d-flex align-items-center mb-2">
                                                <span class="badge badge-circle badge-light-primary me-3 fs-3 fw-bold">{{ $lesson->order_number }}</span>
                                                <h5 class="mb-0 me-3">{{ $lesson->title }}</h5>
                                                @if($lesson->status === 'draft')
                                                <span class="badge badge-light-secondary">{{ __('Draft') }}</span>
                                                @else
                                                <span class="badge badge-light-success">{{ __('Published') }}</span>
                                                @endif
                                            </div>
                                            <div class="d-flex flex-wrap gap-5 text-gray-500 fs-7 ms-8">
                                                <div class="d-flex align-items-center">
                                                    {!! getIcon('file-text', 'fs-6 me-2') !!}
                                                    <span>{{ ucfirst(str_replace('_', ' ', $lesson->content_type)) }}</span>
                                                </div>
                                                @if($lesson->estimated_duration)
                                                <div class="d-flex align-items-center">
                                                    {!! getIcon('time', 'fs-6 me-2') !!}
                                                    <span>{{ $lesson->estimated_duration }} {{ __('minutes') }}</span>
                                                </div>
                                                @endif
                                                @if(isset($lessonProgressCounts[$lesson->id]) && $lessonProgressCounts[$lesson->id] > 0)
                                                <div class="d-flex align-items-center text-warning">
                                                    {!! getIcon('profile-user', 'fs-6 me-2') !!}
                                                    <span>{{ $lessonProgressCounts[$lesson->id] }} {{ __('students viewed') }}</span>
                                                </div>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="d-flex gap-2 ms-3">
                                            <button type="button" class="btn btn-sm btn-light-primary" data-bs-toggle="modal" data-bs-target="#kt_modal_edit_lesson_{{ $lesson->id }}" data-lesson-id="{{ $lesson->id }}">
                                                {!! getIcon('pencil', 'fs-2') !!}
                                                {{ __('Edit') }}
                                            </button>
                                            <button type="button" class="btn btn-sm btn-light-danger" data-bs-toggle="modal" data-bs-target="#kt_modal_delete_lesson_{{ $lesson->id }}" data-lesson-id="{{ $lesson->id }}" data-progress-count="{{ $lessonProgressCounts[$lesson->id] ?? 0 }}">
                                                {!! getIcon('trash', 'fs-2') !!}
                                                {{ __('Delete') }}
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <x-tables.empty-state
                            icon="file-text"
                            title="{{ __('No Lessons Created') }}"
                            message="{{ __('This module does not have any lessons yet. Create your first lesson to start building module content.') }}"
                            bgColor="ffffff"
                        />
                    @endif
            </x-cards.section>
            <!--end::Lessons Section-->
        </div>
        <!--end::Col-->

        <!--begin::Col-->
        <div class="col-xl-4">
            <!--begin::Settings-->
            <x-cards.section
                :title="__('Module Settings')"
                class="mb-5 mb-xl-10"
            >
                    <div class="d-flex flex-column gap-5">
                        <div class="d-flex flex-stack">
                            <div class="text-gray-600">{{ __('Order Position') }}</div>
                            <div class="text-gray-900 fw-bold">{{ $module->order_index }}</div>
                        </div>
                        <div class="separator"></div>
                        <div class="d-flex flex-stack">
                            <div class="text-gray-600">{{ __('Status') }}</div>
                            <div>
                                <x-tables.status-badge :status="$module->status" size="sm" />
                            </div>
                        </div>
                        <div class="separator"></div>
                        <div class="d-flex flex-stack">
                            <div class="text-gray-600">{{ __('Release Date') }}</div>
                            <div class="text-gray-900 fw-bold">
                                @if($module->release_date)
                                {{ $module->release_date->format('M d, Y') }}
                                @if($module->release_date->isFuture())
                                <span class="text-warning">({{ __('Upcoming') }})</span>
                                @endif
                                @else
                                {{ __('Immediate') }}
                                @endif
                            </div>
                        </div>
                        <div class="separator"></div>
                        <div class="d-flex flex-stack">
                            <div class="text-gray-600">{{ __('Created') }}</div>
                            <div class="text-gray-900 fw-bold">{{ $module->created_at->format('M d, Y') }}</div>
                        </div>
                        @if($module->updated_at != $module->created_at)
                        <div class="separator"></div>
                        <div class="d-flex flex-stack">
                            <div class="text-gray-600">{{ __('Last Updated') }}</div>
                            <div class="text-gray-900 fw-bold">{{ $module->updated_at->format('M d, Y') }}</div>
                        </div>
                        @endif
                    </div>
            </x-cards.section>
            <!--end::Settings-->

            <!--begin::Course info-->
            <x-cards.section
                :title="__('Course Information')"
            >
                    <div class="d-flex flex-column gap-5">
                        <div>
                            <div class="text-gray-600 mb-2">{{ __('Course Code') }}</div>
                            <div class="text-gray-900 fw-bold">{{ $course->course_code }}</div>
                        </div>
                        <div class="separator"></div>
                        <div>
                            <div class="text-gray-600 mb-2">{{ __('Course Name') }}</div>
                            <div class="text-gray-900 fw-bold">{{ $course->name }}</div>
                        </div>
                        <div class="separator"></div>
                        <div>
                            <div class="text-gray-600 mb-2">{{ __('Total Modules') }}</div>
                            <div class="text-gray-900 fw-bold">{{ $course->modules()->count() }}</div>
                        </div>
                        <div class="separator"></div>
                        <a href="{{ route('admin.programs.courses.show', [$program, $course]) }}" class="btn btn-light-primary w-100">
                            {{ __('View Course Details') }}
                        </a>
                    </div>
            </x-cards.section>
            <!--end::Course info-->
        </div>
        <!--end::Col-->
    </div>

    <!--begin::Add Lesson Modal (Story 2.7)-->
    @include('pages.admin.courses.modules.partials.add-lesson-modal', ['module' => $module, 'program' => $program, 'course' => $course])
    <!--end::Add Lesson Modal-->

    <!--begin::Edit Lesson Modals (Story 2.8)-->
    @foreach($module->lessons as $lesson)
    @include('pages.admin.courses.modules.partials.edit-lesson-modal', ['lesson' => $lesson, 'module' => $module, 'program' => $program, 'course' => $course])
    @endforeach
    <!--end::Edit Lesson Modals-->

    <!--begin::Delete Lesson Modals (Story 2.8)-->
    @foreach($module->lessons as $lesson)
    <div class="modal fade" id="kt_modal_delete_lesson_{{ $lesson->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered mw-500px">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="fw-bold">{{ __('Delete Lesson') }}</h2>
                    <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal">
                        {!! getIcon('cross', 'fs-1') !!}
                    </div>
                </div>
                <form action="{{ route('admin.programs.courses.modules.lessons.destroy', [$program, $course, $module, $lesson]) }}" method="POST" id="deleteLessonForm_{{ $lesson->id }}">
                    @csrf
                    @method('DELETE')
                    <div class="modal-body">
                        <div class="text-center mb-5">
                            {!! getIcon('trash', 'fs-3x text-danger mb-4') !!}
                            <h3 class="fw-bold mb-2">{{ __('Are you sure?') }}</h3>
                            <p class="text-gray-600 mb-4">{{ __('This action cannot be undone.') }}</p>
                        </div>
                        <div class="alert alert-warning d-flex align-items-center p-5 mb-5">
                            {!! getIcon('information-5', 'fs-2hx text-warning me-4') !!}
                            <div class="d-flex flex-column">
                                <h4 class="mb-1">{{ __('Delete Lesson') }}: {{ $lesson->title }}</h4>
                                @if(isset($lessonProgressCounts[$lesson->id]) && $lessonProgressCounts[$lesson->id] > 0)
                                <p class="mb-0">{{ $lessonProgressCounts[$lesson->id] }} {{ __('student(s) have viewed this lesson') }} (Story 2.8 AC #5)</p>
                                @else
                                <p class="mb-0">{{ __('No students have viewed this lesson yet.') }}</p>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                        <button type="submit" class="btn btn-danger">
                            <span class="indicator-label">{{ __('Delete Lesson') }}</span>
                            <span class="indicator-progress">{{ __('Please wait...') }}
                                <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endforeach
    <!--end::Delete Lesson Modals-->

    {{-- Edit Quiz/Exam Modal --}}
    @if($module->exam)
        @php $quiz = $module->exam; @endphp
        <x-modals.edit-quiz-form
            context="admin"
            :program="$program"
            :course="$course"
            :quiz="$quiz"
            :module="$module"
        />
    @endif

    @push('scripts')
        <script>
            // Delete form submission handlers
            @foreach($module->lessons as $lesson)
            document.getElementById('deleteLessonForm_{{ $lesson->id }}')?.addEventListener('submit', function() {
                const submitBtn = this.querySelector('button[type="submit"]');
                submitBtn.setAttribute('data-kt-indicator', 'on');
                submitBtn.disabled = true;
            });
            @endforeach
        </script>
    @endpush

</x-default-layout>
