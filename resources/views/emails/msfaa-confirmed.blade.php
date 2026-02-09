<x-mail::message>
# MSFAA Confirmed by Student

A student has confirmed their **Master Student Financial Assistance Agreement (MSFAA)** and it is ready for review.

## Details

**Reference Number:** {{ $application->reference_number }}
**Student:** {{ $application->first_name }} {{ $application->last_name }}
**Program:** {{ $application->program_name ?? 'N/A' }}
**Confirmed At:** {{ $application->msfaa_confirmed_at?->format('F d, Y h:i A') }}

<x-mail::button :url="route('admin.applications.show', $application)">
Review in Admin Panel
</x-mail::button>

Best regards,
{{ config('app.name') }} System
</x-mail::message>
