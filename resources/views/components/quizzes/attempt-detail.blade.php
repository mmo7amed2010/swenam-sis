{{--
 * Quiz Attempt Detail Component
 *
 * Displays detailed view of a student's quiz attempt with all answers.
 * Shared between admin and instructor views with context-aware routing.
 *
 * @param string $context - 'admin' or 'instructor'
 * @param \App\Models\Program $program
 * @param \App\Models\Course $course
 * @param \App\Models\Quiz $quiz
 * @param \App\Models\QuizAttempt $attempt
 * @param \Illuminate\Support\Collection $questions
 * @param \Illuminate\Support\Collection $studentAnswers - keyed by question_id
--}}

@props(['context', 'program', 'course', 'quiz', 'attempt', 'questions', 'studentAnswers'])

@php
    $isAdmin = $context === 'admin';

    // Context-aware routes
    $routePrefix = $isAdmin ? 'admin.programs.courses' : 'instructor.courses';
    $attemptsRoute = route("{$routePrefix}.quizzes.attempts", [$program, $course, $quiz]);
    $quizRoute = route("{$routePrefix}.quizzes.show", [$program, $course, $quiz]);

    // Calculate statistics
    $totalPoints = $quiz->total_points ?? 100;
    $earnedPoints = $attempt->score ?? 0;
    $percentage = $totalPoints > 0 ? ($earnedPoints / $totalPoints) * 100 : 0;
    $passed = $percentage >= ($quiz->passing_score ?? 60);

    // Calculate correct/incorrect count
    $correctCount = 0;
    $incorrectCount = 0;
    foreach ($questions as $question) {
        $studentAnswer = $studentAnswers->get($question->id);
        if ($question->isCorrectAnswer($studentAnswer)) {
            $correctCount++;
        } else {
            $incorrectCount++;
        }
    }

    // Calculate time taken
    $timeTaken = null;
    if ($attempt->start_time && $attempt->end_time) {
        $timeTaken = $attempt->start_time->diffInMinutes($attempt->end_time);
    }
@endphp

