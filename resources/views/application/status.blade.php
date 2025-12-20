@extends('application.layout')

@section('page-title', 'Application Status')
@section('page-subtitle', $application->reference_number)

@push('styles')
<style>
    .timeline {
        position: relative;
        padding-left: 60px;
    }

    .timeline::before {
        content: '';
        position: absolute;
        left: 24px;
        top: 10px;
        bottom: 10px;
        width: 3px;
        background: #e4e6ef;
    }

    .timeline-item {
        position: relative;
        padding-bottom: 50px;
    }

    .timeline-item:last-child {
        padding-bottom: 0;
    }

    .timeline-marker {
        position: absolute;
        left: -35px;
        top: 0;
        width: 50px;
        height: 50px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        background: white;
        border: 4px solid #e4e6ef;
        z-index: 1;
    }

    .timeline-marker.completed {
        background: #50cd89;
        border-color: #50cd89;
    }

    .timeline-marker.rejected {
        background: #f1416c;
        border-color: #f1416c;
    }

    .timeline-marker.pending {
        background: white;
        border-color: #d1d5db;
    }

    .timeline-content {
        background: #f9fafb;
        border: 1px solid #e4e6ef;
        border-radius: 0.75rem;
        padding: 1.5rem;
    }
</style>
@endpush

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-10">
        <!-- Back Button -->
        <div class="mb-6">
            <a href="{{ route('application.status') }}" class="btn btn-light-primary">
                <i class="ki-outline ki-arrow-left fs-3 me-2"></i>
                Check Another Application
            </a>
        </div>

        <!-- Status Alert Messages -->
        @if($application->status === 'approved')
            <div class="alert alert-success border-success d-flex align-items-center p-8 mb-10">
                <i class="ki-outline ki-shield-tick fs-3tx text-success me-6"></i>
                <div class="d-flex flex-column">
                    <h4 class="mb-3 text-success">
                        <i class="ki-outline ki-award me-2"></i>
                        Congratulations, {{ $application->first_name }}!
                    </h4>
                    <span class="fs-5 text-gray-800 mb-3">
                        Your application has been <strong>approved</strong>. We're excited to welcome you to our institution!
                    </span>
                    <div class="separator border-success my-4"></div>
                    <div class="d-flex align-items-start">
                        <i class="ki-outline ki-sms fs-2tx text-success me-4"></i>
                        <div>
                            <h6 class="fw-bold mb-2">Account Created - Check Your Email</h6>
                            <span class="text-gray-700">
                                Login credentials have been sent to <strong class="text-gray-900">{{ $application->email }}</strong>.
                                Please check your inbox (and spam folder) for instructions to access the LMS.
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        @elseif($application->status === 'rejected')
            <div class="alert alert-danger border-danger d-flex align-items-center p-8 mb-10">
                <i class="ki-outline ki-cross-circle fs-3tx text-danger me-6"></i>
                <div class="d-flex flex-column">
                    <h4 class="mb-3 text-danger">Application Decision</h4>
                    <span class="fs-5 text-gray-800 mb-3">
                        Unfortunately, your application was not approved at this time.
                    </span>
                    @if($application->rejection_reason)
                        <div class="bg-light-danger border border-danger border-dashed rounded p-5 mb-5">
                            <h6 class="fw-bold text-gray-900 mb-2">
                                <i class="ki-outline ki-information-5 me-2"></i>Reason:
                            </h6>
                            <span class="text-gray-800">{{ $application->rejection_reason }}</span>
                        </div>
                    @endif
                    <div class="separator border-danger my-4"></div>
                    <span class="text-gray-700">
                        <i class="ki-outline ki-arrows-circle me-2"></i>
                        You are welcome to <a href="{{ route('application.step1') }}" class="fw-bold text-primary">reapply for future terms</a>.
                        We appreciate your interest in our institution.
                    </span>
                </div>
            </div>
        @elseif($application->status === 'initial_approved')
            <div class="alert alert-info border-info d-flex align-items-center p-8 mb-10">
                <i class="ki-outline ki-shield-tick fs-3tx text-info me-6"></i>
                <div class="d-flex flex-column">
                    <h4 class="mb-3 text-info">Application Under Consideration</h4>
                    <span class="fs-5 text-gray-800 mb-3">
                        Great news! Your application has passed initial review and is being considered for enrollment.
                    </span>
                    <div class="separator border-info my-4"></div>
                    <div class="d-flex align-items-start">
                        <i class="ki-outline ki-phone fs-2tx text-info me-4"></i>
                        <div>
                            <h6 class="fw-bold mb-2">Expect Contact Soon</h6>
                            <span class="text-gray-700">
                                Our admissions team will contact you at <strong class="text-gray-900">{{ $application->email }}</strong>
                                or <strong class="text-gray-900">{{ $application->phone }}</strong>
                                to discuss next steps including enrollment details and tuition.
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        @elseif($application->status === 'pending')
            <div class="alert alert-warning border-warning d-flex align-items-center p-8 mb-10">
                <i class="ki-outline ki-time fs-3tx text-warning me-6"></i>
                <div class="d-flex flex-column">
                    <h4 class="mb-3 text-warning">Application Under Review</h4>
                    <span class="fs-5 text-gray-800">
                        Your application is currently being reviewed by our admissions team.
                        We typically process applications within 3-5 business days.
                        You will receive an email notification once a decision has been made.
                    </span>
                </div>
            </div>
        @endif

        <!-- Application Summary Card -->
        <div class="card shadow-sm mb-10">
            <div class="card-header" style="background: linear-gradient(135deg, #12294C 0%, #1e3a5f 100%);">
                <div class="card-title">
                    <h3 class="text-white">
                        <i class="ki-outline ki-document text-white fs-2 me-2"></i>
                        Application Summary
                    </h3>
                </div>
                @php
                    $statusConfig = [
                        'pending' => ['class' => 'badge-warning', 'label' => 'Pending Review', 'icon' => 'time'],
                        'initial_approved' => ['class' => 'badge-info', 'label' => 'Under Consideration', 'icon' => 'shield-tick'],
                        'approved' => ['class' => 'badge-success', 'label' => 'Approved', 'icon' => 'check-circle'],
                        'rejected' => ['class' => 'badge-danger', 'label' => 'Not Approved', 'icon' => 'cross-circle'],
                    ];
                    $config = $statusConfig[$application->status] ?? ['class' => 'badge-light', 'label' => ucfirst($application->status), 'icon' => 'information'];
                @endphp
                <div class="card-toolbar">
                    <span class="badge {{ $config['class'] }} px-5 py-3">
                        <i class="ki-outline ki-{{ $config['icon'] }} fs-2 me-2"></i>
                        <span class="fs-5">{{ $config['label'] }}</span>
                    </span>
                </div>
            </div>
            <div class="card-body p-10">
                <div class="row g-8">
                    <!-- Left Column -->
                    <div class="col-md-6">
                        <div class="mb-8">
                            <div class="fw-bold text-gray-500 mb-2">APPLICANT NAME</div>
                            <div class="fs-4 fw-bold text-gray-900">
                                {{ $application->first_name }} {{ $application->middle_name }} {{ $application->last_name }}
                            </div>
                        </div>
                        <div class="mb-8">
                            <div class="fw-bold text-gray-500 mb-2">REFERENCE NUMBER</div>
                            <div class="badge badge-light-primary px-4 py-3 fs-4">
                                {{ $application->reference_number }}
                            </div>
                        </div>
                        <div>
                            <div class="fw-bold text-gray-500 mb-2">EMAIL ADDRESS</div>
                            <div class="fs-5 text-gray-800">
                                <i class="ki-outline ki-sms me-2 text-gray-500"></i>{{ $application->email }}
                            </div>
                        </div>
                    </div>

                    <!-- Right Column -->
                    <div class="col-md-6">
                        <div class="mb-8">
                            <div class="fw-bold text-gray-500 mb-2">PROGRAM APPLIED FOR</div>
                            <div class="fs-5 fw-semibold text-gray-900">
                                <i class="ki-outline ki-book me-2 text-primary"></i>
                                {{ $application->program_name ?? 'N/A' }}
                            </div>
                        </div>
                        <div class="mb-8">
                            <div class="fw-bold text-gray-500 mb-2">APPLICATION SUBMITTED</div>
                            <div class="fs-5 text-gray-800">
                                <i class="ki-outline ki-calendar me-2 text-gray-500"></i>
                                {{ $application->submitted_at ? $application->submitted_at->format('F j, Y \a\t g:i A') : 'Not submitted' }}
                            </div>
                        </div>
                        <div>
                            <div class="fw-bold text-gray-500 mb-2">LAST UPDATED</div>
                            <div class="fs-5 text-gray-800">
                                <i class="ki-outline ki-arrows-circle me-2 text-gray-500"></i>
                                {{ $application->updated_at->format('F j, Y \a\t g:i A') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Application Timeline -->
        <div class="card shadow-sm mb-10">
            <div class="card-header bg-light border-bottom-0">
                <div class="card-title">
                    <h3 class="fw-bold text-gray-900">
                        <i class="ki-outline ki-graph-2 text-primary fs-2 me-2"></i>
                        Application Timeline
                    </h3>
                </div>
            </div>
            <div class="card-body p-10">
                <div class="timeline">
                    <!-- Submitted Step -->
                    <div class="timeline-item">
                        <div class="timeline-marker {{ $application->submitted_at ? 'completed' : 'pending' }}">
                            @if($application->submitted_at)
                                <i class="ki-outline ki-check fs-2x text-white"></i>
                            @else
                                <i class="ki-outline ki-abstract-8 fs-2x text-gray-400"></i>
                            @endif
                        </div>
                        <div class="timeline-content">
                            <h4 class="fw-bold mb-3">
                                <i class="ki-outline ki-send me-2"></i>
                                Application Submitted
                            </h4>
                            @if($application->submitted_at)
                                <div class="text-gray-700 mb-1">
                                    <i class="ki-outline ki-calendar me-2"></i>
                                    {{ $application->submitted_at->format('l, F j, Y') }}
                                </div>
                                <div class="text-gray-600 fs-6">
                                    <i class="ki-outline ki-time me-2"></i>
                                    {{ $application->submitted_at->format('g:i A') }}
                                </div>
                            @else
                                <div class="text-gray-600">Application not yet submitted</div>
                            @endif
                        </div>
                    </div>

                    <!-- Initial Review Step -->
                    <div class="timeline-item">
                        @php
                            $initialApprovalComplete = $application->initial_approved_at !== null;
                        @endphp
                        <div class="timeline-marker {{ $initialApprovalComplete ? 'completed' : 'pending' }}" style="{{ $initialApprovalComplete ? 'background: #009ef7; border-color: #009ef7;' : '' }}">
                            @if($initialApprovalComplete)
                                <i class="ki-outline ki-check fs-2x text-white"></i>
                            @else
                                <i class="ki-outline ki-abstract-8 fs-2x text-gray-400"></i>
                            @endif
                        </div>
                        <div class="timeline-content">
                            <h4 class="fw-bold mb-3">
                                <i class="ki-outline ki-shield-tick me-2"></i>
                                Initial Review
                            </h4>
                            @if($initialApprovalComplete)
                                <div class="mb-3">
                                    <span class="badge badge-info px-4 py-2">
                                        <i class="ki-outline ki-check me-2"></i>
                                        Passed
                                    </span>
                                </div>
                                <div class="text-gray-700 mb-1">
                                    <i class="ki-outline ki-calendar me-2"></i>
                                    {{ $application->initial_approved_at->format('l, F j, Y') }}
                                </div>
                                <div class="text-gray-600 fs-6">
                                    <i class="ki-outline ki-time me-2"></i>
                                    {{ $application->initial_approved_at->format('g:i A') }}
                                </div>
                            @elseif($application->status === 'rejected')
                                <div class="text-gray-600 mb-2">
                                    <i class="ki-outline ki-minus me-2"></i>
                                    Skipped
                                </div>
                            @else
                                <div class="text-gray-700 mb-2">
                                    <i class="ki-outline ki-loading me-2"></i>
                                    Awaiting initial review
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Final Decision Step -->
                    <div class="timeline-item">
                        <div class="timeline-marker {{ in_array($application->status, ['approved', 'rejected']) ? ($application->status === 'approved' ? 'completed' : 'rejected') : 'pending' }}">
                            @if($application->status === 'approved')
                                <i class="ki-outline ki-check fs-2x text-white"></i>
                            @elseif($application->status === 'rejected')
                                <i class="ki-outline ki-cross fs-2x text-white"></i>
                            @else
                                <i class="ki-outline ki-abstract-8 fs-2x text-gray-400"></i>
                            @endif
                        </div>
                        <div class="timeline-content">
                            <h4 class="fw-bold mb-3">
                                <i class="ki-outline ki-verify me-2"></i>
                                Final Decision
                            </h4>
                            @if(in_array($application->status, ['approved', 'rejected']))
                                <div class="mb-3">
                                    <span class="badge {{ $config['class'] }} px-4 py-2">
                                        <i class="ki-outline ki-{{ $config['icon'] }} me-2"></i>
                                        {{ $config['label'] }}
                                    </span>
                                </div>
                                @if($application->reviewed_at)
                                    <div class="text-gray-700 mb-1">
                                        <i class="ki-outline ki-calendar me-2"></i>
                                        {{ $application->reviewed_at->format('l, F j, Y') }}
                                    </div>
                                    <div class="text-gray-600 fs-6">
                                        <i class="ki-outline ki-time me-2"></i>
                                        {{ $application->reviewed_at->format('g:i A') }}
                                    </div>
                                @endif
                            @elseif($application->status === 'initial_approved')
                                <div class="text-gray-700 mb-2">
                                    <i class="ki-outline ki-phone me-2"></i>
                                    Our team will contact you soon
                                </div>
                                <div class="text-gray-600 fs-6">
                                    To discuss enrollment details and tuition
                                </div>
                            @else
                                <div class="text-gray-700 mb-2">
                                    <i class="ki-outline ki-loading me-2"></i>
                                    Pending initial review completion
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="text-center mb-10">
            <a href="{{ route('application.status') }}" class="btn btn-primary btn-lg">
                <i class="ki-outline ki-magnifier fs-3 me-2"></i>
                Check Another Application
            </a>
            @if($application->status === 'rejected')
                <a href="{{ route('application.step1') }}" class="btn btn-light-primary btn-lg ms-3">
                    <i class="ki-outline ki-arrows-circle fs-3 me-2"></i>
                    Submit New Application
                </a>
            @endif
        </div>
    </div>
</div>
@endsection
