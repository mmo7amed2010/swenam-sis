{{--
 * Assignment Edit Modal Component
 *
 * Modal for editing an existing assignment.
 * Shared between admin and instructor views with context-aware routing.
 *
 * @param \App\Models\Program $program
 * @param \App\Models\Course $course
 * @param \App\Models\Assignment $assignment
 * @param string $context - 'admin' or 'instructor'
--}}

@props(['program', 'course', 'assignment', 'context'])

@php
    $isAdmin = $context === 'admin';

    $updateRoute = $isAdmin
        ? route('admin.programs.courses.assignments.update', [$program, $course, $assignment])
        : route('instructor.courses.assignments.update', [$program, $course, $assignment]);
@endphp

<div class="modal fade" id="kt_modal_edit_assignment_{{ $assignment->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mw-650px">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="fw-bold">{{ __('Edit Assignment') }}</h2>
                <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal">
                    {!! getIcon('cross', 'fs-1') !!}
                </div>
            </div>
            <form action="{{ $updateRoute }}" method="POST" id="editAssignmentForm_{{ $assignment->id }}">
                @csrf
                @method('PUT')
                <div class="modal-body scroll-y mx-5 mx-xl-15 my-7">
                    {{-- Title --}}
                    <div class="mb-7 fv-row">
                        <label class="required form-label">{{ __('Title') }}</label>
                        <input type="text"
                               name="title"
                               class="form-control @error('title') is-invalid @enderror"
                               placeholder="{{ __('Assignment title') }}"
                               value="{{ old('title', $assignment->title) }}"
                               required />
                        @error('title')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Description --}}
                    <div class="mb-7 fv-row">
                        <label class="form-label">{{ __('Description') }}</label>
                        <textarea name="description"
                                  class="form-control @error('description') is-invalid @enderror"
                                  rows="4"
                                  placeholder="{{ __('Assignment description and instructions...') }}">{{ old('description', $assignment->description) }}</textarea>
                        @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row mb-7">
                        {{-- Total Points --}}
                        <div class="col-md-6 fv-row">
                            <label class="required form-label">{{ __('Total Points') }}</label>
                            <input type="number"
                                   name="total_points"
                                   class="form-control @error('total_points') is-invalid @enderror"
                                   min="1"
                                   max="1000"
                                   value="{{ old('total_points', $assignment->total_points ?? $assignment->max_points ?? 100) }}"
                                   required />
                            <div class="form-text">{{ __('Maximum: 1000 points') }}</div>
                            @error('total_points')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Passing Score --}}
                        <div class="col-md-6 fv-row">
                            <label class="form-label">{{ __('Passing Score (%)') }}</label>
                            <input type="number"
                                   name="passing_score"
                                   class="form-control @error('passing_score') is-invalid @enderror"
                                   min="0"
                                   max="100"
                                   value="{{ old('passing_score', $assignment->passing_score ?? 60) }}"
                                   placeholder="60" />
                            <div class="form-text">{{ __('Default: 60%') }}</div>
                            @error('passing_score')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    {{-- Submission Type --}}
                    <div class="mb-7 fv-row">
                        <label class="required form-label">{{ __('Submission Type') }}</label>
                        <select name="submission_type"
                                class="form-select @error('submission_type') is-invalid @enderror"
                                id="submission_type_{{ $assignment->id }}"
                                required>
                            <option value="file_upload" {{ old('submission_type', $assignment->submission_type) === 'file_upload' ? 'selected' : '' }}>
                                {{ __('File Upload') }}
                            </option>
                            <option value="text_entry" {{ old('submission_type', $assignment->submission_type) === 'text_entry' ? 'selected' : '' }}>
                                {{ __('Text Entry') }}
                            </option>
                            <option value="url_submission" {{ old('submission_type', $assignment->submission_type) === 'url_submission' ? 'selected' : '' }}>
                                {{ __('URL Submission') }}
                            </option>
                            <option value="multiple" {{ old('submission_type', $assignment->submission_type) === 'multiple' ? 'selected' : '' }}>
                                {{ __('Multiple (File + Text)') }}
                            </option>
                        </select>
                        @error('submission_type')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Max File Size (conditional) --}}
                    <div class="mb-7 fv-row" id="file_size_container_{{ $assignment->id }}" style="{{ in_array($assignment->submission_type, ['file_upload', 'multiple']) ? '' : 'display: none;' }}">
                        <label class="form-label">{{ __('Max File Size (MB)') }}</label>
                        <input type="number"
                               name="max_file_size_mb"
                               class="form-control @error('max_file_size_mb') is-invalid @enderror"
                               min="1"
                               max="50"
                               value="{{ old('max_file_size_mb', $assignment->max_file_size_mb ?? 10) }}"
                               placeholder="10" />
                        <div class="form-text">{{ __('Maximum: 50MB') }}</div>
                        @error('max_file_size_mb')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Module Assignment --}}
                    @if($course->modules->count() > 0)
                    <div class="mb-7 fv-row">
                        <label class="form-label">{{ __('Assign to Module') }}</label>
                        <select name="module_id" class="form-select @error('module_id') is-invalid @enderror">
                            <option value="">{{ __('-- No Module (Standalone) --') }}</option>
                            @foreach($course->modules as $module)
                            <option value="{{ $module->id }}" {{ old('module_id', $assignment->moduleItem?->course_module_id) == $module->id ? 'selected' : '' }}>
                                {{ $module->title }}
                            </option>
                            @endforeach
                        </select>
                        <div class="form-text">{{ __('Optionally assign this assignment to a course module.') }}</div>
                        @error('module_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    @endif

                    {{-- Published Status --}}
                    <div class="mb-7 fv-row">
                        <label class="form-check form-check-custom form-check-solid">
                            <input type="checkbox"
                                   class="form-check-input"
                                   name="is_published"
                                   value="1"
                                   {{ old('is_published', $assignment->is_published) ? 'checked' : '' }}>
                            <span class="form-check-label fw-semibold">
                                {{ __('Published') }}
                            </span>
                        </label>
                        <div class="form-text">{{ __('Published assignments are visible to students.') }}</div>
                    </div>

                    {{-- Warning for graded assignments --}}
                    @if($assignment->submissions()->whereHas('grades', fn($q) => $q->where('is_published', true))->exists())
                    <div class="alert alert-warning d-flex align-items-center p-5">
                        {!! getIcon('information-5', 'fs-2hx text-warning me-4') !!}
                        <div>
                            <strong>{{ __('Graded Submissions Exist') }}</strong>
                            <p class="mb-0">{{ __('Total points cannot be changed because grades have been published.') }}</p>
                        </div>
                    </div>
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="submit" class="btn btn-primary">
                        <span class="indicator-label">{{ __('Save Changes') }}</span>
                        <span class="indicator-progress">{{ __('Please wait...') }}
                            <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    (function() {
        const submissionTypeSelect = document.getElementById('submission_type_{{ $assignment->id }}');
        const fileSizeContainer = document.getElementById('file_size_container_{{ $assignment->id }}');
        const form = document.getElementById('editAssignmentForm_{{ $assignment->id }}');

        if (submissionTypeSelect && fileSizeContainer) {
            submissionTypeSelect.addEventListener('change', function() {
                if (this.value === 'file_upload' || this.value === 'multiple') {
                    fileSizeContainer.style.display = '';
                } else {
                    fileSizeContainer.style.display = 'none';
                }
            });
        }

        if (form) {
            form.addEventListener('submit', function() {
                const submitBtn = this.querySelector('button[type="submit"]');
                submitBtn.setAttribute('data-kt-indicator', 'on');
                submitBtn.disabled = true;
            });
        }
    })();
</script>
@endpush
