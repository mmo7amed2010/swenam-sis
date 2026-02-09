<x-default-layout>

    @section('title')
        {{ __('My Application') }}
    @endsection

    @section('breadcrumbs')
        {{ Breadcrumbs::render('dashboard') }}
    @endsection

    <div class="row g-5 g-xl-10 mb-5 mb-xl-10">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-header border-0 pt-5">
                    <h3 class="card-title align-items-start flex-column">
                        <span class="card-label fw-bold fs-3 mb-1">{{ __('My Application') }}</span>
                        @if($student)
                            <span class="text-muted mt-1 fw-semibold fs-7">
                                {{ __('Student Number:') }} {{ $student->student_number }}
                            </span>
                        @endif
                    </h3>
                </div>
                <div class="card-body py-5">
                    @if(session('error'))
                        <div class="alert alert-danger d-flex align-items-center p-5 mb-10">
                            <i class="ki-duotone ki-shield-cross fs-2hx text-danger me-4">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                            <div class="d-flex flex-column">
                                <h4 class="mb-1 text-danger">{{ __('Error') }}</h4>
                                <span>{{ session('error') }}</span>
                            </div>
                        </div>
                    @endif

                    @if(!empty($isBypassStudent) && empty($application))
                        {{-- Bypass Student without Application - Show Approved State --}}
                        <div class="row mb-7">
                            <div class="col-lg-6">
                                @if($program)
                                    <div class="d-flex align-items-center mb-5">
                                        <div class="symbol symbol-45px me-4">
                                            <span class="symbol-label bg-light-info">
                                                <i class="ki-duotone ki-route fs-2 text-info">
                                                    <span class="path1"></span>
                                                    <span class="path2"></span>
                                                    <span class="path3"></span>
                                                    <span class="path4"></span>
                                                </i>
                                            </span>
                                        </div>
                                        <div>
                                            <div class="fs-7 text-muted">{{ __('Program') }}</div>
                                            <div class="fw-bold text-gray-800 fs-5">{{ $program->name }}</div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                            <div class="col-lg-6">
                                <div class="d-flex align-items-center mb-5">
                                    <div class="symbol symbol-45px me-4">
                                        <span class="symbol-label bg-light-success">
                                            <i class="ki-duotone ki-check-circle fs-2 text-success">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                            </i>
                                        </span>
                                    </div>
                                    <div>
                                        <div class="fs-7 text-muted">{{ __('Status') }}</div>
                                        <div class="fw-bold fs-5">
                                            <span class="text-success">{{ __('Active') }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Access Granted Message --}}
                        <div class="notice d-flex bg-light-success rounded border-success border border-dashed p-6 mb-5">
                            <i class="ki-duotone ki-shield-tick fs-2tx text-success me-4">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                            <div class="d-flex flex-stack flex-grow-1 flex-wrap flex-md-nowrap">
                                <div class="fw-semibold">
                                    <h4 class="text-gray-900 fw-bold">{{ __('Program Access Granted!') }}</h4>
                                    <div class="fs-6 text-gray-700">
                                        {{ __('You have been granted access to your courses. Click the button to start learning.') }}
                                    </div>
                                </div>
                                <a href="{{ route('student.my-courses.redirect') }}" class="btn btn-success px-6 align-self-center text-nowrap ms-4" target="_blank">
                                    {{ __('Go to My Courses') }}
                                </a>
                            </div>
                        </div>
                    @elseif($application)
                        {{-- Application Details --}}
                        <div class="row mb-7">
                            <div class="col-lg-6">
                                <div class="d-flex align-items-center mb-5">
                                    <div class="symbol symbol-45px me-4">
                                        <span class="symbol-label bg-light-primary">
                                            <i class="ki-duotone ki-document fs-2 text-primary">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                            </i>
                                        </span>
                                    </div>
                                    <div>
                                        <div class="fs-7 text-muted">{{ __('Reference Number') }}</div>
                                        <div class="fw-bold text-gray-800 fs-5">{{ $application->reference_number }}</div>
                                    </div>
                                </div>

                                <div class="d-flex align-items-center mb-5">
                                    <div class="symbol symbol-45px me-4">
                                        <span class="symbol-label bg-light-info">
                                            <i class="ki-duotone ki-route fs-2 text-info">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                                <span class="path3"></span>
                                                <span class="path4"></span>
                                            </i>
                                        </span>
                                    </div>
                                    <div>
                                        <div class="fs-7 text-muted">{{ __('Program') }}</div>
                                        <div class="fw-bold text-gray-800 fs-5">{{ $application->program_name ?? 'N/A' }}</div>
                                    </div>
                                </div>

                                <div class="d-flex align-items-center mb-5">
                                    <div class="symbol symbol-45px me-4">
                                        <span class="symbol-label bg-light-warning">
                                            <i class="ki-duotone ki-calendar fs-2 text-warning">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                            </i>
                                        </span>
                                    </div>
                                    <div>
                                        <div class="fs-7 text-muted">{{ __('Preferred Intake') }}</div>
                                        <div class="fw-bold text-gray-800 fs-5">{{ $application->preferred_intake ?? 'N/A' }}</div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-6">
                                <div class="d-flex align-items-center mb-5">
                                    <div class="symbol symbol-45px me-4">
                                        <span class="symbol-label bg-light-secondary">
                                            <i class="ki-duotone ki-time fs-2 text-gray-600">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                            </i>
                                        </span>
                                    </div>
                                    <div>
                                        <div class="fs-7 text-muted">{{ __('Submitted On') }}</div>
                                        <div class="fw-bold text-gray-800 fs-5">{{ $application->created_at->format('F d, Y') }}</div>
                                    </div>
                                </div>

                                @php
                                    $statusDisplay = match($application->status) {
                                        'approved' => ['icon' => 'check-circle', 'bg' => 'success', 'label' => __('Approved')],
                                        'initial_approved' => ['icon' => 'verify', 'bg' => 'info', 'label' => __('Under Review')],
                                        'contract_sent' => ['icon' => 'document', 'bg' => 'primary', 'label' => __('Contract Sent')],
                                        'contract_uploaded' => ['icon' => 'document', 'bg' => 'info', 'label' => __('Contract Under Review')],
                                        'contract_approved' => ['icon' => 'verify', 'bg' => 'success', 'label' => __('Contract Approved')],
                                        'payment_pending' => ['icon' => 'wallet', 'bg' => 'warning', 'label' => __('Payment Required')],
                                        'payment_uploaded' => ['icon' => 'wallet', 'bg' => 'info', 'label' => __('Payment Under Review')],
                                        'payment_approved' => ['icon' => 'wallet', 'bg' => 'success', 'label' => __('Payment Approved')],
                                        'rejected' => ['icon' => 'cross-circle', 'bg' => 'danger', 'label' => __('Rejected')],
                                        default => ['icon' => 'timer', 'bg' => 'warning', 'label' => __('Pending Review')],
                                    };
                                @endphp
                                <div class="d-flex align-items-center mb-5">
                                    <div class="symbol symbol-45px me-4">
                                        <span class="symbol-label bg-light-{{ $statusDisplay['bg'] }}">
                                            <i class="ki-duotone ki-{{ $statusDisplay['icon'] }} fs-2 text-{{ $statusDisplay['bg'] }}">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                                <span class="path3"></span>
                                            </i>
                                        </span>
                                    </div>
                                    <div>
                                        <div class="fs-7 text-muted">{{ __('Status') }}</div>
                                        <div class="fw-bold fs-5">
                                            <span class="text-{{ $statusDisplay['bg'] }}">{{ $statusDisplay['label'] }}</span>
                                        </div>
                                    </div>
                                </div>

                                @if($application->reviewed_at)
                                    <div class="d-flex align-items-center mb-5">
                                        <div class="symbol symbol-45px me-4">
                                            <span class="symbol-label bg-light-success">
                                                <i class="ki-duotone ki-calendar-tick fs-2 text-success">
                                                    <span class="path1"></span>
                                                    <span class="path2"></span>
                                                    <span class="path3"></span>
                                                    <span class="path4"></span>
                                                    <span class="path5"></span>
                                                    <span class="path6"></span>
                                                </i>
                                            </span>
                                        </div>
                                        <div>
                                            <div class="fs-7 text-muted">{{ __('Reviewed On') }}</div>
                                            <div class="fw-bold text-gray-800 fs-5">{{ $application->reviewed_at->format('F d, Y') }}</div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>

                        {{-- Status Message --}}
                        @if($application->status === 'approved')
                            <div class="notice d-flex bg-light-success rounded border-success border border-dashed p-6 mb-5">
                                <i class="ki-duotone ki-shield-tick fs-2tx text-success me-4">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                                <div class="d-flex flex-stack flex-grow-1">
                                    <div class="fw-semibold">
                                        <h4 class="text-gray-900 fw-bold">{{ __('Application Approved!') }}</h4>
                                        <div class="fs-6 text-gray-700">
                                            {{ __('Congratulations! Your application has been approved. You can now access your courses.') }}
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- NOA Section --}}
                            @if($application->canUploadNoa())
                                {{-- NOA Rejection Reason (if re-upload after rejection) - stays inline --}}
                                @if($application->noa_rejection_reason)
                                    <div class="notice d-flex bg-light-danger rounded border-danger border border-dashed p-6 mb-5">
                                        <i class="ki-duotone ki-information fs-2tx text-danger me-4">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                            <span class="path3"></span>
                                        </i>
                                        <div class="d-flex flex-stack flex-grow-1">
                                            <div class="fw-semibold">
                                                <h4 class="text-gray-900 fw-bold">{{ __('NOA Document Returned') }}</h4>
                                                <div class="fs-6 text-gray-700">
                                                    <strong>{{ __('Reason:') }}</strong> {{ $application->noa_rejection_reason }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                {{-- NOA Upload Modal (auto-shown on page load) --}}
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
                                            <input type="file" name="noa_document" class="form-control form-control-solid @error('noa_document') is-invalid @enderror" accept=".pdf,.jpg,.jpeg,.png" required />
                                            @error('noa_document')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
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
                                <div class="card border border-dashed border-gray-300 mb-5">
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
                                                <input type="file" name="noa_document" class="form-control form-control-solid @error('noa_document') is-invalid @enderror" accept=".pdf,.jpg,.jpeg,.png" required />
                                                @error('noa_document')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <button type="submit" class="btn btn-success">
                                                <i class="ki-duotone ki-up fs-5 me-2"><span class="path1"></span><span class="path2"></span></i>
                                                {{ __('Upload NOA') }}
                                            </button>
                                        </form>
                                    </div>
                                </div>

                            @elseif($application->isNoaUploaded())
                                <div class="notice d-flex bg-light-info rounded border-info border border-dashed p-6 mb-5">
                                    <i class="ki-duotone ki-time fs-2tx text-info me-4">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                    <div class="d-flex flex-stack flex-grow-1">
                                        <div class="fw-semibold">
                                            <h4 class="text-gray-900 fw-bold">{{ __('NOA Under Review') }}</h4>
                                            <div class="fs-6 text-gray-700">
                                                {{ __('Your NOA document has been received and is being reviewed by our team.') }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @elseif($application->isNoaApproved())
                                <div class="notice d-flex bg-light-success rounded border-success border border-dashed p-6 mb-5">
                                    <i class="ki-duotone ki-check-circle fs-2tx text-success me-4">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                    <div class="d-flex flex-stack flex-grow-1">
                                        <div class="fw-semibold">
                                            <h4 class="text-gray-900 fw-bold">{{ __('NOA Approved') }}</h4>
                                            <div class="fs-6 text-gray-700">
                                                {{ __('Your Notice of Assessment has been reviewed and approved.') }}
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- MSFAA Section (only after NOA is approved) --}}
                                @if($application->canConfirmMsfaa())
                                    @if($application->msfaa_rejection_reason)
                                        <div class="notice d-flex bg-light-danger rounded border-danger border border-dashed p-6 mb-5">
                                            <i class="ki-duotone ki-information fs-2tx text-danger me-4">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                                <span class="path3"></span>
                                            </i>
                                            <div class="d-flex flex-stack flex-grow-1 flex-wrap gap-3">
                                                <div class="fw-semibold">
                                                    <h4 class="text-gray-900 fw-bold">{{ __('MSFAA Returned') }}</h4>
                                                    <div class="fs-6 text-gray-700">
                                                        <strong>{{ __('Reason:') }}</strong> {{ $application->msfaa_rejection_reason }}
                                                    </div>
                                                </div>
                                                <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#msfaaConfirmModal">
                                                    <i class="ki-duotone ki-verify fs-5 me-2"><span class="path1"></span><span class="path2"></span></i>
                                                    {{ __('Confirm MSFAA') }}
                                                </button>
                                            </div>
                                        </div>
                                    @endif

                                    {{-- MSFAA Confirmation Modal (auto-shown on page load) --}}
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
                                    <div class="card border border-dashed border-gray-300 mb-5">
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

                                @elseif($application->isMsfaaConfirmed())
                                    <div class="notice d-flex bg-light-info rounded border-info border border-dashed p-6 mb-5">
                                        <i class="ki-duotone ki-time fs-2tx text-info me-4">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                        <div class="d-flex flex-stack flex-grow-1">
                                            <div class="fw-semibold">
                                                <h4 class="text-gray-900 fw-bold">{{ __('MSFAA Under Review') }}</h4>
                                                <div class="fs-6 text-gray-700">
                                                    {{ __('Your MSFAA confirmation has been received and is being reviewed by our team.') }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @elseif($application->isMsfaaApproved())
                                    <div class="notice d-flex bg-light-success rounded border-success border border-dashed p-6 mb-5">
                                        <i class="ki-duotone ki-check-circle fs-2tx text-success me-4">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                        <div class="d-flex flex-stack flex-grow-1">
                                            <div class="fw-semibold">
                                                <h4 class="text-gray-900 fw-bold">{{ __('MSFAA Approved') }}</h4>
                                                <div class="fs-6 text-gray-700">
                                                    {{ __('Your Master Student Financial Assistance Agreement has been reviewed and approved.') }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            @endif
                        @elseif($application->status === 'pending' || $application->status === 'initial_approved')
                            <div class="notice d-flex bg-light-warning rounded border-warning border border-dashed p-6 mb-5">
                                <i class="ki-duotone ki-information fs-2tx text-warning me-4">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                    <span class="path3"></span>
                                </i>
                                <div class="d-flex flex-stack flex-grow-1">
                                    <div class="fw-semibold">
                                        <h4 class="text-gray-900 fw-bold">{{ __('Application Under Review') }}</h4>
                                        <div class="fs-6 text-gray-700">
                                            {{ __('Your application is being reviewed by our admissions team. You will receive an email once a decision has been made.') }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @elseif($application->status === 'contract_sent')
                            {{-- Contract Rejection Reason (if re-upload after rejection) --}}
                            @if($application->contract_rejection_reason)
                                <div class="notice d-flex bg-light-danger rounded border-danger border border-dashed p-6 mb-5">
                                    <i class="ki-duotone ki-information fs-2tx text-danger me-4">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                        <span class="path3"></span>
                                    </i>
                                    <div class="d-flex flex-stack flex-grow-1">
                                        <div class="fw-semibold">
                                            <h4 class="text-gray-900 fw-bold">{{ __('Contract Returned for Revision') }}</h4>
                                            <div class="fs-6 text-gray-700">
                                                <strong>{{ __('Reason:') }}</strong> {{ $application->contract_rejection_reason }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            {{-- Contract Sent: Download + Upload signed contract --}}
                            <div class="notice d-flex bg-light-primary rounded border-primary border border-dashed p-6 mb-5">
                                <i class="ki-duotone ki-document fs-2tx text-primary me-4">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                                <div class="d-flex flex-stack flex-grow-1">
                                    <div class="fw-semibold">
                                        <h4 class="text-gray-900 fw-bold">{{ __('Contract Ready for Signing') }}</h4>
                                        <div class="fs-6 text-gray-700">
                                            {{ __('Your enrollment contract is ready. Please download, sign, and upload it below.') }}
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="card border border-dashed border-gray-300 mb-5">
                                <div class="card-body p-6">
                                    <div class="d-flex flex-column flex-md-row gap-5">
                                        {{-- Download Contract --}}
                                        <div class="flex-grow-1">
                                            <h5 class="fw-bold text-gray-800 mb-3">{{ __('Step 1: Download Contract') }}</h5>
                                            <p class="text-gray-600 fs-7 mb-4">{{ __('Download and review your enrollment contract.') }}</p>
                                            <a href="{{ route('student.contract.download') }}" class="btn btn-primary">
                                                <i class="ki-duotone ki-down fs-5 me-2"><span class="path1"></span><span class="path2"></span></i>
                                                {{ __('Download Contract') }}
                                            </a>
                                        </div>

                                        <div class="separator separator-dashed d-md-none"></div>
                                        <div class="vr d-none d-md-block"></div>

                                        {{-- Upload Signed Contract --}}
                                        <div class="flex-grow-1">
                                            <h5 class="fw-bold text-gray-800 mb-3">{{ __('Step 2: Upload Signed Contract') }}</h5>
                                            <p class="text-gray-600 fs-7 mb-4">{{ __('Upload your signed contract (PDF only, max 10MB).') }}</p>
                                            <form action="{{ route('student.contract.upload-signed') }}" method="POST" enctype="multipart/form-data">
                                                @csrf
                                                <div class="mb-3">
                                                    <input type="file" name="signed_contract" class="form-control form-control-solid @error('signed_contract') is-invalid @enderror" accept=".pdf" required />
                                                    @error('signed_contract')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <button type="submit" class="btn btn-success">
                                                    <i class="ki-duotone ki-up fs-5 me-2"><span class="path1"></span><span class="path2"></span></i>
                                                    {{ __('Upload Signed Contract') }}
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @elseif($application->status === 'contract_uploaded')
                            <div class="notice d-flex bg-light-info rounded border-info border border-dashed p-6 mb-5">
                                <i class="ki-duotone ki-time fs-2tx text-info me-4">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                                <div class="d-flex flex-stack flex-grow-1">
                                    <div class="fw-semibold">
                                        <h4 class="text-gray-900 fw-bold">{{ __('Contract Under Review') }}</h4>
                                        <div class="fs-6 text-gray-700">
                                            {{ __('Your signed contract has been received and is being reviewed by our team. You will be notified once it has been approved.') }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @elseif($application->status === 'contract_approved' || $application->status === 'payment_pending')
                            {{-- Payment Rejection Reason (if re-upload after rejection) --}}
                            @if($application->payment_rejection_reason)
                                <div class="notice d-flex bg-light-danger rounded border-danger border border-dashed p-6 mb-5">
                                    <i class="ki-duotone ki-information fs-2tx text-danger me-4">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                        <span class="path3"></span>
                                    </i>
                                    <div class="d-flex flex-stack flex-grow-1">
                                        <div class="fw-semibold">
                                            <h4 class="text-gray-900 fw-bold">{{ __('Payment Receipt Returned') }}</h4>
                                            <div class="fs-6 text-gray-700">
                                                <strong>{{ __('Reason:') }}</strong> {{ $application->payment_rejection_reason }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            {{-- Payment Pending: Upload payment receipt --}}
                            <div class="notice d-flex bg-light-warning rounded border-warning border border-dashed p-6 mb-5">
                                <i class="ki-duotone ki-wallet fs-2tx text-warning me-4">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                                <div class="d-flex flex-stack flex-grow-1">
                                    <div class="fw-semibold">
                                        <h4 class="text-gray-900 fw-bold">{{ __('Payment Required') }}</h4>
                                        <div class="fs-6 text-gray-700">
                                            {{ __('Your contract has been approved. Please submit your payment receipt to complete enrollment.') }}
                                        </div>
                                        @if($application->payment_amount)
                                            <div class="mt-3 fs-5">
                                                <strong>{{ __('Amount Due:') }}</strong>
                                                <span class="text-danger fw-bolder fs-3">${{ number_format($application->payment_amount, 2) }}</span>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="card border border-dashed border-gray-300 mb-5">
                                <div class="card-body p-6">
                                    @if($application->payment_amount)
                                        <div class="d-flex align-items-center bg-light-danger rounded p-4 mb-5">
                                            <i class="ki-duotone ki-wallet fs-2tx text-danger me-4">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                            </i>
                                            <div>
                                                <div class="text-gray-600 fs-7 fw-semibold">{{ __('Amount to Pay') }}</div>
                                                <div class="fw-bolder text-gray-900 fs-1">${{ number_format($application->payment_amount, 2) }}</div>
                                            </div>
                                        </div>
                                    @endif
                                    <h5 class="fw-bold text-gray-800 mb-3">{{ __('Upload Payment Receipt') }}</h5>
                                    <p class="text-gray-600 fs-7 mb-4">{{ __('Upload your payment receipt (PDF, JPG, or PNG, max 10MB).') }}</p>
                                    <form action="{{ route('student.payment.upload-receipt') }}" method="POST" enctype="multipart/form-data">
                                        @csrf
                                        <div class="mb-3">
                                            <input type="file" name="payment_receipt" class="form-control form-control-solid @error('payment_receipt') is-invalid @enderror" accept=".pdf,.jpg,.jpeg,.png" required />
                                            @error('payment_receipt')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <button type="submit" class="btn btn-success">
                                            <i class="ki-duotone ki-up fs-5 me-2"><span class="path1"></span><span class="path2"></span></i>
                                            {{ __('Upload Payment Receipt') }}
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @elseif($application->status === 'payment_uploaded')
                            <div class="notice d-flex bg-light-info rounded border-info border border-dashed p-6 mb-5">
                                <i class="ki-duotone ki-time fs-2tx text-info me-4">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                                <div class="d-flex flex-stack flex-grow-1">
                                    <div class="fw-semibold">
                                        <h4 class="text-gray-900 fw-bold">{{ __('Payment Under Review') }}</h4>
                                        <div class="fs-6 text-gray-700">
                                            {{ __('Your payment receipt has been received and is being reviewed. You will be notified once it has been approved.') }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @elseif($application->status === 'payment_approved')
                            <div class="notice d-flex bg-light-success rounded border-success border border-dashed p-6 mb-5">
                                <i class="ki-duotone ki-check-circle fs-2tx text-success me-4">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                                <div class="d-flex flex-stack flex-grow-1">
                                    <div class="fw-semibold">
                                        <h4 class="text-gray-900 fw-bold">{{ __('Payment Approved!') }}</h4>
                                        <div class="fs-6 text-gray-700">
                                            {{ __('Your payment has been approved. Your enrollment is being finalized.') }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @elseif($application->status === 'rejected')
                            <div class="notice d-flex bg-light-danger rounded border-danger border border-dashed p-6 mb-5">
                                <i class="ki-duotone ki-information fs-2tx text-danger me-4">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                    <span class="path3"></span>
                                </i>
                                <div class="d-flex flex-stack flex-grow-1">
                                    <div class="fw-semibold">
                                        <h4 class="text-gray-900 fw-bold">{{ __('Application Not Approved') }}</h4>
                                        <div class="fs-6 text-gray-700">
                                            {{ __('Unfortunately, your application was not approved. Please contact support for more information.') }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        {{-- Success/Error Messages --}}
                        @if(session('success'))
                            <div class="alert alert-success d-flex align-items-center p-5 mb-5">
                                <i class="ki-duotone ki-check-circle fs-2hx text-success me-4">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                                <div>{{ session('success') }}</div>
                            </div>
                        @endif
                    @else
                        <div class="text-center py-10">
                            <i class="ki-duotone ki-document fs-5tx text-gray-300 mb-5">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                            <h3 class="text-gray-600">{{ __('No Application Found') }}</h3>
                            <p class="text-muted">{{ __('We could not find an application associated with your account.') }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @if(isset($application) && $application && $application->canUploadNoa())
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

    @if(isset($application) && $application && $application->canConfirmMsfaa())
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
