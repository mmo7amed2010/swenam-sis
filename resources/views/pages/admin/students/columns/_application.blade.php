@if($student->studentApplication)
    <a href="{{ route('admin.applications.show', $student->studentApplication) }}" class="text-primary fw-bold">
        {{ $student->studentApplication->reference_number }}
    </a>
@else
    <span class="text-muted">{{ __('N/A') }}</span>
@endif

