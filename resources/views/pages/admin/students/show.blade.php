<x-default-layout>

    @section('title')
        {{ __('Student Details') }} - {{ $student->full_name }}
    @endsection

    @section('breadcrumbs')
        {{ Breadcrumbs::render('admin.students.show', $student) }}
    @endsection

    <div class="row g-5 g-xl-10 mb-5 mb-xl-10">
        <!--begin::Col-->
        <div class="col-xl-8">
            <!--begin::Card-->
            <x-cards.section
                :title="__('Student Information')"
            >
                <x-slot:toolbar>
                    <x-buttons.action-button
                        text="{{ __('Edit') }}"
                        icon="pencil"
                        href="{{ route('admin.students.edit', $student) }}"
                        color="primary"
                        size="sm"
                    />
                </x-slot:toolbar>
                    <div class="row mb-7">
                        <label class="col-lg-4 fw-semibold text-muted">{{ __('Full Name') }}</label>
                        <div class="col-lg-8">
                            <span class="fw-bold fs-6 text-gray-800">{{ $student->full_name }}</span>
                        </div>
                    </div>
                    <div class="row mb-7">
                        <label class="col-lg-4 fw-semibold text-muted">{{ __('First Name') }}</label>
                        <div class="col-lg-8">
                            <span class="fw-bold fs-6 text-gray-800">{{ $student->first_name }}</span>
                        </div>
                    </div>
                    <div class="row mb-7">
                        <label class="col-lg-4 fw-semibold text-muted">{{ __('Last Name') }}</label>
                        <div class="col-lg-8">
                            <span class="fw-bold fs-6 text-gray-800">{{ $student->last_name }}</span>
                        </div>
                    </div>
                    <div class="row mb-7">
                        <label class="col-lg-4 fw-semibold text-muted">{{ __('Email') }}</label>
                        <div class="col-lg-8">
                            <span class="fw-bold fs-6 text-gray-800">{{ $student->email }}</span>
                        </div>
                    </div>
                    <div class="row mb-7">
                        <label class="col-lg-4 fw-semibold text-muted">{{ __('User Type') }}</label>
                        <div class="col-lg-8">
                            <span class="badge badge-light-primary">{{ ucfirst($student->user_type) }}</span>
                        </div>
                    </div>
                    <div class="row mb-7">
                        <label class="col-lg-4 fw-semibold text-muted">{{ __('Created At') }}</label>
                        <div class="col-lg-8">
                            <span class="fw-bold fs-6 text-gray-800">{{ $student->created_at->format('F d, Y h:i A') }}</span>
                        </div>
                    </div>
                    @if($student->last_login_at)
                    <div class="row mb-7">
                        <label class="col-lg-4 fw-semibold text-muted">{{ __('Last Login') }}</label>
                        <div class="col-lg-8">
                            <span class="fw-bold fs-6 text-gray-800">{{ $student->last_login_at->diffForHumans() }}</span>
                        </div>
                    </div>
                    @endif
            </x-cards.section>
            <!--end::Card-->

            <!--begin::Card - Courses (Program-based Access)-->
            @if($student->user && $student->user->program)
                @php
                    $courses = \App\Models\Course::where('program_id', $student->user->program_id)
                        ->where('status', 'active')
                        ->with('instructors.instructor')
                        ->orderBy('course_code')
                        ->get();
                @endphp
                @if($courses->count() > 0)
                <x-cards.section
                    :title="__('Accessible Courses')"
                    :subtitle="__('Courses available via program membership')"
                    class="mt-5"
                >
                        <div class="table-responsive">
                            <table class="table align-middle table-row-dashed fs-6 gy-5">
                                <thead>
                                    <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                                        <th class="min-w-200px">{{ __('Course') }}</th>
                                        <th class="min-w-100px">{{ __('Code') }}</th>
                                        <th class="min-w-100px">{{ __('Instructor') }}</th>
                                    </tr>
                                </thead>
                                <tbody class="text-gray-600 fw-semibold">
                                    @foreach($courses as $course)
                                    <tr>
                                        <td>
                                            <a href="{{ route('admin.programs.courses.show', [$student->user->program, $course]) }}" class="text-gray-800 text-hover-primary fw-bold">
                                                {{ $course->name }}
                                            </a>
                                        </td>
                                        <td>
                                            <span class="badge badge-light-primary">{{ $course->course_code }}</span>
                                        </td>
                                        <td>
                                            @if($course->instructors->whereNull('removed_at')->first())
                                                {{ $course->instructors->whereNull('removed_at')->first()->instructor->name ?? 'N/A' }}
                                            @else
                                                <span class="text-muted">{{ __('No instructor') }}</span>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                </x-cards.section>
                @endif
            @endif
            <!--end::Card - Courses-->
        </div>
        <!--end::Col-->

        <!--begin::Col-->
        <div class="col-xl-4">
            <!--begin::Card - Application Info-->
            @if($student->studentApplication)
            <x-cards.section
                :title="__('Application Information')"
            >
                    <div class="mb-7">
                        <label class="fw-semibold text-muted mb-3">{{ __('Reference Number') }}</label>
                        <div>
                            <a href="{{ route('admin.applications.show', $student->studentApplication) }}" class="text-primary fw-bold">
                                {{ $student->studentApplication->reference_number }}
                            </a>
                        </div>
                    </div>
                    @if($student->studentApplication->program_name)
                    <div class="mb-7">
                        <label class="fw-semibold text-muted mb-3">{{ __('Program') }}</label>
                        <div>
                            <span class="fw-bold text-gray-800">{{ $student->studentApplication->program_name }}</span>
                        </div>
                    </div>
                    @endif
                    <div class="mb-7">
                        <label class="fw-semibold text-muted mb-3">{{ __('Status') }}</label>
                        <div>
                            @if($student->studentApplication->status === 'approved')
                                <span class="badge badge-success">{{ __('Approved') }}</span>
                            @elseif($student->studentApplication->status === 'rejected')
                                <span class="badge badge-danger">{{ __('Rejected') }}</span>
                            @else
                                <span class="badge badge-warning">{{ __('Pending') }}</span>
                            @endif
                        </div>
                    </div>
                    <div class="d-flex justify-content-end">
                        <a href="{{ route('admin.applications.show', $student->studentApplication) }}" class="btn btn-sm btn-light-primary">
                            {{ __('View Application') }}
                        </a>
                    </div>
            </x-cards.section>
            @else
            <x-cards.section>
                <div class="alert alert-warning">
                    <i class="ki-outline ki-information-5 fs-2 text-warning me-2"></i>
                    <span>{{ __('No application linked to this student.') }}</span>
                </div>
            </x-cards.section>
            @endif
            <!--end::Card - Application Info-->

            <!--begin::Card - Actions-->
            <x-cards.section
                :title="__('Actions')"
                class="mt-5"
            >
                <form action="{{ route('admin.students.destroy', $student) }}" method="POST" onsubmit="return confirm('{{ __('Are you sure you want to delete this student?') }}');">
                    @csrf
                    @method('DELETE')
                    <x-buttons.action-button
                        text="{{ __('Delete Student') }}"
                        icon="trash"
                        color="danger"
                        type="submit"
                        class="w-100"
                    />
                </form>
            </x-cards.section>
            <!--end::Card - Actions-->
        </div>
        <!--end::Col-->
    </div>

</x-default-layout>

