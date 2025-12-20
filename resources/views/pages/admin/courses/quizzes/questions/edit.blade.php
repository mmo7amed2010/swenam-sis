<x-default-layout>

    @section('title')
        {{ __('Edit Question') }} - {{ $quiz->title }}
    @endsection

    @section('breadcrumbs')
        <x-breadcrumb :items="[
            ['title' => __('Programs'), 'url' => route('admin.programs.index')],
            ['title' => $program->name, 'url' => route('admin.programs.show', $program)],
            
            ['title' => $course->course_code, 'url' => route('admin.programs.courses.show', [$program, $course])],
            ['title' => $quiz->title, 'url' => route('admin.programs.courses.quizzes.show', [$program, $course, $quiz])],
            ['title' => __('Edit Question')]
        ]" />
    @endsection

    @php
        if ($question->question_type === 'mcq') {
            $defaultAnswers = $question->answers_json ?? [['text' => '', 'is_correct' => false], ['text' => '', 'is_correct' => false]];
        } else {
            $defaultAnswers = [];
        }
        $answers = old('answers', $defaultAnswers);

        if ($question->question_type === 'true_false') {
            $defaultCorrect = $question->answers_json['correct'] ?? null;
        } else {
            $defaultCorrect = null;
        }
        $correctAnswer = old('correct_answer', $defaultCorrect);
        $questionType = old('question_type', $question->question_type);
    @endphp

    <form action="{{ route('admin.programs.courses.quizzes.questions.update', [$program, $course, $quiz, $question]) }}" method="POST" id="questionForm">
        @csrf
        @method('PUT')

        <x-forms.validation-errors />

        @php
            $attemptCount = $quiz->attempts()->count();
        @endphp

        @if($attemptCount > 0)
        <div class="alert alert-warning mb-5">
            {!! getIcon('information-5', 'fs-5 me-2') !!}
            <strong>{{ __('Warning:') }}</strong> {{ __(':count students have taken this quiz. Changes may affect scoring.', ['count' => $attemptCount]) }}
        </div>
        @endif

        <x-forms.crud-layout>
            <x-slot name="main">
                <x-forms.card-section title="{{ __('Question Type') }}">
                    <div class="mb-5">
                        <label class="form-label required">{{ __('Question Type') }}</label>
                        <select name="question_type"
                                id="question_type"
                                class="form-select"
                                required
                                onchange="window.toggleQuestionType(this.value)">
                            <option value="mcq" {{ $questionType === 'mcq' ? 'selected' : '' }}>{{ __('Multiple Choice') }}</option>
                            <option value="true_false" {{ $questionType === 'true_false' ? 'selected' : '' }}>{{ __('True/False') }}</option>
                        </select>
                        <div class="form-text">{{ __('Changing question type will delete existing answers') }}</div>
                    </div>
                </x-forms.card-section>

                <x-forms.card-section title="{{ __('Question Content') }}">
                    <x-forms.textarea
                        name="question_text"
                        label="{{ __('Question Text') }}"
                        :rows="8"
                        placeholder="{{ __('Enter your question here...') }}"
                        :value="old('question_text', $question->question_text)"
                        help="{{ __('Rich text editor for question content') }}"
                        :required="true"
                    />

                    <x-forms.form-group
                        name="points"
                        label="{{ __('Points') }}"
                        type="number"
                        :required="true"
                        :min="1"
                        :max="100"
                        :value="old('points', $question->points)"
                        help="{{ __('Points for correct answer (1-100)') }}"
                    />
                </x-forms.card-section>

                <div id="mcq-section" style="display: {{ $questionType === 'mcq' ? 'block' : 'none' }};">
                    <x-forms.card-section title="{{ __('Answer Options') }}">
                        <div class="mb-5">
                            <label class="form-label">{{ __('Add up to 6 answer options. Mark at least one as correct.') }}</label>
                        </div>

                        <div class="d-flex flex-column gap-3" id="answers-container">
                            @foreach($answers as $index => $answer)
                            <div class="d-flex gap-3 align-items-start answer-row" data-index="{{ $index }}">
                                <div class="flex-grow-1">
                                    <input type="text"
                                           class="form-control"
                                           name="answers[{{ $index }}][text]"
                                           value="{{ old("answers.{$index}.text", $answer['text'] ?? '') }}"
                                           placeholder="{{ __('Option :num', ['num' => $index + 1]) }}"
                                           required>
                                </div>
                                <div class="form-check form-check-custom form-check-solid">
                                    <input type="hidden" name="answers[{{ $index }}][is_correct]" value="0">
                                    <input class="form-check-input"
                                           type="checkbox"
                                           name="answers[{{ $index }}][is_correct]"
                                           value="1"
                                           id="correct-{{ $index }}"
                                           {{ old("answers.{$index}.is_correct", $answer['is_correct'] ?? false) ? 'checked' : '' }}
                                           onchange="window.checkCorrectAnswer()">
                                    <label class="form-check-label" for="correct-{{ $index }}">
                                        {{ __('Correct') }}
                                    </label>
                                </div>
                                @if(count($answers) > 2)
                                <button type="button"
                                        class="btn btn-sm btn-light-danger"
                                        onclick="window.removeAnswer({{ $index }})">
                                    {!! getIcon('trash', 'fs-2') !!}
                                </button>
                                @endif
                            </div>
                            @endforeach
                        </div>

                        <button type="button"
                                class="btn btn-sm btn-light-primary mt-3"
                                onclick="window.addAnswer()"
                                id="add-answer-btn"
                                style="display: {{ count($answers) < 6 ? 'block' : 'none' }};">
                            {!! getIcon('plus', 'fs-2') !!} {{ __('Add Answer Option') }}
                        </button>

                        <input type="hidden" name="has_correct_answer" id="has_correct_answer" value="0">
                    </x-forms.card-section>
                </div>

                <div id="true-false-section" style="display: {{ $questionType === 'true_false' ? 'block' : 'none' }};">
                    <x-forms.card-section title="{{ __('Correct Answer') }}">
                        <div class="form-check form-check-custom form-check-solid mb-3">
                            <input class="form-check-input"
                                   type="radio"
                                   name="correct_answer"
                                   value="1"
                                   id="true_answer"
                                   {{ $questionType === 'true_false' ? 'required' : '' }}
                                   {{ ($correctAnswer === true || $correctAnswer === 1 || $correctAnswer === '1') ? 'checked' : '' }}>
                            <label class="form-check-label" for="true_answer">
                                {{ __('True') }}
                            </label>
                        </div>
                        <div class="form-check form-check-custom form-check-solid">
                            <input class="form-check-input"
                                   type="radio"
                                   name="correct_answer"
                                   value="0"
                                   id="false_answer"
                                   {{ ($correctAnswer === false || $correctAnswer === 0 || $correctAnswer === '0') ? 'checked' : '' }}>
                            <label class="form-check-label" for="false_answer">
                                {{ __('False') }}
                            </label>
                        </div>
                    </x-forms.card-section>
                </div>
            </x-slot>
        </x-forms.crud-layout>

        <x-forms.form-actions
            cancel-route="{{ route('admin.programs.courses.quizzes.show', [$program, $course, $quiz]) }}"
            submit-text="{{ __('Update Question') }}"
        />
    </form>

    @push('scripts')
    <script src="{{ asset('js/forms/question-management.js') }}"></script>
    <script>
        // Wait for the script to load and expose functions globally
        document.addEventListener('DOMContentLoaded', function() {
            // Check if QuestionManagement is available (from webpack bundle)
            if (typeof window.QuestionManagement !== 'undefined') {
                window.toggleQuestionType = window.QuestionManagement.toggleQuestionType;
                window.addAnswer = window.QuestionManagement.addAnswer;
                window.removeAnswer = window.QuestionManagement.removeAnswer;
                window.checkCorrectAnswer = window.QuestionManagement.checkCorrectAnswer;
                window.reindexAnswers = window.QuestionManagement.reindexAnswers;
                
                // Initialize question management
                window.QuestionManagement.initQuestionManagement({ 
                    initialCount: {{ count($answers) }}, 
                    minAnswers: 2, 
                    maxAnswers: 6 
                });
                
                // Set initial state based on question type
                const questionType = document.getElementById('question_type').value;
                window.toggleQuestionType(questionType);
            } else {
                console.error('QuestionManagement module not loaded. Available:', Object.keys(window).filter(k => k.includes('Question')));
            }
        });
    </script>
    @endpush

</x-default-layout>
