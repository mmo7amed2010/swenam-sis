@extends('application.layout')

@section('content')
    {{-- Progress Indicator --}}
    <x-application-progress currentStep="4" />

    <form method="POST" action="{{ route('application.step4.store') }}" class="mt-10">
        @csrf

        {{-- Section Title --}}
        <div class="mb-10">
            <h2 class="text-gray-900 fw-bold fs-2 mb-2">Work History</h2>
            <p class="text-gray-600 fs-6">Tell us about your professional experience</p>
        </div>

        {{-- Work Experience Question --}}
        <div class="mb-10">
            <label class="form-label required fs-6 fw-semibold mb-3">
                <i class="ki-outline ki-briefcase fs-5 text-primary me-2"></i>
                Do You Have Any Work Experience?
            </label>
            <select name="has_work_experience" id="has_work_experience" class="form-select form-select-lg @error('has_work_experience') is-invalid @enderror" required>
                <option value="">Select</option>
                <option value="1" {{ old('has_work_experience', $data['has_work_experience'] ?? '') == '1' ? 'selected' : '' }}>Yes</option>
                <option value="0" {{ old('has_work_experience', $data['has_work_experience'] ?? '') === '0' ? 'selected' : '' }}>No</option>
            </select>
            @error('has_work_experience')
                <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
        </div>

        <div id="work_experience_fields" style="{{ (old('has_work_experience', $data['has_work_experience'] ?? '') == '1') ? '' : 'display: none;' }}">
            <div class="mb-10">
                <h3 class="text-gray-800 fw-bold fs-4 mb-6">
                    <i class="ki-outline ki-profile-user fs-3 text-primary me-2"></i>
                    Work Experience Details
                </h3>

                <div class="row g-6">
                    {{-- Position Level --}}
                    <div class="col-md-6">
                        <label class="form-label required fs-6 fw-semibold mb-3">Position Level</label>
                        <select name="position_level" id="position_level" class="form-select form-select-lg @error('position_level') is-invalid @enderror">
                            <option value="">Select</option>
                            <option value="Senior Managerial Position" {{ old('position_level', $data['position_level'] ?? '') == 'Senior Managerial Position' ? 'selected' : '' }}>Senior Managerial Position</option>
                            <option value="Managerial Position" {{ old('position_level', $data['position_level'] ?? '') == 'Managerial Position' ? 'selected' : '' }}>Managerial Position</option>
                            <option value="Entry Level Position" {{ old('position_level', $data['position_level'] ?? '') == 'Entry Level Position' ? 'selected' : '' }}>Entry Level Position</option>
                        </select>
                        @error('position_level')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Years of Experience --}}
                    <div class="col-md-6">
                        <label class="form-label required fs-6 fw-semibold mb-3">Years of Experience</label>
                        <select name="years_of_experience" id="years_of_experience" class="form-select form-select-lg @error('years_of_experience') is-invalid @enderror">
                            <option value="">Select</option>
                            <option value="None, or less than one year" {{ old('years_of_experience', $data['years_of_experience'] ?? '') == 'None, or less than one year' ? 'selected' : '' }}>None, or less than one year</option>
                            <option value="Two years" {{ old('years_of_experience', $data['years_of_experience'] ?? '') == 'Two years' ? 'selected' : '' }}>Two years</option>
                            <option value="Three years" {{ old('years_of_experience', $data['years_of_experience'] ?? '') == 'Three years' ? 'selected' : '' }}>Three years</option>
                            <option value="Four years" {{ old('years_of_experience', $data['years_of_experience'] ?? '') == 'Four years' ? 'selected' : '' }}>Four years</option>
                            <option value="Five years or more" {{ old('years_of_experience', $data['years_of_experience'] ?? '') == 'Five years or more' ? 'selected' : '' }}>Five years or more</option>
                        </select>
                        @error('years_of_experience')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Position Title --}}
                    <div class="col-md-6">
                        <label class="form-label required fs-6 fw-semibold mb-3">Position Title</label>
                        <input type="text" name="position_title" id="position_title" class="form-control form-control-lg @error('position_title') is-invalid @enderror" value="{{ old('position_title', $data['position_title'] ?? '') }}" placeholder="e.g., Software Engineer">
                        @error('position_title')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Organization Name --}}
                    <div class="col-md-6">
                        <label class="form-label required fs-6 fw-semibold mb-3">Organization Name</label>
                        <input type="text" name="organization_name" id="organization_name" class="form-control form-control-lg @error('organization_name') is-invalid @enderror" value="{{ old('organization_name', $data['organization_name'] ?? '') }}" placeholder="e.g., Tech Company Inc.">
                        @error('organization_name')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Start Date --}}
                    <div class="col-md-6">
                        <label class="form-label required fs-6 fw-semibold mb-3">Start Date</label>
                        <div class="row g-3">
                            <div class="col-4">
                                <select name="work_start_day" class="form-select form-select-lg @error('work_start_date') is-invalid @enderror">
                                    <option value="">Day</option>
                                    @for($i = 1; $i <= 31; $i++)
                                        <option value="{{ $i }}" {{ old('work_start_day') == $i ? 'selected' : '' }}>{{ $i }}</option>
                                    @endfor
                                </select>
                            </div>
                            <div class="col-4">
                                <select name="work_start_month" class="form-select form-select-lg @error('work_start_date') is-invalid @enderror">
                                    <option value="">Month</option>
                                    @for($i = 1; $i <= 12; $i++)
                                        <option value="{{ $i }}" {{ old('work_start_month') == $i ? 'selected' : '' }}>{{ $i }}</option>
                                    @endfor
                                </select>
                            </div>
                            <div class="col-4">
                                <select name="work_start_year" class="form-select form-select-lg @error('work_start_date') is-invalid @enderror">
                                    <option value="">Year</option>
                                    @for($i = date('Y'); $i >= 1950; $i--)
                                        <option value="{{ $i }}" {{ old('work_start_year') == $i ? 'selected' : '' }}>{{ $i }}</option>
                                    @endfor
                                </select>
                            </div>
                        </div>
                        @error('work_start_date')
                            <div class="text-danger small mt-2 d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- End Date --}}
                    <div class="col-md-6">
                        <label class="form-label fs-6 fw-semibold mb-3">End Date <span class="text-muted">(Optional)</span></label>
                        <div class="row g-3">
                            <div class="col-4">
                                <select name="work_end_day" class="form-select form-select-lg @error('work_end_date') is-invalid @enderror">
                                    <option value="">Day</option>
                                    @for($i = 1; $i <= 31; $i++)
                                        <option value="{{ $i }}" {{ old('work_end_day') == $i ? 'selected' : '' }}>{{ $i }}</option>
                                    @endfor
                                </select>
                            </div>
                            <div class="col-4">
                                <select name="work_end_month" class="form-select form-select-lg @error('work_end_date') is-invalid @enderror">
                                    <option value="">Month</option>
                                    @for($i = 1; $i <= 12; $i++)
                                        <option value="{{ $i }}" {{ old('work_end_month') == $i ? 'selected' : '' }}>{{ $i }}</option>
                                    @endfor
                                </select>
                            </div>
                            <div class="col-4">
                                <select name="work_end_year" class="form-select form-select-lg @error('work_end_date') is-invalid @enderror">
                                    <option value="">Year</option>
                                    @for($i = date('Y'); $i >= 1950; $i--)
                                        <option value="{{ $i }}" {{ old('work_end_year') == $i ? 'selected' : '' }}>{{ $i }}</option>
                                    @endfor
                                </select>
                            </div>
                        </div>
                        <div class="form-text mt-2">
                            <i class="ki-outline ki-information-5 fs-6 text-muted me-1"></i>
                            Leave blank if still employed
                        </div>
                        @error('work_end_date')
                            <div class="text-danger small mt-2 d-block">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        {{-- Navigation Buttons --}}
        <div class="d-flex justify-content-between mt-10 pt-8 border-top border-gray-300">
            <a href="{{ route('application.step3') }}" class="btn btn-light btn-lg fw-semibold px-8">
                <i class="ki-outline ki-arrow-left fs-3 me-2"></i>
                Previous
            </a>
            <button type="submit" class="btn btn-primary btn-lg fw-semibold px-8">
                Next
                <i class="ki-outline ki-arrow-right fs-3 ms-2"></i>
            </button>
        </div>
    </form>

    @push('scripts')
        <script>
            document.getElementById('has_work_experience').addEventListener('change', function() {
                const workFields = document.getElementById('work_experience_fields');
                if (this.value == '1') {
                    workFields.style.display = 'block';
                    // Make fields required
                    document.getElementById('position_level').required = true;
                    document.getElementById('position_title').required = true;
                    document.getElementById('organization_name').required = true;
                    document.getElementById('years_of_experience').required = true;
                } else {
                    workFields.style.display = 'none';
                    // Make fields not required
                    document.getElementById('position_level').required = false;
                    document.getElementById('position_title').required = false;
                    document.getElementById('organization_name').required = false;
                    document.getElementById('years_of_experience').required = false;
                }
            });
        </script>
    @endpush
@endsection
