<x-mail::message>
# Application Status Update

Dear {{ $application->first_name }} {{ $application->last_name }},

Thank you for your interest in **LMS College** and for taking the time to complete your application for the **{{ $application->program_name ?? 'selected' }}** program.

After careful consideration of your application, we regret to inform you that we are unable to offer you admission at this time.

## Application Details

**Reference Number:** {{ $application->reference_number }}
**Program:** {{ $application->program_name ?? 'N/A' }}
**Preferred Intake:** {{ $application->preferred_intake }}
**Decision Date:** {{ $application->reviewed_at->format('F d, Y') }}

## Reason for Decision

{{ $application->rejection_reason }}

## Moving Forward

While we cannot offer admission at this time, we encourage you to:

- **Strengthen your qualifications:** Consider additional coursework, certifications, or relevant experience in your field of interest
- **Reapply in the future:** You are welcome to submit a new application for a future intake period
- **Explore other programs:** We offer various programs that might align with your educational goals and current qualifications

## Alternative Options

We encourage you to explore our other available programs that might be a good fit for your background and goals:

<x-mail::button :url="config('app.url') . '/programs'">
Browse All Programs
</x-mail::button>

## Questions or Feedback?

If you would like to discuss your application or receive feedback that might help strengthen a future application, please contact our admissions office:

**Admissions Office:**
- **Email:** admissions@lmscollege.edu
- **Phone:** +1 (555) 123-4567
- **Hours:** Monday - Friday, 9:00 AM - 5:00 PM

We appreciate your interest in LMS College and wish you the best in your educational pursuits.

Sincerely,
{{ config('app.name') }} Admissions Team
</x-mail::message>
