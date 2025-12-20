<x-mail::message>
# Congratulations! Your Application Has Been Approved

Dear {{ $application->first_name }} {{ $application->last_name }},

We are delighted to inform you that your application to **LMS College** has been **approved**! Welcome to the **{{ $application->program_name ?? 'selected' }}** program.

Your student account has been created, and you can now access the LMS College student portal.

## Your Student Account Credentials

**Username:** {{ $user->username }}
**Email:** {{ $user->email }}
**Temporary Password:** `{!! $password !!}`

⚠️ **Important Security Notice:**
- This is a temporary password - please change it immediately upon your first login
- Do not share your login credentials with anyone
- Keep this email in a secure location

<x-mail::button :url="config('app.url') . '/login'">
Login to Student Portal
</x-mail::button>

## Application Details

**Reference Number:** {{ $application->reference_number }}
**Program:** {{ $application->program_name ?? 'N/A' }}
**Preferred Intake:** {{ $application->preferred_intake }}
**Approval Date:** {{ $application->reviewed_at->format('F d, Y') }}

## Next Steps

1. **Login:** Use the credentials above to log into the student portal
2. **Change Password:** Set a secure password of your choice
3. **Complete Profile:** Fill out any additional required information
4. **Enroll in Courses:** Browse and enroll in your program courses
5. **Orientation:** Check your dashboard for orientation schedule and materials

## Need Help?

If you have any questions or encounter any issues accessing your account, please contact:

**Student Support:**
- **Email:** support@lmscollege.edu
- **Phone:** +1 (555) 123-4567
- **Hours:** Monday - Friday, 9:00 AM - 5:00 PM

We look forward to supporting you on your educational journey at LMS College!

Best regards,
{{ config('app.name') }} Admissions Team
</x-mail::message>
