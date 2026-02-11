<x-mail::message>
# Your Agent Account Has Been Created!

Hi {{ $user->name }},

An agent account has been created for you at {{ config('app.name') }}. You can now log in to the agent portal and submit applications on behalf of students.

## Account Details

- **Name:** {{ $user->name }}
- **Email:** {{ $user->email }}

## Login Credentials

- **Username:** {{ $user->email }}
- **Temporary Password:** `{!! $tempPassword !!}`

**IMPORTANT:** You will be required to change your password on first login for security.

<x-mail::button :url="route('login')">
Login to Agent Portal
</x-mail::button>

## What Can You Do?

1. **Submit** student applications on behalf of students
2. **Track** the status of applications you've submitted
3. **View** application details and updates

## Important Information

- You must change your temporary password on your first login
- Keep your login credentials secure and do not share them
- If you experience any issues, please contact enrolment@swenamcollege.com

Best regards,<br>
{{ config('app.name') }} Admissions Team
</x-mail::message>