{{-- Summary Card --}}
<div class="card card-flush mb-5 mb-xl-8">
    <div class="card-header pt-7">
        <h3 class="card-title align-items-start flex-column">
            <span class="card-label fw-bold text-gray-800">{{ __('Attempt Details') }}</span>
            <span class="text-muted mt-1 fw-semibold fs-7">{{ $quiz->title }}</span>
        </h3>
        <div class="card-toolbar">
            <a href="{{ $attemptsRoute }}" class="btn btn-sm btn-light">
                {!! getIcon('arrow-left', 'fs-5 me-1') !!}
                {{ __('Back to Attempts') }}
            </a>
        </div>
    </div>

    <div class="card-body">
        <div class="row g-5 g-xl-10">
            {{-- Student Info --}}
            <div class="col-xl-4">
                <div class="d-flex align-items-center mb-7">
                    <div class="symbol symbol-60px symbol-circle me-5">
                        <span class="symbol-label bg-light-primary text-primary fs-2 fw-bold">
                            {{ strtoupper(substr($attempt->student->name ?? 'U', 0, 1)) }}
                        </span>
                    </div>
                    <div class="d-flex flex-column">
                        <span class="text-gray-800 fs-4 fw-bold">{{ $attempt->student->name ?? __('Unknown Student') }}</span>
                        <span class="text-gray-500 fw-semibold fs-7">{{ $attempt->student->email ?? '' }}</span>
                    </div>
                </div>

                {{-- Submission Info --}}
                @if($attempt->start_time)
                <div class="d-flex align-items-center mb-5">
                    <div class="symbol symbol-45px me-4">
                        <span class="symbol-label bg-light-info">
                            {!! getIcon('calendar', 'fs-2 text-info') !!}
                        </span>
                    </div>
                    <div class="d-flex flex-column">
                        <span class="text-gray-800 fs-6 fw-bold">{{ $attempt->start_time->format('M d, Y') }}</span>
                        <span class="text-gray-500 fw-semibold fs-7">{{ __('Started at') }} {{ $attempt->start_time->format('g:i A') }}</span>
                    </div>
                </div>
                @endif

                @if($attempt->end_time)
                <div class="d-flex align-items-center mb-5">
                    <div class="symbol symbol-45px me-4">
                        <span class="symbol-label bg-light-success">
                            {!! getIcon('check-circle', 'fs-2 text-success') !!}
                        </span>
                    </div>
                    <div class="d-flex flex-column">
                        <span class="text-gray-800 fs-6 fw-bold">{{ $attempt->end_time->format('M d, Y') }}</span>
                        <span class="text-gray-500 fw-semibold fs-7">{{ __('Submitted at') }} {{ $attempt->end_time->format('g:i A') }}</span>
                    </div>
                </div>
                @endif

                @if($timeTaken !== null)
                <div class="d-flex align-items-center">
                    <div class="symbol symbol-45px me-4">
                        <span class="symbol-label bg-light-warning">
                            {!! getIcon('timer', 'fs-2 text-warning') !!}
                        </span>
                    </div>
                    <div class="d-flex flex-column">
                        <span class="text-gray-800 fs-6 fw-bold">{{ $timeTaken }} {{ __('minutes') }}</span>
                        <span class="text-gray-500 fw-semibold fs-7">{{ __('Time Taken') }}</span>
                    </div>
                </div>
                @endif
            </div>

            {{-- Score Display --}}
            <div class="col-xl-4">
                <div class="d-flex flex-column align-items-center justify-content-center h-100">
                    {{-- Score Circle --}}
                    <div class="symbol symbol-100px mb-5">
                        <div class="symbol-label bg-light-{{ $passed ? 'success' : 'danger' }} position-relative rounded-circle">
                            <div class="position-absolute top-50 start-50 translate-middle text-center">
                                <span class="fs-2x fw-bold text-{{ $passed ? 'success' : 'danger' }}">{{ number_format($percentage, 0) }}%</span>
                            </div>
                        </div>
                    </div>

                    {{-- Score Points --}}
                    <div class="text-center mb-3">
                        <span class="fs-2hx fw-bold text-gray-900">{{ number_format($earnedPoints, 1) }}</span>
                        <span class="fs-4 fw-semibold text-gray-500"> / {{ $totalPoints }}</span>
                    </div>

                    {{-- Pass/Fail Badge --}}
                    @if($passed)
                        <span class="badge badge-light-success fs-4 fw-bold px-5 py-3">
                            {!! getIcon('check-circle', 'fs-2 me-2') !!}
                            {{ __('PASSED') }}
                        </span>
                    @else
                        <span class="badge badge-light-danger fs-4 fw-bold px-5 py-3">
                            {!! getIcon('cross-circle', 'fs-2 me-2') !!}
                            {{ __('FAILED') }}
                        </span>
                    @endif

                    <span class="text-muted fs-7 mt-2">{{ __('Passing: :score%', ['score' => $quiz->passing_score ?? 60]) }}</span>
                </div>
            </div>

            {{-- Question Stats --}}
            <div class="col-xl-4">
                <div class="d-flex flex-column justify-content-center h-100">
                    <div class="d-flex align-items-center mb-5">
                        <div class="symbol symbol-45px me-4">
                            <span class="symbol-label bg-light-success">
                                {!! getIcon('check', 'fs-2 text-success') !!}
                            </span>
                        </div>
                        <div class="d-flex flex-column">
                            <span class="text-gray-800 fs-4 fw-bold">{{ $correctCount }}</span>
                            <span class="text-gray-500 fw-semibold fs-7">{{ __('Correct Answers') }}</span>
                        </div>
                    </div>

                    <div class="d-flex align-items-center mb-5">
                        <div class="symbol symbol-45px me-4">
                            <span class="symbol-label bg-light-danger">
                                {!! getIcon('cross', 'fs-2 text-danger') !!}
                            </span>
                        </div>
                        <div class="d-flex flex-column">
                            <span class="text-gray-800 fs-4 fw-bold">{{ $incorrectCount }}</span>
                            <span class="text-gray-500 fw-semibold fs-7">{{ __('Incorrect Answers') }}</span>
                        </div>
                    </div>

                    <div class="d-flex align-items-center">
                        <div class="symbol symbol-45px me-4">
                            <span class="symbol-label bg-light-primary">
                                {!! getIcon('questionnaire-tablet', 'fs-2 text-primary') !!}
                            </span>
                        </div>
                        <div class="d-flex flex-column">
                            <span class="text-gray-800 fs-4 fw-bold">{{ $questions->count() }}</span>
                            <span class="text-gray-500 fw-semibold fs-7">{{ __('Total Questions') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Question Review Section --}}
