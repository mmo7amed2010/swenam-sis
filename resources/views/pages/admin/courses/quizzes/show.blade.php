<x-default-layout>

    @section('title')
        {{ $quiz->title }} - {{ $course->course_code }}
    @endsection

    @section('breadcrumbs')
        <x-breadcrumb :items="[
            ['title' => __('Programs'), 'url' => route('admin.programs.index')],
            ['title' => $program->name, 'url' => route('admin.programs.show', $program)],
            ['title' => $course->course_code, 'url' => route('admin.programs.courses.show', [$program, $course])],
            ['title' => $quiz->title]
        ]" />
    @endsection

    {{-- Success/Error Messages --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
            <i class="ki-outline ki-check-circle fs-2hx text-success me-4"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
            <i class="ki-outline ki-information-5 fs-2hx text-danger me-4"></i>
            <div class="d-flex flex-column">
                <h5 class="mb-1 text-danger">{{ __('Error') }}</h5>
                <span>{{ session('error') }}</span>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Quiz Header --}}
    <x-quizzes.header
        context="admin"
        :program="$program"
        :course="$course"
        :quiz="$quiz"
    />

    {{-- Main Content Row --}}
    <div class="row g-5 g-xl-10">
        {{-- Questions List (Left/Main Column) --}}
        <div class="col-xl-8">
            <x-quizzes.questions-list
                context="admin"
                :program="$program"
                :course="$course"
                :quiz="$quiz"
            />
        </div>

        {{-- Settings Sidebar (Right Column) --}}
        <div class="col-xl-4">
            <x-quizzes.settings-sidebar
                context="admin"
                :program="$program"
                :course="$course"
                :quiz="$quiz"
            />
        </div>
    </div>

        {{-- Edit Quiz Modal --}}
        <x-modals.edit-quiz-form
            context="admin"
            :program="$program"
            :course="$course"
            :quiz="$quiz"
            :module="$quiz->module"
        />

        {{-- Add Question Modal --}}
        <x-quizzes.add-question-modal
            context="admin"
            :program="$program"
            :course="$course"
            :quiz="$quiz"
        />

        {{-- Edit Question Modals --}}
        @foreach($quiz->questions as $question)
            <x-quizzes.edit-question-modal
                context="admin"
                :program="$program"
                :course="$course"
                :quiz="$quiz"
                :question="$question"
            />
        @endforeach

    {{-- Quiz Page Styles --}}
    @push('styles')
        <link rel="stylesheet" href="{{ asset('assets/css/custom/admin/courses/quiz-view.css') }}">
    @endpush

    {{-- Quiz Page Scripts --}}
    @push('scripts')
        {{-- AJAX modal + partial refresh helpers --}}
        <script src="{{ asset('assets/js/custom/admin/courses/main.js') }}"></script>
        <script src="{{ asset('assets/js/custom/admin/courses/quiz-questions.js') }}"></script>
    @endpush

</x-default-layout>
