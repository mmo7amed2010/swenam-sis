<!--begin::sidebar menu-->
<div class="app-sidebar-menu overflow-hidden flex-column-fluid">
    <!--begin::Menu wrapper-->
    <div id="kt_app_sidebar_menu_wrapper" class="app-sidebar-wrapper hover-scroll-overlay-y my-5" data-kt-scroll="true"
        data-kt-scroll-activate="true" data-kt-scroll-height="auto"
        data-kt-scroll-dependencies="#kt_app_sidebar_logo, #kt_app_sidebar_footer"
        data-kt-scroll-wrappers="#kt_app_sidebar_menu" data-kt-scroll-offset="5px" data-kt-scroll-save-state="true">
        <!--begin::Menu-->
        <div class="menu menu-column menu-rounded menu-sub-indention px-3 fw-semibold fs-6" id="#kt_app_sidebar_menu"
            data-kt-menu="true" data-kt-menu-expand="false">
            {{-- Student Menu Items (visible only for students) --}}
            @if(auth()->user()->isStudent())
                <!--begin:Menu item - Student Dashboard-->
                <div class="menu-item">
                    <!--begin:Menu link-->
                    <a class="menu-link {{ request()->routeIs('dashboard') ? 'active' : '' }}"
                        href="{{ route('dashboard') }}">
                        <span class="menu-icon">{!! getIcon('element-11', 'fs-2') !!}</span>
                        <span class="menu-title">{{ __('Dashboard') }}</span>
                    </a>
                    <!--end:Menu link-->
                </div>
                <!--end:Menu item-->

                <!--begin:Menu item - My Application -->
                <div class="menu-item">
                    <!--begin:Menu link-->
                    <a class="menu-link {{ request()->routeIs('student.program.*') ? 'active' : '' }}"
                        href="{{ route('student.program.index') }}">
                        <span class="menu-icon">{!! getIcon('document', 'fs-2') !!}</span>
                        <span class="menu-title">{{ __('My Application') }}</span>
                    </a>
                    <!--end:Menu link-->
                </div>
                <!--end:Menu item-->

                <!--begin:Menu item - My Courses -->
                <div class="menu-item">
                    <!--begin:Menu link-->
                    @if(auth()->user()->hasLmsAccount())
                        <a class="menu-link" href="{{ route('student.my-courses.redirect') }}" target="_blank">
                            <span class="menu-icon">{!! getIcon('book-open', 'fs-2') !!}</span>
                            <span class="menu-title">{{ __('My Courses') }}</span>
                            <span class="menu-badge">
                                <span class="badge badge-light-success badge-circle">
                                    {!! getIcon('entrance-left', 'fs-7') !!}
                                </span>
                            </span>
                        </a>
                    @else
                        <span class="menu-link disabled" data-bs-toggle="tooltip" data-bs-placement="right" title="{{ __('Available after application approval') }}">
                            <span class="menu-icon">{!! getIcon('book-open', 'fs-2 text-muted') !!}</span>
                            <span class="menu-title text-muted">{{ __('My Courses') }}</span>
                            <span class="menu-badge">
                                <span class="badge badge-light-secondary badge-circle">
                                    {!! getIcon('lock', 'fs-7') !!}
                                </span>
                            </span>
                        </span>
                    @endif
                    <!--end:Menu link-->
                </div>
                <!--end:Menu item-->

                <!--begin:Menu item - My Grades -->
                <div class="menu-item">
                    <!--begin:Menu link-->
                    @if(auth()->user()->hasLmsAccount())
                        <a class="menu-link" href="{{ route('student.my-grades.redirect') }}" target="_blank">
                            <span class="menu-icon">{!! getIcon('chart-simple', 'fs-2') !!}</span>
                            <span class="menu-title">{{ __('My Grades') }}</span>
                            <span class="menu-badge">
                                <span class="badge badge-light-success badge-circle">
                                    {!! getIcon('entrance-left', 'fs-7') !!}
                                </span>
                            </span>
                        </a>
                    @else
                        <span class="menu-link disabled" data-bs-toggle="tooltip" data-bs-placement="right" title="{{ __('Available after application approval') }}">
                            <span class="menu-icon">{!! getIcon('chart-simple', 'fs-2 text-muted') !!}</span>
                            <span class="menu-title text-muted">{{ __('My Grades') }}</span>
                            <span class="menu-badge">
                                <span class="badge badge-light-secondary badge-circle">
                                    {!! getIcon('lock', 'fs-7') !!}
                                </span>
                            </span>
                        </span>
                    @endif
                    <!--end:Menu link-->
                </div>
                <!--end:Menu item-->
            @endif

            {{-- Instructor Menu Items (visible only for instructors) --}}
            @if(auth()->user()->isInstructor())
                <!--begin:Menu item - Instructor Dashboard-->
                <div class="menu-item">
                    <!--begin:Menu link-->
                    <a class="menu-link {{ request()->routeIs('dashboard') ? 'active' : '' }}"
                        href="{{ route('dashboard') }}">
                        <span class="menu-icon">{!! getIcon('element-11', 'fs-2') !!}</span>
                        <span class="menu-title">{{ __('Dashboard') }}</span>
                    </a>
                    <!--end:Menu link-->
                </div>
                <!--end:Menu item-->

                <!--begin:Menu item - My Courses-->
                <div class="menu-item">
                    <!--begin:Menu link-->
                    <a class="menu-link {{ request()->routeIs('instructor.courses.*') || request()->routeIs('instructor.programs.*') ? 'active' : '' }}"
                        href="{{ route('instructor.courses.index') }}">
                        <span class="menu-icon">{!! getIcon('book-open', 'fs-2') !!}</span>
                        <span class="menu-title">{{ __('My Courses') }}</span>
                    </a>
                    <!--end:Menu link-->
                </div>
                <!--end:Menu item-->
            @endif

            {{-- Admin Menu Items --}}
            @if(auth()->user()->isAdmin())
                <!--begin:Menu item-->
                <div class="menu-item">
                    <!--begin:Menu link-->
                    <a class="menu-link {{ request()->routeIs('dashboard') ? 'active' : '' }}"
                        href="{{ route('dashboard') }}">
                        <span class="menu-icon">{!! getIcon('element-11', 'fs-2') !!}</span>
                        <span class="menu-title">{{ __('Dashboards') }}</span>
                    </a>
                    <!--end:Menu link-->
                </div>
                <!--end:Menu item-->

                <!--begin:Menu item-->
                <div class="menu-item pt-5">
                    <!--begin:Menu content-->
                    <div class="menu-content">
                        <span class="menu-heading fw-bold text-uppercase fs-7">{{ __('Apps') }}</span>
                    </div>
                    <!--end:Menu content-->
                </div>
                <!--end:Menu item-->

                <!--begin:Menu item - User Management-->
                <div data-kt-menu-trigger="click" class="menu-item menu-accordion">
                    <!--begin:Menu link-->
                    <span class="menu-link">
                        <span class="menu-icon">{!! getIcon('abstract-28', 'fs-2') !!}</span>
                        <span class="menu-title">{{ __('User Management') }}</span>
                        <span class="menu-arrow"></span>
                    </span>
                    <!--end:Menu link-->
                    <!--begin:Menu sub-->
                    <div class="menu-sub menu-sub-accordion">
                        <!--begin:Menu item-->
                        <div class="menu-item">
                            <!--begin:Menu link-->
                            <a class="menu-link {{ request()->routeIs('user-management.users.*') ? 'active' : '' }}"
                                href="{{ route('user-management.users.index') }}">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">{{ __('Admins') }}</span>
                            </a>
                            <!--end:Menu link-->
                        </div>
                        <!--end:Menu item-->
                        <!--begin:Menu item-->
                        <div class="menu-item">
                            <!--begin:Menu link-->
                            <a class="menu-link {{ request()->routeIs('admin.students.*') ? 'active' : '' }}"
                                href="{{ route('admin.students.index') }}">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">{{ __('Students') }}</span>
                            </a>
                            <!--end:Menu link-->
                        </div>
                        <!--end:Menu item-->

                    </div>
                    <!--end:Menu sub-->
                </div>
                <!--end:Menu item-->

                {{-- Programs and Intakes are managed from LMS (master system) --}}

                <!--begin:Menu item - Applications-->
                <div class="menu-item">
                    <!--begin:Menu link-->
                    <a class="menu-link {{ request()->routeIs('admin.applications.*') ? 'active' : '' }}"
                        href="{{ route('admin.applications.index') }}">
                        <span class="menu-icon">{!! getIcon('document', 'fs-2') !!}</span>
                        <span class="menu-title">{{ __('Applications') }}</span>
                    </a>
                    <!--end:Menu link-->
                </div>
                <!--end:Menu item-->

                <!--begin:Menu item - System Announcements-->
                <div class="menu-item">
                    <!--begin:Menu link-->
                    <a class="menu-link {{ request()->routeIs('admin.announcements.*') ? 'active' : '' }}"
                        href="{{ route('admin.announcements.index') }}">
                        <span class="menu-icon">{!! getIcon('notification-bing', 'fs-2') !!}</span>
                        <span class="menu-title">{{ __('Announcements') }}</span>
                    </a>
                    <!--end:Menu link-->
                </div>
                <!--end:Menu item-->
            @endif

        </div>
        <!--end::Menu-->
    </div>
    <!--end::Menu wrapper-->
</div>
<!--end::sidebar menu-->
