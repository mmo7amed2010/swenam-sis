<x-mail::message>
# Contract Approved

Dear {{ $application->first_name }} {{ $application->last_name }},

Your signed enrollment contract has been reviewed and **approved**.

## Application Details

**Reference Number:** {{ $application->reference_number }}
**Program:** {{ $application->program_name ?? 'N/A' }}

@if($application->isSelfFunded())
## Next Step: Payment

As a self-funded student, you need to submit your payment receipt to complete the enrollment process. Please log in to the student portal to upload your payment receipt.

<x-mail::button :url="config('app.url') . '/student/application'">
Upload Payment Receipt
</x-mail::button>
@else
Your enrollment process is being finalized. You will receive another email with your account credentials shortly.
@endif

Best regards,
{{ config('app.name') }} Admissions Team
</x-mail::message>
