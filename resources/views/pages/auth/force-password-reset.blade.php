<x-auth-layout>

    <!--begin::Form-->
    <form class="form w-100" novalidate="novalidate" id="kt_force_password_reset_form" action="{{ route('password.reset.update') }}" method="POST" x-data="passwordForm()">
        @csrf

        <!--begin::Heading-->
        <div class="text-center mb-10">
            <!--begin::Title-->
            <h1 class="text-gray-900 fw-bolder mb-3">
                {{ __('Password Reset Required') }}
            </h1>
            <!--end::Title-->

            <!--begin::Link-->
            <div class="text-gray-500 fw-semibold fs-6">
                {{ __('For security reasons, you must change your temporary password before accessing your account.') }}
            </div>
            <!--end::Link-->
        </div>
        <!--begin::Heading-->

        @if(session('warning'))
            <div class="alert alert-warning d-flex align-items-center p-5 mb-10">
                <i class="ki-outline ki-information-5 fs-2hx text-warning me-4"></i>
                <div class="d-flex flex-column">
                    <h4 class="mb-1 text-warning">{{ __('Security Notice') }}</h4>
                    <span>{{ session('warning') }}</span>
                </div>
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger d-flex align-items-center p-5 mb-10">
                <i class="ki-outline ki-information-5 fs-2hx text-danger me-4"></i>
                <div class="d-flex flex-column">
                    <h4 class="mb-1 text-danger">{{ __('Please fix the following errors:') }}</h4>
                    <ul class="mb-0 ps-4">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        <!--begin::Input group-->
        <div class="fv-row mb-8">
            <label class="form-label fw-bold text-gray-900 fs-6">{{ __('Current Password (Temporary)') }}</label>
            <input class="form-control bg-transparent @error('current_password') is-invalid @enderror" 
                   type="password" 
                   name="current_password" 
                   autocomplete="current-password"
                   required/>
            @error('current_password')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <!--end::Input group-->

        <!--begin::Input group-->
        <div class="fv-row mb-8" data-kt-password-meter="true">
            <!--begin::Wrapper-->
            <div class="mb-1">
                <!--begin::Input wrapper-->
                <div class="position-relative mb-3">
                    <label class="form-label fw-bold text-gray-900 fs-6">{{ __('New Password') }}</label>
                    <input class="form-control bg-transparent @error('password') is-invalid @enderror" 
                           type="password" 
                           placeholder="{{ __('New Password') }}" 
                           name="password" 
                           autocomplete="new-password"
                           x-model="newPassword"
                           @input="checkRequirements()"
                           required/>

                    <span class="btn btn-sm btn-icon position-absolute translate-middle top-50 end-0 me-n2" data-kt-password-meter-control="visibility">
                        <i class="bi bi-eye-slash fs-2"></i>
                        <i class="bi bi-eye fs-2 d-none"></i>
                    </span>
                </div>
                <!--end::Input wrapper-->

                <!--begin::Meter-->
                <div class="d-flex align-items-center mb-3" data-kt-password-meter-control="highlight">
                    <div class="flex-grow-1 bg-secondary bg-active-success rounded h-5px me-2"></div>
                    <div class="flex-grow-1 bg-secondary bg-active-success rounded h-5px me-2"></div>
                    <div class="flex-grow-1 bg-secondary bg-active-success rounded h-5px me-2"></div>
                    <div class="flex-grow-1 bg-secondary bg-active-success rounded h-5px"></div>
                </div>
                <!--end::Meter-->
            </div>
            <!--end::Wrapper-->

            <!--begin::Requirements-->
            <div class="text-muted mb-3">
                <div class="d-flex align-items-center mb-2">
                    <i :class="requirements.length ? 'bi bi-check-circle-fill text-success' : 'bi bi-circle text-gray-400'" class="me-2"></i>
                    <span :class="requirements.length ? 'text-success' : 'text-muted'">At least 8 characters</span>
                </div>
                <div class="d-flex align-items-center mb-2">
                    <i :class="requirements.uppercase ? 'bi bi-check-circle-fill text-success' : 'bi bi-circle text-gray-400'" class="me-2"></i>
                    <span :class="requirements.uppercase ? 'text-success' : 'text-muted'">One uppercase letter</span>
                </div>
                <div class="d-flex align-items-center mb-2">
                    <i :class="requirements.lowercase ? 'bi bi-check-circle-fill text-success' : 'bi bi-circle text-gray-400'" class="me-2"></i>
                    <span :class="requirements.lowercase ? 'text-success' : 'text-muted'">One lowercase letter</span>
                </div>
                <div class="d-flex align-items-center mb-2">
                    <i :class="requirements.number ? 'bi bi-check-circle-fill text-success' : 'bi bi-circle text-gray-400'" class="me-2"></i>
                    <span :class="requirements.number ? 'text-success' : 'text-muted'">One number</span>
                </div>
                <div class="d-flex align-items-center">
                    <i :class="requirements.symbol ? 'bi bi-check-circle-fill text-success' : 'bi bi-circle text-gray-400'" class="me-2"></i>
                    <span :class="requirements.symbol ? 'text-success' : 'text-muted'">One special character</span>
                </div>
            </div>
            <!--end::Requirements-->

            @error('password')
                <div class="text-danger fs-7">{{ $message }}</div>
            @enderror
        </div>
        <!--end::Input group-->

        <!--begin::Input group-->
        <div class="fv-row mb-8">
            <label class="form-label fw-bold text-gray-900 fs-6">{{ __('Confirm New Password') }}</label>
            <input class="form-control bg-transparent" 
                   type="password" 
                   placeholder="{{ __('Repeat New Password') }}" 
                   name="password_confirmation" 
                   autocomplete="new-password"
                   required/>
        </div>
        <!--end::Input group-->

        <!--begin::Actions-->
        <div class="d-flex flex-wrap justify-content-center pb-lg-0">
            <button type="submit" 
                    id="kt_force_password_reset_submit" 
                    class="btn btn-primary me-4"
                    :disabled="!allRequirementsMet()">
                @include('partials/general/_button-indicator', ['label' => 'Change Password & Continue'])
            </button>
        </div>
        <!--end::Actions-->
    </form>
    <!--end::Form-->

    @push('scripts')
    <script>
        function passwordForm() {
            return {
                newPassword: '',
                requirements: {
                    length: false,
                    uppercase: false,
                    lowercase: false,
                    number: false,
                    symbol: false,
                },

                checkRequirements() {
                    this.requirements.length = this.newPassword.length >= 8;
                    this.requirements.uppercase = /[A-Z]/.test(this.newPassword);
                    this.requirements.lowercase = /[a-z]/.test(this.newPassword);
                    this.requirements.number = /[0-9]/.test(this.newPassword);
                    this.requirements.symbol = /[!@#$%^&*(),.?":{}|<>]/.test(this.newPassword);
                },

                allRequirementsMet() {
                    return Object.values(this.requirements).every(req => req === true);
                }
            };
        }
    </script>
    @endpush

</x-auth-layout>

