{{--
 * Assignment Delete Modal Component
 *
 * Modal for deleting an assignment with confirmation.
 * Shared between admin and instructor views with context-aware routing.
 *
 * @param \App\Models\Assignment $assignment
 * @param \App\Models\Program $program
 * @param \App\Models\Course $course
 * @param string $context - 'admin' or 'instructor'
--}}

@props(['assignment', 'program', 'course', 'context'])

@php
    $isAdmin = $context === 'admin';

    $deleteRoute = $isAdmin
        ? route('admin.programs.courses.assignments.destroy', [$program, $course, $assignment])
        : route('instructor.courses.assignments.destroy', [$program, $course, $assignment]);
@endphp

<div class="modal fade" id="kt_modal_delete_assignment" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mw-500px">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="fw-bold">{{ __('Delete Assignment') }}</h2>
                <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal">
                    {!! getIcon('cross', 'fs-1') !!}
                </div>
            </div>
            <form action="{{ $deleteRoute }}" method="POST" id="deleteAssignmentForm">
                @csrf
                @method('DELETE')
                <div class="modal-body">
                    <div class="text-center mb-5">
                        {!! getIcon('trash', 'fs-3x text-danger mb-4') !!}
                        <h3 class="fw-bold mb-2">{{ __('Are you sure?') }}</h3>
                        <p class="text-gray-600 mb-4">{{ __('This action cannot be undone.') }}</p>
                    </div>
                    @if($assignment->submissions()->count() > 0)
                    <div class="alert alert-warning d-flex align-items-center p-5 mb-5">
                        {!! getIcon('information-5', 'fs-2hx text-warning me-4') !!}
                        <div class="d-flex flex-column">
                            <h4 class="mb-1">{{ __('Submissions Exist') }}</h4>
                            <p class="mb-0">{{ $assignment->submissions()->count() }} {{ __('submission(s) will be soft deleted.') }}</p>
                        </div>
                    </div>
                    <div class="mb-5">
                        <label class="required form-label">{{ __('Type "DELETE ASSIGNMENT" to confirm') }}</label>
                        <input type="text" name="confirmation" class="form-control" placeholder="DELETE ASSIGNMENT" required />
                    </div>
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="submit" class="btn btn-danger">
                        <span class="indicator-label">{{ __('Delete Assignment') }}</span>
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
    document.getElementById('deleteAssignmentForm')?.addEventListener('submit', function() {
        const submitBtn = this.querySelector('button[type="submit"]');
        submitBtn.setAttribute('data-kt-indicator', 'on');
        submitBtn.disabled = true;
    });
</script>
@endpush
