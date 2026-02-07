<x-mail::message>
# Your Enrollment Contract is Ready

Dear {{ $application->first_name }} {{ $application->last_name }},

Your enrollment contract for the **{{ $application->program_name ?? 'selected' }}** program is ready for your review and signature.

## Application Details

**Reference Number:** {{ $application->reference_number }}
**Program:** {{ $application->program_name ?? 'N/A' }}
**Funding Type:** {{ $application->isSelfFunded() ? 'Self-Funded' : 'Government-Funded' }}

## What You Need To Do

1. **Review** the attached contract carefully
2. **Print** the contract
3. **Sign** the contract
4. **Scan** the signed contract as a PDF
5. **Upload** the signed contract through the student portal

<x-mail::button :url="config('app.url') . '/student/application'">
Go to Student Portal
</x-mail::button>

The contract is also attached to this email for your convenience.

If you have any questions about the contract, please contact our Admissions Office.

Best regards,
{{ config('app.name') }} Admissions Team
</x-mail::message>
