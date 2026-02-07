<x-mail::message>
# Payment Required

Dear {{ $application->first_name }} {{ $application->last_name }},

Your enrollment contract has been approved. To complete your enrollment in the **{{ $application->program_name ?? 'selected' }}** program, please submit your payment receipt.

## Application Details

**Reference Number:** {{ $application->reference_number }}
**Program:** {{ $application->program_name ?? 'N/A' }}

## How to Submit Payment

1. Complete your payment as per the contract terms
2. Log in to the student portal
3. Upload your payment receipt (PDF, JPG, or PNG format)

<x-mail::button :url="config('app.url') . '/student/application'">
Upload Payment Receipt
</x-mail::button>

If you have any questions about payment, please contact our Admissions Office.

Best regards,
{{ config('app.name') }} Admissions Team
</x-mail::message>
