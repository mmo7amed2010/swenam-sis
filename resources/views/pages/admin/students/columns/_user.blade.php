<!--begin:: Avatar -->
<div class="symbol symbol-circle symbol-50px overflow-hidden me-3">
    <a href="{{ route('admin.students.show', $student) }}">
        @if($student->profile_photo_url)
            <div class="symbol-label">
                <img src="{{ $student->profile_photo_url }}" class="w-100"/>
            </div>
        @else
            <div class="symbol-label fs-3 bg-light-primary text-primary">
                {{ strtoupper(substr($student->first_name ?? '', 0, 1)) }}
            </div>
        @endif
    </a>
</div>
<!--end::Avatar-->
<!--begin::User details-->
<div class="d-flex flex-column">
    <a href="{{ route('admin.students.show', $student) }}" class="text-gray-800 text-hover-primary mb-1">
        {{ $student->full_name }}
    </a>
    <span>{{ $student->email }}</span>
</div>
<!--end::User details-->

