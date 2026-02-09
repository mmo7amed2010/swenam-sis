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
    />

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
            </ul>

            {{-- Tab Content --}}
            <div class="tab-content">
                {{-- Personal Details Tab --}}
                <div class="tab-pane fade show active" id="tab_details" role="tabpanel">
                    {{-- Program Information --}}
                    <x-cards.section title="Program Information" class="mb-5">
                        <div class="row g-4">
                            <div class="col-md-4">
                                <x-detail.info-card icon="abstract-26" label="Program" :value="$application->program_name ?? 'N/A'" color="primary" />
                            </div>
                            <div class="col-md-4">
                                <x-detail.info-card icon="calendar" label="Preferred Intake" :value="$application->intake_name ?? $application->preferred_intake ?? 'N/A'" color="primary" />
                            </div>
                            <div class="col-md-4">
                                <x-detail.info-card icon="people" label="Agency Referral" color="primary">
                                    @if($application->has_referral)
                                        {{ $application->referral_agency_name }}
                                    @else
                                        <span class="text-gray-500">None</span>
                                    @endif
                                </x-detail.info-card>
                            </div>
                            <div class="col-md-4">
                                <x-detail.info-card icon="wallet" label="Funding Type" color="primary">
                                    @if($application->isSelfFunded())
                                        <span class="badge badge-light-warning">Self-Funded</span>
                                    @elseif($application->isGovernmentFunded())
                                        <span class="badge badge-light-success">Government-Funded</span>
                                    @else
                                        <span class="text-gray-500">N/A</span>
                                    @endif
                                </x-detail.info-card>
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
                                                                onclick="previewDocument('{{ route('admin.applications.download', [$application, $doc['key']]) }}?preview=1', '{{ $doc['label'] }}')">
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
                                            onclick="previewDocument('{{ route('admin.applications.noa.download', $application) }}?preview=1', 'NOA Document')">
                                        {!! getIcon('eye', 'fs-5 me-1') !!}
                                        Preview
                                    </button>
                                </div>
                            </x-cards.section>
                        @endif
                    </div>
                @endif
            </div>
        </div>

        {{-- Sidebar --}}
        <div class="col-xl-4">
            {{-- Action Card for Pending --}}
            @if($application->isPending())
                <div class="card border-0 shadow-sm mb-5">
                    <div class="card-header border-0 bg-light-primary py-5">
                        <h3 class="card-title fw-bold text-gray-800">
                            {!! getIcon('shield-tick', 'fs-4 me-2 text-primary') !!}
                            Review Actions
                        </h3>
                    </div>
                    <div class="card-body p-6">
                        <div class="d-grid gap-3">
                            <button type="button"
                                    class="btn btn-info btn-lg d-flex align-items-center justify-content-center gap-2"
                                    data-bs-toggle="modal"
                                    data-bs-target="#initialApproveModal">
                                {!! getIcon('shield-tick', 'fs-3') !!}
                                <span>Initial Approve</span>
                            </button>

                            <button type="button"
                                    class="btn btn-danger btn-lg d-flex align-items-center justify-content-center gap-2"
                                    data-bs-toggle="modal"
                                    data-bs-target="#rejectModal">
                                {!! getIcon('cross-circle', 'fs-3') !!}
                                <span>Reject Application</span>
                            </button>
                        </div>

                        <div class="separator separator-dashed my-5"></div>

                        <div class="notice d-flex bg-light-warning rounded border-warning border border-dashed p-4">
                            {!! getIcon('information-5', 'fs-2x text-warning me-3 flex-shrink-0') !!}
                            <div class="text-gray-700 fs-7">
                                <strong>Initial Approve:</strong> Use this to mark the application for further discussion with the student (pricing, etc.).
                                No student account will be created yet.
                            </div>
                        </div>
                    </div>
                </div>
            @elseif($application->isInitialApproved())
                {{-- Action Card for Initial Approved: Send Contract --}}
                <div class="card border-0 shadow-sm mb-5">
                    <div class="card-header border-0 bg-light-info py-5">
                        <h3 class="card-title fw-bold text-gray-800">
                            {!! getIcon('shield-tick', 'fs-4 me-2 text-info') !!}
                            Next Step: Send Contract
                        </h3>
                    </div>
                    <div class="card-body p-6">
                        <div class="notice d-flex bg-light-info rounded border-info border border-dashed p-4 mb-5">
                            {!! getIcon('information-5', 'fs-2x text-info me-3 flex-shrink-0') !!}
                            <div class="text-gray-700 fs-7">
                                This application has been initially approved. Send an enrollment contract to proceed.
                            </div>
                        </div>

                        <div class="d-grid gap-3">
                            <a href="{{ route('admin.applications.contract.create', $application) }}"
                               class="btn btn-primary btn-lg d-flex align-items-center justify-content-center gap-2">
                                {!! getIcon('send', 'fs-3') !!}
                                <span>Send Contract</span>
                            </a>

                            <button type="button"
                                    class="btn btn-danger btn-lg d-flex align-items-center justify-content-center gap-2"
                                    data-bs-toggle="modal"
                                    data-bs-target="#rejectModal">
                                {!! getIcon('cross-circle', 'fs-3') !!}
                                <span>Reject Application</span>
                            </button>
                        </div>
                    </div>
                </div>
            @elseif($application->isContractSent())
                {{-- Contract Sent: Waiting for student --}}
                <div class="card border-0 shadow-sm mb-5">
                    <div class="card-header border-0 bg-light-primary py-5">
                        <h3 class="card-title fw-bold text-gray-800">
                            {!! getIcon('send', 'fs-4 me-2 text-primary') !!}
                            Contract Sent
                        </h3>
                    </div>
                    <div class="card-body p-6">
                        <div class="notice d-flex bg-light-warning rounded border-warning border border-dashed p-4 mb-5">
                            {!! getIcon('time', 'fs-2x text-warning me-3 flex-shrink-0') !!}
                            <div class="text-gray-700 fs-7">
                                Waiting for the student to upload their signed contract.
                            </div>
                        </div>

                        <div class="d-grid gap-3">
                            <button type="button"
                                    class="btn btn-danger btn-lg d-flex align-items-center justify-content-center gap-2"
                                    data-bs-toggle="modal"
                                    data-bs-target="#rejectModal">
                                {!! getIcon('cross-circle', 'fs-3') !!}
                                <span>Reject Application</span>
                            </button>
                        </div>
                    </div>
                </div>
            @elseif($application->isContractUploaded())
                {{-- Contract Uploaded: Approve/Reject --}}
                <div class="card border-0 shadow-sm mb-5">
                    <div class="card-header border-0 bg-light-info py-5">
                        <h3 class="card-title fw-bold text-gray-800">
                            {!! getIcon('document', 'fs-4 me-2 text-info') !!}
                            Review Signed Contract
                        </h3>
                    </div>
                    <div class="card-body p-6">
                        <div class="notice d-flex bg-light-info rounded border-info border border-dashed p-4 mb-5">
                            {!! getIcon('information-5', 'fs-2x text-info me-3 flex-shrink-0') !!}
                            <div class="text-gray-700 fs-7">
                                The student has uploaded their signed contract. Review and approve or reject.
                            </div>
                        </div>

                        @if($application->latestContract?->signed_pdf_path)
                            <a href="{{ route('admin.contracts.download-signed', $application->latestContract) }}" class="btn btn-light-primary w-100 mb-4">
                                {!! getIcon('down', 'fs-5 me-1') !!}
                                Download Signed Contract
                            </a>
                        @endif

                        <div class="d-grid gap-3">
                            <button type="button"
                                    class="btn btn-success btn-lg d-flex align-items-center justify-content-center gap-2"
                                    data-bs-toggle="modal"
                                    data-bs-target="#approveContractModal">
                                {!! getIcon('check-circle', 'fs-3') !!}
                                <span>Approve Contract</span>
                            </button>

                            <button type="button"
                                    class="btn btn-warning btn-lg d-flex align-items-center justify-content-center gap-2"
                                    data-bs-toggle="modal"
                                    data-bs-target="#rejectContractModal">
                                {!! getIcon('cross-circle', 'fs-3') !!}
                                <span>Reject Contract</span>
                            </button>

                            <button type="button"
                                    class="btn btn-danger btn-lg d-flex align-items-center justify-content-center gap-2"
                                    data-bs-toggle="modal"
                                    data-bs-target="#rejectModal">
                                {!! getIcon('cross-circle', 'fs-3') !!}
                                <span>Reject Application</span>
                            </button>
                        </div>
                    </div>
                </div>
            @elseif($application->isContractApproved() && $application->isSelfFunded())
                {{-- Contract Approved (Self-Funded): Waiting for payment --}}
                <div class="card border-0 shadow-sm mb-5">
                    <div class="card-header border-0 bg-light-warning py-5">
                        <h3 class="card-title fw-bold text-gray-800">
                            {!! getIcon('wallet', 'fs-4 me-2 text-warning') !!}
                            Awaiting Payment
                        </h3>
                    </div>
                    <div class="card-body p-6">
                        <div class="notice d-flex bg-light-warning rounded border-warning border border-dashed p-4 mb-5">
                            {!! getIcon('time', 'fs-2x text-warning me-3 flex-shrink-0') !!}
                            <div class="text-gray-700 fs-7">
                                Contract approved. Waiting for student to upload payment receipt.
                            </div>
                        </div>
                        <div class="d-grid gap-3">
                            <button type="button"
                                    class="btn btn-danger btn-lg d-flex align-items-center justify-content-center gap-2"
                                    data-bs-toggle="modal"
                                    data-bs-target="#rejectModal">
                                {!! getIcon('cross-circle', 'fs-3') !!}
                                <span>Reject Application</span>
                            </button>
                        </div>
                    </div>
                </div>
            @elseif($application->isPaymentPending())
                {{-- Payment Pending: Waiting for student --}}
                <div class="card border-0 shadow-sm mb-5">
                    <div class="card-header border-0 bg-light-warning py-5">
                        <h3 class="card-title fw-bold text-gray-800">
                            {!! getIcon('wallet', 'fs-4 me-2 text-warning') !!}
                            Awaiting Payment
                        </h3>
                    </div>
                    <div class="card-body p-6">
                        <div class="notice d-flex bg-light-warning rounded border-warning border border-dashed p-4 mb-5">
                            {!! getIcon('time', 'fs-2x text-warning me-3 flex-shrink-0') !!}
                            <div class="text-gray-700 fs-7">
                                Waiting for the student to upload their payment receipt.
                            </div>
                        </div>
                        <div class="d-grid gap-3">
                            <button type="button"
                                    class="btn btn-danger btn-lg d-flex align-items-center justify-content-center gap-2"
                                    data-bs-toggle="modal"
                                    data-bs-target="#rejectModal">
                                {!! getIcon('cross-circle', 'fs-3') !!}
                                <span>Reject Application</span>
                            </button>
                        </div>
                    </div>
                </div>
            @elseif($application->isPaymentUploaded())
                {{-- Payment Uploaded: Approve/Reject --}}
                <div class="card border-0 shadow-sm mb-5">
                    <div class="card-header border-0 bg-light-info py-5">
                        <h3 class="card-title fw-bold text-gray-800">
                            {!! getIcon('wallet', 'fs-4 me-2 text-info') !!}
                            Review Payment
                        </h3>
                    </div>
                    <div class="card-body p-6">
                        <div class="notice d-flex bg-light-info rounded border-info border border-dashed p-4 mb-5">
                            {!! getIcon('information-5', 'fs-2x text-info me-3 flex-shrink-0') !!}
                            <div class="text-gray-700 fs-7">
                                The student has uploaded their payment receipt. Review and approve or reject.
                            </div>
                        </div>

                        @if($application->payment_receipt_path)
                            <a href="{{ route('admin.applications.payment.download', $application) }}" class="btn btn-light-warning w-100 mb-4">
                                {!! getIcon('down', 'fs-5 me-1') !!}
                                Download Payment Receipt
                            </a>
                        @endif

                        <div class="d-grid gap-3">
                            <button type="button"
                                    class="btn btn-success btn-lg d-flex align-items-center justify-content-center gap-2"
                                    data-bs-toggle="modal"
                                    data-bs-target="#approvePaymentModal">
                                {!! getIcon('check-circle', 'fs-3') !!}
                                <span>Approve Payment</span>
                            </button>

                            <button type="button"
                                    class="btn btn-warning btn-lg d-flex align-items-center justify-content-center gap-2"
                                    data-bs-toggle="modal"
                                    data-bs-target="#rejectPaymentModal">
                                {!! getIcon('cross-circle', 'fs-3') !!}
                                <span>Reject Payment</span>
                            </button>

                            <button type="button"
                                    class="btn btn-danger btn-lg d-flex align-items-center justify-content-center gap-2"
                                    data-bs-toggle="modal"
                                    data-bs-target="#rejectModal">
                                {!! getIcon('cross-circle', 'fs-3') !!}
                                <span>Reject Application</span>
                            </button>
                        </div>
                    </div>
                </div>
            @else
                {{-- Status Card for Reviewed (Approved/Rejected/Payment Approved) --}}
                <div class="card border-0 shadow-sm mb-5">
                    <div class="card-body text-center p-8">
                        <div class="symbol symbol-80px mb-5">
                            <span class="symbol-label bg-light-{{ $config['badge'] }}">
                                {!! getIcon($config['icon'], 'fs-2x text-' . $config['badge']) !!}
                            </span>
                        </div>
                        <h2 class="fw-bolder text-gray-800 mb-2">{{ $config['label'] }}</h2>
                        <p class="text-gray-600 fs-7 mb-0">
                            @if($application->status === 'approved')
                                Student account has been created successfully
                            @elseif($application->status === 'rejected')
                                This application has been rejected
                            @else
                                Processing...
                            @endif
                        </p>
                    </div>
                </div>

                @if($application->isRejected())
                    <div class="notice d-flex bg-light-danger rounded border-danger border border-dashed p-5 mb-5">
                        {!! getIcon('information-5', 'fs-2x text-danger me-3 flex-shrink-0') !!}
                        <div>
                            <h4 class="text-danger fw-bold mb-1">Application Rejected</h4>
                            <p class="text-gray-700 fs-7 mb-0">The applicant must submit a new application to be considered.</p>
                        </div>
                    </div>
                @endif

                {{-- NOA Actions (only for approved applications) --}}
                @if($application->status === 'approved')
                    @if($application->canRequestNoa())
                        {{-- No NOA requested yet --}}
                        <div class="card border-0 shadow-sm mb-5">
                            <div class="card-header border-0 bg-light-primary py-5">
                                <h3 class="card-title fw-bold text-gray-800">
                                    {!! getIcon('document', 'fs-4 me-2 text-primary') !!}
                                    NOA (Notice of Acceptance)
                                </h3>
                            </div>
                            <div class="card-body p-6">
                                <div class="notice d-flex bg-light-info rounded border-info border border-dashed p-4 mb-5">
                                    {!! getIcon('information-5', 'fs-2x text-info me-3 flex-shrink-0') !!}
                                    <div class="text-gray-700 fs-7">
                                        Request the student to upload their Notice of Acceptance (NOA) document. The student will be notified via email.
                                    </div>
                                </div>

                                <form action="{{ route('admin.applications.noa.request', $application) }}" method="POST">
                                    @csrf
                                    <div class="d-grid">
                                        <button type="submit" class="btn btn-primary btn-lg d-flex align-items-center justify-content-center gap-2">
                                            {!! getIcon('send', 'fs-3') !!}
                                            <span>Request NOA</span>
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    @elseif($application->isNoaRequested())
                        {{-- NOA requested, waiting for student --}}
                        <div class="card border-0 shadow-sm mb-5">
                            <div class="card-header border-0 bg-light-warning py-5">
                                <h3 class="card-title fw-bold text-gray-800">
                                    {!! getIcon('document', 'fs-4 me-2 text-warning') !!}
                                    NOA Requested
                                </h3>
                            </div>
                            <div class="card-body p-6">
                                <div class="notice d-flex bg-light-warning rounded border-warning border border-dashed p-4 mb-5">
                                    {!! getIcon('time', 'fs-2x text-warning me-3 flex-shrink-0') !!}
                                    <div class="text-gray-700 fs-7">
                                        Waiting for the student to upload their NOA document.
                                    </div>
                                </div>

                                <div class="d-flex align-items-center bg-gray-100 rounded p-3">
                                    {!! getIcon('time', 'fs-4 text-warning me-3') !!}
                                    <span class="text-gray-700 fs-7">
                                        <strong>{{ $application->noa_elapsed_days ?? 0 }} days</strong> since NOA was requested
                                    </span>
                                </div>
                            </div>
                        </div>
                    @elseif($application->canReviewNoa())
                        {{-- NOA uploaded, needs review --}}
                        <div class="card border-0 shadow-sm mb-5">
                            <div class="card-header border-0 bg-light-info py-5">
                                <h3 class="card-title fw-bold text-gray-800">
                                    {!! getIcon('document', 'fs-4 me-2 text-info') !!}
                                    Review NOA
                                </h3>
                            </div>
                            <div class="card-body p-6">
                                <div class="notice d-flex bg-light-info rounded border-info border border-dashed p-4 mb-5">
                                    {!! getIcon('information-5', 'fs-2x text-info me-3 flex-shrink-0') !!}
                                    <div class="text-gray-700 fs-7">
                                        The student has uploaded their NOA document. Review and approve or reject.
                                    </div>
                                </div>

                                @if($application->noa_document_path)
                                    <a href="{{ route('admin.applications.noa.download', $application) }}" class="btn btn-light-primary w-100 mb-4">
                                        {!! getIcon('down', 'fs-5 me-1') !!}
                                        Download NOA Document
                                    </a>
                                @endif

                                <div class="d-grid gap-3">
                                    <button type="button"
                                            class="btn btn-success btn-lg d-flex align-items-center justify-content-center gap-2"
                                            data-bs-toggle="modal"
                                            data-bs-target="#approveNoaModal">
                                        {!! getIcon('check-circle', 'fs-3') !!}
                                        <span>Approve NOA</span>
                                    </button>

                                    <button type="button"
                                            class="btn btn-warning btn-lg d-flex align-items-center justify-content-center gap-2"
                                            data-bs-toggle="modal"
                                            data-bs-target="#rejectNoaModal">
                                        {!! getIcon('cross-circle', 'fs-3') !!}
                                        <span>Reject NOA</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    @elseif($application->isNoaApproved())
                        {{-- NOA approved --}}
                        <div class="card border-0 shadow-sm mb-5">
                            <div class="card-header border-0 bg-light-success py-5">
                                <h3 class="card-title fw-bold text-gray-800">
                                    {!! getIcon('check-circle', 'fs-4 me-2 text-success') !!}
                                    NOA Approved
                                </h3>
                            </div>
                            <div class="card-body p-6">
                                <div class="notice d-flex bg-light-success rounded border-success border border-dashed p-4 mb-5">
                                    {!! getIcon('check-circle', 'fs-2x text-success me-3 flex-shrink-0') !!}
                                    <div class="text-gray-700 fs-7">
                                        The NOA document has been approved.
                                    </div>
                                </div>

                                @if($application->noa_document_path)
                                    <a href="{{ route('admin.applications.noa.download', $application) }}" class="btn btn-light-primary w-100">
                                        {!! getIcon('down', 'fs-5 me-1') !!}
                                        Download NOA Document
                                    </a>
                                @endif
                            </div>
                        </div>
                    @elseif($application->noa_status === 'rejected')
                        {{-- NOA rejected --}}
                        <div class="card border-0 shadow-sm mb-5">
                            <div class="card-header border-0 bg-light-danger py-5">
                                <h3 class="card-title fw-bold text-gray-800">
                                    {!! getIcon('cross-circle', 'fs-4 me-2 text-danger') !!}
                                    NOA Rejected
                                </h3>
                            </div>
                            <div class="card-body p-6">
                                @if($application->noa_rejection_reason)
                                    <div class="bg-light-danger rounded p-4 mb-5">
                                        <label class="text-gray-500 fs-8 text-uppercase fw-semibold mb-2 d-block">Rejection Reason</label>
                                        <p class="text-danger mb-0">{{ $application->noa_rejection_reason }}</p>
                                    </div>
                                @endif

                                <div class="notice d-flex bg-light-warning rounded border-warning border border-dashed p-4">
                                    {!! getIcon('information-5', 'fs-2x text-warning me-3 flex-shrink-0') !!}
                                    <div class="text-gray-700 fs-7">
                                        The student can re-upload a new NOA document.
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                @endif
            @endif
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

    {{-- Approve NOA Modal --}}
    <div class="modal fade" id="approveNoaModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered mw-650px">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-light-success border-0">
                    <div class="d-flex align-items-center">
                        <div class="symbol symbol-50px me-4">
                            <span class="symbol-label bg-success">
                                {!! getIcon('check', 'fs-2x text-white') !!}
                            </span>
                        </div>
                        <div>
                            <h2 class="fw-bolder text-gray-800 mb-1">Approve NOA</h2>
                            <p class="text-gray-600 fs-7 mb-0">Approve {{ $application->full_name }}'s NOA document</p>
                        </div>
                    </div>
                    <div class="btn btn-icon btn-sm btn-active-light-primary" data-bs-dismiss="modal">
                        {!! getIcon('cross', 'fs-1') !!}
                    </div>
                </div>

                <form action="{{ route('admin.applications.noa.approve', $application) }}" method="POST">
                    @csrf
                    <div class="modal-body py-8">
                        <div class="mb-6">
                            <label class="text-gray-700 fw-bold fs-6 mb-4 d-block">This action will:</label>
                            <div class="d-flex flex-column gap-3">
                                <div class="d-flex align-items-center bg-gray-100 rounded p-3">
                                    {!! getIcon('check-circle', 'fs-4 text-success me-3') !!}
                                    <span class="text-gray-700 fs-7">Mark the NOA as <strong>Approved</strong></span>
                                </div>
                                <div class="d-flex align-items-center bg-gray-100 rounded p-3">
                                    {!! getIcon('check-circle', 'fs-4 text-success me-3') !!}
                                    <span class="text-gray-700 fs-7">The student will be notified of the approval</span>
                                </div>
                            </div>
                        </div>

                        <div class="mb-5">
                            <label class="form-label text-gray-700 fw-semibold">Admin Notes <span class="text-gray-500 fs-8">(Optional)</span></label>
                            <textarea name="admin_notes" class="form-control form-control-solid" rows="3" placeholder="Add any notes about this approval..."></textarea>
                        </div>

                        <div class="notice d-flex bg-light-info rounded border-info border border-dashed p-4">
                            {!! getIcon('information-5', 'fs-2x text-info me-3 flex-shrink-0') !!}
                            <div class="text-gray-700 fs-7">
                                <strong>Note:</strong> Approving the NOA confirms the student's Notice of Acceptance document is valid.
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer border-0 pt-0">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success">
                            {!! getIcon('check', 'fs-4 me-2') !!}
                            Approve NOA
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Reject NOA Modal --}}
    <div class="modal fade" id="rejectNoaModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered mw-650px">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-light-warning border-0">
                    <div class="d-flex align-items-center">
                        <div class="symbol symbol-50px me-4">
                            <span class="symbol-label bg-warning">
                                {!! getIcon('cross', 'fs-2x text-white') !!}
                            </span>
                        </div>
                        <div>
                            <h2 class="fw-bolder text-gray-800 mb-1">Reject NOA</h2>
                            <p class="text-gray-600 fs-7 mb-0">Student will need to re-upload their NOA document</p>
                        </div>
                    </div>
                    <div class="btn btn-icon btn-sm btn-active-light-primary" data-bs-dismiss="modal">
                        {!! getIcon('cross', 'fs-1') !!}
                    </div>
                </div>

                <form action="{{ route('admin.applications.noa.reject', $application) }}" method="POST" x-data="{ noaReason: '', minLength: 10, maxLength: 1000 }">
                    @csrf
                    <div class="modal-body py-8">
                        <div class="mb-5">
                            <label class="form-label text-gray-700 fw-semibold required">Rejection Reason</label>
                            <textarea
                                name="rejection_reason"
                                class="form-control form-control-solid @error('rejection_reason') is-invalid @enderror"
                                rows="4"
                                placeholder="Explain why the NOA document is being rejected..."
                                x-model="noaReason"
                                required
                                minlength="10"
                                maxlength="1000"></textarea>
                            @error('rejection_reason')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="d-flex justify-content-between mt-2">
                                <span class="text-gray-500 fs-8">
                                    <span x-text="noaReason.length"></span> / <span x-text="maxLength"></span> characters
                                </span>
                                <span x-show="noaReason.length > 0 && noaReason.length < minLength" class="text-danger fs-8">
                                    Minimum <span x-text="minLength"></span> characters required
                                </span>
                            </div>
                        </div>

                        <div class="mb-5">
                            <label class="form-label text-gray-700 fw-semibold">Admin Notes <span class="text-gray-500 fs-8">(Optional)</span></label>
                            <textarea name="admin_notes" class="form-control form-control-solid" rows="3" placeholder="Add any internal notes..."></textarea>
                        </div>

                        <div class="notice d-flex bg-light-warning rounded border-warning border border-dashed p-4">
                            {!! getIcon('information-5', 'fs-2x text-warning me-3 flex-shrink-0') !!}
                            <div class="text-gray-700 fs-7">
                                The student will be able to re-upload a new NOA document.
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer border-0 pt-0">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-warning" :disabled="noaReason.length < minLength">
                            {!! getIcon('cross', 'fs-4 me-2') !!}
                            Reject NOA
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function previewDocument(url, type) {
            const modal = new bootstrap.Modal(document.getElementById('previewModal'));
            const titleEl = document.getElementById('previewTitle');
            const bodyEl = document.getElementById('previewBody');

            titleEl.textContent = type;

            bodyEl.innerHTML = `
                <div class="d-flex flex-column align-items-center justify-content-center" style="min-height: 600px;">
                    <div class="spinner-border text-gray-600 mb-3" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <span class="text-gray-600">Loading document...</span>
                </div>
            `;

            modal.show();

            const isPdf = url.toLowerCase().includes('.pdf') || url.includes('preview=1');
            const isImage = /\.(jpg|jpeg|png|gif|webp)/i.test(url);

            setTimeout(() => {
                if (isPdf) {
                    bodyEl.innerHTML = `<embed src="${url}" type="application/pdf" width="100%" height="700px" style="border: none;">`;
                } else if (isImage) {
                    bodyEl.innerHTML = `
                        <div class="d-flex align-items-center justify-content-center p-5" style="min-height: 600px; background: #f8f9fa;">
                            <img src="${url}" class="img-fluid shadow-sm rounded" alt="${type}" style="max-height: 700px;">
                        </div>
                    `;
                } else {
                    bodyEl.innerHTML = `
                        <div class="d-flex flex-column align-items-center justify-content-center" style="min-height: 600px;">
                            <div class="symbol symbol-100px mb-5">
                                <span class="symbol-label bg-gray-100">
                                    <i class="ki-outline ki-document fs-2x text-gray-600"></i>
                                </span>
                            </div>
                            <h4 class="text-gray-600 fw-semibold mb-2">Preview Not Available</h4>
                            <p class="text-gray-500 fs-7 mb-0">Please download the file to view its contents</p>
                        </div>
                    `;
                }
            }, 300);
        }
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
