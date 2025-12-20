<x-default-layout>

    @section('title')
        {{ __('Instructor Dashboard') }}
    @endsection

    @section('breadcrumbs')
        {{ Breadcrumbs::render('dashboard') }}
    @endsection

    <div class="row g-5 mb-6">
        <div class="col-12">
            <div class="card">
                <div class="card-body p-10 text-center">
                    <div class="symbol symbol-100px mb-5">
                        <div class="symbol-label bg-light-primary">
                            <i class="ki-duotone ki-book-open fs-3x text-primary">
                                <span class="path1"></span>
                                <span class="path2"></span>
                                <span class="path3"></span>
                                <span class="path4"></span>
                            </i>
                        </div>
                    </div>
                    <h2 class="fw-bold text-gray-800 mb-3">{{ __('Welcome, Instructor!') }}</h2>
                    <p class="text-muted fs-5 mb-7">
                        {{ __('Course management, grading, and student interactions are handled in the Learning Management System (LMS).') }}
                    </p>
                    @if(config('services.lms.url'))
                        <a href="{{ config('services.lms.url') }}" target="_blank" class="btn btn-primary">
                            <i class="ki-duotone ki-entrance-left fs-2 me-2">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                            {{ __('Go to LMS') }}
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>

</x-default-layout>
