<!--begin:: Avatar -->
<div class="symbol symbol-circle symbol-50px overflow-hidden me-3">
    <a href="{{ route('admin.instructors.show', $instructor) }}">
        @if($instructor->profile_photo_url)
            <div class="symbol-label">
                <img src="{{ $instructor->profile_photo_url }}" class="w-100"/>
            </div>
        @else
            <div class="symbol-label fs-3 bg-light-primary text-primary">
                {{ strtoupper(substr($instructor->first_name ?? $instructor->name, 0, 1)) }}
            </div>
        @endif
    </a>
</div>
<!--end::Avatar-->
<!--begin::User details-->
<div class="d-flex flex-column">
    <a href="{{ route('admin.instructors.show', $instructor) }}" class="text-gray-800 text-hover-primary mb-1">
        {{ $instructor->name }}
    </a>
    <span>{{ $instructor->email }}</span>
</div>
<!--end::User details-->

