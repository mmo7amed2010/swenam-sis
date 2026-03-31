<x-default-layout>
    @section('title')
        Application Details - {{ $application->reference_number }}
    @endsection

    @section('breadcrumbs')
        {{ Breadcrumbs::render('admin.applications.show', $application) }}
    @endsection

    @php
        $statusConfig = [
            'pending' => ['badge' => 'warning', 'icon' => 'time', 'label' => 'Pending Review'],
            'initial_approved' => ['badge' => 'info', 'icon' => 'shield-tick', 'label' => 'Initial Approved'],
            'contract_sent' => ['badge' => 'primary', 'icon' => 'send', 'label' => 'Contract Sent'],
            'contract_uploaded' => ['badge' => 'info', 'icon' => 'document', 'label' => 'Contract Uploaded'],
            'contract_approved' => ['badge' => 'success', 'icon' => 'verify', 'label' => 'Contract Approved'],
            'payment_pending' => ['badge' => 'warning', 'icon' => 'wallet', 'label' => 'Payment Pending'],
            'payment_uploaded' => ['badge' => 'info', 'icon' => 'wallet', 'label' => 'Payment Uploaded'],
            'payment_approved' => ['badge' => 'success', 'icon' => 'wallet', 'label' => 'Payment Approved'],
            'approved' => ['badge' => 'success', 'icon' => 'check-circle', 'label' => 'Approved'],
            'rejected' => ['badge' => 'danger', 'icon' => 'cross-circle', 'label' => 'Rejected'],
        ];
        $config = $statusConfig[$application->status] ?? $statusConfig['pending'];

        $noaStatusConfig = [
            'requested' => ['badge' => 'warning', 'label' => 'Requested'],
            'uploaded'  => ['badge' => 'info', 'label' => 'Uploaded'],
            'approved'  => ['badge' => 'success', 'label' => 'Approved'],
            'rejected'  => ['badge' => 'danger', 'label' => 'Rejected'],
        ];
        $noaConfig = $noaStatusConfig[$application->noa_status] ?? null;

        $msfaaStatusConfig = [
            'requested'  => ['badge' => 'warning', 'label' => 'Requested'],
            'confirmed'  => ['badge' => 'info', 'label' => 'Confirmed'],
            'approved'   => ['badge' => 'success', 'label' => 'Approved'],
            'rejected'   => ['badge' => 'danger', 'label' => 'Rejected'],
        ];
        $msfaaConfig = $msfaaStatusConfig[$application->msfaa_status] ?? null;

        $docCount = collect([
            $application->government_id_path,
            $application->degree_certificate_path,
            $application->transcripts_path,
            $application->cv_path,
            $application->english_test_path
        ])->filter()->count();

        $headerPills = [
            'Reference' => $application->reference_number,
            'Program' => $application->program_name ?? 'N/A',
            'Submitted' => $application->created_at->format('M d, Y'),
        ];
        if ($application->initial_approved_at) {
            $headerPills['Initial Approved'] = $application->initial_approved_at->format('M d, Y');
        }
        if ($application->reviewed_at) {
            $headerPills['Final Reviewed'] = $application->reviewed_at->format('M d, Y');
        }
    @endphp

    {{-- Profile Header --}}
    <x-profile.header
        :name="$application->full_name"
        :initials="strtoupper(substr($application->first_name, 0, 1) . substr($application->last_name, 0, 1))"
        :email="$application->email"
        :phone="$application->phone"
        :status="$application->status"
        :statusLabel="$config['label']"
        :pills="$headerPills"
    >
        <x-slot name="actions">
            <a href="{{ route('admin.applications.export-pdf', $application) }}"
               class="btn btn-sm btn-light-primary d-flex align-items-center gap-2">
                {!! getIcon('document', 'fs-5') !!}
                {{ __('Export as PDF') }}
            </a>
        </x-slot>
    </x-profile.header>

    <div class="row g-6">
        {{-- Main Content --}}
        <div class="col-xl-8">
            {{-- Tabs Navigation --}}
            <ul class="nav nav-tabs nav-line-tabs nav-line-tabs-2x mb-6 fs-6" role="tablist">
                <li class="nav-item" role="presentation">
                    <a class="nav-link active d-flex align-items-center gap-2" data-bs-toggle="tab" href="#tab_details" role="tab">
                        {!! getIcon('user', 'fs-5') !!}
                        Personal Details
                    </a>
                </li>
                <li class="nav-item" role="presentation">
                    <a class="nav-link d-flex align-items-center gap-2" data-bs-toggle="tab" href="#tab_education" role="tab">
                        {!! getIcon('teacher', 'fs-5') !!}
                        Education & Work
                    </a>
                </li>
                <li class="nav-item" role="presentation">
                    <a class="nav-link d-flex align-items-center gap-2" data-bs-toggle="tab" href="#tab_documents" role="tab">
                        {!! getIcon('document', 'fs-5') !!}
                        Documents
                        <span class="badge badge-sm badge-circle badge-secondary">{{ $docCount }}</span>
                    </a>
                </li>
                @if($application->latestContract)
                    <li class="nav-item" role="presentation">
                        <a class="nav-link d-flex align-items-center gap-2" data-bs-toggle="tab" href="#tab_contract" role="tab">
                            {!! getIcon('document', 'fs-5') !!}
                            Contract & Payment
                        </a>
                    </li>
                @endif
                @if($application->reviewed_at || $application->initial_approved_at)
                    <li class="nav-item" role="presentation">
                        <a class="nav-link d-flex align-items-center gap-2" data-bs-toggle="tab" href="#tab_review" role="tab">
                            {!! getIcon('shield-tick', 'fs-5') !!}
                            Review Details
                        </a>
                    </li>
                @endif
                @if($application->noa_status)
                    <li class="nav-item" role="presentation">
                        <a class="nav-link d-flex align-items-center gap-2" data-bs-toggle="tab" href="#tab_noa" role="tab">
                            {!! getIcon('document', 'fs-5') !!}
                            NOA
                            <span class="badge badge-sm badge-light-{{ $noaConfig['badge'] }}">{{ $noaConfig['label'] }}</span>
                        </a>
                    </li>
                @endif
                @if($application->msfaa_status)
                    <li class="nav-item" role="presentation">
                        <a class="nav-link d-flex align-items-center gap-2" data-bs-toggle="tab" href="#tab_msfaa" role="tab">
                            {!! getIcon('verify', 'fs-5') !!}
                            MSFAA
                            <span class="badge badge-sm badge-light-{{ $msfaaConfig['badge'] }}">{{ $msfaaConfig['label'] }}</span>
                        </a>
                    </li>
                @endif
            </ul>

            {{-- Tab Content --}}
            <div class="tab-content">
                {{-- Personal Details Tab --}}
                <div class="tab-pane fade show active" id="tab_details" role="tabpanel">
                    {{-- Agent Information (if submitted by agent) --}}
                    @if($application->agent_id && $application->agent)
                        <div class="notice d-flex bg-light-info rounded border-info border border-dashed p-6 mb-5">
                            <i class="ki-outline ki-people fs-2tx text-info me-4"></i>
                            <div class="d-flex flex-stack flex-grow-1">
                                <div class="fw-semibold">
                                    <h6 class="text-gray-900 fw-bold mb-1">{{ __('Submitted by Agent') }}</h6>
                                    <div class="fs-6 text-gray-700">
                                        {{ $application->agent->name ?? ($application->agent->first_name . ' ' . $application->agent->last_name) }}
                                        <span class="text-muted">({{ $application->agent->email }})</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- Program Information --}}
                    <x-cards.section title="Program Information" class="mb-5">
                        <div class="row g-4">
                            <div class="col-md-4">
                                <div class="position-relative">
                                    <x-detail.info-card icon="abstract-26" label="Program" :value="$application->program_name ?? 'N/A'" color="primary" />
                                    @if(!$application->isRejected())
                                        <button type="button"
                                                class="btn btn-icon btn-sm btn-light-primary position-absolute"
                                                style="top: 8px; right: 8px;"
                                                data-bs-toggle="modal"
                                                data-bs-target="#changeProgramModal"
                                                title="Change Program">
                                            {!! getIcon('pencil', 'fs-6') !!}
                                        </button>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="position-relative">
                                    <x-detail.info-card icon="calendar" label="Preferred Intake" :value="$application->intake_name ?? $application->preferred_intake ?? 'N/A'" color="primary" />
                                    @if(!$application->isRejected())
                                        <button type="button"
                                                class="btn btn-icon btn-sm btn-light-primary position-absolute"
                                                style="top: 8px; right: 8px;"
                                                data-bs-toggle="modal"
                                                data-bs-target="#changeIntakeModal"
                                                title="Change Intake">
                                            {!! getIcon('pencil', 'fs-6') !!}
                                        </button>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="position-relative">
                                    <x-detail.info-card icon="people" label="Agency Referral" color="primary">
                                        @if($application->has_referral)
                                            {{ $application->referral_agency_name }}
                                        @else
                                            <span class="text-gray-500">None</span>
                                        @endif
                                    </x-detail.info-card>
                                    @if(!$application->isRejected())
                                        <button type="button"
                                                class="btn btn-icon btn-sm btn-light-primary position-absolute"
                                                style="top: 8px; right: 8px;"
                                                data-bs-toggle="modal"
                                                data-bs-target="#changeAgencyModal"
                                                title="Change Agency Referral">
                                            {!! getIcon('pencil', 'fs-6') !!}
                                        </button>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="position-relative">
                                    <x-detail.info-card icon="wallet" label="Funding Type" color="primary">
                                        @if($application->isSelfFunded())
                                            <span class="badge badge-light-warning">Self-Funded</span>
                                        @elseif($application->isGovernmentFunded())
                                            <span class="badge badge-light-success">Government-Funded</span>
                                        @else
                                            <span class="text-gray-500">N/A</span>
                                        @endif
                                    </x-detail.info-card>
                                    @if(!$application->isRejected())
                                        <button type="button"
                                                class="btn btn-icon btn-sm btn-light-primary position-absolute"
                                                style="top: 8px; right: 8px;"
                                                data-bs-toggle="modal"
                                                data-bs-target="#changeFundingModal"
                                                title="Change Funding Type">
                                            {!! getIcon('pencil', 'fs-6') !!}
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </x-cards.section>

                    {{-- Personal Information --}}
                    <x-cards.section title="Personal Information" class="mb-5">
                        <div class="row g-4">
                            <div class="col-md-6">
                                <x-detail.field icon="user" label="Full Name" :value="$application->full_name" color="info" />
                            </div>
                            <div class="col-md-6">
                                <x-detail.field icon="sms" label="Email Address" :value="$application->email" :href="'mailto:' . $application->email" color="info" />
                            </div>
                            <div class="col-md-6">
                                <x-detail.field icon="phone" label="Phone Number" :value="$application->phone" color="info" />
                            </div>
                            <div class="col-md-6">
                                <x-detail.field icon="calendar" label="Date of Birth" :value="\Carbon\Carbon::parse($application->date_of_birth)->format('F d, Y')" color="info" />
                            </div>
                            <div class="col-md-6">
                                <x-detail.field icon="flag" label="Citizenship" :value="$application->country_of_citizenship" color="info" />
                            </div>
                            <div class="col-md-6">
                                <x-detail.field icon="home" label="Residency Status" :value="$application->residency_status" color="info" />
                            </div>
                            <div class="col-md-6">
                                <x-detail.field icon="message-text-2" label="Primary Language" :value="$application->primary_language" color="info" />
                            </div>
                            <div class="col-md-6">
                                <x-detail.field icon="geolocation" label="Address" color="info">
                                    {{ $application->address_line1 }}
                                    @if($application->address_line2)<br>{{ $application->address_line2 }}@endif
                                    <br>{{ $application->city }}, {{ $application->state_province }} {{ $application->postal_code }}
                                    <br>{{ $application->country }}
                                </x-detail.field>
                            </div>
                        </div>
                    </x-cards.section>
                </div>

                {{-- Education & Work Tab --}}
                <div class="tab-pane fade" id="tab_education" role="tabpanel">
                    <x-cards.section title="Education History" class="mb-5">
                        @if(!$application->isRejected())
                            <x-slot:toolbar>
                                <button type="button"
                                        class="btn btn-icon btn-sm btn-light-primary"
                                        data-bs-toggle="modal"
                                        data-bs-target="#changeEducationModal"
                                        title="Edit Education Details">
                                    {!! getIcon('pencil', 'fs-6') !!}
                                </button>
                            </x-slot:toolbar>
                        @endif
                        <div class="card border border-dashed border-gray-300">
                            <div class="card-body p-5">
                                <div class="d-flex justify-content-between align-items-start mb-4">
                                    <div>
                                        <h4 class="fs-5 fw-bold text-gray-800 mb-1">{{ $application->highest_education_level }}</h4>
                                        <div class="text-gray-600 fs-7">{{ $application->education_field }}</div>
                                    </div>
                                    <span class="badge badge-light-success fs-8">{{ ucfirst(str_replace('_', ' ', $application->education_completed)) }}</span>
                                </div>

                                <div class="separator separator-dashed mb-4"></div>

                                <div class="row g-4">
                                    <div class="col-md-6">
                                        <x-detail.field icon="bank" label="Institution" :value="$application->institution_name" color="success" />
                                    </div>
                                    <div class="col-md-6">
                                        <x-detail.field icon="geolocation" label="Country" :value="$application->education_country" color="success" />
                                    </div>
                                </div>

                                @if($application->has_disciplinary_action)
                                    <div class="notice d-flex bg-light-danger rounded border-danger border border-dashed p-4 mt-4">
                                        {!! getIcon('information-5', 'fs-2x text-danger me-3') !!}
                                        <div class="text-danger fw-semibold fs-7">
                                            Applicant has reported prior disciplinary action
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </x-cards.section>

                    <x-cards.section title="Work Experience">
                        @if(!$application->isRejected() && $application->has_work_experience)
                            <x-slot:toolbar>
                                <button type="button"
                                        class="btn btn-icon btn-sm btn-light-primary"
                                        data-bs-toggle="modal"
                                        data-bs-target="#changeWorkModal"
                                        title="Edit Work Details">
                                    {!! getIcon('pencil', 'fs-6') !!}
                                </button>
                            </x-slot:toolbar>
                        @endif
                        @if($application->has_work_experience)
                            <div class="card border border-dashed border-gray-300">
                                <div class="card-body p-5">
                                    <div class="d-flex justify-content-between align-items-start mb-4">
                                        <div>
                                            <h4 class="fs-5 fw-bold text-gray-800 mb-1">{{ $application->position_title }}</h4>
                                            <div class="text-gray-600 fs-7">{{ $application->organization_name }}</div>
                                        </div>
                                        <span class="badge badge-light-warning fs-8">{{ $application->position_level }}</span>
                                    </div>

                                    <div class="separator separator-dashed mb-4"></div>

                                    <div class="row g-4">
                                        <div class="col-md-4">
                                            <x-detail.field icon="calendar" label="Start Date" :value="$application->work_start_date ? \Carbon\Carbon::parse($application->work_start_date)->format('M Y') : 'N/A'" color="warning" />
                                        </div>
                                        <div class="col-md-4">
                                            <x-detail.field icon="calendar-tick" label="End Date" :value="$application->work_end_date ? \Carbon\Carbon::parse($application->work_end_date)->format('M Y') : 'Present'" color="warning" />
                                        </div>
                                        <div class="col-md-4">
                                            <x-detail.field icon="timer" label="Experience" :value="$application->years_of_experience . ' years'" color="warning" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @else
                            <x-tables.empty-state
                                icon="briefcase"
                                title="No Work Experience"
                                message="The applicant has not provided work experience"
                                size="sm"
                            />
                        @endif
                    </x-cards.section>
                </div>

                {{-- Documents Tab --}}
                <div class="tab-pane fade" id="tab_documents" role="tabpanel">
                    <x-cards.section title="Uploaded Documents">
                        @if(!$application->isRejected())
                            <x-slot:toolbar>
                                <button type="button"
                                        class="btn btn-icon btn-sm btn-light-primary"
                                        data-bs-toggle="modal"
                                        data-bs-target="#changeDocumentsModal"
                                        title="Upload/Replace Documents">
                                    {!! getIcon('pencil', 'fs-6') !!}
                                </button>
                            </x-slot:toolbar>
                        @endif
                        @php
                            $documents = [
                                ['key' => 'government_id', 'path' => $application->government_id_path, 'label' => 'Government-Issued Photo ID', 'icon' => 'verify', 'desc' => 'Passport, official national ID'],
                                ['key' => 'degree_certificate', 'path' => $application->degree_certificate_path, 'label' => 'Degree Certificate', 'icon' => 'award', 'desc' => 'Academic degree or diploma'],
                                ['key' => 'transcripts', 'path' => $application->transcripts_path, 'label' => 'Academic Transcripts', 'icon' => 'document', 'desc' => 'Official academic records'],
                                ['key' => 'cv', 'path' => $application->cv_path, 'label' => 'Curriculum Vitae', 'icon' => 'profile-user', 'desc' => 'Resume or CV document'],
                                ['key' => 'english_test', 'path' => $application->english_test_path, 'label' => 'English Test Results', 'icon' => 'message-text-2', 'desc' => 'IELTS, TOEFL, or equivalent'],
                            ];
                            $uploadedDocs = collect($documents)->filter(fn($d) => $d['path']);
                        @endphp

                        @if($uploadedDocs->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-row-bordered table-row-gray-100 align-middle gs-0 gy-4 mb-0">
                                    <tbody>
                                        @foreach($documents as $doc)
                                            @if($doc['path'])
                                                <tr>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <div class="symbol symbol-45px me-4">
                                                                <span class="symbol-label bg-light-secondary">
                                                                    {!! getIcon($doc['icon'], 'fs-3 text-secondary') !!}
                                                                </span>
                                                            </div>
                                                            <div>
                                                                <div class="text-gray-800 fw-bold fs-6">{{ $doc['label'] }}</div>
                                                                <div class="text-gray-500 fs-7">{{ $doc['desc'] }}</div>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td class="text-end">
                                                        <a href="{{ route('admin.applications.download', [$application, $doc['key']]) }}"
                                                           class="btn btn-sm btn-light-primary me-2">
                                                            {!! getIcon('down', 'fs-5 me-1') !!}
                                                            Download
                                                        </a>
                                                        <button type="button"
                                                                class="btn btn-sm btn-light-info"
                                                                onclick="previewDocument('{{ route('admin.applications.download', [$application, $doc['key']]) }}?preview=1', '{{ $doc['label'] }}', '{{ strtolower(pathinfo($doc['path'], PATHINFO_EXTENSION)) }}')">
                                                            {!! getIcon('eye', 'fs-5 me-1') !!}
                                                            Preview
                                                        </button>
                                                    </td>
                                                </tr>
                                            @endif
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <x-tables.empty-state
                                icon="document"
                                title="No Documents Uploaded"
                                message="The applicant has not uploaded any documents"
                                size="sm"
                            />
                        @endif
                    </x-cards.section>
                </div>

                {{-- Contract & Payment Tab --}}
                @include('pages.admin.applications._partials.tab-contract')

                {{-- Review Details Tab --}}
                @if($application->reviewed_at || $application->initial_approved_at)
                    <div class="tab-pane fade" id="tab_review" role="tabpanel">
                        @if($application->initial_approved_at)
                            <x-cards.section title="Initial Approval" class="mb-5">
                                <div class="row g-4">
                                    <div class="col-md-6">
                                        <x-detail.field icon="profile-user" label="Initially Approved By" :value="$application->initialApprover->name ?? 'N/A'" color="info" />
                                    </div>
                                    <div class="col-md-6">
                                        <x-detail.field icon="calendar" label="Initial Approval Date" :value="$application->initial_approved_at->format('F d, Y \a\t h:i A')" color="info" />
                                    </div>
                                </div>
                            </x-cards.section>
                        @endif

                        @if($application->reviewed_at)
                            <x-cards.section title="Final Review">
                                <div class="row g-4">
                                    <div class="col-md-6">
                                        <x-detail.field icon="profile-user" label="Final Reviewed By" :value="$application->reviewer->name ?? 'N/A'" :color="$application->isApproved() ? 'success' : 'danger'" />
                                    </div>
                                    <div class="col-md-6">
                                        <x-detail.field icon="calendar" label="Final Review Date" :value="$application->reviewed_at->format('F d, Y \a\t h:i A')" :color="$application->isApproved() ? 'success' : 'danger'" />
                                    </div>

                                    @if($application->admin_notes)
                                        <div class="col-12">
                                            <div class="separator separator-dashed mb-4"></div>
                                            <label class="text-gray-500 fs-8 text-uppercase fw-semibold mb-3 d-block">Admin Notes</label>
                                            <div class="bg-light-info rounded p-4">
                                                <p class="text-gray-800 mb-0">{{ $application->admin_notes }}</p>
                                            </div>
                                        </div>
                                    @endif

                                    @if($application->rejection_reason)
                                        <div class="col-12">
                                            <div class="separator separator-dashed mb-4"></div>
                                            <label class="text-gray-500 fs-8 text-uppercase fw-semibold mb-3 d-block">Rejection Reason</label>
                                            <div class="bg-light-danger rounded p-4">
                                                <p class="text-danger mb-0">{{ $application->rejection_reason }}</p>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </x-cards.section>
                        @endif
                    </div>
                @endif

                {{-- NOA Tab --}}
                @if($application->noa_status)
                    <div class="tab-pane fade" id="tab_noa" role="tabpanel">
                        <x-cards.section title="NOA Details" class="mb-5">
                            <div class="row g-4">
                                <div class="col-md-6">
                                    <x-detail.field icon="verify" label="NOA Status" color="{{ $noaConfig['badge'] }}">
                                        <span class="badge badge-light-{{ $noaConfig['badge'] }}">{{ $noaConfig['label'] }}</span>
                                    </x-detail.field>
                                </div>

                                @if($application->noa_requested_at)
                                    <div class="col-md-6">
                                        <x-detail.field icon="profile-user" label="Requested By" :value="$application->noaRequester->name ?? 'N/A'" color="warning" />
                                    </div>
                                    <div class="col-md-6">
                                        <x-detail.field icon="calendar" label="Requested At" :value="$application->noa_requested_at->format('F d, Y \a\t h:i A')" color="warning" />
                                    </div>
                                    <div class="col-md-6">
                                        <x-detail.field icon="time" label="Days Elapsed" :value="($application->noa_elapsed_days ?? 0) . ' days'" color="warning" />
                                    </div>
                                @endif

                                @if($application->noa_uploaded_at)
                                    <div class="col-md-6">
                                        <x-detail.field icon="calendar" label="Uploaded At" :value="$application->noa_uploaded_at->format('F d, Y \a\t h:i A')" color="info" />
                                    </div>
                                @endif

                                @if($application->noa_approved_at)
                                    <div class="col-md-6">
                                        <x-detail.field icon="profile-user" label="Approved By" :value="$application->noaApprover->name ?? 'N/A'" color="success" />
                                    </div>
                                    <div class="col-md-6">
                                        <x-detail.field icon="calendar" label="Approved At" :value="$application->noa_approved_at->format('F d, Y \a\t h:i A')" color="success" />
                                    </div>
                                @endif

                                @if($application->noa_rejection_reason)
                                    <div class="col-12">
                                        <div class="separator separator-dashed mb-4"></div>
                                        <label class="text-gray-500 fs-8 text-uppercase fw-semibold mb-3 d-block">Rejection Reason</label>
                                        <div class="bg-light-danger rounded p-4">
                                            <p class="text-danger mb-0">{{ $application->noa_rejection_reason }}</p>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </x-cards.section>

                        @if($application->noa_document_path)
                            <x-cards.section title="NOA Document">
                                <div class="d-flex gap-3">
                                    <a href="{{ route('admin.applications.noa.download', $application) }}" class="btn btn-sm btn-light-primary">
                                        {!! getIcon('down', 'fs-5 me-1') !!}
                                        Download NOA
                                    </a>
                                    <button type="button"
                                            class="btn btn-sm btn-light-info"
                                            onclick="previewDocument('{{ route('admin.applications.noa.download', $application) }}?preview=1', 'NOA Document', '{{ strtolower(pathinfo($application->noa_document_path, PATHINFO_EXTENSION)) }}')">
                                        {!! getIcon('eye', 'fs-5 me-1') !!}
                                        Preview
                                    </button>
                                </div>
                            </x-cards.section>
                        @endif
                    </div>
                @endif

                {{-- MSFAA Tab --}}
                @if($application->msfaa_status)
                    <div class="tab-pane fade" id="tab_msfaa" role="tabpanel">
                        <x-cards.section title="MSFAA Details" class="mb-5">
                            <div class="row g-4">
                                <div class="col-md-6">
                                    <x-detail.field icon="verify" label="MSFAA Status" color="{{ $msfaaConfig['badge'] }}">
                                        <span class="badge badge-light-{{ $msfaaConfig['badge'] }}">{{ $msfaaConfig['label'] }}</span>
                                    </x-detail.field>
                                </div>

                                @if($application->msfaa_requested_at)
                                    <div class="col-md-6">
                                        <x-detail.field icon="profile-user" label="Requested By" :value="$application->msfaaRequester->name ?? 'N/A'" color="warning" />
                                    </div>
                                    <div class="col-md-6">
                                        <x-detail.field icon="calendar" label="Requested At" :value="$application->msfaa_requested_at->format('F d, Y \a\t h:i A')" color="warning" />
                                    </div>
                                    <div class="col-md-6">
                                        <x-detail.field icon="time" label="Days Elapsed" :value="($application->msfaa_elapsed_days ?? 0) . ' days'" color="warning" />
                                    </div>
                                @endif

                                @if($application->msfaa_confirmed_at)
                                    <div class="col-md-6">
                                        <x-detail.field icon="calendar" label="Confirmed At" :value="$application->msfaa_confirmed_at->format('F d, Y \a\t h:i A')" color="info" />
                                    </div>
                                @endif

                                @if($application->msfaa_approved_at)
                                    <div class="col-md-6">
                                        <x-detail.field icon="profile-user" label="Approved By" :value="$application->msfaaApprover->name ?? 'N/A'" color="success" />
                                    </div>
                                    <div class="col-md-6">
                                        <x-detail.field icon="calendar" label="Approved At" :value="$application->msfaa_approved_at->format('F d, Y \a\t h:i A')" color="success" />
                                    </div>
                                @endif

                                @if($application->msfaa_rejection_reason)
                                    <div class="col-12">
                                        <div class="separator separator-dashed mb-4"></div>
                                        <label class="text-gray-500 fs-8 text-uppercase fw-semibold mb-3 d-block">Rejection Reason</label>
                                        <div class="bg-light-danger rounded p-4">
                                            <p class="text-danger mb-0">{{ $application->msfaa_rejection_reason }}</p>
                                        </div>
                                    </div>
                                @endif

                                @if($application->msfaa_admin_notes)
                                    <div class="col-12">
                                        <div class="separator separator-dashed mb-4"></div>
                                        <label class="text-gray-500 fs-8 text-uppercase fw-semibold mb-3 d-block">Admin Notes</label>
                                        <div class="bg-light-primary rounded p-4">
                                            <p class="text-gray-800 mb-0">{{ $application->msfaa_admin_notes }}</p>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </x-cards.section>
                    </div>
                @endif
            </div>
        </div>

        {{-- Sidebar --}}
        <div class="col-xl-4">
            @include('pages.admin.applications._partials.sidebar-actions')

            {{-- Quick Info Card --}}
            <x-detail.quick-info
                title="Quick Info"
                icon="information-2"
                :items="[
                    ['label' => 'Days Since Submission', 'value' => floor($application->created_at->diffInDays(now())) . ' days', 'badge' => 'info'],
                    ['label' => 'Documents Uploaded', 'value' => $docCount . ' / 5', 'badge' => $docCount >= 4 ? 'success' : 'warning'],
                    ['label' => 'Work Experience', 'value' => $application->has_work_experience ? 'Yes' : 'No', 'badge' => $application->has_work_experience ? 'success' : 'secondary'],
                    ['label' => 'Agency Referral', 'value' => $application->has_referral ? 'Yes' : 'No', 'badge' => $application->has_referral ? 'primary' : 'secondary'],
                ]"
                class="mb-5"
            />

            {{-- Back Button --}}
            <a href="{{ route('admin.applications.index') }}" class="btn btn-light w-100 d-flex align-items-center justify-content-center gap-2">
                {!! getIcon('left', 'fs-4') !!}
                Back to Applications
            </a>
        </div>
    </div>

    {{-- All Modals --}}
    @include('pages.admin.applications._partials.modals')

    @push('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/viewerjs/1.11.7/viewer.min.css" />
    @endpush

    @push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/viewerjs/1.11.7/viewer.min.js"></script>
    <script>
        function previewDocument(url, title, ext) {
            const imageExts = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp', 'svg'];
            const pdfExts = ['pdf'];

            if (imageExts.includes(ext)) {
                // Use Viewer.js for images — fullscreen overlay with toolbar
                const img = document.createElement('img');
                img.src = url;
                img.alt = title;
                img.style.display = 'none';
                document.body.appendChild(img);

                const viewer = new Viewer(img, {
                    inline: false,
                    title: [1, () => title],
                    toolbar: {
                        zoomIn: 1,
                        zoomOut: 1,
                        oneToOne: 1,
                        reset: 1,
                        rotateLeft: 1,
                        rotateRight: 1,
                        flipHorizontal: 1,
                        flipVertical: 1,
                    },
                    navbar: false,
                    keyboard: true,
                    ready() {
                        var toolbar = document.querySelector('.viewer-toolbar ul');
                        if (!toolbar) return;
                        var li = document.createElement('li');
                        li.setAttribute('role', 'button');
                        li.setAttribute('tabindex', '0');
                        li.setAttribute('title', 'Print (P)');
                        li.style.cssText = 'cursor:pointer';
                        li.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 9V2h12v7"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg>';
                        li.addEventListener('click', function() {
                            var viewedImg = document.querySelector('.viewer-canvas img');
                            if (!viewedImg) return;
                            var transform = viewedImg.style.transform || '';
                            var printWin = window.open('', '_blank');
                            var s = '<' + 'style>';
                            var se = '</' + 'style>';
                            printWin.document.write(
                                '<html><head><title>Print</' + 'title>' + s +
                                '@media print{@page{margin:0.5cm}}' +
                                'body{margin:0;display:flex;justify-content:center;align-items:center;min-height:100vh}' +
                                'img{max-width:100%;max-height:100vh;transform:' + transform + '}' +
                                se + '</' + 'head>' +
                                '<body><img src="' + url + '" onload="setTimeout(function(){window.print();window.close()},100)"></' + 'body></' + 'html>'
                            );
                            printWin.document.close();
                        });
                        toolbar.appendChild(li);

                        document.addEventListener('keydown', function onKey(e) {
                            if (e.key === 'p' || e.key === 'P') {
                                if (!document.querySelector('.viewer-container')) {
                                    document.removeEventListener('keydown', onKey);
                                    return;
                                }
                                li.click();
                            }
                        });
                    },
                    hidden() {
                        viewer.destroy();
                        img.remove();
                    },
                });
                viewer.show();

            } else if (pdfExts.includes(ext)) {
                // Open PDF in new tab with browser's native PDF controls
                window.open(url, '_blank');

            } else {
                // Fallback for unsupported file types
                var modal = new bootstrap.Modal(document.getElementById('previewModal'));
                document.getElementById('previewTitle').textContent = title;
                document.getElementById('previewBody').innerHTML =
                    '<div class="d-flex flex-column align-items-center justify-content-center" style="min-height: 600px;">' +
                    '<div class="symbol symbol-100px mb-5"><span class="symbol-label bg-gray-100">' +
                    '<i class="ki-outline ki-document fs-2x text-gray-600"></i></span></div>' +
                    '<h4 class="text-gray-600 fw-semibold mb-2">Preview Not Available</h4>' +
                    '<p class="text-gray-500 fs-7 mb-0">Please download the file to view its contents</p></div>';
                modal.show();
            }
        }

        // Clean up iframe when PDF modal closes to prevent memory leaks
        document.getElementById('previewModal').addEventListener('hidden.bs.modal', function () {
            document.getElementById('previewBody').innerHTML = '';
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const rejectOnlyRadio = document.getElementById('rejectOnlyRadio');
            const regenerateRadio = document.getElementById('regenerateRadio');
            const rejectOnlyNotice = document.getElementById('rejectOnlyNotice');
            const regenerateNotice = document.getElementById('regenerateNotice');
            const rejectOnlyLabel = document.getElementById('rejectOnlyLabel');
            const regenerateLabel = document.getElementById('regenerateLabel');
            const rejectBtn = document.getElementById('rejectContractBtn');

            if (!rejectOnlyRadio || !regenerateRadio) return;

            function updateRejectUI() {
                if (regenerateRadio.checked) {
                    rejectOnlyNotice.classList.add('d-none');
                    regenerateNotice.classList.remove('d-none');
                    rejectOnlyLabel.classList.remove('border-primary');
                    regenerateLabel.classList.add('border-primary');
                    rejectBtn.innerHTML = '{!! getIcon("arrows-circle", "fs-4 me-2") !!} Reject & Regenerate';
                    rejectBtn.classList.remove('btn-warning');
                    rejectBtn.classList.add('btn-primary');
                } else {
                    rejectOnlyNotice.classList.remove('d-none');
                    regenerateNotice.classList.add('d-none');
                    rejectOnlyLabel.classList.add('border-primary');
                    regenerateLabel.classList.remove('border-primary');
                    rejectBtn.innerHTML = '{!! getIcon("cross", "fs-4 me-2") !!} Reject Contract';
                    rejectBtn.classList.remove('btn-primary');
                    rejectBtn.classList.add('btn-warning');
                }
            }

            rejectOnlyRadio.addEventListener('change', updateRejectUI);
            regenerateRadio.addEventListener('change', updateRejectUI);
            updateRejectUI();
        });
    </script>
    @endpush
</x-default-layout>