<div class="card card-flush">
    <div class="card-header pt-7">
        <h3 class="card-title align-items-start flex-column">
            <span class="card-label fw-bold text-gray-800">{{ __('Question Review') }}</span>
            <span class="text-muted mt-1 fw-semibold fs-7">{{ __('Review student answers and correct solutions') }}</span>
        </h3>
    </div>

    <div class="card-body">
        @foreach($questions as $idx => $question)
        @php
            $studentAnswer = $studentAnswers->get($question->id);
            $isCorrect = $question->isCorrectAnswer($studentAnswer);
            $earnedQuestionPoints = $isCorrect ? $question->points : 0;
        @endphp

        <div class="question-review-item mb-8 p-6 rounded border border-dashed {{ $isCorrect ? 'border-success bg-light-success' : 'border-danger bg-light-danger' }}">
            {{-- Question Header --}}
            <div class="d-flex justify-content-between align-items-start mb-5">
                <div class="d-flex align-items-center">
                    <div class="symbol symbol-45px me-4">
                        <div class="symbol-label bg-{{ $isCorrect ? 'success' : 'danger' }}">
                            <span class="fs-4 fw-bold text-white">{{ $idx + 1 }}</span>
                        </div>
                    </div>
                    <div class="d-flex flex-column">
                        <span class="fs-5 fw-bold text-gray-900">{{ __('Question :num', ['num' => $idx + 1]) }}</span>
                        <span class="fs-7 fw-semibold text-gray-500">
                            {{ $question->points }} {{ __('points') }}
                            @if($question->question_type === 'mcq')
                                <span class="badge badge-light-primary ms-2">{{ __('Multiple Choice') }}</span>
                            @elseif($question->question_type === 'true_false')
                                <span class="badge badge-light-info ms-2">{{ __('True/False') }}</span>
                            @endif
                        </span>
                    </div>
                </div>
                <span class="badge badge-light-{{ $isCorrect ? 'success' : 'danger' }} fs-5 fw-bold">
                    @if($isCorrect)
                    {!! getIcon('check-circle', 'fs-2 me-1') !!}
                    {{ __('Correct') }}
                    @else
                    {!! getIcon('cross-circle', 'fs-2 me-1') !!}
                    {{ __('Incorrect') }}
                    @endif
                </span>
            </div>

            {{-- Question Text --}}
            <div class="mb-6 fs-5 text-gray-800 lh-lg">
                {!! $question->question_text !!}
            </div>

            {{-- Student Answer --}}
            <div class="notice d-flex bg-white rounded border border-{{ $isCorrect ? 'success' : 'danger' }} p-5 mb-5">
                <div class="d-flex flex-stack flex-grow-1">
                    <div class="fw-semibold">
                        <h5 class="text-gray-900 fw-bold mb-2">
                            {!! getIcon('user', 'fs-2 text-' . ($isCorrect ? 'success' : 'danger') . ' me-2') !!}
                            {{ __("Student's Answer:") }}
                        </h5>
                        <div class="fs-6 text-gray-700">
                            @if($question->question_type === 'mcq')
                                @php
                                    $answers = $question->answers_json ?? [];
                                    $studentAnswerArray = is_array($studentAnswer) ? $studentAnswer : [$studentAnswer];
                                @endphp
                                @if(empty($studentAnswerArray) || (count($studentAnswerArray) === 1 && $studentAnswerArray[0] === null))
                                    <span class="text-muted fst-italic">{{ __('Not answered') }}</span>
                                @else
                                    <ul class="mb-0">
                                        @foreach($studentAnswerArray as $ansIdx)
                                            @if(isset($answers[$ansIdx]))
                                            <li class="mb-1">{{ $answers[$ansIdx]['text'] ?? '' }}</li>
                                            @endif
                                        @endforeach
                                    </ul>
                                @endif
                            @elseif($question->question_type === 'true_false')
                                @if($studentAnswer === null)
                                    <span class="text-muted fst-italic">{{ __('Not answered') }}</span>
                                @else
                                    <span class="badge badge-light-{{ $studentAnswer === 'true' || $studentAnswer === true || $studentAnswer === '1' || $studentAnswer === 1 ? 'success' : 'danger' }} fs-6">
                                        @if($studentAnswer === 'true' || $studentAnswer === true || $studentAnswer === '1' || $studentAnswer === 1)
                                        {!! getIcon('check-circle', 'fs-2 me-1') !!}
                                        {{ __('True') }}
                                        @else
                                        {!! getIcon('cross-circle', 'fs-2 me-1') !!}
                                        {{ __('False') }}
                                        @endif
                                    </span>
                                @endif
                            @else
                                {{ $studentAnswer ?? __('Not answered') }}
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- Correct Answer (always shown for instructor/admin) --}}
            <div class="notice d-flex bg-light-success rounded border-success border border-dashed p-5 mb-5">
                {!! getIcon('information-5', 'fs-2tx text-success me-4') !!}
                <div class="d-flex flex-stack flex-grow-1">
                    <div class="fw-semibold">
                        <h5 class="text-gray-900 fw-bold mb-2">{{ __('Correct Answer:') }}</h5>
                        <div class="fs-6 text-gray-700">
                            @if($question->question_type === 'mcq')
                                @php
                                    $answers = $question->answers_json ?? [];
                                    $correctIndices = collect($answers)->filter(fn($a) => $a['is_correct'] ?? false)->keys();
                                @endphp
                                <ul class="mb-0">
                                    @foreach($correctIndices as $idx)
                                        <li class="mb-1 text-success fw-bold">{{ $answers[$idx]['text'] ?? '' }}</li>
                                    @endforeach
                                </ul>
                            @elseif($question->question_type === 'true_false')
                                @php
                                    $correctValue = ($question->answers_json['correct'] ?? false);
                                @endphp
                                <span class="badge badge-success fs-6">
                                    @if($correctValue)
                                    {!! getIcon('check-circle', 'fs-2 me-1') !!}
                                    {{ __('True') }}
                                    @else
                                    {!! getIcon('cross-circle', 'fs-2 me-1') !!}
                                    {{ __('False') }}
                                    @endif
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- Points Earned --}}
            <div class="d-flex align-items-center justify-content-end">
                <span class="badge badge-light-{{ $isCorrect ? 'success' : 'danger' }} fs-6 fw-bold">
                    {!! getIcon('medal-star', 'fs-2 me-1') !!}
                    {{ __('Points:') }} {{ number_format($earnedQuestionPoints, 1) }} / {{ $question->points }}
                </span>
            </div>
        </div>
        @endforeach
    </div>
</div>
