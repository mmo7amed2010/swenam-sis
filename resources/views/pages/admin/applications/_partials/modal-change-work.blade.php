{{-- Change Work Experience Modal --}}
@if($application->has_work_experience)
<div class="modal fade" id="changeWorkModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mw-650px">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-light-warning border-0">
                <div class="d-flex align-items-center">
                    <div class="symbol symbol-50px me-4">
                        <span class="symbol-label bg-warning">
                            {!! getIcon('briefcase', 'fs-2x text-white') !!}
                        </span>
                    </div>
                    <div>
                        <h2 class="fw-bolder text-gray-800 mb-1">Edit Work Experience</h2>
                        <p class="text-gray-600 fs-7 mb-0">Update work experience for {{ $application->full_name }}'s application</p>
                    </div>
                </div>
                <div class="btn btn-icon btn-sm btn-active-light-primary" data-bs-dismiss="modal">
                    {!! getIcon('cross', 'fs-1') !!}
                </div>
            </div>

            <form action="{{ route('admin.applications.update-work', $application) }}" method="POST">
                @csrf
                <div class="modal-body py-8">
                    <div class="row g-5">
                        {{-- Position Level --}}
                        <div class="col-md-6">
                            <label class="form-label text-gray-700 fw-semibold required">Position Level</label>
                            <select name="position_level" class="form-select form-select-solid @error('position_level') is-invalid @enderror" required>
                                <option value="">-- Select Level --</option>
                                <option value="Senior Managerial Position" {{ $application->position_level === 'Senior Managerial Position' ? 'selected' : '' }}>Senior Managerial Position</option>
                                <option value="Managerial Position" {{ $application->position_level === 'Managerial Position' ? 'selected' : '' }}>Managerial Position</option>
                                <option value="Entry Level Position" {{ $application->position_level === 'Entry Level Position' ? 'selected' : '' }}>Entry Level Position</option>
                            </select>
                            @error('position_level')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Years of Experience --}}
                        <div class="col-md-6">
                            <label class="form-label text-gray-700 fw-semibold required">Years of Experience</label>
                            <select name="years_of_experience" class="form-select form-select-solid @error('years_of_experience') is-invalid @enderror" required>
                                <option value="">-- Select --</option>
                                <option value="None, or less than one year" {{ $application->years_of_experience === 'None, or less than one year' ? 'selected' : '' }}>None, or less than one year</option>
                                <option value="Two years" {{ $application->years_of_experience === 'Two years' ? 'selected' : '' }}>Two years</option>
                                <option value="Three years" {{ $application->years_of_experience === 'Three years' ? 'selected' : '' }}>Three years</option>
                                <option value="Four years" {{ $application->years_of_experience === 'Four years' ? 'selected' : '' }}>Four years</option>
                                <option value="Five years or more" {{ $application->years_of_experience === 'Five years or more' ? 'selected' : '' }}>Five years or more</option>
                            </select>
                            @error('years_of_experience')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Position Title --}}
                        <div class="col-md-6">
                            <label class="form-label text-gray-700 fw-semibold required">Position Title</label>
                            <input type="text" name="position_title" class="form-control form-control-solid @error('position_title') is-invalid @enderror"
                                   value="{{ old('position_title', $application->position_title) }}"
                                   placeholder="e.g., Software Engineer" required>
                            @error('position_title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Organization Name --}}
                        <div class="col-md-6">
                            <label class="form-label text-gray-700 fw-semibold required">Organization Name</label>
                            <input type="text" name="organization_name" class="form-control form-control-solid @error('organization_name') is-invalid @enderror"
                                   value="{{ old('organization_name', $application->organization_name) }}"
                                   placeholder="e.g., Tech Company Inc." required>
                            @error('organization_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Start Date --}}
                        <div class="col-md-6">
                            <label class="form-label text-gray-700 fw-semibold required">Start Date</label>
                            <input type="date" name="work_start_date" class="form-control form-control-solid @error('work_start_date') is-invalid @enderror"
                                   value="{{ old('work_start_date', $application->work_start_date ? $application->work_start_date->format('Y-m-d') : '') }}"
                                   required>
                            @error('work_start_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- End Date --}}
                        <div class="col-md-6">
                            <label class="form-label text-gray-700 fw-semibold">End Date <span class="text-gray-500 fs-8">(Leave blank if still employed)</span></label>
                            <input type="date" name="work_end_date" class="form-control form-control-solid @error('work_end_date') is-invalid @enderror"
                                   value="{{ old('work_end_date', $application->work_end_date ? $application->work_end_date->format('Y-m-d') : '') }}">
                            @error('work_end_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        {!! getIcon('check', 'fs-4 me-2') !!}
                        Update Work Experience
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
