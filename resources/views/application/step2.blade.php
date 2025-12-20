@extends('application.layout')

@section('content')
    {{-- Progress Indicator --}}
    <x-application-progress currentStep="2" />

    <form method="POST" action="{{ route('application.step2.store') }}" class="mt-10">
        @csrf

        {{-- Section Title --}}
        <div class="mb-10">
            <h2 class="text-gray-900 fw-bold fs-2 mb-2">Personal Information</h2>
            <p class="text-gray-600 fs-6">Please provide your personal details and contact information</p>
        </div>

        {{-- Basic Information Section --}}
        <div class="mb-10">
            <h3 class="text-gray-800 fw-bold fs-4 mb-6">
                <i class="ki-outline ki-profile-user fs-3 text-primary me-2"></i>
                Basic Information
            </h3>

            <div class="row g-6">
                {{-- First Name --}}
                <div class="col-md-6">
                    <label class="form-label required fs-6 fw-semibold mb-3">First Name</label>
                    <input type="text" name="first_name" id="first_name" class="form-control form-control-lg @error('first_name') is-invalid @enderror" value="{{ old('first_name', $data['first_name'] ?? '') }}" placeholder="e.g., John" required>
                    @error('first_name')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Last Name --}}
                <div class="col-md-6">
                    <label class="form-label required fs-6 fw-semibold mb-3">Last Name</label>
                    <input type="text" name="last_name" id="last_name" class="form-control form-control-lg @error('last_name') is-invalid @enderror" value="{{ old('last_name', $data['last_name'] ?? '') }}" placeholder="e.g., Doe" required>
                    @error('last_name')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>

        {{-- Contact Information Section --}}
        <div class="mb-10">
            <h3 class="text-gray-800 fw-bold fs-4 mb-6">
                <i class="ki-outline ki-sms fs-3 text-primary me-2"></i>
                Contact Information
            </h3>

            <div class="row g-6">
                {{-- Email Address --}}
                <div class="col-md-6">
                    <label class="form-label required fs-6 fw-semibold mb-3">Email Address</label>
                    <input type="email" name="email" id="email" class="form-control form-control-lg @error('email') is-invalid @enderror" value="{{ old('email', $data['email'] ?? '') }}" placeholder="e.g., john.doe@example.com" required>
                    @error('email')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Confirm Email Address --}}
                <div class="col-md-6">
                    <label class="form-label required fs-6 fw-semibold mb-3">Confirm Email Address</label>
                    <input type="email" name="email_confirmation" id="email_confirmation" class="form-control form-control-lg @error('email_confirmation') is-invalid @enderror" value="{{ old('email_confirmation', $data['email'] ?? '') }}" placeholder="e.g., john.doe@example.com" required>
                    @error('email_confirmation')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Phone Number --}}
                <div class="col-md-6">
                    <label class="form-label required fs-6 fw-semibold mb-3">
                        <i class="ki-outline ki-phone fs-5 text-primary me-2"></i>
                        Phone Number
                    </label>
                    <input type="tel" name="phone" id="phone" class="form-control form-control-lg @error('phone') is-invalid @enderror" value="{{ old('phone', $data['phone'] ?? '') }}" placeholder="e.g., +1 234 567 8900" required>
                    @error('phone')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>

        {{-- Personal Details Section --}}
        <div class="mb-10">
            <h3 class="text-gray-800 fw-bold fs-4 mb-6">
                <i class="ki-outline ki-calendar-tick fs-3 text-primary me-2"></i>
                Personal Details
            </h3>

            @php
                // Parse stored date of birth for pre-filling
                $storedDob = $data['date_of_birth'] ?? null;
                $dobDay = old('dob_day');
                $dobMonth = old('dob_month');
                $dobYear = old('dob_year');

                if (!$dobDay && !$dobMonth && !$dobYear && $storedDob) {
                    $dobParts = explode('-', $storedDob);
                    if (count($dobParts) === 3) {
                        $dobYear = (int) $dobParts[0];
                        $dobMonth = (int) $dobParts[1];
                        $dobDay = (int) $dobParts[2];
                    }
                }
            @endphp
            <div class="row g-6">
                {{-- Date of Birth --}}
                <div class="col-12">
                    <label class="form-label required fs-6 fw-semibold mb-3">Date of Birth</label>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <select name="dob_day" class="form-select form-select-lg @error('date_of_birth') is-invalid @enderror" required>
                                <option value="">Day</option>
                                @for($i = 1; $i <= 31; $i++)
                                    <option value="{{ $i }}" {{ $dobDay == $i ? 'selected' : '' }}>{{ $i }}</option>
                                @endfor
                            </select>
                        </div>
                        <div class="col-md-4">
                            <select name="dob_month" class="form-select form-select-lg @error('date_of_birth') is-invalid @enderror" required>
                                <option value="">Month</option>
                                @for($i = 1; $i <= 12; $i++)
                                    <option value="{{ $i }}" {{ $dobMonth == $i ? 'selected' : '' }}>{{ $i }}</option>
                                @endfor
                            </select>
                        </div>
                        <div class="col-md-4">
                            <select name="dob_year" class="form-select form-select-lg @error('date_of_birth') is-invalid @enderror" required>
                                <option value="">Year</option>
                                @for($i = date('Y') - 16; $i >= 1950; $i--)
                                    <option value="{{ $i }}" {{ $dobYear == $i ? 'selected' : '' }}>{{ $i }}</option>
                                @endfor
                            </select>
                        </div>
                    </div>
                    <div class="form-text mt-2">
                        <i class="ki-outline ki-information-5 fs-6 text-muted me-1"></i>
                        You must be at least 16 years old to apply
                    </div>
                    @error('date_of_birth')
                        <div class="text-danger small mt-2 d-block">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>

        {{-- Citizenship & Residency Section --}}
        <div class="mb-10">
            <h3 class="text-gray-800 fw-bold fs-4 mb-6">
                <i class="ki-outline ki-global fs-3 text-primary me-2"></i>
                Citizenship & Residency
            </h3>

            <div class="row g-6">
                {{-- Country of Citizenship --}}
                <div class="col-md-6">
                    <label class="form-label required fs-6 fw-semibold mb-3">Country of Citizenship</label>
                    <select name="country_of_citizenship" id="country_of_citizenship" class="form-select form-select-lg @error('country_of_citizenship') is-invalid @enderror" required>
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
                            <option value="{{ $country }}" {{ old('country_of_citizenship', $data['country_of_citizenship'] ?? '') == $country ? 'selected' : '' }}>{{ $country }}</option>
                        @endforeach
                    </select>
                    @error('country_of_citizenship')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Residency Status in Canada --}}
                <div class="col-md-6">
                    <label class="form-label required fs-6 fw-semibold mb-3">Residency Status in Canada</label>
                    <select name="residency_status" id="residency_status" class="form-select form-select-lg @error('residency_status') is-invalid @enderror" required>
                        <option value="">Select status</option>
                        <option value="Permanent Resident" {{ old('residency_status', $data['residency_status'] ?? '') == 'Permanent Resident' ? 'selected' : '' }}>Permanent Resident</option>
                        <option value="Canadian Citizen" {{ old('residency_status', $data['residency_status'] ?? '') == 'Canadian Citizen' ? 'selected' : '' }}>Canadian Citizen</option>
                        <option value="Study Permit" {{ old('residency_status', $data['residency_status'] ?? '') == 'Study Permit' ? 'selected' : '' }}>Study Permit</option>
                        <option value="Work Permit" {{ old('residency_status', $data['residency_status'] ?? '') == 'Work Permit' ? 'selected' : '' }}>Work Permit</option>
                        <option value="Visitor Visa" {{ old('residency_status', $data['residency_status'] ?? '') == 'Visitor Visa' ? 'selected' : '' }}>Visitor Visa</option>
                        <option value="None (No Residency Status)" {{ old('residency_status', $data['residency_status'] ?? '') == 'None (No Residency Status)' ? 'selected' : '' }}>None (No Residency Status)</option>
                    </select>
                    @error('residency_status')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Primary Language (Native Language) --}}
                <div class="col-md-6">
                    <label class="form-label required fs-6 fw-semibold mb-3">
                        <i class="ki-outline ki-message-text-2 fs-5 text-primary me-2"></i>
                        Primary Language (Native Language)
                    </label>
                    <select name="primary_language" id="primary_language" class="form-select form-select-lg @error('primary_language') is-invalid @enderror" required>
                        <option value="">Select language</option>
                        <option value="Arabic" {{ old('primary_language', $data['primary_language'] ?? '') == 'Arabic' ? 'selected' : '' }}>Arabic</option>
                        <option value="Cantonese" {{ old('primary_language', $data['primary_language'] ?? '') == 'Cantonese' ? 'selected' : '' }}>Cantonese</option>
                        <option value="English" {{ old('primary_language', $data['primary_language'] ?? '') == 'English' ? 'selected' : '' }}>English</option>
                        <option value="French" {{ old('primary_language', $data['primary_language'] ?? '') == 'French' ? 'selected' : '' }}>French</option>
                        <option value="Farsi" {{ old('primary_language', $data['primary_language'] ?? '') == 'Farsi' ? 'selected' : '' }}>Farsi</option>
                        <option value="Hindi" {{ old('primary_language', $data['primary_language'] ?? '') == 'Hindi' ? 'selected' : '' }}>Hindi</option>
                        <option value="Italian" {{ old('primary_language', $data['primary_language'] ?? '') == 'Italian' ? 'selected' : '' }}>Italian</option>
                        <option value="Mandarin" {{ old('primary_language', $data['primary_language'] ?? '') == 'Mandarin' ? 'selected' : '' }}>Mandarin</option>
                        <option value="Portuguese" {{ old('primary_language', $data['primary_language'] ?? '') == 'Portuguese' ? 'selected' : '' }}>Portuguese</option>
                        <option value="Punjabi" {{ old('primary_language', $data['primary_language'] ?? '') == 'Punjabi' ? 'selected' : '' }}>Punjabi</option>
                        <option value="Spanish" {{ old('primary_language', $data['primary_language'] ?? '') == 'Spanish' ? 'selected' : '' }}>Spanish</option>
                        <option value="Turkish" {{ old('primary_language', $data['primary_language'] ?? '') == 'Turkish' ? 'selected' : '' }}>Turkish</option>
                        <option value="Urdu" {{ old('primary_language', $data['primary_language'] ?? '') == 'Urdu' ? 'selected' : '' }}>Urdu</option>
                        <option value="Other" {{ old('primary_language', $data['primary_language'] ?? '') == 'Other' ? 'selected' : '' }}>Other</option>
                    </select>
                    @error('primary_language')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>

        {{-- Address Information Section --}}
        <div class="mb-10">
            <h3 class="text-gray-800 fw-bold fs-4 mb-6">
                <i class="ki-outline ki-geolocation fs-3 text-primary me-2"></i>
                Address Information
            </h3>

            <div class="row g-6">
                {{-- Street Address --}}
                <div class="col-12">
                    <label class="form-label required fs-6 fw-semibold mb-3">Street Address</label>
                    <input type="text" name="address_line1" id="address_line1" class="form-control form-control-lg @error('address_line1') is-invalid @enderror" value="{{ old('address_line1', $data['address_line1'] ?? '') }}" placeholder="e.g., 123 Main Street" required>
                    @error('address_line1')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Address Line 2 (Optional) --}}
                <div class="col-12">
                    <label class="form-label fs-6 fw-semibold mb-3">Address Line 2 <span class="text-muted">(Optional)</span></label>
                    <input type="text" name="address_line2" id="address_line2" class="form-control form-control-lg @error('address_line2') is-invalid @enderror" value="{{ old('address_line2', $data['address_line2'] ?? '') }}" placeholder="e.g., Apartment 4B">
                    @error('address_line2')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                {{-- City --}}
                <div class="col-md-6">
                    <label class="form-label required fs-6 fw-semibold mb-3">City</label>
                    <input type="text" name="city" id="city" class="form-control form-control-lg @error('city') is-invalid @enderror" value="{{ old('city', $data['city'] ?? '') }}" placeholder="e.g., New York" required>
                    @error('city')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                {{-- State/Province --}}
                <div class="col-md-6">
                    <label class="form-label required fs-6 fw-semibold mb-3">State/Province</label>
                    <input type="text" name="state_province" id="state_province" class="form-control form-control-lg @error('state_province') is-invalid @enderror" value="{{ old('state_province', $data['state_province'] ?? '') }}" placeholder="e.g., NY" required>
                    @error('state_province')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Postal Code --}}
                <div class="col-md-6">
                    <label class="form-label required fs-6 fw-semibold mb-3">Postal Code</label>
                    <input type="text" name="postal_code" id="postal_code" class="form-control form-control-lg @error('postal_code') is-invalid @enderror" value="{{ old('postal_code', $data['postal_code'] ?? '') }}" placeholder="e.g., 10001" required>
                    @error('postal_code')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Country --}}
                <div class="col-md-6">
                    <label class="form-label required fs-6 fw-semibold mb-3">Country</label>
                    <select name="country" id="country" class="form-select form-select-lg @error('country') is-invalid @enderror" required>
                        <option value="">Select country</option>
                        @foreach($countries as $country)
                            <option value="{{ $country }}" {{ old('country', $data['country'] ?? '') == $country ? 'selected' : '' }}>{{ $country }}</option>
                        @endforeach
                    </select>
                    @error('country')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>

        {{-- Navigation Buttons --}}
        <div class="d-flex justify-content-between mt-10 pt-8 border-top border-gray-300">
            <a href="{{ route('application.step1') }}" class="btn btn-light btn-lg fw-semibold px-8">
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
