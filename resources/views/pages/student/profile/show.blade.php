<x-default-layout>

    @section('title')
        {{ __('My Profile') }}
    @endsection

    @section('breadcrumbs')
        {{ Breadcrumbs::render('dashboard') }}
    @endsection

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

    {{-- Success Message --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-5" role="alert">
            <i class="ki-duotone ki-check-circle fs-2 me-2">
                <span class="path1"></span>
                <span class="path2"></span>
            </i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Warning Message --}}
    @if(session('warning'))
        <div class="alert alert-warning alert-dismissible fade show mb-5" role="alert">
            <i class="ki-duotone ki-information-5 fs-2 me-2">
                <span class="path1"></span>
                <span class="path2"></span>
                <span class="path3"></span>
            </i>
            {{ session('warning') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <x-forms.validation-errors />

    <form action="{{ route('student.profile.update') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="row g-5 g-xl-10">
            {{-- Left Column: Profile Photo & Basic Info --}}
            <div class="col-xl-4">
                <div class="card card-flush mb-5">
                    <div class="card-body text-center pt-15 pb-10">
                        {{-- Profile Photo Section --}}
                        <div class="mb-10">
                            <div class="symbol symbol-150px symbol-circle mb-7">
                                @if($user->profile_photo_path)
                                    <img src="{{ $user->profile_photo_url }}" alt="{{ $profile['first_name'] }}" id="profile_preview">
                                @else
                                    <div class="symbol-label bg-light-primary text-primary fs-2 fw-bold" id="profile_initials">
                                        {{ strtoupper(substr($profile['first_name'] ?? '', 0, 1)) }}{{ strtoupper(substr($profile['last_name'] ?? '', 0, 1)) }}
                                    </div>
                                @endif
                            </div>

                            <div class="mt-5">
                                <x-forms.file-upload
                                    name="profile_photo"
                                    label="{{ __('Update Profile Photo') }}"
                                    accept="image/*"
                                    maxSize="2MB"
                                    :preview="true"
                                    help="{{ __('Allowed: JPEG, PNG, GIF. Max 2MB.') }}"
                                />

                                @if($user->profile_photo_path)
                                    <div class="form-check mt-3">
                                        <input type="checkbox" class="form-check-input" name="profile_photo_remove" value="1" id="profile_photo_remove">
                                        <label class="form-check-label text-muted" for="profile_photo_remove">
                                            {{ __('Remove current photo') }}
                                        </label>
                                    </div>
                                @endif
                            </div>
                        </div>

                        {{-- Display Info --}}
                        <p class="text-muted fs-6 mb-5">{{ $profile['email'] }}</p>

                        @if($student)
                            <span class="badge badge-light-primary fs-7">
                                {{ $student->student_number }}
                            </span>
                        @endif
                    </div>
                </div>

                {{-- Info Notice --}}
                <div class="notice d-flex bg-light-info rounded border-info border border-dashed p-6">
                    <i class="ki-duotone ki-information fs-2tx text-info me-4">
                        <span class="path1"></span>
                        <span class="path2"></span>
                        <span class="path3"></span>
                    </i>
                    <div class="d-flex flex-stack flex-grow-1">
                        <div class="fw-semibold">
                            <div class="fs-6 text-gray-700">
                                {{ __('Your email cannot be changed here. Please contact support if you need to update your email address.') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Right Column: Editable Sections --}}
            <div class="col-xl-8">
                {{-- Personal Information --}}
                <div class="card card-flush mb-5">
                    <div class="card-header pt-7">
                        <h3 class="card-title fw-bold text-gray-800">
                            <i class="ki-duotone ki-profile-user fs-2 me-2 text-primary">
                                <span class="path1"></span>
                                <span class="path2"></span>
                                <span class="path3"></span>
                                <span class="path4"></span>
                            </i>
                            {{ __('Personal Information') }}
                        </h3>
                    </div>
                    <div class="card-body pt-5">
                        <div class="row g-6">
                            {{-- First Name --}}
                            <div class="col-md-6">
                                <label class="form-label required fs-6 fw-semibold mb-3">{{ __('First Name') }}</label>
                                <input type="text" name="first_name" class="form-control form-control-lg @error('first_name') is-invalid @enderror" value="{{ old('first_name', $profile['first_name'] ?? '') }}" placeholder="{{ __('e.g., John') }}" required>
                                @error('first_name')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Last Name --}}
                            <div class="col-md-6">
                                <label class="form-label required fs-6 fw-semibold mb-3">{{ __('Last Name') }}</label>
                                <input type="text" name="last_name" class="form-control form-control-lg @error('last_name') is-invalid @enderror" value="{{ old('last_name', $profile['last_name'] ?? '') }}" placeholder="{{ __('e.g., Doe') }}" required>
                                @error('last_name')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Contact Information --}}
                <div class="card card-flush mb-5">
                    <div class="card-header pt-7">
                        <h3 class="card-title fw-bold text-gray-800">
                            <i class="ki-duotone ki-phone fs-2 me-2 text-primary">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                            {{ __('Contact Information') }}
                        </h3>
                    </div>
                    <div class="card-body pt-5">
                        <div class="row g-6">
                            {{-- Phone Number --}}
                            <div class="col-md-6">
                                <label class="form-label fs-6 fw-semibold mb-3">{{ __('Phone Number') }}</label>
                                <input type="tel" name="phone" class="form-control form-control-lg @error('phone') is-invalid @enderror" value="{{ old('phone', $profile['phone'] ?? '') }}" placeholder="{{ __('e.g., +1 234 567 8900') }}">
                                @error('phone')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="separator my-7"></div>
                        <h5 class="fw-semibold text-gray-700 mb-5">
                            <i class="ki-outline ki-geolocation fs-5 text-primary me-2"></i>
                            {{ __('Address') }}
                        </h5>

                        <div class="row g-6">
                            {{-- Street Address --}}
                            <div class="col-12">
                                <label class="form-label fs-6 fw-semibold mb-3">{{ __('Street Address') }}</label>
                                <input type="text" name="address_line1" class="form-control form-control-lg @error('address_line1') is-invalid @enderror" value="{{ old('address_line1', $profile['address']['line1'] ?? '') }}" placeholder="{{ __('e.g., 123 Main Street') }}">
                                @error('address_line1')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Address Line 2 --}}
                            <div class="col-12">
                                <label class="form-label fs-6 fw-semibold mb-3">{{ __('Address Line 2') }} <span class="text-muted">({{ __('Optional') }})</span></label>
                                <input type="text" name="address_line2" class="form-control form-control-lg @error('address_line2') is-invalid @enderror" value="{{ old('address_line2', $profile['address']['line2'] ?? '') }}" placeholder="{{ __('e.g., Apartment 4B') }}">
                                @error('address_line2')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- City --}}
                            <div class="col-md-6">
                                <label class="form-label fs-6 fw-semibold mb-3">{{ __('City') }}</label>
                                <input type="text" name="city" class="form-control form-control-lg @error('city') is-invalid @enderror" value="{{ old('city', $profile['address']['city'] ?? '') }}" placeholder="{{ __('e.g., Toronto') }}">
                                @error('city')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- State/Province --}}
                            <div class="col-md-6">
                                <label class="form-label fs-6 fw-semibold mb-3">{{ __('State/Province') }}</label>
                                <input type="text" name="state_province" class="form-control form-control-lg @error('state_province') is-invalid @enderror" value="{{ old('state_province', $profile['address']['state_province'] ?? '') }}" placeholder="{{ __('e.g., Ontario') }}">
                                @error('state_province')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Postal Code --}}
                            <div class="col-md-6">
                                <label class="form-label fs-6 fw-semibold mb-3">{{ __('Postal Code') }}</label>
                                <input type="text" name="postal_code" class="form-control form-control-lg @error('postal_code') is-invalid @enderror" value="{{ old('postal_code', $profile['address']['postal_code'] ?? '') }}" placeholder="{{ __('e.g., M5V 2T6') }}">
                                @error('postal_code')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Country --}}
                            <div class="col-md-6">
                                <label class="form-label fs-6 fw-semibold mb-3">{{ __('Country') }}</label>
                                <select name="country" class="form-select form-select-lg @error('country') is-invalid @enderror">
                                    <option value="">{{ __('Select country') }}</option>
                                    @foreach($countries as $countryOption)
                                        <option value="{{ $countryOption }}" {{ old('country', $profile['address']['country'] ?? '') == $countryOption ? 'selected' : '' }}>{{ $countryOption }}</option>
                                    @endforeach
                                </select>
                                @error('country')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Emergency Contact --}}
                <div class="card card-flush mb-5">
                    <div class="card-header pt-7">
                        <h3 class="card-title fw-bold text-gray-800">
                            <i class="ki-duotone ki-shield-tick fs-2 me-2 text-danger">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                            {{ __('Emergency Contact') }}
                        </h3>
                    </div>
                    <div class="card-body pt-5">
                        <div class="row g-6">
                            {{-- First Name --}}
                            <div class="col-md-6">
                                <label class="form-label fs-6 fw-semibold mb-3">{{ __('First Name') }}</label>
                                <input type="text" name="emergency_first_name" class="form-control form-control-lg @error('emergency_first_name') is-invalid @enderror" value="{{ old('emergency_first_name', $profile['emergency_first_name'] ?? '') }}" placeholder="{{ __('e.g., John') }}">
                                @error('emergency_first_name')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Last Name --}}
                            <div class="col-md-6">
                                <label class="form-label fs-6 fw-semibold mb-3">{{ __('Last Name') }}</label>
                                <input type="text" name="emergency_last_name" class="form-control form-control-lg @error('emergency_last_name') is-invalid @enderror" value="{{ old('emergency_last_name', $profile['emergency_last_name'] ?? '') }}" placeholder="{{ __('e.g., Doe') }}">
                                @error('emergency_last_name')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Phone Number --}}
                            <div class="col-md-6">
                                <label class="form-label fs-6 fw-semibold mb-3">{{ __('Phone Number') }}</label>
                                <input type="tel" name="emergency_phone" class="form-control form-control-lg @error('emergency_phone') is-invalid @enderror" value="{{ old('emergency_phone', $profile['emergency_phone'] ?? '') }}" placeholder="{{ __('e.g., +1 234 567 8900') }}">
                                @error('emergency_phone')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="separator my-7"></div>
                        <h5 class="fw-semibold text-gray-700 mb-5">
                            <i class="ki-outline ki-geolocation fs-5 text-primary me-2"></i>
                            {{ __('Emergency Contact Address') }}
                        </h5>

                        <div class="row g-6">
                            {{-- Street Address --}}
                            <div class="col-12">
                                <label class="form-label fs-6 fw-semibold mb-3">{{ __('Street Address') }}</label>
                                <input type="text" name="emergency_address_line1" class="form-control form-control-lg @error('emergency_address_line1') is-invalid @enderror" value="{{ old('emergency_address_line1', $profile['emergency_address']['line1'] ?? '') }}" placeholder="{{ __('e.g., 123 Main Street') }}">
                                @error('emergency_address_line1')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Address Line 2 --}}
                            <div class="col-12">
                                <label class="form-label fs-6 fw-semibold mb-3">{{ __('Address Line 2') }} <span class="text-muted">({{ __('Optional') }})</span></label>
                                <input type="text" name="emergency_address_line2" class="form-control form-control-lg @error('emergency_address_line2') is-invalid @enderror" value="{{ old('emergency_address_line2', $profile['emergency_address']['line2'] ?? '') }}" placeholder="{{ __('e.g., Apartment 4B') }}">
                                @error('emergency_address_line2')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- City --}}
                            <div class="col-md-6">
                                <label class="form-label fs-6 fw-semibold mb-3">{{ __('City') }}</label>
                                <input type="text" name="emergency_city" class="form-control form-control-lg @error('emergency_city') is-invalid @enderror" value="{{ old('emergency_city', $profile['emergency_address']['city'] ?? '') }}" placeholder="{{ __('e.g., Toronto') }}">
                                @error('emergency_city')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- State/Province --}}
                            <div class="col-md-6">
                                <label class="form-label fs-6 fw-semibold mb-3">{{ __('State/Province') }}</label>
                                <input type="text" name="emergency_state_province" class="form-control form-control-lg @error('emergency_state_province') is-invalid @enderror" value="{{ old('emergency_state_province', $profile['emergency_address']['state_province'] ?? '') }}" placeholder="{{ __('e.g., Ontario') }}">
                                @error('emergency_state_province')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Postal Code --}}
                            <div class="col-md-6">
                                <label class="form-label fs-6 fw-semibold mb-3">{{ __('Postal Code') }}</label>
                                <input type="text" name="emergency_postal_code" class="form-control form-control-lg @error('emergency_postal_code') is-invalid @enderror" value="{{ old('emergency_postal_code', $profile['emergency_address']['postal_code'] ?? '') }}" placeholder="{{ __('e.g., M5V 2T6') }}">
                                @error('emergency_postal_code')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Country --}}
                            <div class="col-md-6">
                                <label class="form-label fs-6 fw-semibold mb-3">{{ __('Country') }}</label>
                                <select name="emergency_country" class="form-select form-select-lg @error('emergency_country') is-invalid @enderror">
                                    <option value="">{{ __('Select country') }}</option>
                                    @foreach($countries as $countryOption)
                                        <option value="{{ $countryOption }}" {{ old('emergency_country', $profile['emergency_address']['country'] ?? '') == $countryOption ? 'selected' : '' }}>{{ $countryOption }}</option>
                                    @endforeach
                                </select>
                                @error('emergency_country')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Save Button --}}
                <div class="d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary btn-lg fw-semibold px-8">
                        <i class="ki-duotone ki-check fs-4 me-1">
                            <span class="path1"></span>
                            <span class="path2"></span>
                        </i>
                        {{ __('Save Changes') }}
                    </button>
                </div>
            </div>
        </div>
    </form>

</x-default-layout>
