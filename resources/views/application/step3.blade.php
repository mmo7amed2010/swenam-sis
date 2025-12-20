@extends('application.layout')

@section('content')
    {{-- Progress Indicator --}}
    <x-application-progress currentStep="3" />

    <form method="POST" action="{{ route('application.step3.store') }}" class="mt-10">
        @csrf

        {{-- Section Title --}}
        <div class="mb-10">
            <h2 class="text-gray-900 fw-bold fs-2 mb-2">Education History</h2>
            <p class="text-gray-600 fs-6">Provide details about your educational background</p>
        </div>

        {{-- Education Details Section --}}
        <div class="mb-10">
            <h3 class="text-gray-800 fw-bold fs-4 mb-6">
                <i class="ki-outline ki-graduation fs-3 text-primary me-2"></i>
                Education Details
            </h3>

            <div class="row g-6">
                {{-- Highest Level of Education --}}
                <div class="col-12">
                    <label class="form-label required fs-6 fw-semibold mb-3">What Is Your Highest Level Of Education?</label>
                    <select name="highest_education_level" id="highest_education_level" class="form-select form-select-lg @error('highest_education_level') is-invalid @enderror" required>
                        <option value="">Select education level</option>
                        <option value="None, or less than secondary (high school)" {{ old('highest_education_level', $data['highest_education_level'] ?? '') == 'None, or less than secondary (high school)' ? 'selected' : '' }}>None, or less than secondary (high school)</option>
                        <option value="Secondary diploma (high school graduation)" {{ old('highest_education_level', $data['highest_education_level'] ?? '') == 'Secondary diploma (high school graduation)' ? 'selected' : '' }}>Secondary diploma (high school graduation)</option>
                        <option value="One-year program, diploma or certificate from a university, college, trade or technical school, or other institute" {{ old('highest_education_level', $data['highest_education_level'] ?? '') == 'One-year program, diploma or certificate from a university, college, trade or technical school, or other institute' ? 'selected' : '' }}>One-year program, diploma or certificate from a university, college, trade or technical school, or other institute</option>
                        <option value="Two-year program at a university, college, trade or technical school, or other institute" {{ old('highest_education_level', $data['highest_education_level'] ?? '') == 'Two-year program at a university, college, trade or technical school, or other institute' ? 'selected' : '' }}>Two-year program at a university, college, trade or technical school, or other institute</option>
                        <option value="University/College Diploma or Certificate" {{ old('highest_education_level', $data['highest_education_level'] ?? '') == 'University/College Diploma or Certificate' ? 'selected' : '' }}>University/College Diploma or Certificate</option>
                        <option value="University/College Bachelor's Degree" {{ old('highest_education_level', $data['highest_education_level'] ?? '') == "University/College Bachelor's Degree" ? 'selected' : '' }}>University/College Bachelor's Degree</option>
                        <option value="Master's Degree" {{ old('highest_education_level', $data['highest_education_level'] ?? '') == "Master's Degree" ? 'selected' : '' }}>Master's Degree</option>
                        <option value="Doctoral level university degree (Ph.D.)" {{ old('highest_education_level', $data['highest_education_level'] ?? '') == 'Doctoral level university degree (Ph.D.)' ? 'selected' : '' }}>Doctoral level university degree (Ph.D.)</option>
                    </select>
                    @error('highest_education_level')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Field of Study --}}
                <div class="col-md-6">
                    <label class="form-label required fs-6 fw-semibold mb-3">Field of Study</label>
                    <input type="text" name="education_field" id="education_field" class="form-control form-control-lg @error('education_field') is-invalid @enderror" value="{{ old('education_field', $data['education_field'] ?? '') }}" placeholder="e.g., Computer Science" required>
                    @error('education_field')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Name of Institution --}}
                <div class="col-md-6">
                    <label class="form-label required fs-6 fw-semibold mb-3">Institution Name</label>
                    <input type="text" name="institution_name" id="institution_name" class="form-control form-control-lg @error('institution_name') is-invalid @enderror" value="{{ old('institution_name', $data['institution_name'] ?? '') }}" placeholder="e.g., Harvard University" required>
                    @error('institution_name')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Country Where Education Was Obtained --}}
                <div class="col-md-6">
                    <label class="form-label required fs-6 fw-semibold mb-3">Country of Education</label>
                    <select name="education_country" id="education_country" class="form-select form-select-lg @error('education_country') is-invalid @enderror" required>
                        <option value="">Select country</option>
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
                        @foreach($countries as $country)
                            <option value="{{ $country }}" {{ old('education_country', $data['education_country'] ?? '') == $country ? 'selected' : '' }}>{{ $country }}</option>
                        @endforeach
                    </select>
                    @error('education_country')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Have You Completed This Education? --}}
                <div class="col-12">
                    <label class="form-label required fs-6 fw-semibold mb-3">Completion Status</label>
                    <div class="d-flex flex-wrap gap-4">
                        <div class="form-check form-check-custom form-check-solid">
                            <input class="form-check-input @error('education_completed') is-invalid @enderror" type="radio" name="education_completed" id="education_completed_yes" value="yes" {{ old('education_completed', $data['education_completed'] ?? '') == 'yes' ? 'checked' : '' }} required>
                            <label class="form-check-label fs-6 fw-semibold" for="education_completed_yes">
                                Yes
                            </label>
                        </div>
                        <div class="form-check form-check-custom form-check-solid">
                            <input class="form-check-input @error('education_completed') is-invalid @enderror" type="radio" name="education_completed" id="education_completed_no" value="no" {{ old('education_completed', $data['education_completed'] ?? '') == 'no' ? 'checked' : '' }}>
                            <label class="form-check-label fs-6 fw-semibold" for="education_completed_no">
                                No
                            </label>
                        </div>
                        <div class="form-check form-check-custom form-check-solid">
                            <input class="form-check-input @error('education_completed') is-invalid @enderror" type="radio" name="education_completed" id="education_completed_still_studying" value="still_studying" {{ old('education_completed', $data['education_completed'] ?? '') == 'still_studying' ? 'checked' : '' }}>
                            <label class="form-check-label fs-6 fw-semibold" for="education_completed_still_studying">
                                Still Studying
                            </label>
                        </div>
                    </div>
                    @error('education_completed')
                        <div class="text-danger small mt-2 d-block">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>

        {{-- Additional Information Section --}}
        <div class="mb-10">
            <h3 class="text-gray-800 fw-bold fs-4 mb-6">
                <i class="ki-outline ki-information-5 fs-3 text-primary me-2"></i>
                Additional Information
            </h3>

            {{-- Disciplinary Action --}}
            <div class="mb-6">
                <label class="form-label required fs-6 fw-semibold mb-3">Have you ever been subject to any disciplinary action at an educational institution?</label>
                <div class="d-flex flex-wrap gap-4">
                    <div class="form-check form-check-custom form-check-solid">
                        <input class="form-check-input @error('has_disciplinary_action') is-invalid @enderror" type="radio" name="has_disciplinary_action" id="has_disciplinary_action_yes" value="1" {{ old('has_disciplinary_action', $data['has_disciplinary_action'] ?? '') == '1' ? 'checked' : '' }} required>
                        <label class="form-check-label fs-6 fw-semibold" for="has_disciplinary_action_yes">
                            Yes
                        </label>
                    </div>
                    <div class="form-check form-check-custom form-check-solid">
                        <input class="form-check-input @error('has_disciplinary_action') is-invalid @enderror" type="radio" name="has_disciplinary_action" id="has_disciplinary_action_no" value="0" {{ old('has_disciplinary_action', $data['has_disciplinary_action'] ?? '') === '0' ? 'checked' : '' }}>
                        <label class="form-check-label fs-6 fw-semibold" for="has_disciplinary_action_no">
                            No
                        </label>
                    </div>
                </div>
                @error('has_disciplinary_action')
                    <div class="text-danger small mt-2 d-block">{{ $message }}</div>
                @enderror
            </div>
        </div>

        {{-- Navigation Buttons --}}
        <div class="d-flex justify-content-between mt-10 pt-8 border-top border-gray-300">
            <a href="{{ route('application.step2') }}" class="btn btn-light btn-lg fw-semibold px-8">
                <i class="ki-outline ki-arrow-left fs-3 me-2"></i>
                Previous
            </a>
            <button type="submit" class="btn btn-primary btn-lg fw-semibold px-8">
                Next
                <i class="ki-outline ki-arrow-right fs-3 ms-2"></i>
            </button>
        </div>
    </form>
@endsection
