<x-mail::message>
# You've Been Assigned to a Course

Hi {{ $instructor->name }},

You have been assigned to **{{ $course->name }}** ({{ $course->course_code }}).

You now have full access to manage this course, including:
- Creating and managing modules and lessons
- Creating and managing assignments and quizzes
- Grading student submissions
- Viewing student progress

<x-mail::button :url="route('instructor.courses.show', $course)">
View Course
</x-mail::button>

Best regards,<br>
{{ config('app.name') }}
</x-mail::message>
