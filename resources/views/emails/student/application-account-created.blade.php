<x-mail::message>
# Your Account Has Been Created!

Hi {{ $student->first_name }},

Thank you for submitting your application to {{ config('app.name') }}. Your student portal account has been created so you can track the status of your application.

## Your Application Details

- **Reference Number:** {{ $application->reference_number }}
- **Student Number:** {{ $student->student_number }}
- **Program Applied:** {{ $application->program_name ?? 'N/A' }}
- **Status:** Pending Review

## Login Credentials

- **Username:** {{ $user->email }}
- **Temporary Password:** `{!! $tempPassword !!}`

**IMPORTANT:** You will be required to change your password on first login for security.

<x-mail::button :url="route('login')">
Login to Student Portal
</x-mail::button>

## What Can You Do Now?

1. **Login** to your student portal
2. **Track** your application status from your dashboard
3. **View** your application details

## What Happens Next?

1. Our admissions team will review your application (typically 5-7 business days)
2. You will receive email updates as your application progresses
3. Once approved, your course access will be activated

**Note:** Course access will be enabled after your application is approved.

If you have any questions, please contact support@swenamcollege.ca.

Best regards,<br>
{{ config('app.name') }} Admissions Team
</x-mail::message>
