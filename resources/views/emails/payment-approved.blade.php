<x-mail::message>
# Payment Approved

Dear {{ $application->first_name }} {{ $application->last_name }},

Your payment has been received and **approved**. Your enrollment process is now complete!

## Application Details

**Reference Number:** {{ $application->reference_number }}
**Program:** {{ $application->program_name ?? 'N/A' }}

Your student account is being set up and you will receive your login credentials in a separate email shortly.

Best regards,
{{ config('app.name') }} Admissions Team
</x-mail::message>
