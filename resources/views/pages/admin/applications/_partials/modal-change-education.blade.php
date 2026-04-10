{{-- Change Education Modal --}}
<div class="modal fade" id="changeEducationModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mw-650px">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-light-success border-0">
                <div class="d-flex align-items-center">
                    <div class="symbol symbol-50px me-4">
                        <span class="symbol-label bg-success">
                            {!! getIcon('teacher', 'fs-2x text-white') !!}
                        </span>
                    </div>
                    <div>
                        <h2 class="fw-bolder text-gray-800 mb-1">Edit Education Details</h2>
                        <p class="text-gray-600 fs-7 mb-0">Update education information for {{ $application->full_name }}'s application</p>
                    </div>
                </div>
                <div class="btn btn-icon btn-sm btn-active-light-primary" data-bs-dismiss="modal">
                    {!! getIcon('cross', 'fs-1') !!}
                </div>
            </div>

            <form action="{{ route('admin.applications.update-education', $application) }}" method="POST">
                @csrf
                <div class="modal-body py-8">
                    <div class="row g-5">
                        {{-- Highest Level of Education --}}
                        <div class="col-12">
                            <label class="form-label text-gray-700 fw-semibold required">Highest Level of Education</label>
                            <select name="highest_education_level" class="form-select form-select-solid @error('highest_education_level') is-invalid @enderror" required>
                                <option value="">-- Select education level --</option>
                                @php
                                    $educationLevels = [
                                        'None, or less than secondary (high school)',
                                        'Secondary diploma (high school graduation)',
                                        'One-year program, diploma or certificate from a university, college, trade or technical school, or other institute',
                                        'Two-year program at a university, college, trade or technical school, or other institute',
                                        'University/College Diploma or Certificate',
                                        "University/College Bachelor's Degree",
                                        "Master's Degree",
                                        'Doctoral level university degree (Ph.D.)',
                                    ];
                                @endphp
                                @foreach($educationLevels as $level)
                                    <option value="{{ $level }}" {{ $application->highest_education_level === $level ? 'selected' : '' }}>{{ $level }}</option>
                                @endforeach
                            </select>
                            @error('highest_education_level')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Field of Study --}}
                        <div class="col-md-6">
                            <label class="form-label text-gray-700 fw-semibold required">Field of Study</label>
                            <input type="text" name="education_field" class="form-control form-control-solid @error('education_field') is-invalid @enderror"
                                   value="{{ old('education_field', $application->education_field) }}"
                                   placeholder="e.g., Computer Science" required>
                            @error('education_field')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Institution Name --}}
                        <div class="col-md-6">
                            <label class="form-label text-gray-700 fw-semibold required">Institution Name</label>
                            <input type="text" name="institution_name" class="form-control form-control-solid @error('institution_name') is-invalid @enderror"
                                   value="{{ old('institution_name', $application->institution_name) }}"
                                   placeholder="e.g., Harvard University" required>
                            @error('institution_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Country of Education --}}
                        <div class="col-md-6">
                            <label class="form-label text-gray-700 fw-semibold required">Country of Education</label>
                            @php
                                $countries = [
                                    'Afghanistan', 'Albania', 'Algeria', 'Andorra', 'Angola', 'Argentina', 'Armenia', 'Australia', 'Austria', 'Azerbaijan',
                                    'Bahamas', 'Bahrain', 'Bangladesh', 'Barbados', 'Belarus', 'Belgium', 'Belize', 'Benin', 'Bhutan', 'Bolivia',
                                    'Bosnia and Herzegovina', 'Botswana', 'Brazil', 'Brunei', 'Bulgaria', 'Burkina Faso', 'Burundi', 'Cambodia', 'Cameroon',
                                    'Canada', 'Cape Verde', 'Central African Republic', 'Chad', 'Chile', 'China', 'Colombia', 'Comoros', 'Congo', 'Costa Rica',
                                    'Croatia', 'Cuba', 'Cyprus', 'Czech Republic', 'Denmark', 'Djibouti', 'Dominica', 'Dominican Republic', 'Ecuador', 'Egypt',
                                    'El Salvador', 'Equatorial Guinea', 'Eritrea', 'Estonia', 'Ethiopia', 'Fiji', 'Finland', 'France', 'Gabon', 'Gambia',
                                    'Georgia', 'Germany', 'Ghana', 'Greece', 'Grenada', 'Guatemala', 'Guinea', 'Guinea-Bissau', 'Guyana', 'Haiti', 'Honduras',
                                    'Hungary', 'Iceland', 'India', 'Indonesia', 'Iran', 'Iraq', 'Ireland', 'Israel', 'Italy', 'Jamaica', 'Japan', 'Jordan',
                                    'Kazakhstan', 'Kenya', 'Kiribati', 'Korea, North', 'Korea, South', 'Kuwait', 'Kyrgyzstan', 'Laos', 'Latvia', 'Lebanon',
                                    'Lesotho', 'Liberia', 'Libya', 'Liechtenstein', 'Lithuania', 'Luxembourg', 'Macedonia', 'Madagascar', 'Malawi', 'Malaysia',
                                    'Maldives', 'Mali', 'Malta', 'Marshall Islands', 'Mauritania', 'Mauritius', 'Mexico', 'Micronesia', 'Moldova', 'Monaco',
                                    'Mongolia', 'Montenegro', 'Morocco', 'Mozambique', 'Myanmar', 'Namibia', 'Nauru', 'Nepal', 'Netherlands', 'New Zealand',
                                    'Nicaragua', 'Niger', 'Nigeria', 'Norway', 'Oman', 'Pakistan', 'Palau', 'Panama', 'Papua New Guinea', 'Paraguay', 'Peru',
                                    'Philippines', 'Poland', 'Portugal', 'Qatar', 'Romania', 'Russia', 'Rwanda', 'Saint Kitts and Nevis', 'Saint Lucia',
                                    'Saint Vincent and the Grenadines', 'Samoa', 'San Marino', 'Sao Tome and Principe', 'Saudi Arabia', 'Senegal', 'Serbia',
                                    'Seychelles', 'Sierra Leone', 'Singapore', 'Slovakia', 'Slovenia', 'Solomon Islands', 'Somalia', 'South Africa', 'Spain',
                                    'Sri Lanka', 'Sudan', 'Suriname', 'Swaziland', 'Sweden', 'Switzerland', 'Syria', 'Taiwan', 'Tajikistan', 'Tanzania',
                                    'Thailand', 'Togo', 'Tonga', 'Trinidad and Tobago', 'Tunisia', 'Turkey', 'Turkmenistan', 'Tuvalu', 'Uganda', 'Ukraine',
                                    'United Arab Emirates', 'United Kingdom', 'United States', 'Uruguay', 'Uzbekistan', 'Vanuatu', 'Vatican City', 'Venezuela',
                                    'Vietnam', 'Yemen', 'Zambia', 'Zimbabwe'
                                ];
                            @endphp
                            <select name="education_country" class="form-select form-select-solid @error('education_country') is-invalid @enderror" required>
                                <option value="">-- Select Country --</option>
                                @foreach($countries as $country)
                                    <option value="{{ $country }}" {{ $application->education_country === $country ? 'selected' : '' }}>{{ $country }}</option>
                                @endforeach
                            </select>
                            @error('education_country')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Completion Status --}}
                        <div class="col-md-6">
                            <label class="form-label text-gray-700 fw-semibold required">Completion Status</label>
                            <div class="d-flex flex-wrap gap-4 mt-2">
                                <label class="d-flex align-items-center cursor-pointer">
                                    <input type="radio" name="education_completed" value="yes" class="form-check-input me-2" {{ $application->education_completed === 'yes' ? 'checked' : '' }} required>
                                    <span class="text-gray-700 fw-semibold">Yes</span>
                                </label>
                                <label class="d-flex align-items-center cursor-pointer">
                                    <input type="radio" name="education_completed" value="no" class="form-check-input me-2" {{ $application->education_completed === 'no' ? 'checked' : '' }}>
                                    <span class="text-gray-700 fw-semibold">No</span>
                                </label>
                                <label class="d-flex align-items-center cursor-pointer">
                                    <input type="radio" name="education_completed" value="still_studying" class="form-check-input me-2" {{ $application->education_completed === 'still_studying' ? 'checked' : '' }}>
                                    <span class="text-gray-700 fw-semibold">Still Studying</span>
                                </label>
                            </div>
                            @error('education_completed')
                                <div class="text-danger small mt-2">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Disciplinary Action --}}
                        <div class="col-12">
                            <label class="form-label text-gray-700 fw-semibold required">Has the applicant been subject to any disciplinary action?</label>
                            <div class="d-flex gap-4 mt-2">
                                <label class="d-flex align-items-center cursor-pointer">
                                    <input type="radio" name="has_disciplinary_action" value="1" class="form-check-input me-2" {{ $application->has_disciplinary_action ? 'checked' : '' }} required>
                                    <span class="text-gray-700 fw-semibold">Yes</span>
                                </label>
                                <label class="d-flex align-items-center cursor-pointer">
                                    <input type="radio" name="has_disciplinary_action" value="0" class="form-check-input me-2" {{ !$application->has_disciplinary_action ? 'checked' : '' }}>
                                    <span class="text-gray-700 fw-semibold">No</span>
                                </label>
                            </div>
                            @error('has_disciplinary_action')
                                <div class="text-danger small mt-2">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        {!! getIcon('check', 'fs-4 me-2') !!}
                        Update Education
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
