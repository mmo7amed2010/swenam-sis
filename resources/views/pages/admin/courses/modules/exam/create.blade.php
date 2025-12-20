<x-default-layout>

    @section('title')
        {{ __('Create Module Exam') }} - {{ $module->title }}
    @endsection

    @section('breadcrumbs')
        <x-breadcrumb :items="[
            ['title' => __('Programs'), 'url' => route('admin.programs.index')],
            ['title' => $program->name, 'url' => route('admin.programs.show', $program)],
            ['title' => $course->course_code, 'url' => route('admin.programs.courses.show', [$program, $course])],
            ['title' => $module->title, 'url' => '#'],
            ['title' => __('Create Module Exam')]
        ]" />
    @endsection

    <div class="alert alert-info border-2 mb-4">
        <div class="d-flex align-items-center">
            <i class="fas fa-info-circle fs-4 me-3"></i>
            <div>
                <h6 class="mb-1">{{ __('Module Exam Information') }}</h6>
                <p class="mb-0">{{ __('This will create a module-level exam that covers all content in ":module". Exams have fixed 2 attempts and unlock the next module upon passing.', ['module' => $module->title]) }}</p>
            </div>
        </div>
    </div>

    <form action="{{ route('admin.programs.courses.modules.exam.store', [$program, $course, $module]) }}" method="POST" id="examForm">
        @csrf

        <x-forms.validation-errors />

        <x-forms.crud-layout>
            <x-slot:main>
                <x-forms.card-section :title="__('General Information')">
                    <x-forms.form-group
                        name="title"
                        label="{{ __('Exam Title') }}"
                        :required="true"
                        placeholder="{{ __('E.g., Module :num Final Exam', ['num' => $module->order_number]) }}"
                        :value="old('title', 'Module ' . $module->order_number . ' Final Exam')"
                    />

                    <x-forms.textarea
                        name="description"
                        label="{{ __('Description & Instructions') }}"
                        :rows="8"
                        placeholder="{{ __('Comprehensive exam covering all material from this module...') }}"
                        :value="old('description')"
                        help="{{ __('Rich text editor for exam description and student instructions') }}"
                    />

                    <div class="row g-9 mb-10">
                        <div class="col-md-6">
                            <x-forms.form-group
                                name="due_date"
                                label="{{ __('Due Date & Time') }}"
                                type="datetime-local"
                                :value="old('due_date')"
                                help="{{ __('Optional due date - students cannot start after this time') }}"
                            />
                        </div>
                        <div class="col-md-6">
                            <x-forms.form-group
                                name="time_limit"
                                label="{{ __('Time Limit (minutes)') }}"
                                type="number"
                                :min="1"
                                :max="480"
                                placeholder="120"
                                :value="old('time_limit', 120)"
                                help="{{ __('Leave empty for unlimited time. Max 8 hours (480 minutes)') }}"
                            />
                        </div>
                    </div>
                </x-forms.card-section>

                <x-forms.card-section :title="__('Exam Settings')" class="border-warning">
                    <div class="alert alert-warning border-2 mb-4">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>{{ __('Module exams have specific requirements:') }}</strong>
                        <ul class="mb-0 mt-2">
                            <li>{{ __('Fixed 2 attempts per student') }}</li>
                            <li>{{ __('Module-level scope (covers entire module)') }}</li>
                            <li>{{ __('Passing unlocks next module content') }}</li>
                            <li>{{ __('Students must contact instructor if both attempts are failed') }}</li>
                        </ul>
                    </div>

                    <div class="row g-9 mb-10">
                        <div class="col-md-6">
                            <x-forms.form-group
                                name="passing_score"
                                label="{{ __('Passing Score (%)') }}"
                                type="number"
                                :required="true"
                                :min="0"
                                :max="100"
                                :value="old('passing_score', 70)"
                                help="{{ __('Minimum percentage to pass (0-100). This is configurable per exam.') }}"
                            />
                        </div>
                        <div class="col-md-6">
                            <x-forms.form-group
                                name="max_attempts"
                                label="{{ __('Maximum Attempts') }}"
                                type="number"
                                value="2"
                                readonly
                                help="{{ __('Module exams always have exactly 2 attempts') }}"
                            />
                        </div>
                    </div>

                    <x-forms.select
                        name="show_correct_answers"
                        label="{{ __('Show Correct Answers') }}"
                        :required="true"
                        :options="[
                            ['value' => 'never', 'text' => __('Never')],
                            ['value' => 'after_all_attempts', 'text' => __('After All Attempts Used')],
                            ['value' => 'after_due_date', 'text' => __('After Due Date')]
                        ]"
                        :value="old('show_correct_answers', 'after_all_attempts')"
                        help="{{ __('When should students see correct answers? Recommended: After All Attempts') }}"
                    />

                    <div class="mt-6">
                        <label class="form-label fw-bold">{{ __('Additional Settings') }}</label>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-check form-check-custom form-check-solid form-check-sm mb-3">
                                    <input class="form-check-input" type="checkbox" name="shuffle_questions" value="1"
                                           @if(old('shuffle_questions')) checked @endif id="shuffle_questions">
                                    <label class="form-check-label" for="shuffle_questions">
                                        {{ __('Shuffle Questions') }}
                                        <span class="form-text d-block">{{ __('Randomize question order for each student') }}</span>
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-check form-check-custom form-check-solid form-check-sm mb-3">
                                    <input class="form-check-input" type="checkbox" name="shuffle_answers" value="1"
                                           @if(old('shuffle_answers')) checked @endif id="shuffle_answers">
                                    <label class="form-check-label" for="shuffle_answers">
                                        {{ __('Shuffle Answer Options') }}
                                        <span class="form-text d-block">{{ __('Randomize answer choices for multiple choice questions') }}</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Hidden fields for exam-specific settings -->
                    <input type="hidden" name="assessment_type" value="exam">
                    <input type="hidden" name="scope" value="module">
                    <input type="hidden" name="module_id" value="{{ $module->id }}">
                </x-forms.card-section>

                <x-forms.card-section :title="__('Publishing Settings')">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-check form-check-custom form-check-solid mb-3">
                                <input class="form-check-input" type="checkbox" name="published" value="1"
                                       @if(old('published', true)) checked @endif id="published">
                                <label class="form-check-label fw-bold" for="published">
                                    {{ __('Publish Exam Immediately') }}
                                    <span class="form-text d-block">{{ __('Students can access this exam as soon as it has questions') }}</span>
                                </label>
                            </div>
                        </div>
                    </div>
                </x-forms.card-section>
            </x-slot:main>

            <x-slot:sidebar>
                <x-forms.card-section :title="__('Exam Overview')">
                    <div class="mb-4">
                        <h6 class="fw-bold">{{ __('Module Information') }}</h6>
                        <div class="p-3 bg-light rounded">
                            <p class="mb-1"><strong>{{ __('Module') }}:</strong> {{ $module->title }}</p>
                            <p class="mb-1"><strong>{{ __('Order') }}:</strong> {{ $module->order_number }}</p>
                            <p class="mb-1"><strong>{{ __('Course') }}:</strong> {{ $course->course_code }}</p>
                            <p class="mb-0"><strong>{{ __('Program') }}:</strong> {{ $program->name }}</p>
                        </div>
                    </div>

                    <div class="alert alert-info mb-4">
                        <h6 class="alert-heading">{{ __('What happens next?') }}</h6>
                        <ol class="mb-0 small">
                            <li>{{ __('After creating the exam, you will be redirected to add questions') }}</li>
                            <li>{{ __('Students must complete all module content before attempting the exam') }}</li>
                            <li>{{ __('Students who pass will unlock the next module automatically') }}</li>
                            <li>{{ __('Students who fail both attempts will need instructor intervention') }}</li>
                        </ol>
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i>{{ __('Create Exam') }}
                        </button>
                        <a href="{{ route('admin.programs.courses.show', [$program, $course]) }}" class="btn btn-secondary">
                            <i class="fas fa-times me-2"></i>{{ __('Cancel') }}
                        </a>
                    </div>
                </x-forms.card-section>
            </x-slot:sidebar>
        </x-forms.crud-layout>
    </form>

    @push('scripts')
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Add confirmation before form submission
        const form = document.getElementById('examForm');

        form.addEventListener('submit', function(e) {
            const passingScore = document.querySelector('input[name="passing_score"]').value;

            if (passingScore < 50) {
                if (!confirm('Warning: You set a passing score below 50%. This may be too lenient for a module exam. Continue anyway?')) {
                    e.preventDefault();
                    return false;
                }
            } else if (passingScore > 90) {
                if (!confirm('Warning: You set a passing score above 90%. This may be too strict for a module exam. Continue anyway?')) {
                    e.preventDefault();
                    return false;
                }
            }

            return true;
        });

        // Add visual indicators for exam-specific fields
        const examFields = document.querySelectorAll('input[readonly]');
        examFields.forEach(field => {
            field.classList.add('bg-light');
            field.setAttribute('title', 'This setting is fixed for module exams');
        });
    });
    </script>
    @endpush

</x-default-layout>