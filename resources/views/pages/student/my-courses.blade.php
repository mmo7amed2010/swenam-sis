<x-default-layout>

    @section('title')
        {{ __('My Courses') }}
    @endsection

    @section('breadcrumbs')
        {{ Breadcrumbs::render('dashboard') }}
    @endsection

    <div class="row g-5 g-xl-10 mb-5 mb-xl-10">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-header border-0 pt-5">
                    <h3 class="card-title align-items-start flex-column">
                        <span class="card-label fw-bold fs-3 mb-1">{{ __('Access Your Courses') }}</span>
                        <span class="text-muted mt-1 fw-semibold fs-7">{{ __('Click below to access your courses in the Learning Management System') }}</span>
                    </h3>
                </div>
                <div class="card-body py-10">
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

                    @if($hasLmsAccount)
                        <div class="text-center">
                            <div class="mb-8">
                                <i class="ki-duotone ki-book-open fs-5tx text-primary mb-5">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                    <span class="path3"></span>
                                    <span class="path4"></span>
                                </i>
                                <h2 class="fs-1 fw-bold text-gray-800 mb-3">{{ __('Ready to Learn?') }}</h2>
                                <p class="text-gray-600 fs-5 mb-8">
                                    {{ __('Access your courses, assignments, quizzes, and track your progress in the Learning Management System.') }}
                                </p>
                            </div>
                            <a href="{{ route('student.my-courses.redirect') }}" class="btn btn-primary btn-lg px-10">
                                <i class="ki-duotone ki-entrance-left fs-2 me-2">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                                {{ __('Go to My Courses') }}
                            </a>
                            <p class="text-muted fs-7 mt-5">
                                {{ __('You will be securely redirected to the Learning Management System') }}
                            </p>
                        </div>
                    @else
                        <div class="text-center">
                            <div class="mb-8">
                                <i class="ki-duotone ki-information-5 fs-5tx text-warning mb-5">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                    <span class="path3"></span>
                                </i>
                                <h2 class="fs-1 fw-bold text-gray-800 mb-3">{{ __('Account Setup Pending') }}</h2>
                                <p class="text-gray-600 fs-5 mb-5">
                                    {{ __('Your Learning Management System account is being set up.') }}
                                </p>
                                <p class="text-gray-500 fs-6">
                                    {{ __('Please wait for your account to be fully provisioned, or contact administration if you believe this is an error.') }}
                                </p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

</x-default-layout>
