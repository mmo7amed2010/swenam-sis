<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Profile - {{ $student->full_name }}</title>
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
        .status-active { background-color: #d1fae5; color: #065f46; }
        .status-suspended { background-color: #fee2e2; color: #991b1b; }
        .status-pending { background-color: #fef3c7; color: #92400e; }
        .status-info { background-color: #dbeafe; color: #1e40af; }
        .status-approved { background-color: #d1fae5; color: #065f46; }
        .status-rejected { background-color: #fee2e2; color: #991b1b; }

        /* ========== COURSE TABLE ========== */
        .course-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        .course-table th {
            background-color: #f7fafc;
            font-weight: bold;
            color: #2d3748;
            padding: 6px 10px;
            border: 1px solid #e2e8f0;
            font-size: 9px;
            text-align: left;
        }
        .course-table td {
            padding: 6px 10px;
            border: 1px solid #e2e8f0;
            font-size: 9px;
        }

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
                    <div class="document-title">Student Profile</div>
                    <div class="student-info-header">
                        <strong>Student:</strong> {{ $student->full_name }} &nbsp;|&nbsp;
                        <strong>ID:</strong> {{ $student->student_number ?? 'N/A' }}
                    </div>
                </td>
                <td class="header-right">
                    <div class="header-right-row">
                        <span class="header-right-label">Export Date:</span><br>
                        {{ $generatedAt->format('M d, Y') }}
                    </div>
                    <div class="header-right-row">
                        <span class="header-right-label">Program:</span><br>
                        {{ $student->user?->program?->name ?? 'N/A' }}
                    </div>
                    <div class="header-right-row">
                        <span class="header-right-label">Status:</span><br>
                        @if($student->user?->is_suspended)
                            Suspended
                        @else
                            Active
                        @endif
                    </div>
                </td>
            </tr>
        </table>
    </div>

    {{-- Student Details --}}
    <div class="section-title">Student Details</div>
    <table class="info-table">
        <tr>
            <td class="info-label">Student Number</td>
            <td>{{ $student->student_number ?? 'N/A' }}</td>
            <td class="info-label">Full Name</td>
            <td>{{ $student->full_name }}</td>
        </tr>
        <tr>
            <td class="info-label">Email</td>
            <td>{{ $student->email }}</td>
            <td class="info-label">Phone</td>
            <td>{{ $student->phone ?? 'N/A' }}</td>
        </tr>
        <tr>
            <td class="info-label">Date of Birth</td>
            <td>{{ $student->date_of_birth ? $student->date_of_birth->format('M d, Y') : 'N/A' }}</td>
            <td class="info-label">Program</td>
            <td>{{ $student->user?->program?->name ?? 'N/A' }}</td>
        </tr>
        <tr>
            <td class="info-label">Enrollment Status</td>
            <td>
                <span class="status-badge status-{{ $student->enrollment_status === 'active' ? 'active' : ($student->enrollment_status === 'suspended' ? 'suspended' : 'info') }}">
                    {{ ucfirst($student->enrollment_status ?? 'N/A') }}
                </span>
            </td>
            <td class="info-label">Account Status</td>
            <td>
                @if($student->user?->is_suspended)
                    <span class="status-badge status-suspended">Suspended</span>
                @else
                    <span class="status-badge status-active">Active</span>
                @endif
            </td>
        </tr>
        <tr>
            <td class="info-label">LMS Account</td>
            <td>
                @if($lmsStatus['active'])
                    <span class="status-badge status-active">Active</span>
                @elseif($student->user?->lms_user_id)
                    <span class="status-badge status-pending">Inactive</span>
                @else
                    No Account
                @endif
            </td>
            <td class="info-label">Member Since</td>
            <td>{{ $student->created_at->format('M d, Y') }}</td>
        </tr>
        <tr>
            <td class="info-label">Last Login</td>
            <td colspan="3">{{ $student->user?->last_login_at ? $student->user->last_login_at->format('M d, Y h:i A') : 'Never' }}</td>
        </tr>
    </table>

    {{-- Address --}}
    @php
        $address = $student->address;
        $hasAddress = $address && (!empty($address['line1'] ?? $address['address_line1'] ?? ''));
    @endphp
    @if($hasAddress)
    <table class="info-table">
        <tr>
            <td class="info-label">Address</td>
            <td colspan="3">
                {{ $address['line1'] ?? $address['address_line1'] ?? '' }}
                @if(!empty($address['line2'] ?? $address['address_line2'] ?? ''))
                    <br>{{ $address['line2'] ?? $address['address_line2'] }}
                @endif
                @if(!empty($address['city'] ?? ''))
                    <br>{{ $address['city'] ?? '' }}{{ !empty($address['province'] ?? $address['state_province'] ?? '') ? ', ' . ($address['province'] ?? $address['state_province'] ?? '') : '' }} {{ $address['postal_code'] ?? '' }}
                @endif
                @if(!empty($address['country'] ?? ''))
                    <br>{{ $address['country'] }}
                @endif
            </td>
        </tr>
    </table>
    @endif

    {{-- Emergency Contact --}}
    @if($student->emergency_first_name || $student->emergency_last_name || $student->emergency_phone)
    <div class="section-title">Emergency Contact</div>
    <table class="info-table">
        <tr>
            <td class="info-label">Contact Name</td>
            <td>{{ trim(($student->emergency_first_name ?? '') . ' ' . ($student->emergency_last_name ?? '')) ?: 'N/A' }}</td>
            <td class="info-label">Phone</td>
            <td>{{ $student->emergency_phone ?? 'N/A' }}</td>
        </tr>
        @if($student->emergency_address)
        <tr>
            <td class="info-label">Address</td>
            <td colspan="3">
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
            </td>
        </tr>
        @endif
    </table>
    @endif

    {{-- Application Details --}}
    @if($application)
    <div class="section-title">Application Details</div>
    <table class="info-table">
        <tr>
            <td class="info-label">Reference Number</td>
            <td>{{ $application->reference_number }}</td>
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
        </tr>
        <tr>
            <td class="info-label">Program</td>
            <td>{{ $application->program_name ?? 'N/A' }}</td>
            <td class="info-label">Intake</td>
            <td>{{ $application->intake_name ?? $application->preferred_intake ?? 'N/A' }}</td>
        </tr>
        <tr>
            <td class="info-label">Funding Type</td>
            <td>{{ $application->funding_type ? ucwords(str_replace('_', ' ', $application->funding_type)) : 'N/A' }}</td>
            <td class="info-label">Agency Referral</td>
            <td>{{ $application->has_referral ? $application->referral_agency_name : 'None' }}</td>
        </tr>
        <tr>
            <td class="info-label">Submitted</td>
            <td>{{ $application->created_at->format('M d, Y h:i A') }}</td>
            <td class="info-label">Citizenship</td>
            <td>{{ $application->country_of_citizenship ?? 'N/A' }}</td>
        </tr>
        <tr>
            <td class="info-label">Residency Status</td>
            <td>{{ $application->residency_status ?? 'N/A' }}</td>
            <td class="info-label">Primary Language</td>
            <td>{{ $application->primary_language ?? 'N/A' }}</td>
        </tr>
    </table>

    {{-- Education --}}
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
            <td class="info-label">Country</td>
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

    {{-- Contract & Payment --}}
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

    {{-- NOA --}}
    @if($application->noa_status)
    <div class="section-title">NOA (Notice of Acceptance)</div>
    <table class="info-table">
        <tr>
            <td class="info-label">NOA Status</td>
            <td>
                <span class="status-badge status-{{ $application->noa_status === 'approved' ? 'approved' : ($application->noa_status === 'rejected' ? 'rejected' : 'pending') }}">
                    {{ ucwords($application->noa_status) }}
                </span>
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

    {{-- MSFAA --}}
    @if($application->msfaa_status)
    <div class="section-title">MSFAA</div>
    <table class="info-table">
        <tr>
            <td class="info-label">MSFAA Status</td>
            <td>
                <span class="status-badge status-{{ $application->msfaa_status === 'approved' ? 'approved' : ($application->msfaa_status === 'rejected' ? 'rejected' : 'pending') }}">
                    {{ ucwords($application->msfaa_status) }}
                </span>
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
    @else
    {{-- No Application --}}
    <div class="section-title">Application Details</div>
    <table class="info-table">
        <tr>
            <td style="text-align: center; color: #718096; padding: 10px;">No application linked to this student.</td>
        </tr>
    </table>
    @endif

    {{-- Courses --}}
    @if($courses->count() > 0)
    <div class="section-title">Accessible Courses</div>
    <table class="course-table">
        <thead>
            <tr>
                <th>Course Name</th>
                <th>Code</th>
                <th>Instructor</th>
            </tr>
        </thead>
        <tbody>
            @foreach($courses as $course)
            <tr>
                <td>{{ $course->name }}</td>
                <td>{{ $course->course_code }}</td>
                <td>{{ $course->instructors->whereNull('removed_at')->first()?->instructor?->name ?? 'No instructor' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    {{-- Footer --}}
    <div class="document-footer">
        <table class="footer-table">
            <tr>
                <td class="footer-left">
                    Student: {{ $student->student_number ?? $student->full_name }}
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
