@extends('application.layout')

@section('page-title', 'Check Application Status')
@section('page-subtitle', 'Track your admission application progress')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-6 col-md-8">
        <!-- Status Check Form Card -->
        <div class="card shadow-sm">
            <div class="card-header" style="background: linear-gradient(135deg, #12294C 0%, #1e3a5f 100%);">
                <div class="card-title text-white">
                    <div class="d-flex align-items-center">
                        <i class="ki-outline ki-magnifier text-white fs-2x me-3"></i>
                        <div>
                            <h3 class="text-white mb-1">Application Lookup</h3>
                            <p class="text-white opacity-75 mb-0 fs-6">Enter your details below</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-body p-10">
                <!-- Instructions -->
                <div class="notice d-flex bg-light-info rounded border-info border border-dashed p-6 mb-8">
                    <i class="ki-outline ki-information-5 fs-2tx text-info me-4"></i>
                    <div class="d-flex flex-stack flex-grow-1">
                        <div class="fw-semibold">
                            <h6 class="text-gray-900 fw-bold mb-2">How to Check Your Status</h6>
                            <div class="fs-6 text-gray-700">
                                Enter the email address and reference number you received via email
                                after submitting your application. Both are required for verification.
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Error Messages -->
                @if($errors->any())
                    <div class="alert alert-danger d-flex align-items-center p-5 mb-8">
                        <i class="ki-outline ki-shield-cross fs-2hx text-danger me-4"></i>
                        <div class="d-flex flex-column">
                            <h5 class="mb-2">Error</h5>
                            @foreach($errors->all() as $error)
                                <span>{{ $error }}</span>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Status Check Form -->
                <form method="POST" action="{{ route('application.status.check') }}" id="statusCheckForm">
                    @csrf

                    <!-- Email Field -->
                    <div class="mb-8">
                        <label class="form-label required fw-bold text-gray-900">Email Address</label>
                        <div class="input-group input-group-solid">
                            <span class="input-group-text">
                                <i class="ki-outline ki-sms fs-2 text-primary"></i>
                            </span>
                            <input
                                type="email"
                                name="email"
                                class="form-control form-control-solid @error('email') is-invalid @enderror"
                                value="{{ old('email') }}"
                                required
                                placeholder="your.email@example.com"
                                autocomplete="email"
                            >
                        </div>
                        @error('email')
                            <div class="text-danger fw-semibold fs-7 mt-2">{{ $message }}</div>
                        @enderror
                        <div class="form-text">
                            Use the same email address you provided in your application
                        </div>
                    </div>

                    <!-- Reference Number Field -->
                    <div class="mb-10">
                        <label class="form-label required fw-bold text-gray-900">Reference Number</label>
                        <div class="input-group input-group-solid">
                            <span class="input-group-text">
                                <i class="ki-outline ki-barcode fs-2 text-primary"></i>
                            </span>
                            <input
                                type="text"
                                name="reference_number"
                                class="form-control form-control-solid @error('reference_number') is-invalid @enderror"
                                required
                                placeholder="APP-20251123-A1B2"
                                pattern="APP-\d{8}-[A-Z0-9]{4}"
                                title="Format: APP-YYYYMMDD-XXXX"
                            >
                        </div>
                        @error('reference_number')
                            <div class="text-danger fw-semibold fs-7 mt-2">{{ $message }}</div>
                        @enderror
                        <div class="form-text">
                            Example format: APP-20251123-A1B2 (provided in your confirmation email)
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="ki-outline ki-magnifier fs-3 me-2"></i>
                        Check Application Status
                    </button>
                </form>

                <!-- Divider -->
                <div class="separator separator-content my-10">
                    <span class="w-250px text-gray-500 fw-semibold fs-6">OR</span>
                </div>

                <!-- Additional Actions -->
                <div class="text-center">
                    <p class="text-gray-700 fw-semibold mb-5">Don't have an application yet?</p>
                    <a href="{{ route('application.step1') }}" class="btn btn-light-primary">
                        <i class="ki-outline ki-document fs-3 me-2"></i>
                        Start New Application
                    </a>
                </div>
            </div>
        </div>

        <!-- Help Section -->
        <div class="notice d-flex bg-light rounded border border-gray-300 p-8 mt-8">
            <i class="ki-outline ki-question-2 fs-2tx text-primary me-4"></i>
            <div class="d-flex flex-stack flex-grow-1">
                <div class="fw-semibold">
                    <h6 class="text-gray-900 fw-bold mb-3">Need Help?</h6>
                    <div class="fs-6 text-gray-700">
                        <ul class="mb-0">
                            <li class="mb-2">Your reference number was sent to your email after submission</li>
                            <li class="mb-2">Check your spam/junk folder if you can't find the email</li>
                            <li class="mb-2">For technical support, contact admissions@{{ config('app.url') }}</li>
                            <li>Status updates are processed within 1-2 business days</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Client-side validation enhancement
    document.getElementById('statusCheckForm').addEventListener('submit', function(e) {
        const refNumber = document.querySelector('input[name="reference_number"]').value;

        // Validate reference number format
        const refNumberPattern = /^APP-\d{8}-[A-Z0-9]{4}$/;
        if (!refNumberPattern.test(refNumber)) {
            e.preventDefault();
            alert('Please enter a valid reference number format: APP-YYYYMMDD-XXXX');
            return false;
        }
    });
</script>
@endpush
@endsection
