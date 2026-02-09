<x-mail::message>
# MSFAA Confirmation Required

Dear {{ $application->first_name }} {{ $application->last_name }},

We require you to confirm your **Master Student Financial Assistance Agreement (MSFAA)** for your enrollment in the **{{ $application->program_name ?? 'selected' }}** program.

## Application Details

**Reference Number:** {{ $application->reference_number }}
**Program:** {{ $application->program_name ?? 'N/A' }}

## What You Need To Do

1. **Log in** to the student portal
2. **Confirm** your MSFAA by clicking "Yes" on the confirmation prompt

<x-mail::button :url="route('student.program.index')">
Go to Student Portal
</x-mail::button>

If you have any questions, please contact our Admissions Office.

Best regards,
{{ config('app.name') }} Admissions Team
</x-mail::message>
