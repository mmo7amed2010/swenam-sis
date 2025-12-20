<x-default-layout>

    @section('title')
        {{ __('My Application') }}
    @endsection

    @section('breadcrumbs')
        {{ Breadcrumbs::render('dashboard') }}
    @endsection

    <div class="row g-5 g-xl-10 mb-5 mb-xl-10">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-header border-0 pt-5">
                    <h3 class="card-title align-items-start flex-column">
                        <span class="card-label fw-bold fs-3 mb-1">{{ __('My Application') }}</span>
                        @if($student)
                            <span class="text-muted mt-1 fw-semibold fs-7">
                                {{ __('Student Number:') }} {{ $student->student_number }}
                            </span>
                        @endif
                    </h3>
                </div>
                <div class="card-body py-5">
                    @if(session('error'))
                        <div class="alert alert-danger d-flex align-items-center p-5 mb-10">
                            <i class="ki-duotone ki-shield-cross fs-2hx text-danger me-4">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                            <div class="d-flex flex-column">
                                <h4 class="mb-1 text-danger">{{ __('Error') }}</h4>
                                <span>{{ session('error') }}</span>
                            </div>
                        </div>
                    @endif

                    @if($application)
                        {{-- Application Details --}}
                        <div class="row mb-7">
                            <div class="col-lg-6">
                                <div class="d-flex align-items-center mb-5">
                                    <div class="symbol symbol-45px me-4">
                                        <span class="symbol-label bg-light-primary">
                                            <i class="ki-duotone ki-document fs-2 text-primary">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                            </i>
                                        </span>
                                    </div>
                                    <div>
                                        <div class="fs-7 text-muted">{{ __('Reference Number') }}</div>
                                        <div class="fw-bold text-gray-800 fs-5">{{ $application->reference_number }}</div>
                                    </div>
                                </div>

                                <div class="d-flex align-items-center mb-5">
                                    <div class="symbol symbol-45px me-4">
                                        <span class="symbol-label bg-light-info">
                                            <i class="ki-duotone ki-route fs-2 text-info">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                                <span class="path3"></span>
                                                <span class="path4"></span>
                                            </i>
                                        </span>
                                    </div>
                                    <div>
                                        <div class="fs-7 text-muted">{{ __('Program') }}</div>
                                        <div class="fw-bold text-gray-800 fs-5">{{ $application->program_name ?? 'N/A' }}</div>
                                    </div>
                                </div>

                                <div class="d-flex align-items-center mb-5">
                                    <div class="symbol symbol-45px me-4">
                                        <span class="symbol-label bg-light-warning">
                                            <i class="ki-duotone ki-calendar fs-2 text-warning">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                            </i>
                                        </span>
                                    </div>
                                    <div>
                                        <div class="fs-7 text-muted">{{ __('Preferred Intake') }}</div>
                                        <div class="fw-bold text-gray-800 fs-5">{{ $application->preferred_intake ?? 'N/A' }}</div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-6">
                                <div class="d-flex align-items-center mb-5">
                                    <div class="symbol symbol-45px me-4">
                                        <span class="symbol-label bg-light-secondary">
                                            <i class="ki-duotone ki-time fs-2 text-gray-600">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                            </i>
                                        </span>
                                    </div>
                                    <div>
                                        <div class="fs-7 text-muted">{{ __('Submitted On') }}</div>
                                        <div class="fw-bold text-gray-800 fs-5">{{ $application->created_at->format('F d, Y') }}</div>
                                    </div>
                                </div>

                                <div class="d-flex align-items-center mb-5">
                                    <div class="symbol symbol-45px me-4">
                                        @if($application->status === 'approved')
                                            <span class="symbol-label bg-light-success">
                                                <i class="ki-duotone ki-check-circle fs-2 text-success">
                                                    <span class="path1"></span>
                                                    <span class="path2"></span>
                                                </i>
                                            </span>
                                        @elseif($application->status === 'initial_approved')
                                            <span class="symbol-label bg-light-info">
                                                <i class="ki-duotone ki-verify fs-2 text-info">
                                                    <span class="path1"></span>
                                                    <span class="path2"></span>
                                                </i>
                                            </span>
                                        @elseif($application->status === 'rejected')
                                            <span class="symbol-label bg-light-danger">
                                                <i class="ki-duotone ki-cross-circle fs-2 text-danger">
                                                    <span class="path1"></span>
                                                    <span class="path2"></span>
                                                </i>
                                            </span>
                                        @else
                                            <span class="symbol-label bg-light-warning">
                                                <i class="ki-duotone ki-timer fs-2 text-warning">
                                                    <span class="path1"></span>
                                                    <span class="path2"></span>
                                                    <span class="path3"></span>
                                                </i>
                                            </span>
                                        @endif
                                    </div>
                                    <div>
                                        <div class="fs-7 text-muted">{{ __('Status') }}</div>
                                        <div class="fw-bold fs-5">
                                            @if($application->status === 'approved')
                                                <span class="text-success">{{ __('Approved') }}</span>
                                            @elseif($application->status === 'initial_approved')
                                                <span class="text-info">{{ __('Under Final Review') }}</span>
                                            @elseif($application->status === 'rejected')
                                                <span class="text-danger">{{ __('Rejected') }}</span>
                                            @elseif($application->status === 'pending')
                                                <span class="text-warning">{{ __('Pending Review') }}</span>
                                            @else
                                                <span class="text-gray-600">{{ ucfirst(str_replace('_', ' ', $application->status)) }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                @if($application->reviewed_at)
                                    <div class="d-flex align-items-center mb-5">
                                        <div class="symbol symbol-45px me-4">
                                            <span class="symbol-label bg-light-success">
                                                <i class="ki-duotone ki-calendar-tick fs-2 text-success">
                                                    <span class="path1"></span>
                                                    <span class="path2"></span>
                                                    <span class="path3"></span>
                                                    <span class="path4"></span>
                                                    <span class="path5"></span>
                                                    <span class="path6"></span>
                                                </i>
                                            </span>
                                        </div>
                                        <div>
                                            <div class="fs-7 text-muted">{{ __('Reviewed On') }}</div>
                                            <div class="fw-bold text-gray-800 fs-5">{{ $application->reviewed_at->format('F d, Y') }}</div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>

                        {{-- Status Message --}}
                        @if($application->status === 'approved')
                            <div class="notice d-flex bg-light-success rounded border-success border border-dashed p-6 mb-5">
                                <i class="ki-duotone ki-shield-tick fs-2tx text-success me-4">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                                <div class="d-flex flex-stack flex-grow-1">
                                    <div class="fw-semibold">
                                        <h4 class="text-gray-900 fw-bold">{{ __('Application Approved!') }}</h4>
                                        <div class="fs-6 text-gray-700">
                                            {{ __('Congratulations! Your application has been approved. You can now access your courses.') }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @elseif($application->status === 'pending' || $application->status === 'initial_approved')
                            <div class="notice d-flex bg-light-warning rounded border-warning border border-dashed p-6 mb-5">
                                <i class="ki-duotone ki-information fs-2tx text-warning me-4">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                    <span class="path3"></span>
                                </i>
                                <div class="d-flex flex-stack flex-grow-1">
                                    <div class="fw-semibold">
                                        <h4 class="text-gray-900 fw-bold">{{ __('Application Under Review') }}</h4>
                                        <div class="fs-6 text-gray-700">
                                            {{ __('Your application is being reviewed by our admissions team. You will receive an email once a decision has been made.') }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @elseif($application->status === 'rejected')
                            <div class="notice d-flex bg-light-danger rounded border-danger border border-dashed p-6 mb-5">
                                <i class="ki-duotone ki-information fs-2tx text-danger me-4">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                    <span class="path3"></span>
                                </i>
                                <div class="d-flex flex-stack flex-grow-1">
                                    <div class="fw-semibold">
                                        <h4 class="text-gray-900 fw-bold">{{ __('Application Not Approved') }}</h4>
                                        <div class="fs-6 text-gray-700">
                                            {{ __('Unfortunately, your application was not approved. Please contact support for more information.') }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @else
                        <div class="text-center py-10">
                            <i class="ki-duotone ki-document fs-5tx text-gray-300 mb-5">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                            <h3 class="text-gray-600">{{ __('No Application Found') }}</h3>
                            <p class="text-muted">{{ __('We could not find an application associated with your account.') }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

</x-default-layout>
