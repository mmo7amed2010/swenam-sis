<x-mail::message>
# Your Assignment Has Been Graded

Hi {{ $student->name }},

Your submission for **{{ $assignment->title }}** has been graded.

**Grade Details:**
- Assignment: {{ $assignment->title }}
- Course: {{ $assignment->course->course_code ?? 'N/A' }}
- Points Awarded: **{{ number_format($grade->points_awarded, 2) }} / {{ number_format($grade->max_points, 2) }}**
- Percentage: **{{ number_format($grade->percentage, 2) }}%**
- Letter Grade: **{{ $grade->letter_grade }}**
- Graded By: {{ $grader->name }}
- Published: {{ $grade->published_at->format('F d, Y g:i A') }}

@if($grade->feedback)
**Feedback:**
{!! nl2br(e($grade->feedback)) !!}
@endif

<x-mail::button :url="route('student.assignments.show', $assignment->id)">
View Assignment
</x-mail::button>

Best regards,<br>
{{ config('app.name') }}
</x-mail::message>





