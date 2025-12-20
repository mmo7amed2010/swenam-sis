<x-mail::message>
# Application Submitted Successfully

Dear {{ $application->first_name }} {{ $application->last_name }},

Thank you for submitting your application to **LMS College**. We have successfully received your application for the **{{ $application->program_name ?? 'selected' }}** program.

## Application Details

**Reference Number:** {{ $application->reference_number }}
**Program:** {{ $application->program_name ?? 'N/A' }}
**Preferred Intake:** {{ $application->preferred_intake }}
**Submission Date:** {{ $application->created_at->format('F d, Y \a\t h:i A') }}

## What Happens Next?

1. **Application Review:** Our admissions team will carefully review your application and supporting documents. This typically takes 5-7 business days.

2. **Decision Notification:** Once a decision has been made, we will contact you via email with the outcome.

3. **If Approved:** You will receive your student account credentials and instructions on how to complete your enrollment.

## Important Notes

- Please keep your reference number **{{ $application->reference_number }}** for future correspondence
- Check your spam/junk folder if you don't receive updates from us
- For any inquiries, please reference your application number when contacting us

<x-mail::button :url="config('app.url')">
Visit Our Website
</x-mail::button>

If you have any questions about your application, please contact our admissions office at:
**Email:** admissions@lmscollege.edu
**Phone:** +1 (555) 123-4567

Thank you for choosing LMS College!

Best regards,
{{ config('app.name') }} Admissions Team
</x-mail::message>
