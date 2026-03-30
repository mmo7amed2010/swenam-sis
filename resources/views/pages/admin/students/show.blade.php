<x-default-layout>

    @section('title')
        {{ __('Student Details') }} - {{ $student->full_name }}
    @endsection

    @section('breadcrumbs')
        {{ Breadcrumbs::render('admin.students.show', $student) }}
    @endsection

    @php
        $isSuspended = $student->user?->is_suspended ?? false;
        $headerStatus = $isSuspended ? 'inactive' : 'active';
        $headerStatusLabel = $isSuspended ? __('Suspended') : __('Active');

        $headerPills = [
            __('Student #') => $student->student_number ?? 'N/A',
            __('Program') => $student->user?->program?->name ?? 'N/A',
            __('Member Since') => $student->created_at->format('M d, Y'),
        ];
        if ($student->user?->last_login_at) {
            $headerPills[__('Last Login')] = $student->user->last_login_at->diffForHumans();
        }

        $application = $student->studentApplication;

        if ($application) {
            $statusConfig = [
                'pending' => ['badge' => 'warning', 'icon' => 'time', 'label' => __('Pending Review')],
                'initial_approved' => ['badge' => 'info', 'icon' => 'shield-tick', 'label' => __('Initial Approved')],
                'contract_sent' => ['badge' => 'primary', 'icon' => 'send', 'label' => __('Contract Sent')],
                'contract_uploaded' => ['badge' => 'info', 'icon' => 'document', 'label' => __('Contract Uploaded')],
                'contract_approved' => ['badge' => 'success', 'icon' => 'verify', 'label' => __('Contract Approved')],
                'payment_pending' => ['badge' => 'warning', 'icon' => 'wallet', 'label' => __('Payment Pending')],
                'payment_uploaded' => ['badge' => 'info', 'icon' => 'wallet', 'label' => __('Payment Uploaded')],
                'payment_approved' => ['badge' => 'success', 'icon' => 'wallet', 'label' => __('Payment Approved')],
                'approved' => ['badge' => 'success', 'icon' => 'check-circle', 'label' => __('Approved')],
                'rejected' => ['badge' => 'danger', 'icon' => 'cross-circle', 'label' => __('Rejected')],
            ];
            $appConfig = $statusConfig[$application->status] ?? $statusConfig['pending'];

            $noaStatusConfig = [
                'requested' => ['badge' => 'warning', 'label' => __('Requested')],
                'uploaded'  => ['badge' => 'info', 'label' => __('Uploaded')],
                'approved'  => ['badge' => 'success', 'label' => __('Approved')],
                'rejected'  => ['badge' => 'danger', 'label' => __('Rejected')],
            ];
            $noaConfig = $noaStatusConfig[$application->noa_status] ?? null;

            $msfaaStatusConfig = [
                'requested'  => ['badge' => 'warning', 'label' => __('Requested')],
                'confirmed'  => ['badge' => 'info', 'label' => __('Confirmed')],
                'approved'   => ['badge' => 'success', 'label' => __('Approved')],
                'rejected'   => ['badge' => 'danger', 'label' => __('Rejected')],
            ];
            $msfaaConfig = $msfaaStatusConfig[$application->msfaa_status] ?? null;

            $docCount = collect([
                $application->government_id_path,
                $application->degree_certificate_path,
                $application->transcripts_path,
                $application->cv_path,
                $application->english_test_path,
            ])->filter()->count();
        }
    @endphp

    {{-- Profile Header --}}
    <x-profile.header
        :name="$student->full_name"
        :initials="strtoupper(substr($student->first_name, 0, 1) . substr($student->last_name, 0, 1))"
        :email="$student->email"
        :phone="$student->phone"
        :status="$headerStatus"
        :statusLabel="$headerStatusLabel"
        :pills="$headerPills"
    />

    <div class="row g-6">
        {{-- Main Content --}}
        <div class="col-xl-8">

            {{-- Student Details --}}
            <x-cards.section :title="__('Student Details')" class="mb-5">
                <div class="row g-4">
                    <div class="col-md-6">
                        <x-detail.field icon="hashtag" :label="__('Student Number')" :value="$student->student_number ?? 'N/A'" color="primary" />
                    </div>
                    <div class="col-md-6">
                        <x-detail.field icon="user" :label="__('Full Name')" :value="$student->full_name" color="primary" />
                    </div>
                    <div class="col-md-6">
                        <x-detail.field icon="sms" :label="__('Email')" :value="$student->email" :href="'mailto:' . $student->email" color="primary" />
                    </div>
                    <div class="col-md-6">
                        <x-detail.field icon="phone" :label="__('Phone')" :value="$student->phone ?? 'N/A'" color="primary" />
                    </div>
                    <div class="col-md-6">
                        <x-detail.field icon="calendar" :label="__('Date of Birth')" :value="$student->date_of_birth ? $student->date_of_birth->format('F d, Y') : 'N/A'" color="primary" />
                    </div>
                    @if($application)
                        <div class="col-md-6">
                            <x-detail.field icon="flag" :label="__('Citizenship')" :value="$application->country_of_citizenship ?? 'N/A'" color="primary" />
                        </div>
                        <div class="col-md-6">
                            <x-detail.field icon="home" :label="__('Residency Status')" :value="$application->residency_status ?? 'N/A'" color="primary" />
                        </div>
                        <div class="col-md-6">
                            <x-detail.field icon="message-text-2" :label="__('Primary Language')" :value="$application->primary_language ?? 'N/A'" color="primary" />
                        </div>
                    @endif
                    <div class="col-md-6">
                        <x-detail.field icon="abstract-26" :label="__('Program')" :value="$student->user?->program?->name ?? 'N/A'" color="info" />
                    </div>
                    <div class="col-md-6">
                        <x-detail.field icon="flag" :label="__('Enrollment Status')" color="info">
                            @php
                                $enrollmentBadge = match($student->enrollment_status) {
                                    'active' => 'success',
                                    'suspended' => 'danger',
                                    'withdrawn' => 'warning',
                                    'graduated' => 'primary',
                                    default => 'secondary',
                                };
                            @endphp
                            <span class="badge badge-light-{{ $enrollmentBadge }}">{{ ucfirst($student->enrollment_status ?? 'N/A') }}</span>
                        </x-detail.field>
                    </div>
                    <div class="col-md-6">
                        <x-detail.field icon="shield-tick" :label="__('Account Status')" color="info">
                            @if($isSuspended)
                                <span class="badge badge-light-danger">{{ __('Suspended') }}</span>
                            @else
                                <span class="badge badge-light-success">{{ __('Active') }}</span>
                            @endif
                        </x-detail.field>
                    </div>
                    <div class="col-md-6">
                        <x-detail.field icon="setting-2" :label="__('LMS Account')" color="info">
                            @if($lmsStatus['active'])
                                <span class="badge badge-light-success">{{ __('Active') }}</span>
                            @elseif($student->user?->lms_user_id)
                                <span class="badge badge-light-warning">{{ __('Inactive') }}</span>
                                <span class="text-muted fs-8 ms-1">{{ $lmsStatus['reason'] ?? '' }}</span>
                            @else
                                <span class="badge badge-light-secondary">{{ __('No Account') }}</span>
                            @endif
                        </x-detail.field>
                    </div>
                    <div class="col-md-6">
                        <x-detail.field icon="calendar" :label="__('Created At')" :value="$student->created_at->format('F d, Y h:i A')" color="info" />
                    </div>
                    <div class="col-md-6">
                        <x-detail.field icon="time" :label="__('Last Login')" :value="$student->user?->last_login_at ? $student->user->last_login_at->diffForHumans() : __('Never')" color="info" />
                    </div>
                    <div class="col-12">
                        <x-detail.field icon="geolocation" :label="__('Address')" color="info">
                            @if($student->address)
                                {{ $student->address['line1'] ?? $student->address['address_line1'] ?? '' }}
                                @if(!empty($student->address['line2'] ?? $student->address['address_line2'] ?? ''))
                                    <br>{{ $student->address['line2'] ?? $student->address['address_line2'] }}
                                @endif
                                @if(!empty($student->address['city'] ?? ''))
                                    <br>{{ $student->address['city'] ?? '' }}{{ !empty($student->address['province'] ?? $student->address['state_province'] ?? '') ? ', ' . ($student->address['province'] ?? $student->address['state_province'] ?? '') : '' }} {{ $student->address['postal_code'] ?? '' }}
                                @endif
                                @if(!empty($student->address['country'] ?? ''))
                                    <br>{{ $student->address['country'] }}
                                @endif
                            @elseif($application && $application->address_line1)
                                {{ $application->address_line1 }}
                                @if($application->address_line2)<br>{{ $application->address_line2 }}@endif
                                <br>{{ $application->city }}, {{ $application->state_province }} {{ $application->postal_code }}
                                <br>{{ $application->country }}
                            @else
                                <span class="text-gray-500">{{ __('Not provided') }}</span>
                            @endif
                        </x-detail.field>
                    </div>
                </div>
            </x-cards.section>

            {{-- Emergency Contact --}}
            <x-cards.section :title="__('Emergency Contact')" class="mb-5">
                @if($student->emergency_first_name || $student->emergency_last_name || $student->emergency_phone)
                    <div class="row g-4">
                        <div class="col-md-6">
                            <x-detail.field icon="user" :label="__('Contact Name')" :value="trim(($student->emergency_first_name ?? '') . ' ' . ($student->emergency_last_name ?? '')) ?: 'N/A'" color="danger" />
                        </div>
                        <div class="col-md-6">
                            <x-detail.field icon="phone" :label="__('Phone')" :value="$student->emergency_phone ?? 'N/A'" color="danger" />
                        </div>
                        @if($student->emergency_address)
                            <div class="col-md-12">
                                <x-detail.field icon="geolocation" :label="__('Address')" color="danger">
                                    {{ $student->emergency_address['line1'] ?? $student->emergency_address['address_line1'] ?? '' }}
                                    @if(!empty($student->emergency_address['line2'] ?? $student->emergency_address['address_line2'] ?? ''))
                                        <br>{{ $student->emergency_address['line2'] ?? $student->emergency_address['address_line2'] }}
                                    @endif
                                    @if(!empty($student->emergency_address['city'] ?? ''))
                                        <br>{{ $student->emergency_address['city'] ?? '' }}{{ !empty($student->emergency_address['province'] ?? $student->emergency_address['state_province'] ?? '') ? ', ' . ($student->emergency_address['province'] ?? $student->emergency_address['state_province'] ?? '') : '' }} {{ $student->emergency_address['postal_code'] ?? '' }}
                                    @endif
                                    @if(!empty($student->emergency_address['country'] ?? ''))
                                        <br>{{ $student->emergency_address['country'] }}
                                    @endif
                                </x-detail.field>
                            </div>
                        @endif
                    </div>
                @else
                    <div class="text-gray-500 fs-7">{{ __('No emergency contact information provided.') }}</div>
                @endif
            </x-cards.section>

            {{-- Application Details --}}
            @if($application)
                <div class="d-flex align-items-center justify-content-between mb-5 mt-8">
                    <h3 class="fs-2 fw-bold text-gray-900 mb-0">{{ __('Application Details') }}</h3>
                    <div class="d-flex align-items-center gap-3">
                        <span class="badge badge-lg badge-{{ $appConfig['badge'] }} d-flex align-items-center gap-2 px-4 py-3">
                            {!! getIcon($appConfig['icon'], 'fs-5') !!}
                            {{ $appConfig['label'] }}
                        </span>
                        <a href="{{ route('admin.applications.show', $application) }}" class="btn btn-sm btn-light-primary">
                            {!! getIcon('exit-right', 'fs-5 me-1') !!}
                            {{ __('View Full Application') }}
                        </a>
                    </div>
                </div>

                {{-- Tabs Navigation --}}
                <ul class="nav nav-tabs nav-line-tabs nav-line-tabs-2x mb-6 fs-6" role="tablist">
                    <li class="nav-item" role="presentation">
                        <a class="nav-link active d-flex align-items-center gap-2" data-bs-toggle="tab" href="#tab_details" role="tab">
                            {!! getIcon('abstract-26', 'fs-5') !!}
                            {{ __('Program Information') }}
                        </a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a class="nav-link d-flex align-items-center gap-2" data-bs-toggle="tab" href="#tab_education" role="tab">
                            {!! getIcon('teacher', 'fs-5') !!}
                            {{ __('Education & Work') }}
                        </a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a class="nav-link d-flex align-items-center gap-2" data-bs-toggle="tab" href="#tab_documents" role="tab">
                            {!! getIcon('document', 'fs-5') !!}
                            {{ __('Documents') }}
                            <span class="badge badge-sm badge-circle badge-secondary">{{ $docCount }}</span>
                        </a>
                    </li>
                    @if($application->latestContract)
                        <li class="nav-item" role="presentation">
                            <a class="nav-link d-flex align-items-center gap-2" data-bs-toggle="tab" href="#tab_contract" role="tab">
                                {!! getIcon('document', 'fs-5') !!}
                                {{ __('Contract & Payment') }}
                            </a>
                        </li>
                    @endif
                    @if($application->reviewed_at || $application->initial_approved_at)
                        <li class="nav-item" role="presentation">
                            <a class="nav-link d-flex align-items-center gap-2" data-bs-toggle="tab" href="#tab_review" role="tab">
                                {!! getIcon('shield-tick', 'fs-5') !!}
                                {{ __('Review Details') }}
                            </a>
                        </li>
                    @endif
                    @if($application->noa_status)
                        <li class="nav-item" role="presentation">
                            <a class="nav-link d-flex align-items-center gap-2" data-bs-toggle="tab" href="#tab_noa" role="tab">
                                {!! getIcon('document', 'fs-5') !!}
                                {{ __('NOA') }}
                                <span class="badge badge-sm badge-light-{{ $noaConfig['badge'] }}">{{ $noaConfig['label'] }}</span>
                            </a>
                        </li>
                    @endif
                    @if($application->msfaa_status)
                        <li class="nav-item" role="presentation">
                            <a class="nav-link d-flex align-items-center gap-2" data-bs-toggle="tab" href="#tab_msfaa" role="tab">
                                {!! getIcon('verify', 'fs-5') !!}
                                {{ __('MSFAA') }}
                                <span class="badge badge-sm badge-light-{{ $msfaaConfig['badge'] }}">{{ $msfaaConfig['label'] }}</span>
                            </a>
                        </li>
                    @endif
                </ul>

                {{-- Tab Content --}}
                <div class="tab-content">
                    {{-- Personal Details Tab --}}
                    <div class="tab-pane fade show active" id="tab_details" role="tabpanel">
                        {{-- Agent Information --}}
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
                        <x-cards.section :title="__('Program Information')" class="mb-5">
                            <div class="row g-4">
                                <div class="col-md-4">
                                    <x-detail.info-card icon="abstract-26" :label="__('Program')" :value="$appProgramName ?? 'N/A'" color="primary" />
                                </div>
                                <div class="col-md-4">
                                    <x-detail.info-card icon="calendar" :label="__('Preferred Intake')" :value="$appIntakeName ?? $application->preferred_intake ?? 'N/A'" color="primary" />
                                </div>
                                <div class="col-md-4">
                                    <x-detail.info-card icon="people" :label="__('Agency Referral')" color="primary">
                                        @if($application->has_referral)
                                            {{ $application->referral_agency_name }}
                                        @else
                                            <span class="text-gray-500">{{ __('None') }}</span>
                                        @endif
                                    </x-detail.info-card>
                                </div>
                                <div class="col-md-4">
                                    <x-detail.info-card icon="wallet" :label="__('Funding Type')" color="primary">
                                        @if($application->isSelfFunded())
                                            <span class="badge badge-light-warning">{{ __('Self-Funded') }}</span>
                                        @elseif($application->isGovernmentFunded())
                                            <span class="badge badge-light-success">{{ __('Government-Funded') }}</span>
                                        @else
                                            <span class="text-gray-500">N/A</span>
                                        @endif
                                    </x-detail.info-card>
                                </div>
                            </div>
                        </x-cards.section>

                    </div>

                    {{-- Education & Work Tab --}}
                    <div class="tab-pane fade" id="tab_education" role="tabpanel">
                        <x-cards.section :title="__('Education History')" class="mb-5">
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
                                            <x-detail.field icon="bank" :label="__('Institution')" :value="$application->institution_name" color="success" />
                                        </div>
                                        <div class="col-md-6">
                                            <x-detail.field icon="geolocation" :label="__('Country')" :value="$application->education_country" color="success" />
                                        </div>
                                    </div>

                                    @if($application->has_disciplinary_action)
                                        <div class="notice d-flex bg-light-danger rounded border-danger border border-dashed p-4 mt-4">
                                            {!! getIcon('information-5', 'fs-2x text-danger me-3') !!}
                                            <div class="text-danger fw-semibold fs-7">
                                                {{ __('Applicant has reported prior disciplinary action') }}
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </x-cards.section>

                        <x-cards.section :title="__('Work Experience')">
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
                                                <x-detail.field icon="calendar" :label="__('Start Date')" :value="$application->work_start_date ? \Carbon\Carbon::parse($application->work_start_date)->format('M Y') : 'N/A'" color="warning" />
                                            </div>
                                            <div class="col-md-4">
                                                <x-detail.field icon="calendar-tick" :label="__('End Date')" :value="$application->work_end_date ? \Carbon\Carbon::parse($application->work_end_date)->format('M Y') : __('Present')" color="warning" />
                                            </div>
                                            <div class="col-md-4">
                                                <x-detail.field icon="timer" :label="__('Experience')" :value="$application->years_of_experience . ' years'" color="warning" />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <x-tables.empty-state
                                    icon="briefcase"
                                    :title="__('No Work Experience')"
                                    :message="__('The applicant has not provided work experience')"
                                    size="sm"
                                />
                            @endif
                        </x-cards.section>
                    </div>

                    {{-- Documents Tab --}}
                    <div class="tab-pane fade" id="tab_documents" role="tabpanel">
                        <x-cards.section :title="__('Uploaded Documents')">
                            @php
                                $documents = [
                                    ['key' => 'government_id', 'path' => $application->government_id_path, 'label' => __('Government-Issued Photo ID'), 'icon' => 'verify', 'desc' => __('Passport, official national ID')],
                                    ['key' => 'degree_certificate', 'path' => $application->degree_certificate_path, 'label' => __('Degree Certificate'), 'icon' => 'award', 'desc' => __('Academic degree or diploma')],
                                    ['key' => 'transcripts', 'path' => $application->transcripts_path, 'label' => __('Academic Transcripts'), 'icon' => 'document', 'desc' => __('Official academic records')],
                                    ['key' => 'cv', 'path' => $application->cv_path, 'label' => __('Curriculum Vitae'), 'icon' => 'profile-user', 'desc' => __('Resume or CV document')],
                                    ['key' => 'english_test', 'path' => $application->english_test_path, 'label' => __('English Test Results'), 'icon' => 'message-text-2', 'desc' => __('IELTS, TOEFL, or equivalent')],
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
                                                                {{ __('Download') }}
                                                            </a>
                                                            <button type="button"
                                                                    class="btn btn-sm btn-light-info"
                                                                    onclick="previewDocument('{{ route('admin.applications.download', [$application, $doc['key']]) }}?preview=1', '{{ $doc['label'] }}', '{{ strtolower(pathinfo($doc['path'], PATHINFO_EXTENSION)) }}')">
                                                                {!! getIcon('eye', 'fs-5 me-1') !!}
                                                                {{ __('Preview') }}
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
                                    :title="__('No Documents Uploaded')"
                                    :message="__('The applicant has not uploaded any documents')"
                                    size="sm"
                                />
                            @endif
                        </x-cards.section>
                    </div>

                    {{-- Contract & Payment Tab --}}
                    @if($application->latestContract)
                        <div class="tab-pane fade" id="tab_contract" role="tabpanel">
                            <x-cards.section :title="__('Contract Details')" class="mb-5">
                                @php $contract = $application->latestContract; @endphp
                                <div class="row g-4 mb-4">
                                    <div class="col-md-6">
                                        <x-detail.field icon="document" :label="__('Template Used')" :value="$contract->template?->name ?? 'N/A'" color="primary" />
                                    </div>
                                    <div class="col-md-6">
                                        <x-detail.field icon="calendar" :label="__('Issued Date')" :value="$contract->issued_at ? $contract->issued_at->format('F d, Y \a\t h:i A') : 'N/A'" color="primary" />
                                    </div>
                                    <div class="col-md-6">
                                        <x-detail.field icon="profile-user" :label="__('Issued By')" :value="$contract->issuer?->name ?? 'N/A'" color="primary" />
                                    </div>
                                    <div class="col-md-6">
                                        <x-detail.field icon="verify" :label="__('Signed')" color="primary">
                                            @if($contract->isSigned())
                                                <span class="badge badge-light-success">{{ __('Yes') }} - {{ $contract->signed_at?->format('M d, Y') }}</span>
                                            @else
                                                <span class="badge badge-light-warning">{{ __('Pending') }}</span>
                                            @endif
                                        </x-detail.field>
                                    </div>
                                </div>

                                <div class="d-flex gap-3">
                                    @if($contract->generated_pdf_path)
                                        <a href="{{ route('admin.contracts.download-generated', $contract) }}" class="btn btn-sm btn-light-primary">
                                            {!! getIcon('down', 'fs-5 me-1') !!}
                                            {{ __('Download Generated Contract') }}
                                        </a>
                                    @endif
                                    @if($contract->signed_pdf_path)
                                        <a href="{{ route('admin.contracts.download-signed', $contract) }}" class="btn btn-sm btn-light-success">
                                            {!! getIcon('down', 'fs-5 me-1') !!}
                                            {{ __('Download Signed Contract') }}
                                        </a>
                                    @endif
                                </div>
                            </x-cards.section>

                            @if($application->isSelfFunded() && $application->payment_amount)
                                <x-cards.section :title="__('Payment Information')" class="mb-5">
                                    <div class="row g-4">
                                        <div class="col-md-6">
                                            <x-detail.field icon="wallet" :label="__('Payment Amount')" color="warning">
                                                <span class="fw-bolder fs-4">${{ number_format($application->payment_amount, 2) }}</span>
                                            </x-detail.field>
                                        </div>
                                        <div class="col-md-6">
                                            <x-detail.field icon="verify" :label="__('Payment Status')" color="warning">
                                                @if($application->isPaymentApproved() || $application->isApproved())
                                                    <span class="badge badge-light-success">{{ __('Approved') }}</span>
                                                @elseif($application->isPaymentUploaded())
                                                    <span class="badge badge-light-info">{{ __('Receipt Uploaded - Awaiting Review') }}</span>
                                                @elseif($application->isPaymentPending())
                                                    <span class="badge badge-light-warning">{{ __('Awaiting Payment') }}</span>
                                                @endif
                                            </x-detail.field>
                                        </div>
                                    </div>
                                </x-cards.section>
                            @endif

                            @if($application->isSelfFunded() && $application->payment_receipt_path)
                                <x-cards.section :title="__('Payment Receipt')">
                                    <div class="row g-4 mb-4">
                                        <div class="col-md-6">
                                            <x-detail.field icon="calendar" :label="__('Uploaded At')" :value="$application->payment_uploaded_at ? $application->payment_uploaded_at->format('F d, Y \a\t h:i A') : 'N/A'" color="warning" />
                                        </div>
                                        <div class="col-md-6">
                                            <x-detail.field icon="verify" :label="__('Payment Status')" color="warning">
                                                @if($application->isPaymentApproved() || $application->isApproved())
                                                    <span class="badge badge-light-success">{{ __('Approved') }}</span>
                                                @elseif($application->isPaymentUploaded())
                                                    <span class="badge badge-light-info">{{ __('Awaiting Review') }}</span>
                                                @else
                                                    <span class="badge badge-light-warning">{{ __('Pending') }}</span>
                                                @endif
                                            </x-detail.field>
                                        </div>
                                    </div>
                                    <a href="{{ route('admin.applications.payment.download', $application) }}" class="btn btn-sm btn-light-warning">
                                        {!! getIcon('down', 'fs-5 me-1') !!}
                                        {{ __('Download Payment Receipt') }}
                                    </a>
                                </x-cards.section>
                            @endif
                        </div>
                    @endif

                    {{-- Review Details Tab --}}
                    @if($application->reviewed_at || $application->initial_approved_at)
                        <div class="tab-pane fade" id="tab_review" role="tabpanel">
                            @if($application->initial_approved_at)
                                <x-cards.section :title="__('Initial Approval')" class="mb-5">
                                    <div class="row g-4">
                                        <div class="col-md-6">
                                            <x-detail.field icon="profile-user" :label="__('Initially Approved By')" :value="$application->initialApprover->name ?? 'N/A'" color="info" />
                                        </div>
                                        <div class="col-md-6">
                                            <x-detail.field icon="calendar" :label="__('Initial Approval Date')" :value="$application->initial_approved_at->format('F d, Y \a\t h:i A')" color="info" />
                                        </div>
                                    </div>
                                </x-cards.section>
                            @endif

                            @if($application->reviewed_at)
                                <x-cards.section :title="__('Final Review')">
                                    <div class="row g-4">
                                        <div class="col-md-6">
                                            <x-detail.field icon="profile-user" :label="__('Final Reviewed By')" :value="$application->reviewer->name ?? 'N/A'" :color="$application->isApproved() ? 'success' : 'danger'" />
                                        </div>
                                        <div class="col-md-6">
                                            <x-detail.field icon="calendar" :label="__('Final Review Date')" :value="$application->reviewed_at->format('F d, Y \a\t h:i A')" :color="$application->isApproved() ? 'success' : 'danger'" />
                                        </div>

                                        @if($application->admin_notes)
                                            <div class="col-12">
                                                <div class="separator separator-dashed mb-4"></div>
                                                <label class="text-gray-500 fs-8 text-uppercase fw-semibold mb-3 d-block">{{ __('Admin Notes') }}</label>
                                                <div class="bg-light-info rounded p-4">
                                                    <p class="text-gray-800 mb-0">{{ $application->admin_notes }}</p>
                                                </div>
                                            </div>
                                        @endif

                                        @if($application->rejection_reason)
                                            <div class="col-12">
                                                <div class="separator separator-dashed mb-4"></div>
                                                <label class="text-gray-500 fs-8 text-uppercase fw-semibold mb-3 d-block">{{ __('Rejection Reason') }}</label>
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
                            <x-cards.section :title="__('NOA Details')" class="mb-5">
                                <div class="row g-4">
                                    <div class="col-md-6">
                                        <x-detail.field icon="verify" :label="__('NOA Status')" :color="$noaConfig['badge']">
                                            <span class="badge badge-light-{{ $noaConfig['badge'] }}">{{ $noaConfig['label'] }}</span>
                                        </x-detail.field>
                                    </div>

                                    @if($application->noa_requested_at)
                                        <div class="col-md-6">
                                            <x-detail.field icon="profile-user" :label="__('Requested By')" :value="$application->noaRequester->name ?? 'N/A'" color="warning" />
                                        </div>
                                        <div class="col-md-6">
                                            <x-detail.field icon="calendar" :label="__('Requested At')" :value="$application->noa_requested_at->format('F d, Y \a\t h:i A')" color="warning" />
                                        </div>
                                        <div class="col-md-6">
                                            <x-detail.field icon="time" :label="__('Days Elapsed')" :value="($application->noa_elapsed_days ?? 0) . ' days'" color="warning" />
                                        </div>
                                    @endif

                                    @if($application->noa_uploaded_at)
                                        <div class="col-md-6">
                                            <x-detail.field icon="calendar" :label="__('Uploaded At')" :value="$application->noa_uploaded_at->format('F d, Y \a\t h:i A')" color="info" />
                                        </div>
                                    @endif

                                    @if($application->noa_approved_at)
                                        <div class="col-md-6">
                                            <x-detail.field icon="profile-user" :label="__('Approved By')" :value="$application->noaApprover->name ?? 'N/A'" color="success" />
                                        </div>
                                        <div class="col-md-6">
                                            <x-detail.field icon="calendar" :label="__('Approved At')" :value="$application->noa_approved_at->format('F d, Y \a\t h:i A')" color="success" />
                                        </div>
                                    @endif

                                    @if($application->noa_rejection_reason)
                                        <div class="col-12">
                                            <div class="separator separator-dashed mb-4"></div>
                                            <label class="text-gray-500 fs-8 text-uppercase fw-semibold mb-3 d-block">{{ __('Rejection Reason') }}</label>
                                            <div class="bg-light-danger rounded p-4">
                                                <p class="text-danger mb-0">{{ $application->noa_rejection_reason }}</p>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </x-cards.section>

                            @if($application->noa_document_path)
                                <x-cards.section :title="__('NOA Document')">
                                    <div class="d-flex gap-3">
                                        <a href="{{ route('admin.applications.noa.download', $application) }}" class="btn btn-sm btn-light-primary">
                                            {!! getIcon('down', 'fs-5 me-1') !!}
                                            {{ __('Download NOA') }}
                                        </a>
                                        <button type="button"
                                                class="btn btn-sm btn-light-info"
                                                onclick="previewDocument('{{ route('admin.applications.noa.download', $application) }}?preview=1', 'NOA Document', '{{ strtolower(pathinfo($application->noa_document_path, PATHINFO_EXTENSION)) }}')">
                                            {!! getIcon('eye', 'fs-5 me-1') !!}
                                            {{ __('Preview') }}
                                        </button>
                                    </div>
                                </x-cards.section>
                            @endif
                        </div>
                    @endif

                    {{-- MSFAA Tab --}}
                    @if($application->msfaa_status)
                        <div class="tab-pane fade" id="tab_msfaa" role="tabpanel">
                            <x-cards.section :title="__('MSFAA Details')" class="mb-5">
                                <div class="row g-4">
                                    <div class="col-md-6">
                                        <x-detail.field icon="verify" :label="__('MSFAA Status')" :color="$msfaaConfig['badge']">
                                            <span class="badge badge-light-{{ $msfaaConfig['badge'] }}">{{ $msfaaConfig['label'] }}</span>
                                        </x-detail.field>
                                    </div>

                                    @if($application->msfaa_requested_at)
                                        <div class="col-md-6">
                                            <x-detail.field icon="profile-user" :label="__('Requested By')" :value="$application->msfaaRequester->name ?? 'N/A'" color="warning" />
                                        </div>
                                        <div class="col-md-6">
                                            <x-detail.field icon="calendar" :label="__('Requested At')" :value="$application->msfaa_requested_at->format('F d, Y \a\t h:i A')" color="warning" />
                                        </div>
                                        <div class="col-md-6">
                                            <x-detail.field icon="time" :label="__('Days Elapsed')" :value="($application->msfaa_elapsed_days ?? 0) . ' days'" color="warning" />
                                        </div>
                                    @endif

                                    @if($application->msfaa_confirmed_at)
                                        <div class="col-md-6">
                                            <x-detail.field icon="calendar" :label="__('Confirmed At')" :value="$application->msfaa_confirmed_at->format('F d, Y \a\t h:i A')" color="info" />
                                        </div>
                                    @endif

                                    @if($application->msfaa_approved_at)
                                        <div class="col-md-6">
                                            <x-detail.field icon="profile-user" :label="__('Approved By')" :value="$application->msfaaApprover->name ?? 'N/A'" color="success" />
                                        </div>
                                        <div class="col-md-6">
                                            <x-detail.field icon="calendar" :label="__('Approved At')" :value="$application->msfaa_approved_at->format('F d, Y \a\t h:i A')" color="success" />
                                        </div>
                                    @endif

                                    @if($application->msfaa_rejection_reason)
                                        <div class="col-12">
                                            <div class="separator separator-dashed mb-4"></div>
                                            <label class="text-gray-500 fs-8 text-uppercase fw-semibold mb-3 d-block">{{ __('Rejection Reason') }}</label>
                                            <div class="bg-light-danger rounded p-4">
                                                <p class="text-danger mb-0">{{ $application->msfaa_rejection_reason }}</p>
                                            </div>
                                        </div>
                                    @endif

                                    @if($application->msfaa_admin_notes)
                                        <div class="col-12">
                                            <div class="separator separator-dashed mb-4"></div>
                                            <label class="text-gray-500 fs-8 text-uppercase fw-semibold mb-3 d-block">{{ __('Admin Notes') }}</label>
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

            @else
                {{-- No Application --}}
                <div class="card border-0 shadow-sm mb-5">
                    <div class="card-body">
                        <div class="alert alert-warning mb-0">
                            <i class="ki-outline ki-information-5 fs-2 text-warning me-2"></i>
                            <span>{{ __('No application linked to this student.') }}</span>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Accessible Courses --}}
            @if($courses->count() > 0)
                <x-cards.section
                    :title="__('Accessible Courses')"
                    :subtitle="__('Courses available via program membership')"
                    class="mt-5"
                >
                    <div class="table-responsive">
                        <table class="table align-middle table-row-dashed fs-6 gy-5">
                            <thead>
                                <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                                    <th class="min-w-200px">{{ __('Course') }}</th>
                                    <th class="min-w-100px">{{ __('Code') }}</th>
                                    <th class="min-w-100px">{{ __('Instructor') }}</th>
                                </tr>
                            </thead>
                            <tbody class="text-gray-600 fw-semibold">
                                @foreach($courses as $course)
                                <tr>
                                    <td>
                                        <a href="{{ route('admin.programs.courses.show', [$student->user->program, $course]) }}" class="text-gray-800 text-hover-primary fw-bold">
                                            {{ $course->name }}
                                        </a>
                                    </td>
                                    <td>
                                        <span class="badge badge-light-primary">{{ $course->course_code }}</span>
                                    </td>
                                    <td>
                                        @if($course->instructors->whereNull('removed_at')->first())
                                            {{ $course->instructors->whereNull('removed_at')->first()->instructor->name ?? 'N/A' }}
                                        @else
                                            <span class="text-muted">{{ __('No instructor') }}</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </x-cards.section>
            @endif
        </div>

        {{-- Sidebar --}}
        <div class="col-xl-4">
            {{-- Quick Info --}}
            <x-detail.quick-info
                :title="__('Quick Info')"
                icon="information-2"
                :items="[
                    ['label' => __('Student Number'), 'value' => $student->student_number ?? 'N/A', 'badge' => 'primary'],
                    ['label' => __('LMS Account'), 'value' => $lmsStatus['active'] ? __('Active') : ($student->user?->lms_user_id ? __('Inactive') : __('No Account')), 'badge' => $lmsStatus['active'] ? 'success' : ($student->user?->lms_user_id ? 'warning' : 'secondary')],
                    ['label' => __('Account Status'), 'value' => $isSuspended ? __('Suspended') : __('Active'), 'badge' => $isSuspended ? 'danger' : 'success'],
                    ['label' => __('Application'), 'value' => $application ? ucfirst(str_replace('_', ' ', $application->status)) : __('None'), 'badge' => $application ? ($application->isApproved() ? 'success' : 'info') : 'secondary'],
                    ['label' => __('Courses'), 'value' => $courses->count() . ' ' . __('courses'), 'badge' => 'info'],
                ]"
                class="mb-5"
            />

            {{-- Application Link --}}
            @if($application)
                <x-cards.section :title="__('Application')" class="mb-5">
                    <div class="mb-3">
                        <span class="text-gray-500 fs-7">{{ __('Reference:') }}</span>
                        <span class="fw-bold">{{ $application->reference_number }}</span>
                    </div>
                    <div class="mb-4">
                        <span class="text-gray-500 fs-7">{{ __('Status:') }}</span>
                        <span class="badge badge-light-{{ $appConfig['badge'] }}">{{ $appConfig['label'] }}</span>
                    </div>
                    <a href="{{ route('admin.applications.show', $application) }}" class="btn btn-sm btn-light-primary w-100">
                        {!! getIcon('exit-right', 'fs-5 me-1') !!}
                        {{ __('View Full Application') }}
                    </a>
                </x-cards.section>
            @endif

            {{-- Actions --}}
            <x-cards.section :title="__('Actions')" class="mb-5">
                <form action="{{ route('admin.students.destroy', $student) }}" method="POST" onsubmit="return confirm('{{ __('Are you sure you want to delete this student?') }}');">
                    @csrf
                    @method('DELETE')
                    <x-buttons.action-button
                        text="{{ __('Delete Student') }}"
                        icon="trash"
                        color="danger"
                        type="submit"
                        class="w-100"
                    />
                </form>
            </x-cards.section>

            {{-- Back Button --}}
            <a href="{{ route('admin.students.index') }}" class="btn btn-light w-100 d-flex align-items-center justify-content-center gap-2">
                {!! getIcon('left', 'fs-4') !!}
                {{ __('Back to Students') }}
            </a>
        </div>
    </div>

    {{-- Document Preview Modal --}}
    @if($application)
        <div class="modal fade" id="previewModal" tabindex="-1">
            <div class="modal-dialog modal-xl modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="previewTitle"></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body p-0" id="previewBody"></div>
                </div>
            </div>
        </div>

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
                        hidden() {
                            viewer.destroy();
                            img.remove();
                        },
                    });
                    viewer.show();

                } else if (pdfExts.includes(ext)) {
                    window.open(url, '_blank');

                } else {
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

            document.getElementById('previewModal').addEventListener('hidden.bs.modal', function () {
                document.getElementById('previewBody').innerHTML = '';
            });
        </script>
        @endpush
    @endif

</x-default-layout>
