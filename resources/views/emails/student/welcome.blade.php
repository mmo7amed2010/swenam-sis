<x-mail::message>
# Welcome to {{ config('app.name') }}!

Hi {{ $student->first_name }},

Congratulations! Your application has been approved and your student account has been created.

## Your Student Information

- **Student Number:** {{ $student->student_number }}
- **Program:** {{ $application->program_name ?? 'N/A' }}
- **Email:** {{ $user->email }}

## Login Credentials

- **Username:** {{ $user->email }}
- **Temporary Password:** `{!! $tempPassword !!}`

⚠️ **IMPORTANT:** You will be required to change your password on first login for security.

<x-mail::button :url="route('login')">
Login to LMS
</x-mail::button>

## Next Steps

1. Click the button above to login
2. Change your temporary password when prompted
3. Explore your student dashboard
4. Access your enrolled courses

If you have any questions, please contact support@swenamcollege.ca.

Welcome aboard!

Best regards,<br>
{{ config('app.name') }} Admissions Team
</x-mail::message>

