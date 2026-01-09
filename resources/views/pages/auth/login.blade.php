<x-auth-layout>

    <!--begin::Form-->
    <form class="form w-100" novalidate="novalidate" id="kt_sign_in_form" data-kt-redirect-url="{{ route('dashboard') }}" action="{{ route('login') }}" method="POST">
        @csrf
        <!--begin::Heading-->
        <div class="text-center mb-11">
            <!--begin::Title-->
            <h1 class="text-gray-900 fw-bolder mb-3">
                {{ __('Sign In') }}
            </h1>
            <!--end::Title-->

            <!--begin::Subtitle-->
            <!-- <div class="text-gray-500 fw-semibold fs-6">
                {{ __('Sign in to your account') }}
            </div> -->
            <!--end::Subtitle--->
        </div>
        <!--begin::Heading-->

        <!--begin::Success Message-->
        @if(session('success'))
            <div class="alert alert-success d-flex align-items-center mb-10">
                <i class="ki-outline ki-shield-tick fs-2hx text-success me-4"></i>
                <div class="d-flex flex-column">
                    <h4 class="mb-1 text-success">{{ __('Success!') }}</h4>
                    <span>{{ session('success') }}</span>
                </div>
            </div>
        @endif
        <!--end::Success Message-->


        <!--begin::Input group--->
        <div class="fv-row mb-8">
            <!--begin::Email-->
            <input type="text" placeholder="{{ __('Email') }}" name="email" autocomplete="off" value="{{ old('email') }}" class="form-control bg-transparent @error('email') is-invalid @enderror" />
            @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
            <!--end::Email-->
        </div>

        <!--end::Input group--->
        <div class="fv-row mb-3">
            <!--begin::Password-->
            <div class="position-relative">
                <input type="password" placeholder="{{ __('Password') }}" name="password" id="password" autocomplete="off" class="form-control bg-transparent @error('password') is-invalid @enderror"/>
                <span class="btn btn-sm btn-icon position-absolute translate-middle top-50 end-0 me-n2" id="togglePassword" style="cursor: pointer;">
                    <i class="ki-duotone ki-eye fs-2" id="eyeIcon">
                        <span class="path1"></span>
                        <span class="path2"></span>
                        <span class="path3"></span>
                    </i>
                </span>
            </div>
            @error('password')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
            <!--end::Password-->
        </div>
        
        @if($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <!--end::Input group--->

        <!--begin::Wrapper-->
        <div class="d-flex flex-stack flex-wrap gap-3 fs-base fw-semibold mb-8">
            <div></div>

            <!--begin::Link-->
            <a href="{{ route('password.request') }}" class="link-primary">
                {{ __('Forgot Password ?') }}
            </a>
            <!--end::Link-->
        </div>
        <!--end::Wrapper-->

        <!--begin::Submit button-->
        <div class="d-grid mb-10">
            <button type="submit" id="kt_sign_in_submit" class="btn btn-primary">
                @include('partials/general/_button-indicator', ['label' => 'Sign In'])
            </button>
        </div>
        <!--end::Submit button-->

     
    </form>
    <!--end::Form-->

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const togglePassword = document.getElementById('togglePassword');
            const passwordInput = document.getElementById('password');
            const eyeIcon = document.getElementById('eyeIcon');

            togglePassword.addEventListener('click', function() {
                // Toggle the type attribute
                const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordInput.setAttribute('type', type);
                
                // Toggle the icon
                if (type === 'password') {
                    eyeIcon.className = 'ki-duotone ki-eye fs-2';
                    eyeIcon.innerHTML = '<span class="path1"></span><span class="path2"></span><span class="path3"></span>';
                } else {
                    eyeIcon.className = 'ki-duotone ki-eye-slash fs-2';
                    eyeIcon.innerHTML = '<span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span>';
                }
            });
        });
    </script>
    @endpush

</x-auth-layout>
