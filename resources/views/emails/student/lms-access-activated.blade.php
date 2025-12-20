<x-mail::message>
# Congratulations! Your Application Has Been Approved!

Hi {{ $student->first_name }},

Great news! Your application has been approved and your course access is now active.

## Your Student Information

- **Student Number:** {{ $student->student_number }}
- **Program:** {{ $application->program_name ?? 'N/A' }}
- **Email:** {{ $user->email }}

## Access Your Courses

You can now access your enrolled courses through "My Courses" in your student portal.

<x-mail::button :url="route('login')">
Login to Access Courses
</x-mail::button>

## Next Steps

1. **Login** to your student portal (use your existing credentials)
2. Click on **"My Courses"** in the sidebar
3. Start learning!

## Need Help?

If you have any questions or need assistance, please contact:

- **Email:** support@swenamcollege.ca
- **Phone:** +1 (555) 123-4567

Welcome aboard!

Best regards,<br>
{{ config('app.name') }} Team
</x-mail::message>
