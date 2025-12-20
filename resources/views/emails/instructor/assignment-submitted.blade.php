<x-mail::message>
# New Assignment Submission

Hi {{ $instructor->name }},

**{{ $student->name }}** has submitted an assignment for **{{ $assignment->title }}** in **{{ $assignment->course->course_code }}**.

**Submission Details:**
- Assignment: {{ $assignment->title }}
- Course: {{ $assignment->course->course_code }} - {{ $assignment->course->name }}
- Student: {{ $student->name }} ({{ $student->email }})
- Submitted: {{ $submission->submitted_at->format('F d, Y g:i A') }}
@if($submission->is_late)
- **Status: Late Submission** ({{ $submission->late_days }} day(s) late)
@endif

@if($assignment->course && $assignment->course->program)
<x-mail::button :url="route('admin.programs.courses.assignments.show', [$assignment->course->program, $assignment->course, $assignment])">
View Submission
</x-mail::button>
@endif

Best regards,<br>
{{ config('app.name') }}
</x-mail::message>

