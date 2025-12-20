<x-default-layout>

    @section('title')
        {{ __('Student Dashboard') }}
    @endsection

    @section('breadcrumbs')
        {{ Breadcrumbs::render('dashboard') }}
    @endsection

    {{-- Welcome Header --}}
    <div class="row g-5 g-xl-10 mb-5 mb-xl-10">
        <div class="col-xl-12">
            <div class="card card-flush">
                <div class="card-body p-8">
                    <div class="d-flex align-items-center">
                        <div class="symbol symbol-60px symbol-circle me-5">
                            @if($user->profile_photo_path)
                                <img src="{{ $user->profile_photo_url }}" alt="{{ $user->name }}">
                            @else
                                <div class="symbol-label bg-light-primary text-primary fs-2 fw-bold">
                                    {{ strtoupper(substr($user->first_name ?? $user->name, 0, 1)) }}
                                </div>
                            @endif
                        </div>
                        <div class="flex-grow-1">
                            <h2 class="mb-1 text-gray-900">{{ __('Welcome back') }}, {{ $user->first_name ?? $user->name }}!</h2>
                            @if($student)
                                <div class="text-muted fs-6">
                                    {{ __('Student Number:') }} <span class="fw-semibold">{{ $student->student_number }}</span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Application Status Banner (shown if pending approval) --}}
    @if(!empty($isPendingApproval) && isset($application))
        <div class="row g-5 g-xl-10 mb-5 mb-xl-10">
            <div class="col-xl-12">
                <div class="card bg-light-info border-0">
                    <div class="card-body p-8">
                        <div class="d-flex align-items-center">
                            <i class="ki-duotone ki-timer fs-3tx text-info me-5">
                                <span class="path1"></span>
                                <span class="path2"></span>
                                <span class="path3"></span>
                            </i>
                            <div class="flex-grow-1">
                                <h3 class="mb-1 text-gray-900">{{ __('Application Under Review') }}</h3>
                                <p class="text-gray-700 mb-3">
                                    {{ __('Your application') }}
                                    <strong class="text-info">{{ $application->reference_number }}</strong>
                                    {{ __('is currently being reviewed by our admissions team.') }}
                                </p>
                                <div class="d-flex flex-wrap gap-3">
                                    <span class="badge badge-light-primary fs-7">
                                        <i class="ki-duotone ki-document fs-6 me-1">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                        {{ __('Program:') }} {{ $application->program_name ?? 'N/A' }}
                                    </span>
                                    <span class="badge badge-light-{{ $application->status === 'initial_approved' ? 'success' : 'warning' }} fs-7">
                                        <i class="ki-duotone ki-check-circle fs-6 me-1">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                        {{ __('Status:') }}
                                        @if($application->status === 'initial_approved')
                                            {{ __('Under Final Review') }}
                                        @else
                                            {{ __('Pending Review') }}
                                        @endif
                                    </span>
                                    <span class="badge badge-light-secondary fs-7">
                                        <i class="ki-duotone ki-calendar fs-6 me-1">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                        {{ __('Submitted:') }} {{ $application->created_at->format('M d, Y') }}
                                    </span>
                                </div>
                                <p class="text-muted mt-3 mb-0 fs-7">
                                    <i class="ki-duotone ki-information-2 fs-6 me-1">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                        <span class="path3"></span>
                                    </i>
                                    {{ __('Course access will be enabled once your application is approved. You will receive an email notification.') }}
                                </p>
                            </div>
                            <div class="ms-5 d-none d-md-block">
                                <a href="{{ route('student.program.index') }}" class="btn btn-info">
                                    {{ __('View Application') }}
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Approved Student - Course Access Card --}}
    @if(empty($isPendingApproval) && !empty($hasLmsAccess))
        <div class="row g-5 g-xl-10 mb-5 mb-xl-10">
            <div class="col-xl-12">
                <div class="card bg-light-success border-0">
                    <div class="card-body p-8">
                        <div class="d-flex align-items-center">
                            <i class="ki-duotone ki-book-open fs-3tx text-success me-5">
                                <span class="path1"></span>
                                <span class="path2"></span>
                                <span class="path3"></span>
                                <span class="path4"></span>
                            </i>
                            <div class="flex-grow-1">
                                <h3 class="mb-1 text-gray-900">{{ __('Your Courses Are Ready!') }}</h3>
                                <p class="text-gray-700 mb-0">
                                    {{ __('Your application has been approved and your courses are now available.') }}
                                </p>
                                @if($program)
                                    <div class="mt-2">
                                        <span class="badge badge-light-primary fs-7">
                                            {{ $program->name ?? $application->program_name ?? __('My Program') }}
                                        </span>
                                    </div>
                                @endif
                            </div>
                            <div class="ms-5">
                                <a href="{{ route('student.program.index') }}" class="btn btn-success">
                                    <i class="ki-duotone ki-arrow-right fs-4 me-1">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                    {{ __('Go to My Courses') }}
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Student Info Card --}}
    <div class="row g-5 g-xl-10 mb-5 mb-xl-10">
        <div class="col-xl-6">
            <div class="card card-flush h-100">
                <div class="card-header pt-7">
                    <h3 class="card-title align-items-start flex-column">
                        <span class="card-label fw-bold text-gray-800">{{ __('My Information') }}</span>
                    </h3>
                </div>
                <div class="card-body pt-5">
                    <div class="d-flex flex-column gap-4">
                        <div class="d-flex align-items-center">
                            <div class="symbol symbol-40px me-4">
                                <span class="symbol-label bg-light-primary">
                                    <i class="ki-duotone ki-user fs-2 text-primary">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                </span>
                            </div>
                            <div>
                                <div class="fs-7 text-muted">{{ __('Full Name') }}</div>
                                <div class="fw-bold text-gray-800">{{ $user->name }}</div>
                            </div>
                        </div>
                        <div class="d-flex align-items-center">
                            <div class="symbol symbol-40px me-4">
                                <span class="symbol-label bg-light-info">
                                    <i class="ki-duotone ki-sms fs-2 text-info">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                </span>
                            </div>
                            <div>
                                <div class="fs-7 text-muted">{{ __('Email') }}</div>
                                <div class="fw-bold text-gray-800">{{ $user->email }}</div>
                            </div>
                        </div>
                        @if($student)
                            <div class="d-flex align-items-center">
                                <div class="symbol symbol-40px me-4">
                                    <span class="symbol-label bg-light-success">
                                        <i class="ki-duotone ki-badge fs-2 text-success">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                            <span class="path3"></span>
                                            <span class="path4"></span>
                                            <span class="path5"></span>
                                        </i>
                                    </span>
                                </div>
                                <div>
                                    <div class="fs-7 text-muted">{{ __('Student Number') }}</div>
                                    <div class="fw-bold text-gray-800">{{ $student->student_number }}</div>
                                </div>
                            </div>
                        @endif
                        @if($program || ($application && $application->program_name))
                            <div class="d-flex align-items-center">
                                <div class="symbol symbol-40px me-4">
                                    <span class="symbol-label bg-light-warning">
                                        <i class="ki-duotone ki-route fs-2 text-warning">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                            <span class="path3"></span>
                                            <span class="path4"></span>
                                        </i>
                                    </span>
                                </div>
                                <div>
                                    <div class="fs-7 text-muted">{{ __('Program') }}</div>
                                    <div class="fw-bold text-gray-800">{{ $program->name ?? $application->program_name ?? 'N/A' }}</div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Quick Actions --}}
        <div class="col-xl-6">
            <div class="card card-flush h-100">
                <div class="card-header pt-7">
                    <h3 class="card-title align-items-start flex-column">
                        <span class="card-label fw-bold text-gray-800">{{ __('Quick Actions') }}</span>
                    </h3>
                </div>
                <div class="card-body pt-5">
                    <div class="d-flex flex-column gap-4">
                        <a href="{{ route('student.program.index') }}" class="btn btn-flex btn-light-primary w-100 py-4">
                            <i class="ki-duotone ki-document fs-2 me-2">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                            {{ __('My Application') }}
                        </a>
                        @if(!empty($hasLmsAccess))
                            <a href="{{ route('student.my-courses.redirect') }}" class="btn btn-flex btn-light-success w-100 py-4" target="_blank">
                                <i class="ki-duotone ki-book-open fs-2 me-2">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                    <span class="path3"></span>
                                    <span class="path4"></span>
                                </i>
                                {{ __('My Courses') }}
                                <i class="ki-duotone ki-entrance-left fs-4 ms-2">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

</x-default-layout>
