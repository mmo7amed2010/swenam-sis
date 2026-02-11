<x-mail::message>
# Notice of Assessment Required

Dear {{ $application->first_name }} {{ $application->last_name }},

We require a **Notice of Assessment (NOA)** document for your enrollment in the **{{ $application->program_name ?? 'selected' }}** program.

## Application Details

**Reference Number:** {{ $application->reference_number }}
**Program:** {{ $application->program_name ?? 'N/A' }}

## What You Need To Do

1. **Log in** to the student portal
2. **Upload** your Notice of Assessment document (PDF, JPG, or PNG)
3. Or **skip** if you do not have it available right now

<x-mail::button :url="route('student.program.index')">
Go to Student Portal
</x-mail::button>

If you have any questions, please contact our Admissions Office.

Best regards,
{{ config('app.name') }} Admissions Team
</x-mail::message>
