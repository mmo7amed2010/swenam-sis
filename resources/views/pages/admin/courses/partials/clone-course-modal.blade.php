{{--
 * Clone Course Modal
 *
 * Modal for cloning a course with options to include modules, lessons, etc.
 *
 * @param \App\Models\Program $program
 * @param \App\Models\Course $course
--}}

<div class="modal fade" id="kt_modal_clone_course" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mw-650px">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="fw-bold">{{ __('Clone Course') }} - {{ $course->course_code }}</h2>
                <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal">
                    {!! getIcon('cross', 'fs-1') !!}
                </div>
            </div>
            <form action="{{ route('admin.programs.courses.clone', [$program, $course]) }}" method="POST" id="cloneCourseForm">
                @csrf
                <div class="modal-body scroll-y mx-5 mx-xl-15 my-7">
                    <div class="mb-10 fv-row">
                        <label class="required form-label">{{ __('New Course Code') }}</label>
                        <input type="text"
                               name="new_course_code"
                               class="form-control @error('new_course_code') is-invalid @enderror"
                               placeholder="{{ __('E.g., CS2025') }}"
                               value="{{ old('new_course_code') }}"
                               required />
                        <div class="form-text">{{ __('Must be 6-10 uppercase letters/numbers (e.g., CS101).') }}</div>
                        @error('new_course_code')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-10 fv-row">
                        <label class="required form-label">{{ __('New Course Name') }}</label>
                        <input type="text"
                               name="new_course_name"
                               class="form-control @error('new_course_name') is-invalid @enderror"
                               placeholder="{{ __('E.g., Advanced Programming Concepts') }}"
                               value="{{ old('new_course_name') }}"
                               required />
                        @error('new_course_name')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-10">
                        <label class="form-label fw-semibold">{{ __('Include in Clone') }}</label>
                        <div class="d-flex flex-column gap-3">
                            <label class="form-check form-check-custom form-check-solid">
                                <input type="checkbox"
                                       class="form-check-input"
                                       name="include_modules"
                                       value="1"
                                       checked>
                                <span class="form-check-label">
                                    {{ __('Modules (structure only)') }}
                                </span>
                            </label>
                            <label class="form-check form-check-custom form-check-solid">
                                <input type="checkbox"
                                       class="form-check-input"
                                       name="include_lessons"
                                       value="1"
                                       checked>
                                <span class="form-check-label">
                                    {{ __('Lessons & instructional content') }}
                                </span>
                            </label>
                            <label class="form-check form-check-custom form-check-solid">
                                <input type="checkbox"
                                       class="form-check-input"
                                       name="include_assignments"
                                       value="1">
                                <span class="form-check-label">
                                    {{ __('Assignments (including settings)') }}
                                </span>
                            </label>
                            <label class="form-check form-check-custom form-check-solid">
                                <input type="checkbox"
                                       class="form-check-input"
                                       name="include_quizzes"
                                       value="1">
                                <span class="form-check-label">
                                    {{ __('Quizzes/Exams') }}
                                </span>
                            </label>
                        </div>
                        <div class="form-text">{{ __('You can disable any content types you do not want to copy forward.') }}</div>
                    </div>

                    <div class="alert alert-warning">
                        {!! getIcon('information-5', 'fs-2hx text-warning me-4') !!}
                        <div>
                            <strong>{{ __('Heads up!') }}</strong>
                            <p class="mb-0">{{ __('Cloning creates a new draft course. Review the cloned content before publishing.') }}</p>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="submit" class="btn btn-primary">
                        <span class="indicator-label">{{ __('Clone Course') }}</span>
                        <span class="indicator-progress">{{ __('Please wait...') }}
                            <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
