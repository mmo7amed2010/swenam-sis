<x-mail::message>
# Notice of Assessment Rejected

Dear {{ $application->first_name }} {{ $application->last_name }},

Your **Notice of Assessment (NOA)** document for the **{{ $application->program_name ?? 'selected' }}** program has been reviewed and requires resubmission.

## Application Details

**Reference Number:** {{ $application->reference_number }}
**Program:** {{ $application->program_name ?? 'N/A' }}

@if($application->noa_rejection_reason)
## Reason for Rejection

{{ $application->noa_rejection_reason }}
@endif

## What You Need To Do

1. **Log in** to the student portal
2. **Upload** a corrected Notice of Assessment document (PDF, JPG, or PNG)

<x-mail::button :url="route('student.program.index')">
Upload New NOA Document
</x-mail::button>

If you have any questions, please contact our Admissions Office.

Best regards,
{{ config('app.name') }} Admissions Team
</x-mail::message>
