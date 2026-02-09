<x-mail::message>
# NOA Document Uploaded

A student has uploaded their **Notice of Assessment (NOA)** document and it is ready for review.

## Details

**Reference Number:** {{ $application->reference_number }}
**Student:** {{ $application->first_name }} {{ $application->last_name }}
**Program:** {{ $application->program_name ?? 'N/A' }}
**Uploaded At:** {{ $application->noa_uploaded_at?->format('F d, Y h:i A') }}

<x-mail::button :url="route('admin.students.index')">
Review in Admin Panel
</x-mail::button>

Best regards,
{{ config('app.name') }} System
</x-mail::message>
