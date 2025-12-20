<x-mail::message>
# Assignment Resubmission

Hi {{ $instructor->name }},

**{{ $student->name }}** has resubmitted an assignment for **{{ $assignment->title }}** in **{{ $assignment->course->course_code }}**.

**Resubmission Details:**
- Assignment: {{ $assignment->title }}
- Course: {{ $assignment->course->course_code }} - {{ $assignment->course->name }}
- Student: {{ $student->name }} ({{ $student->email }})
- Resubmitted: {{ $submission->submitted_at->format('F d, Y g:i A') }}
- Attempt Number: {{ $submission->attempt_number }}

**Note:** The previous submission has been archived and replaced with this new submission.

@if($assignment->course && $assignment->course->program)
<x-mail::button :url="route('admin.programs.courses.assignments.show', [$assignment->course->program, $assignment->course, $assignment])">
View Resubmission
</x-mail::button>
@endif

Best regards,<br>
{{ config('app.name') }}
</x-mail::message>





