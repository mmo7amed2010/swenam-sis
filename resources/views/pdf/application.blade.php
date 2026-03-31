<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Application - {{ $application->full_name }}</title>
    <style>
        @page {
            margin: 0;
        }
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 10px;
            color: #1a1a1a;
            line-height: 1.4;
            padding: 15mm 12mm 15mm 12mm;
        }

        /* ========== HEADER ========== */
        .document-header {
            border-bottom: 3px double #1a365d;
            padding-bottom: 12px;
            margin-bottom: 20px;
        }
        .header-table {
            width: 100%;
            border-collapse: collapse;
        }
        .header-logo {
            width: 80px;
            vertical-align: middle;
            padding-right: 12px;
        }
        .header-logo img {
            height: 80px;
            width: auto;
        }
        .header-center {
            vertical-align: top;
        }
        .document-title {
            font-size: 18px;
            font-weight: bold;
            color: #1a1a1a;
            margin: 4px 0;
            border-bottom: 1px solid #cbd5e0;
            padding-bottom: 4px;
        }
        .student-info-header {
            font-size: 10px;
            color: #4a5568;
        }
        .header-right {
            width: 130px;
            vertical-align: top;
            text-align: right;
            font-size: 8px;
            color: #4a5568;
            border-left: 1px solid #e2e8f0;
            padding-left: 10px;
        }
        .header-right-row {
            margin-bottom: 4px;
        }
        .header-right-label {
            font-weight: bold;
            color: #2d3748;
        }

        /* ========== SECTION TITLES ========== */
        .section-title {
            font-size: 11px;
            font-weight: bold;
            color: #1a365d;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 2px solid #1a365d;
            border-left: 4px solid #1a365d;
            padding: 5px 0 5px 10px;
            margin-bottom: 12px;
            margin-top: 18px;
            background-color: #f7fafc;
        }
        .section-title:first-of-type {
            margin-top: 0;
        }

        /* ========== INFO TABLE ========== */
        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        .info-table td {
            padding: 6px 10px;
            border: 1px solid #e2e8f0;
            font-size: 9px;
        }
        .info-table .info-label {
            background-color: #f7fafc;
            font-weight: bold;
            color: #2d3748;
            width: 140px;
        }

        /* ========== STATUS BADGE ========== */
        .status-badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 4px;
            font-size: 9px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .status-pending { background-color: #fef3c7; color: #92400e; }
        .status-approved { background-color: #d1fae5; color: #065f46; }
        .status-rejected { background-color: #fee2e2; color: #991b1b; }
        .status-info { background-color: #dbeafe; color: #1e40af; }

        /* ========== FOOTER ========== */
        .document-footer {
            margin-top: 25px;
            padding-top: 10px;
            border-top: 2px solid #1a365d;
        }
        .footer-table {
            width: 100%;
            border-collapse: collapse;
        }
        .footer-left {
            font-size: 8px;
            color: #718096;
            vertical-align: bottom;
        }
        .footer-center {
            text-align: center;
            font-size: 8px;
            color: #718096;
            vertical-align: bottom;
        }
        .footer-right {
            text-align: right;
            font-size: 8px;
            color: #718096;
            vertical-align: bottom;
        }
    </style>
</head>
<body>
    {{-- Document Header --}}
    <div class="document-header">
        <table class="header-table">
            <tr>
                <td class="header-logo">
                    <img src="{{ public_path('assets/media/logos/swenam_vertical_logo.png') }}" alt="Institution Logo">
                </td>
                <td class="header-center">
                    <div class="document-title">Student Application</div>
                    <div class="student-info-header">
                        <strong>Applicant:</strong> {{ $application->full_name }} &nbsp;|&nbsp;
                        <strong>Ref:</strong> {{ $application->reference_number }}
                    </div>
                </td>
                <td class="header-right">
                    <div class="header-right-row">
                        <span class="header-right-label">Export Date:</span><br>
                        {{ $generatedAt->format('M d, Y') }}
                    </div>
                    <div class="header-right-row">
                        <span class="header-right-label">Program:</span><br>
                        {{ $application->program_name ?? 'N/A' }}
                    </div>
                    <div class="header-right-row">
                        <span class="header-right-label">Status:</span><br>
                        {{ ucwords(str_replace('_', ' ', $application->status)) }}
                    </div>
                </td>
            </tr>
        </table>
    </div>

    {{-- Application Status --}}
    <div class="section-title">Application Status</div>
    <table class="info-table">
        <tr>
            <td class="info-label">Status</td>
            <td>
                @php
                    $statusClass = match($application->status) {
                        'approved', 'payment_approved', 'contract_approved' => 'status-approved',
                        'rejected' => 'status-rejected',
                        'pending' => 'status-pending',
                        default => 'status-info',
                    };
                @endphp
                <span class="status-badge {{ $statusClass }}">{{ ucwords(str_replace('_', ' ', $application->status)) }}</span>
            </td>
            <td class="info-label">Submitted</td>
            <td>{{ $application->created_at->format('M d, Y h:i A') }}</td>
        </tr>
        <tr>
            <td class="info-label">Funding Type</td>
            <td>{{ $application->funding_type ? ucwords(str_replace('_', ' ', $application->funding_type)) : 'N/A' }}</td>
            <td class="info-label">Agency Referral</td>
            <td>{{ $application->has_referral ? $application->referral_agency_name : 'None' }}</td>
        </tr>
        @if($application->initial_approved_at)
        <tr>
            <td class="info-label">Initial Approved</td>
            <td>{{ $application->initial_approved_at->format('M d, Y h:i A') }}</td>
            <td class="info-label">Approved By</td>
            <td>{{ $application->initialApprover->name ?? 'N/A' }}</td>
        </tr>
        @endif
        @if($application->reviewed_at)
        <tr>
            <td class="info-label">Final Reviewed</td>
            <td>{{ $application->reviewed_at->format('M d, Y h:i A') }}</td>
            <td class="info-label">Reviewed By</td>
            <td>{{ $application->reviewer->name ?? 'N/A' }}</td>
        </tr>
        @endif
        @if($application->isRejected() && $application->rejection_reason)
        <tr>
            <td class="info-label">Rejection Reason</td>
            <td colspan="3">{{ $application->rejection_reason }}</td>
        </tr>
        @endif
        @if($application->admin_notes)
        <tr>
            <td class="info-label">Admin Notes</td>
            <td colspan="3">{{ $application->admin_notes }}</td>
        </tr>
        @endif
    </table>

    {{-- Agent Information --}}
    @if($application->agent_id && $application->agent)
    <div class="section-title">Agent Information</div>
    <table class="info-table">
        <tr>
            <td class="info-label">Agent Name</td>
            <td>{{ $application->agent->name ?? ($application->agent->first_name . ' ' . $application->agent->last_name) }}</td>
            <td class="info-label">Agent Email</td>
            <td>{{ $application->agent->email }}</td>
        </tr>
    </table>
    @endif

    {{-- Program Information --}}
    <div class="section-title">Program Information</div>
    <table class="info-table">
        <tr>
            <td class="info-label">Program</td>
            <td>{{ $application->program_name ?? 'N/A' }}</td>
            <td class="info-label">Preferred Intake</td>
            <td>{{ $application->intake_name ?? $application->preferred_intake ?? 'N/A' }}</td>
        </tr>
    </table>

    {{-- Personal Information --}}
    <div class="section-title">Personal Information</div>
    <table class="info-table">
        <tr>
            <td class="info-label">First Name</td>
            <td>{{ $application->first_name }}</td>
            <td class="info-label">Last Name</td>
            <td>{{ $application->last_name }}</td>
        </tr>
        <tr>
            <td class="info-label">Email</td>
            <td>{{ $application->email }}</td>
            <td class="info-label">Phone</td>
            <td>{{ $application->phone }}</td>
        </tr>
        <tr>
            <td class="info-label">Date of Birth</td>
            <td>{{ $application->date_of_birth ? $application->date_of_birth->format('M d, Y') : 'N/A' }}</td>
            <td class="info-label">Primary Language</td>
            <td>{{ $application->primary_language ?? 'N/A' }}</td>
        </tr>
        <tr>
            <td class="info-label">Country of Citizenship</td>
            <td>{{ $application->country_of_citizenship ?? 'N/A' }}</td>
            <td class="info-label">Residency Status</td>
            <td>{{ $application->residency_status ? ucwords(str_replace('_', ' ', $application->residency_status)) : 'N/A' }}</td>
        </tr>
        <tr>
            <td class="info-label">Address</td>
            <td colspan="3">
                {{ $application->address_line1 }}
                @if($application->address_line2), {{ $application->address_line2 }}@endif
                <br>
                {{ $application->city }}{{ $application->state_province ? ', ' . $application->state_province : '' }}
                {{ $application->postal_code }}
                @if($application->country)<br>{{ $application->country }}@endif
            </td>
        </tr>
    </table>

    {{-- Education History --}}
    <div class="section-title">Education History</div>
    <table class="info-table">
        <tr>
            <td class="info-label">Highest Education Level</td>
            <td>{{ $application->highest_education_level ? ucwords(str_replace('_', ' ', $application->highest_education_level)) : 'N/A' }}</td>
            <td class="info-label">Field of Study</td>
            <td>{{ $application->education_field ?? 'N/A' }}</td>
        </tr>
        <tr>
            <td class="info-label">Institution Name</td>
            <td>{{ $application->institution_name ?? 'N/A' }}</td>
            <td class="info-label">Country of Education</td>
            <td>{{ $application->education_country ?? 'N/A' }}</td>
        </tr>
        <tr>
            <td class="info-label">Completed</td>
            <td>{{ $application->education_completed ? ucwords(str_replace('_', ' ', $application->education_completed)) : 'N/A' }}</td>
            <td class="info-label">Disciplinary Action</td>
            <td>{{ $application->has_disciplinary_action ? 'Yes' : 'No' }}</td>
        </tr>
    </table>

    {{-- Work Experience --}}
    <div class="section-title">Work Experience</div>
    @if($application->has_work_experience)
    <table class="info-table">
        <tr>
            <td class="info-label">Position Title</td>
            <td>{{ $application->position_title ?? 'N/A' }}</td>
            <td class="info-label">Position Level</td>
            <td>{{ $application->position_level ?? 'N/A' }}</td>
        </tr>
        <tr>
            <td class="info-label">Organization</td>
            <td>{{ $application->organization_name ?? 'N/A' }}</td>
            <td class="info-label">Years of Experience</td>
            <td>{{ $application->years_of_experience ?? 'N/A' }}</td>
        </tr>
        <tr>
            <td class="info-label">Start Date</td>
            <td>{{ $application->work_start_date ? $application->work_start_date->format('M d, Y') : 'N/A' }}</td>
            <td class="info-label">End Date</td>
            <td>{{ $application->work_end_date ? $application->work_end_date->format('M d, Y') : 'Present' }}</td>
        </tr>
    </table>
    @else
    <table class="info-table">
        <tr>
            <td style="text-align: center; color: #718096; padding: 10px;">No work experience reported</td>
        </tr>
    </table>
    @endif

    {{-- Documents --}}
    <div class="section-title">Supporting Documents</div>
    <table class="info-table">
        <tr>
            <td class="info-label">Government-Issued Photo ID</td>
            <td>{{ $application->government_id_path ? 'Uploaded' : 'Not Uploaded' }}</td>
            <td class="info-label">Degree Certificate</td>
            <td>{{ $application->degree_certificate_path ? 'Uploaded' : 'Not Uploaded' }}</td>
        </tr>
        <tr>
            <td class="info-label">Academic Transcripts</td>
            <td>{{ $application->transcripts_path ? 'Uploaded' : 'Not Uploaded' }}</td>
            <td class="info-label">Curriculum Vitae</td>
            <td>{{ $application->cv_path ? 'Uploaded' : 'Not Uploaded' }}</td>
        </tr>
        <tr>
            <td class="info-label">English Test Results</td>
            <td colspan="3">{{ $application->english_test_path ? 'Uploaded' : 'Not Uploaded' }}</td>
        </tr>
    </table>

    {{-- Contract Information --}}
    @if($application->contract_sent_at || $application->latestContract)
    <div class="section-title">Contract & Payment</div>
    <table class="info-table">
        @if($application->contract_sent_at)
        <tr>
            <td class="info-label">Contract Sent</td>
            <td>{{ $application->contract_sent_at->format('M d, Y h:i A') }}</td>
            <td class="info-label">Contract Status</td>
            <td>
                @if($application->contract_approved_at)
                    <span class="status-badge status-approved">Approved</span>
                @elseif($application->contract_uploaded_at)
                    <span class="status-badge status-info">Uploaded</span>
                @else
                    <span class="status-badge status-pending">Pending</span>
                @endif
            </td>
        </tr>
        @endif
        @if($application->contract_approved_at)
        <tr>
            <td class="info-label">Contract Approved</td>
            <td colspan="3">{{ $application->contract_approved_at->format('M d, Y h:i A') }}</td>
        </tr>
        @endif
        @if($application->isSelfFunded() && $application->payment_amount)
        <tr>
            <td class="info-label">Payment Amount</td>
            <td>${{ number_format($application->payment_amount, 2) }}</td>
            <td class="info-label">Payment Status</td>
            <td>
                @if($application->payment_approved_at)
                    <span class="status-badge status-approved">Approved</span>
                @elseif($application->payment_uploaded_at)
                    <span class="status-badge status-info">Uploaded</span>
                @else
                    <span class="status-badge status-pending">Pending</span>
                @endif
            </td>
        </tr>
        @endif
    </table>
    @endif

    {{-- NOA Information --}}
    @if($application->noa_status)
    <div class="section-title">NOA (Notice of Acceptance)</div>
    <table class="info-table">
        <tr>
            <td class="info-label">NOA Status</td>
            <td>
                @php
                    $noaBadge = match($application->noa_status) {
                        'approved' => 'status-approved',
                        'rejected' => 'status-rejected',
                        'requested' => 'status-pending',
                        default => 'status-info',
                    };
                @endphp
                <span class="status-badge {{ $noaBadge }}">{{ ucwords($application->noa_status) }}</span>
            </td>
            <td class="info-label">NOA Requested</td>
            <td>{{ $application->noa_requested_at ? $application->noa_requested_at->format('M d, Y') : 'N/A' }}</td>
        </tr>
        @if($application->noa_approved_at)
        <tr>
            <td class="info-label">NOA Approved</td>
            <td colspan="3">{{ $application->noa_approved_at->format('M d, Y h:i A') }}</td>
        </tr>
        @endif
    </table>
    @endif

    {{-- MSFAA Information --}}
    @if($application->msfaa_status)
    <div class="section-title">MSFAA</div>
    <table class="info-table">
        <tr>
            <td class="info-label">MSFAA Status</td>
            <td>
                @php
                    $msfaaBadge = match($application->msfaa_status) {
                        'approved' => 'status-approved',
                        'rejected' => 'status-rejected',
                        'requested' => 'status-pending',
                        default => 'status-info',
                    };
                @endphp
                <span class="status-badge {{ $msfaaBadge }}">{{ ucwords($application->msfaa_status) }}</span>
            </td>
            <td class="info-label">MSFAA Requested</td>
            <td>{{ $application->msfaa_requested_at ? $application->msfaa_requested_at->format('M d, Y') : 'N/A' }}</td>
        </tr>
        @if($application->msfaa_approved_at)
        <tr>
            <td class="info-label">MSFAA Approved</td>
            <td colspan="3">{{ $application->msfaa_approved_at->format('M d, Y h:i A') }}</td>
        </tr>
        @endif
    </table>
    @endif

    {{-- Footer --}}
    <div class="document-footer">
        <table class="footer-table">
            <tr>
                <td class="footer-left">
                    Ref: {{ $application->reference_number }}
                </td>
                <td class="footer-center">
                    This document was generated on {{ $generatedAt->format('M d, Y \a\t h:i A') }}
                </td>
                <td class="footer-right">
                    CONFIDENTIAL
                </td>
            </tr>
        </table>
    </div>
</body>
</html>
