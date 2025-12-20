<!--begin:Dashboards menu-->
<div class="menu-state-bg menu-extended overflow-hidden overflow-lg-visible" data-kt-menu-dismiss="true">
    <!--begin:Row-->
    <div class="row">
        <!--begin:Col-->
        <div class="col-lg-8 mb-3 mb-lg-0 py-3 px-3 py-lg-6 px-lg-6">
            <!--begin:Row-->
            <div class="row">
                <!--begin:Col-->
                <div class="col-lg-12 mb-3">
                    <!--begin:Menu item-->
                    <div class="menu-item p-0 m-0">
                        <!--begin:Menu link-->
                        <a href="{{ route('dashboard') }}" class="menu-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                            <span class="menu-custom-icon d-flex flex-center flex-shrink-0 rounded w-40px h-40px me-3">
                                {!! getIcon('element-11', 'text-primary fs-1') !!}
                            </span>
                            <span class="d-flex flex-column">
                                <span class="fs-6 fw-bold text-gray-800">{{ __('Admin Analytics Dashboard') }}</span>
                                <span class="fs-7 fw-semibold text-muted">{{ __('Unified system metrics powered by the default connection') }}</span>
                            </span>
                        </a>
                        <!--end:Menu link-->
                    </div>
                    <!--end:Menu item-->
                </div>
                <!--end:Col-->
            </div>
            <!--end:Row-->

            <div class="separator separator-dashed mx-5 my-5"></div>

            <div class="d-flex flex-stack flex-wrap flex-lg-nowrap gap-2 mx-5">
                <div class="d-flex flex-column me-5">
                    <div class="fs-6 fw-bold text-gray-800">{{ __('Need quick insights?') }}</div>
                    <div class="fs-7 fw-semibold text-muted">{{ __('Head to the admin dashboard to view analytics, alerts, and activity feed in one place.') }}</div>
                </div>
                <a href="{{ route('dashboard') }}" class="btn btn-sm btn-primary fw-bold">{{ __('Open Dashboard') }}</a>
            </div>
        </div>
        <!--end:Col-->
        <!--begin:Col-->
        <div class="menu-more bg-light col-lg-4 py-3 px-3 py-lg-6 px-lg-6 rounded-end">
            <!--begin:Heading-->
            <h4 class="fs-6 fs-lg-4 text-gray-800 fw-bold mt-3 mb-3 ms-4">{{ __('System Snapshot') }}</h4>
            <!--end:Heading-->

            <div class="menu-item p-0 m-0">
                <div class="menu-link py-2 d-flex align-items-center">
                    <span class="menu-title flex-grow-1">
                        {!! getIcon('database', 'fs-6 me-2 text-primary') !!}
                        {{ __('Default Connection') }}
                    </span>
                    <span class="badge badge-light-success badge-sm">
                        {{ config('database.default') }}
                    </span>
                </div>
            </div>

            <div class="menu-item p-0 m-0">
                <div class="menu-link py-2 d-flex align-items-center">
                    <span class="menu-title flex-grow-1">
                        {!! getIcon('shield-search', 'fs-6 me-2 text-primary') !!}
                        {{ __('Secure, single-source analytics') }}
                    </span>
                </div>
            </div>
        </div>
        <!--end:Col-->
    </div>
    <!--end:Row-->
</div>
<!--end:Dashboards menu-->
