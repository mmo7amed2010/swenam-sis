<x-mail::message>
# Duplicate Email Detected

An approved application cannot be processed due to a duplicate email address.

## Application Details

- **Application Number:** {{ $application->reference_number }}
- **Name:** {{ $application->first_name }} {{ $application->last_name }}
- **Email:** {{ $application->email }}
- **Program:** {{ $application->program_name ?? 'N/A' }}

## Existing User

- **User ID:** {{ $existingUser->id }}
- **Name:** {{ $existingUser->name }}
- **Type:** {{ $existingUser->user_type }}
- **Created:** {{ $existingUser->created_at->format('F j, Y') }}

## Action Required

Please review the application and contact the applicant to resolve the duplicate email issue.

<x-mail::button :url="route('admin.applications.show', $application->id)">
View Application
</x-mail::button>

System notification - no reply needed.
</x-mail::message>

