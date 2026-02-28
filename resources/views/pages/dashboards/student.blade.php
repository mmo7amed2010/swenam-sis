<x-default-layout>

    @section('title')
        {{ __('Student Home') }}
    @endsection

    @section('breadcrumbs')
        {{ Breadcrumbs::render('dashboard') }}
    @endsection

    {{-- Welcome Header --}}
    <div class="row g-5 g-xl-10 mb-5 mb-xl-10">
        <div class="col-xl-12">
            <div class="card card-flush">
                <div class="card-body p-8">
                    <div class="d-flex align-items-center">
                        <div class="symbol symbol-60px symbol-circle me-5">
                            @if($user->profile_photo_path)
                                <img src="{{ $user->profile_photo_url }}" alt="{{ $user->name }}">
                            @else
                                <div class="symbol-label bg-light-primary text-primary fs-2 fw-bold">
                                    {{ strtoupper(substr($user->first_name ?? $user->name, 0, 1)) }}
                                </div>
                            @endif
                        </div>
                        <div class="flex-grow-1">
                            <h2 class="mb-1 text-gray-900">{{ __('Welcome back') }}, {{ $user->first_name ?? $user->name }}!</h2>
                            @if($student)
                                <div class="text-muted fs-6">
                                    {{ __('Student Number:') }} <span class="fw-semibold">{{ $student->student_number }}</span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Application Rejected Banner --}}
    @if(isset($application) && $application->isRejected())
        <div class="row g-5 g-xl-10 mb-5 mb-xl-10">
            <div class="col-xl-12">
                <div class="card bg-light-danger border-0">
                    <div class="card-body p-8">
                        <div class="d-flex align-items-center">
                            <i class="ki-duotone ki-shield-cross fs-3tx text-danger me-5">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                            <div class="flex-grow-1">
                                <h3 class="mb-1 text-gray-900">{{ __('Application Rejected') }}</h3>
                                <p class="text-gray-700 mb-3">
                                    {{ __('Your application') }}
                                    <strong class="text-danger">{{ $application->reference_number }}</strong>
                                    {{ __('has been rejected.') }}
                                </p>
                                <p class="text-muted mb-0 fs-7">
                                    <i class="ki-duotone ki-information-2 fs-6 me-1">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                        <span class="path3"></span>
                                    </i>
                                    {{ __('Please contact support for more information.') }}
                                </p>
                            </div>
                            <div class="ms-5 d-none d-md-block">
                                <a href="{{ route('student.program.index') }}" class="btn btn-danger">
                                    {{ __('View Application') }}
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    {{-- Application Status Banner (shown if pending approval) --}}
    @elseif(!empty($isPendingApproval) && isset($application))
        <div class="row g-5 g-xl-10 mb-5 mb-xl-10">
            <div class="col-xl-12">
                <div class="card bg-light-info border-0">
                    <div class="card-body p-8">
                        <div class="d-flex align-items-center">
                            <i class="ki-duotone ki-timer fs-3tx text-info me-5">
                                <span class="path1"></span>
                                <span class="path2"></span>
                                <span class="path3"></span>
                            </i>
                            <div class="flex-grow-1">
                                <h3 class="mb-1 text-gray-900">{{ __('Application Under Review') }}</h3>
                                <p class="text-gray-700 mb-3">
                                    {{ __('Your application') }}
                                    <strong class="text-info">{{ $application->reference_number }}</strong>
                                    {{ __('is currently being reviewed by our admissions team.') }}
                                </p>
                                <div class="d-flex flex-wrap gap-3">
                                    <span class="badge badge-light-primary fs-7">
                                        <i class="ki-duotone ki-document fs-6 me-1">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                        {{ __('Program:') }} {{ $application->program_name ?? 'N/A' }}
                                    </span>
                                    <span class="badge badge-light-{{ $application->status === 'initial_approved' ? 'success' : 'warning' }} fs-7">
                                        <i class="ki-duotone ki-check-circle fs-6 me-1">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                        {{ __('Status:') }}
                                        @if($application->status === 'initial_approved')
                                            {{ __('Under Final Review') }}
                                        @else
                                            {{ __('Pending Review') }}
                                        @endif
                                    </span>
                                    <span class="badge badge-light-secondary fs-7">
                                        <i class="ki-duotone ki-calendar fs-6 me-1">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                        {{ __('Submitted:') }} {{ $application->created_at->format('M d, Y') }}
                                    </span>
                                </div>
                                <p class="text-muted mt-3 mb-0 fs-7">
                                    <i class="ki-duotone ki-information-2 fs-6 me-1">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                        <span class="path3"></span>
                                    </i>
                                    {{ __('Course access will be enabled once your application is approved. You will receive an email notification.') }}
                                </p>
                            </div>
                            <div class="ms-5 d-none d-md-block">
                                <a href="{{ route('student.program.index') }}" class="btn btn-info">
                                    {{ __('View Application') }}
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Approved Student / Bypass Student - Course Access Card --}}
    @if(auth()->user()->isApplicationApproved() && auth()->user()->hasLmsAccount())
        <div class="row g-5 g-xl-10 mb-5 mb-xl-10">
            <div class="col-xl-12">
                <div class="card bg-light-success border-0">
                    <div class="card-body p-8">
                        <div class="d-flex align-items-center">
                            <i class="ki-duotone ki-book-open fs-3tx text-success me-5">
                                <span class="path1"></span>
                                <span class="path2"></span>
                                <span class="path3"></span>
                                <span class="path4"></span>
                            </i>
                            <div class="flex-grow-1">
                                <h3 class="mb-1 text-gray-900">{{ __('Your Courses Are Ready!') }}</h3>
                                <p class="text-gray-700 mb-0">
                                    @if(!empty($isBypassStudent) && empty($application))
                                        {{ __('Your courses are now available. Click the button to access your learning materials.') }}
                                    @else
                                        {{ __('Your application has been approved and your courses are now available.') }}
                                    @endif
                                </p>
                                @if($program)
                                    <div class="mt-2">
                                        <span class="badge badge-light-primary fs-7">
                                            {{ $program->name ?? $application->program_name ?? __('My Program') }}
                                        </span>
                                    </div>
                                @endif
                            </div>
                            <div class="ms-5">
                                <a href="{{ route('student.my-courses.redirect') }}" class="btn btn-success" target="_blank">
                                    <i class="ki-duotone ki-arrow-right fs-4 me-1">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                    {{ __('Go to My Courses') }}
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Student Info Card --}}
    <div class="row g-5 g-xl-10 mb-5 mb-xl-10">
        <div class="col-xl-6">
            <div class="card card-flush h-100">
                <div class="card-header pt-7">
                    <h3 class="card-title align-items-start flex-column">
                        <span class="card-label fw-bold text-gray-800">{{ __('My Information') }}</span>
                    </h3>
                </div>
                <div class="card-body pt-5">
                    <div class="d-flex flex-column gap-4">
                        <div class="d-flex align-items-center">
                            <div class="symbol symbol-40px me-4">
                                <span class="symbol-label bg-light-primary">
                                    <i class="ki-duotone ki-user fs-2 text-primary">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                </span>
                            </div>
                            <div>
                                <div class="fs-7 text-muted">{{ __('Full Name') }}</div>
                                <div class="fw-bold text-gray-800">{{ $user->name }}</div>
                            </div>
                        </div>
                        <div class="d-flex align-items-center">
                            <div class="symbol symbol-40px me-4">
                                <span class="symbol-label bg-light-info">
                                    <i class="ki-duotone ki-sms fs-2 text-info">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                </span>
                            </div>
                            <div>
                                <div class="fs-7 text-muted">{{ __('Email') }}</div>
                                <div class="fw-bold text-gray-800">{{ $user->email }}</div>
                            </div>
                        </div>
                        @if($student)
                            <div class="d-flex align-items-center">
                                <div class="symbol symbol-40px me-4">
                                    <span class="symbol-label bg-light-success">
                                        <i class="ki-duotone ki-badge fs-2 text-success">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                            <span class="path3"></span>
                                            <span class="path4"></span>
                                            <span class="path5"></span>
                                        </i>
                                    </span>
                                </div>
                                <div>
                                    <div class="fs-7 text-muted">{{ __('Student Number') }}</div>
                                    <div class="fw-bold text-gray-800">{{ $student->student_number }}</div>
                                </div>
                            </div>
                        @endif
                        @if($program || ($application && $application->program_name))
                            <div class="d-flex align-items-center">
                                <div class="symbol symbol-40px me-4">
                                    <span class="symbol-label bg-light-warning">
                                        <i class="ki-duotone ki-route fs-2 text-warning">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                            <span class="path3"></span>
                                            <span class="path4"></span>
                                        </i>
                                    </span>
                                </div>
                                <div>
                                    <div class="fs-7 text-muted">{{ __('Program') }}</div>
                                    <div class="fw-bold text-gray-800">{{ $program->name ?? $application->program_name ?? 'N/A' }}</div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Quick Actions --}}
        <div class="col-xl-6">
            <div class="card card-flush h-100">
                <div class="card-header pt-7">
                    <h3 class="card-title align-items-start flex-column">
                        <span class="card-label fw-bold text-gray-800">{{ __('Quick Actions') }}</span>
                    </h3>
                </div>
                <div class="card-body pt-5">
                    <div class="d-flex flex-column gap-4">
                        <a href="{{ route('student.profile.show') }}" class="btn btn-flex btn-light-info w-100 py-4">
                            <i class="ki-duotone ki-user fs-2 me-2">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                            {{ __('My Profile') }}
                        </a>
                        <a href="{{ route('student.program.index') }}" class="btn btn-flex btn-light-primary w-100 py-4">
                            <i class="ki-duotone ki-document fs-2 me-2">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                            {{ __('My Application') }}
                        </a>
                        @if(auth()->user()->isApplicationApproved() && auth()->user()->hasLmsAccount())
                            <a href="{{ route('student.my-courses.redirect') }}" class="btn btn-flex btn-light-success w-100 py-4" target="_blank">
                                <i class="ki-duotone ki-book-open fs-2 me-2">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                    <span class="path3"></span>
                                    <span class="path4"></span>
                                </i>
                                {{ __('My Courses') }}
                                <i class="ki-duotone ki-entrance-left fs-4 ms-2">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- NOA Upload Modal (auto-shown on dashboard if student needs to upload) --}}
    @if(isset($application) && $application && $application->canUploadNoa())
        <x-modals.base-modal id="noaUploadModal" :static="true">
            <x-slot:header>
                <div class="d-flex align-items-center">
                    <i class="ki-duotone ki-document fs-2 text-primary me-3">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i>
                    <h5 class="modal-title fw-bold mb-0">{{ __('Notice of Assessment Required') }}</h5>
                </div>
            </x-slot:header>

            @if($application->noa_rejection_reason)
                <div class="alert alert-danger d-flex align-items-center p-4 mb-4">
                    <i class="ki-duotone ki-information fs-2x text-danger me-3">
                        <span class="path1"></span>
                        <span class="path2"></span>
                        <span class="path3"></span>
                    </i>
                    <div>
                        <strong>{{ __('NOA Rejected:') }}</strong> {{ $application->noa_rejection_reason }}
                    </div>
                </div>
            @endif

            <p class="text-gray-600 fs-7 mb-4">{{ __('Upload your NOA document (PDF, JPG, or PNG, max 10MB).') }}</p>
            <form id="noaUploadForm" action="{{ route('student.noa.upload') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="mb-4">
                    <input type="file" name="noa_document" class="form-control form-control-solid" accept=".pdf,.jpg,.jpeg,.png" required />
                </div>
                <div class="d-flex gap-3">
                    <button type="submit" class="btn btn-success flex-grow-1">
                        <i class="ki-duotone ki-up fs-5 me-2"><span class="path1"></span><span class="path2"></span></i>
                        {{ __('Upload NOA') }}
                    </button>
                    <button type="button" id="noaSkipBtn" class="btn btn-light-warning flex-grow-1">
                        <i class="ki-duotone ki-time fs-5 me-2"><span class="path1"></span><span class="path2"></span></i>
                        {{ __('Skip for Now') }}
                    </button>
                </div>
            </form>
        </x-modals.base-modal>

        {{-- Inline NOA Upload Card (always visible when NOA is requested) --}}
        <div class="row g-5 g-xl-10 mb-5 mb-xl-10">
            <div class="col-xl-12">
                <div class="card border border-dashed border-gray-300">
                    <div class="card-body p-6">
                        <div class="d-flex align-items-center mb-4">
                            <div class="symbol symbol-45px me-4">
                                <span class="symbol-label bg-light-primary">
                                    <i class="ki-duotone ki-document fs-2 text-primary">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                </span>
                            </div>
                            <div>
                                <h5 class="fw-bold text-gray-800 mb-0">{{ __('Upload Notice of Assessment') }}</h5>
                                <span class="text-muted fs-7">{{ __('PDF, JPG, or PNG, max 10MB') }}</span>
                            </div>
                        </div>

                        @if($application->noa_rejection_reason)
                            <div class="alert alert-danger d-flex align-items-center p-4 mb-4">
                                <i class="ki-duotone ki-information fs-2x text-danger me-3">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                    <span class="path3"></span>
                                </i>
                                <div>
                                    <strong>{{ __('NOA Rejected:') }}</strong> {{ $application->noa_rejection_reason }}
                                </div>
                            </div>
                        @endif

                        <form id="noaInlineUploadForm" action="{{ route('student.noa.upload') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="mb-4">
                                <input type="file" name="noa_document" class="form-control form-control-solid" accept=".pdf,.jpg,.jpeg,.png" required />
                            </div>
                            <button type="submit" class="btn btn-success">
                                <i class="ki-duotone ki-up fs-5 me-2"><span class="path1"></span><span class="path2"></span></i>
                                {{ __('Upload NOA') }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        @push('scripts')
            <script>
                document.addEventListener('DOMContentLoaded', function () {
                    var skipKey = 'noaSkipped_{{ session()->getId() }}';
                    if (!sessionStorage.getItem(skipKey)) {
                        var noaModal = new bootstrap.Modal(document.getElementById('noaUploadModal'));
                        noaModal.show();
                    }
                    document.getElementById('noaSkipBtn').addEventListener('click', function () {
                        sessionStorage.setItem(skipKey, '1');
                        bootstrap.Modal.getInstance(document.getElementById('noaUploadModal')).hide();
                    });

                    // File size validation for both forms
                    var maxSize = 10 * 1024 * 1024;
                    document.querySelectorAll('#noaUploadForm input[name="noa_document"], #noaInlineUploadForm input[name="noa_document"]').forEach(function (fileInput) {
                        fileInput.addEventListener('change', function () {
                            if (this.files[0] && this.files[0].size > maxSize) {
                                this.value = '';
                                alert('{{ __("File size must not exceed 10MB.") }}');
                            }
                        });
                    });

                    // Loading state on submit + cross-disable for both NOA forms
                    var noaModalForm = document.getElementById('noaUploadForm');
                    var noaInlineForm = document.getElementById('noaInlineUploadForm');

                    function disableNoaForm(form, loadingText) {
                        var btn = form.querySelector('button[type="submit"]');
                        if (btn) {
                            btn.disabled = true;
                            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>' + loadingText;
                        }
                    }

                    if (noaModalForm) {
                        noaModalForm.addEventListener('submit', function () {
                            disableNoaForm(noaModalForm, '{{ __("Uploading...") }}');
                            if (noaInlineForm) disableNoaForm(noaInlineForm, '{{ __("Uploading...") }}');
                        });
                    }
                    if (noaInlineForm) {
                        noaInlineForm.addEventListener('submit', function () {
                            disableNoaForm(noaInlineForm, '{{ __("Uploading...") }}');
                            if (noaModalForm) disableNoaForm(noaModalForm, '{{ __("Uploading...") }}');
                        });
                    }
                });
            </script>
        @endpush
    @endif

    {{-- MSFAA Confirmation Modal (auto-shown on dashboard if student needs to confirm) --}}
    @if(isset($application) && $application && $application->canConfirmMsfaa())
        <x-modals.base-modal id="msfaaConfirmModal" :static="true">
            <x-slot:header>
                <div class="d-flex align-items-center">
                    <i class="ki-duotone ki-verify fs-2 text-primary me-3">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i>
                    <h5 class="modal-title fw-bold mb-0">{{ __('MSFAA Confirmation Required') }}</h5>
                </div>
            </x-slot:header>

            @if($application->msfaa_rejection_reason)
                <div class="alert alert-danger d-flex align-items-center p-4 mb-4">
                    <i class="ki-duotone ki-information fs-2x text-danger me-3">
                        <span class="path1"></span>
                        <span class="path2"></span>
                        <span class="path3"></span>
                    </i>
                    <div>
                        <strong>{{ __('MSFAA Rejected:') }}</strong> {{ $application->msfaa_rejection_reason }}
                    </div>
                </div>
            @endif

            <p class="text-gray-600 fs-7 mb-4">{{ __('Please confirm your Master Student Financial Assistance Agreement (MSFAA). Do you agree to the terms of the MSFAA?') }}</p>
            <div class="d-flex gap-3">
                <form action="{{ route('student.msfaa.confirm') }}" method="POST" class="flex-grow-1" id="msfaaConfirmForm">
                    @csrf
                    <button type="submit" class="btn btn-success w-100">
                        <i class="ki-duotone ki-check-circle fs-5 me-2"><span class="path1"></span><span class="path2"></span></i>
                        {{ __('Yes, I Confirm') }}
                    </button>
                </form>
                <button type="button" id="msfaaDeclineBtn" class="btn btn-light-danger flex-grow-1">
                    <i class="ki-duotone ki-cross-circle fs-5 me-2"><span class="path1"></span><span class="path2"></span></i>
                    {{ __('No') }}
                </button>
            </div>
        </x-modals.base-modal>

        {{-- Inline MSFAA Confirmation Card (always visible when MSFAA is requested) --}}
        <div class="row g-5 g-xl-10 mb-5 mb-xl-10">
            <div class="col-xl-12">
                <div class="card border border-dashed border-gray-300">
                    <div class="card-body p-6">
                        <div class="d-flex align-items-center mb-4">
                            <div class="symbol symbol-45px me-4">
                                <span class="symbol-label bg-light-primary">
                                    <i class="ki-duotone ki-verify fs-2 text-primary">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                </span>
                            </div>
                            <div>
                                <h5 class="fw-bold text-gray-800 mb-0">{{ __('MSFAA Confirmation') }}</h5>
                                <span class="text-muted fs-7">{{ __('Confirm your MSFAA agreement') }}</span>
                            </div>
                        </div>

                        @if($application->msfaa_rejection_reason)
                            <div class="alert alert-danger d-flex align-items-center p-4 mb-4">
                                <i class="ki-duotone ki-information fs-2x text-danger me-3">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                    <span class="path3"></span>
                                </i>
                                <div>
                                    <strong>{{ __('MSFAA Rejected:') }}</strong> {{ $application->msfaa_rejection_reason }}
                                </div>
                            </div>
                        @endif

                        <p class="text-gray-600 fs-7 mb-4">{{ __('Do you agree to the terms of the MSFAA?') }}</p>
                        <form id="msfaaInlineConfirmForm" action="{{ route('student.msfaa.confirm') }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-success">
                                <i class="ki-duotone ki-check-circle fs-5 me-2"><span class="path1"></span><span class="path2"></span></i>
                                {{ __('Yes, I Confirm MSFAA') }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        @push('scripts')
            <script>
                document.addEventListener('DOMContentLoaded', function () {
                    var declineKey = 'msfaaDeclined_{{ session()->getId() }}_{{ $application->msfaa_status }}';
                    if (!sessionStorage.getItem(declineKey)) {
                        var msfaaModal = new bootstrap.Modal(document.getElementById('msfaaConfirmModal'));
                        msfaaModal.show();
                    }
                    document.getElementById('msfaaDeclineBtn').addEventListener('click', function () {
                        sessionStorage.setItem(declineKey, '1');
                        bootstrap.Modal.getInstance(document.getElementById('msfaaConfirmModal')).hide();
                    });

                    // Loading state on submit + cross-disable for both MSFAA forms
                    var msfaaModalForm = document.getElementById('msfaaConfirmForm');
                    var msfaaInlineForm = document.getElementById('msfaaInlineConfirmForm');

                    function disableMsfaaForm(form, loadingText) {
                        var btn = form.querySelector('button[type="submit"]');
                        if (btn) {
                            btn.disabled = true;
                            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>' + loadingText;
                        }
                    }

                    if (msfaaModalForm) {
                        msfaaModalForm.addEventListener('submit', function () {
                            disableMsfaaForm(msfaaModalForm, '{{ __("Confirming...") }}');
                            if (msfaaInlineForm) disableMsfaaForm(msfaaInlineForm, '{{ __("Confirming...") }}');
                        });
                    }
                    if (msfaaInlineForm) {
                        msfaaInlineForm.addEventListener('submit', function () {
                            disableMsfaaForm(msfaaInlineForm, '{{ __("Confirming...") }}');
                            if (msfaaModalForm) disableMsfaaForm(msfaaModalForm, '{{ __("Confirming...") }}');
                        });
                    }
                });
            </script>
        @endpush
    @endif

</x-default-layout>
