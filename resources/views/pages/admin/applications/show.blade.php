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
            'approved' => ['badge' => 'success', 'icon' => 'check-circle', 'label' => 'Approved'],
            'rejected' => ['badge' => 'danger', 'icon' => 'cross-circle', 'label' => 'Rejected'],
        ];
        $config = $statusConfig[$application->status] ?? $statusConfig['pending'];

        $docCount = collect([
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
                @if($application->reviewed_at || $application->initial_approved_at)
                    <li class="nav-item" role="presentation">
                        <a class="nav-link d-flex align-items-center gap-2" data-bs-toggle="tab" href="#tab_review" role="tab">
                            {!! getIcon('shield-tick', 'fs-5') !!}
                            Review Details
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
                    {{-- Education History --}}
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

                    {{-- Work Experience --}}
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
                {{-- Action Card for Initial Approved --}}
                <div class="card border-0 shadow-sm mb-5">
                    <div class="card-header border-0 bg-light-info py-5">
                        <h3 class="card-title fw-bold text-gray-800">
                            {!! getIcon('shield-tick', 'fs-4 me-2 text-info') !!}
                            Complete Review
                        </h3>
                    </div>
                    <div class="card-body p-6">
                        <div class="notice d-flex bg-light-info rounded border-info border border-dashed p-4 mb-5">
                            {!! getIcon('information-5', 'fs-2x text-info me-3 flex-shrink-0') !!}
                            <div class="text-gray-700 fs-7">
                                This application has been initially approved. Complete the enrollment process or reject if needed.
                            </div>
                        </div>

                        <div class="d-grid gap-3">
                            <button type="button"
                                    class="btn btn-success btn-lg d-flex align-items-center justify-content-center gap-2"
                                    data-bs-toggle="modal"
                                    data-bs-target="#approveModal">
                                {!! getIcon('check-circle', 'fs-3') !!}
                                <span>Final Approve & Create Account</span>
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
                {{-- Status Card for Reviewed --}}
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
                            @else
                                This application cannot be re-approved
                            @endif
                        </p>

                        @if($application->status === 'approved' && $application->createdUser)
                            <div class="mt-5 pt-5 border-top">
                                <a href="{{ route('user-management.users.show', $application->createdUser) }}"
                                   class="btn btn-light w-100 d-flex align-items-center justify-content-center gap-2">
                                    {!! getIcon('profile-user', 'fs-4') !!}
                                    View Student Account
                                </a>
                            </div>
                        @endif
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
            @endif

            {{-- Quick Info Card --}}
            <x-detail.quick-info
                title="Quick Info"
                icon="information-2"
                :items="[
                    ['label' => 'Days Since Submission', 'value' => $application->created_at->diffInDays(now()) . ' days', 'badge' => 'info'],
                    ['label' => 'Documents Uploaded', 'value' => $docCount . ' / 4', 'badge' => $docCount >= 3 ? 'success' : 'warning'],
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

    {{-- Initial Approve Modal --}}
    <div class="modal fade" id="initialApproveModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered mw-650px">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-light-info border-0">
                    <div class="d-flex align-items-center">
                        <div class="symbol symbol-50px me-4">
                            <span class="symbol-label bg-info">
                                {!! getIcon('shield-tick', 'fs-2x text-white') !!}
                            </span>
                        </div>
                        <div>
                            <h2 class="fw-bolder text-gray-800 mb-1">Initial Approve Application</h2>
                            <p class="text-gray-600 fs-7 mb-0">Mark {{ $application->full_name }}'s application for further processing</p>
                        </div>
                    </div>
                    <div class="btn btn-icon btn-sm btn-active-light-primary" data-bs-dismiss="modal">
                        {!! getIcon('cross', 'fs-1') !!}
                    </div>
                </div>

                <form action="{{ route('admin.applications.initial-approve', $application) }}" method="POST">
                    @csrf
                    <div class="modal-body py-8">
                        {{-- What will happen --}}
                        <div class="mb-6">
                            <label class="text-gray-700 fw-bold fs-6 mb-4 d-block">This action will:</label>
                            <div class="d-flex flex-column gap-3">
                                <div class="d-flex align-items-center bg-gray-100 rounded p-3">
                                    {!! getIcon('check-circle', 'fs-4 text-info me-3') !!}
                                    <span class="text-gray-700 fs-7">Mark the application as <strong>Initially Approved</strong></span>
                                </div>
                                <div class="d-flex align-items-center bg-gray-100 rounded p-3">
                                    {!! getIcon('information-5', 'fs-4 text-warning me-3') !!}
                                    <span class="text-gray-700 fs-7"><strong>No student account</strong> will be created yet</span>
                                </div>
                                <div class="d-flex align-items-center bg-gray-100 rounded p-3">
                                    {!! getIcon('information-5', 'fs-4 text-warning me-3') !!}
                                    <span class="text-gray-700 fs-7"><strong>No email</strong> will be sent to the applicant</span>
                                </div>
                            </div>
                        </div>

                        {{-- Admin Notes --}}
                        <div class="mb-5">
                            <label class="form-label text-gray-700 fw-semibold">Admin Notes <span class="text-gray-500 fs-8">(Optional)</span></label>
                            <textarea name="admin_notes" class="form-control form-control-solid" rows="3" placeholder="Add notes about pricing discussions, next steps, etc..."></textarea>
                        </div>

                        {{-- Info --}}
                        <div class="notice d-flex bg-light-info rounded border-info border border-dashed p-4">
                            {!! getIcon('information-5', 'fs-2x text-info me-3 flex-shrink-0') !!}
                            <div class="text-gray-700 fs-7">
                                <strong>Next Step:</strong> Contact the student offline to discuss pricing and enrollment details.
                                Return here to complete the final approval.
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer border-0 pt-0">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-info">
                            {!! getIcon('shield-tick', 'fs-4 me-2') !!}
                            Initial Approve
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Final Approve Modal --}}
    <div class="modal fade" id="approveModal" tabindex="-1" aria-hidden="true">
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
                            <h2 class="fw-bolder text-gray-800 mb-1">Final Approve Application</h2>
                            <p class="text-gray-600 fs-7 mb-0">Complete {{ $application->full_name }}'s enrollment</p>
                        </div>
                    </div>
                    <div class="btn btn-icon btn-sm btn-active-light-primary" data-bs-dismiss="modal">
                        {!! getIcon('cross', 'fs-1') !!}
                    </div>
                </div>

                <form action="{{ route('admin.applications.approve', $application) }}" method="POST">
                    @csrf
                    <div class="modal-body py-8">
                        {{-- What will happen --}}
                        <div class="mb-6">
                            <label class="text-gray-700 fw-bold fs-6 mb-4 d-block">This action will:</label>
                            <div class="d-flex flex-column gap-3">
                                <div class="d-flex align-items-center bg-gray-100 rounded p-3">
                                    {!! getIcon('check-circle', 'fs-4 text-success me-3') !!}
                                    <span class="text-gray-700 fs-7">Create a student account with email <strong>{{ $application->email }}</strong></span>
                                </div>
                                <div class="d-flex align-items-center bg-gray-100 rounded p-3">
                                    {!! getIcon('check-circle', 'fs-4 text-success me-3') !!}
                                    <span class="text-gray-700 fs-7">Send login credentials to the applicant</span>
                                </div>
                                <div class="d-flex align-items-center bg-gray-100 rounded p-3">
                                    {!! getIcon('check-circle', 'fs-4 text-success me-3') !!}
                                    <span class="text-gray-700 fs-7">Enroll in <strong>{{ $application->program_name ?? 'the program' }}</strong></span>
                                </div>
                            </div>
                        </div>

                        {{-- Admin Notes --}}
                        <div class="mb-5">
                            <label class="form-label text-gray-700 fw-semibold">Admin Notes <span class="text-gray-500 fs-8">(Optional)</span></label>
                            <textarea name="admin_notes" class="form-control form-control-solid" rows="3" placeholder="Add any internal notes about this approval...">{{ $application->admin_notes }}</textarea>
                        </div>

                        {{-- Warning --}}
                        <div class="notice d-flex bg-light-info rounded border-info border border-dashed p-4">
                            {!! getIcon('information-5', 'fs-2x text-info me-3 flex-shrink-0') !!}
                            <div class="text-gray-700 fs-7">
                                <strong>Note:</strong> This action cannot be undone. Please ensure all information has been verified.
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer border-0 pt-0">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success">
                            {!! getIcon('check', 'fs-4 me-2') !!}
                            Final Approve & Create Account
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Reject Modal --}}
    <div class="modal fade" id="rejectModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered mw-650px">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-light-danger border-0">
                    <div class="d-flex align-items-center">
                        <div class="symbol symbol-50px me-4">
                            <span class="symbol-label bg-danger">
                                {!! getIcon('cross', 'fs-2x text-white') !!}
                            </span>
                        </div>
                        <div>
                            <h2 class="fw-bolder text-gray-800 mb-1">Reject Application</h2>
                            <p class="text-gray-600 fs-7 mb-0">Reject {{ $application->full_name }}'s application</p>
                        </div>
                    </div>
                    <div class="btn btn-icon btn-sm btn-active-light-primary" data-bs-dismiss="modal">
                        {!! getIcon('cross', 'fs-1') !!}
                    </div>
                </div>

                <form action="{{ route('admin.applications.reject', $application) }}" method="POST" x-data="{ reason: '', minLength: 20, maxLength: 1000 }">
                    @csrf
                    <div class="modal-body py-8">
                        {{-- Rejection Reason --}}
                        <div class="mb-5">
                            <label class="form-label text-gray-700 fw-semibold required">Rejection Reason</label>
                            <textarea
                                name="rejection_reason"
                                class="form-control form-control-solid @error('rejection_reason') is-invalid @enderror"
                                rows="5"
                                placeholder="Provide a clear and detailed reason for rejection..."
                                x-model="reason"
                                required
                                minlength="20"
                                maxlength="1000"></textarea>
                            @error('rejection_reason')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="d-flex justify-content-between mt-2">
                                <span class="text-gray-500 fs-8">
                                    <span x-text="reason.length"></span> / <span x-text="maxLength"></span> characters
                                </span>
                                <span x-show="reason.length > 0 && reason.length < minLength" class="text-danger fs-8">
                                    Minimum <span x-text="minLength"></span> characters required
                                </span>
                            </div>
                        </div>

                        {{-- Admin Notes --}}
                        <div class="mb-5">
                            <label class="form-label text-gray-700 fw-semibold">Admin Notes <span class="text-gray-500 fs-8">(Optional)</span></label>
                            <textarea name="admin_notes" class="form-control form-control-solid" rows="3" placeholder="Add any internal notes..."></textarea>
                        </div>

                        {{-- Warning --}}
                        <div class="notice d-flex bg-light-danger rounded border-danger border border-dashed p-4">
                            {!! getIcon('information-5', 'fs-2x text-danger me-3 flex-shrink-0') !!}
                            <div class="text-gray-700 fs-7">
                                <strong>Warning:</strong> This action cannot be undone. The applicant will be notified of the rejection.
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer border-0 pt-0">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger" :disabled="reason.length < minLength">
                            {!! getIcon('cross', 'fs-4 me-2') !!}
                            Reject Application
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Preview Modal --}}
    <div class="modal fade" id="previewModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header border-0 bg-light-primary">
                    <h2 class="fw-bold text-gray-800" id="previewTitle">Document Preview</h2>
                    <div class="btn btn-icon btn-sm btn-active-light-primary" data-bs-dismiss="modal">
                        {!! getIcon('cross', 'fs-1') !!}
                    </div>
                </div>
                <div class="modal-body p-0" id="previewBody" style="min-height: 600px;">
                    <!-- Dynamic content loaded here -->
                </div>
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

            // Show loading state
            bodyEl.innerHTML = `
                <div class="d-flex flex-column align-items-center justify-content-center" style="min-height: 600px;">
                    <div class="spinner-border text-gray-600 mb-3" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <span class="text-gray-600">Loading document...</span>
                </div>
            `;

            modal.show();

            // Determine file type and render
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
    @endpush
</x-default-layout>
