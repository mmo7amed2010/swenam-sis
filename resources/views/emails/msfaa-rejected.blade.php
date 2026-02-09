<x-mail::message>
# MSFAA Rejected

Dear {{ $application->first_name }} {{ $application->last_name }},

Your **Master Student Financial Assistance Agreement (MSFAA)** confirmation for the **{{ $application->program_name ?? 'selected' }}** program has been reviewed and requires resubmission.

## Application Details

**Reference Number:** {{ $application->reference_number }}
**Program:** {{ $application->program_name ?? 'N/A' }}

@if($application->msfaa_rejection_reason)
## Reason for Rejection

{{ $application->msfaa_rejection_reason }}
@endif

## What You Need To Do

1. **Log in** to the student portal
2. **Confirm** your MSFAA again by clicking "Yes" on the confirmation prompt

<x-mail::button :url="route('student.program.index')">
Go to Student Portal
</x-mail::button>

If you have any questions, please contact our Admissions Office.

Best regards,
{{ config('app.name') }} Admissions Team
</x-mail::message>
