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
                <!--begin:Menu item - Student Home-->
                <div class="menu-item">
                    <!--begin:Menu link-->
                    <a class="menu-link {{ request()->routeIs('dashboard') ? 'active' : '' }}"
                        href="{{ route('dashboard') }}">
                        <span class="menu-icon">{!! getIcon('element-11', 'fs-2') !!}</span>
                        <span class="menu-title">{{ __('Home') }}</span>
                    </a>
                    <!--end:Menu link-->
                </div>
                <!--end:Menu item-->

                <!--begin:Menu item - Notifications-->
                <div class="menu-item">
                    <!--begin:Menu link-->
                    <a class="menu-link {{ request()->routeIs('notifications.*') ? 'active' : '' }}"
                        href="{{ route('notifications.index') }}">
                        <span class="menu-icon">{!! getIcon('notification-bing', 'fs-2') !!}</span>
                        <span class="menu-title">{{ __('Notifications') }}</span>
                        @if(auth()->user()->unreadNotifications->count() > 0)
                            <span class="menu-badge">
                                <span class="badge badge-sm badge-circle badge-danger">{{ auth()->user()->unreadNotifications->count() }}</span>
                            </span>
                        @endif
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
                    @if(auth()->user()->isApplicationApproved() && auth()->user()->hasLmsAccount())
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
                        <span class="menu-link disabled" data-bs-toggle="tooltip" data-bs-placement="right" title="{{ auth()->user()->student?->studentApplication?->isRejected() ? __('Your application has been rejected') : __('Available after application approval') }}">
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
                    @if(auth()->user()->isApplicationApproved() && auth()->user()->hasLmsAccount())
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
                        <span class="menu-link disabled" data-bs-toggle="tooltip" data-bs-placement="right" title="{{ auth()->user()->student?->studentApplication?->isRejected() ? __('Your application has been rejected') : __('Available after application approval') }}">
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

                <!--begin:Menu item - My Profile-->
                <div class="menu-item">
                    <!--begin:Menu link-->
                    <a class="menu-link {{ request()->routeIs('student.profile.*') ? 'active' : '' }}"
                        href="{{ route('student.profile.show') }}">
                        <span class="menu-icon">{!! getIcon('profile-circle', 'fs-2') !!}</span>
                        <span class="menu-title">{{ __('My Profile') }}</span>
                    </a>
                    <!--end:Menu link-->
                </div>
                <!--end:Menu item-->

                <!--begin:Menu item - Support / Help Center-->
                <div data-kt-menu-trigger="click" class="menu-item menu-accordion">
                    <!--begin:Menu link-->
                    <span class="menu-link">
                        <span class="menu-icon">{!! getIcon('question-2', 'fs-2') !!}</span>
                        <span class="menu-title">{{ __('Support / Help Center') }}</span>
                        <span class="menu-arrow"></span>
                    </span>
                    <!--end:Menu link-->
                    <!--begin:Menu sub-->
                    <div class="menu-sub menu-sub-accordion">
                        <!--begin:Menu item - Swenam College FAQs-->
                        <div class="menu-item">
                            <!--begin:Menu link-->
                            <a class="menu-link" href="https://swenamcollege.ca/college-policies/" target="_blank">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">{{ __('Swenam College FAQs') }}</span>
                                <span class="menu-badge">
                                    <span class="badge badge-light-primary badge-circle">
                                        {!! getIcon('entrance-left', 'fs-7') !!}
                                    </span>
                                </span>
                            </a>
                            <!--end:Menu link-->
                        </div>
                        <!--end:Menu item-->

                        <!--begin:Menu item - Contact Us-->
                        <div class="menu-item">
                            <!--begin:Menu link-->
                            <a class="menu-link" href="https://swenamcollege.ca/contact-us/" target="_blank">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">{{ __('Contact Us') }}</span>
                                <span class="menu-badge">
                                    <span class="badge badge-light-primary badge-circle">
                                        {!! getIcon('entrance-left', 'fs-7') !!}
                                    </span>
                                </span>
                            </a>
                            <!--end:Menu link-->
                        </div>
                        <!--end:Menu item-->

                        <!--begin:Menu item - Campus Directory (Greyed Out)-->
                        <div class="menu-item">
                            <!--begin:Menu link-->
                            <span class="menu-link disabled" data-bs-toggle="tooltip" data-bs-placement="right" title="{{ __('Coming Soon') }}">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot bg-secondary"></span>
                                </span>
                                <span class="menu-title text-muted">{{ __('Campus Directory') }}</span>
                                <span class="menu-badge">
                                    <span class="badge badge-light-secondary badge-circle">
                                        {!! getIcon('lock', 'fs-7') !!}
                                    </span>
                                </span>
                            </span>
                            <!--end:Menu link-->
                        </div>
                        <!--end:Menu item-->
                    </div>
                    <!--end:Menu sub-->
                </div>
                <!--end:Menu item-->

                <!--begin:Menu item - Course Registration (Greyed Out with Sub-items)-->
                <div data-kt-menu-trigger="click" class="menu-item menu-accordion">
                    <!--begin:Menu link-->
                    <span class="menu-link disabled" data-bs-toggle="tooltip" data-bs-placement="right" title="{{ __('Coming Soon') }}">
                        <span class="menu-icon">{!! getIcon('calendar-add', 'fs-2 text-muted') !!}</span>
                        <span class="menu-title text-muted">{{ __('Course Registration') }}</span>
                        <span class="menu-arrow"></span>
                        <span class="menu-badge">
                            <span class="badge badge-light-secondary badge-circle">
                                {!! getIcon('lock', 'fs-7') !!}
                            </span>
                        </span>
                    </span>
                    <!--end:Menu link-->
                    <!--begin:Menu sub-->
                    <div class="menu-sub menu-sub-accordion">
                        <!--begin:Menu item-->
                        <div class="menu-item">
                            <span class="menu-link disabled" data-bs-toggle="tooltip" title="{{ __('Coming Soon') }}">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title text-muted">{{ __('Register for courses') }}</span>
                                <span class="menu-badge">
                                    <span class="badge badge-light-secondary badge-circle">
                                        {!! getIcon('lock', 'fs-7') !!}
                                    </span>
                                </span>
                            </span>
                        </div>
                        <!--end:Menu item-->
                        <!--begin:Menu item-->
                        <div class="menu-item">
                            <span class="menu-link disabled" data-bs-toggle="tooltip" title="{{ __('Coming Soon') }}">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title text-muted">{{ __('Add/Drop courses') }}</span>
                                <span class="menu-badge">
                                    <span class="badge badge-light-secondary badge-circle">
                                        {!! getIcon('lock', 'fs-7') !!}
                                    </span>
                                </span>
                            </span>
                        </div>
                        <!--end:Menu item-->
                        <!--begin:Menu item-->
                        <div class="menu-item">
                            <span class="menu-link disabled" data-bs-toggle="tooltip" title="{{ __('Coming Soon') }}">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title text-muted">{{ __('Request repeats') }}</span>
                                <span class="menu-badge">
                                    <span class="badge badge-light-secondary badge-circle">
                                        {!! getIcon('lock', 'fs-7') !!}
                                    </span>
                                </span>
                            </span>
                        </div>
                        <!--end:Menu item-->
                        <!--begin:Menu item-->
                        <div class="menu-item">
                            <span class="menu-link disabled" data-bs-toggle="tooltip" title="{{ __('Coming Soon') }}">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title text-muted">{{ __('View available sections') }}</span>
                                <span class="menu-badge">
                                    <span class="badge badge-light-secondary badge-circle">
                                        {!! getIcon('lock', 'fs-7') !!}
                                    </span>
                                </span>
                            </span>
                        </div>
                        <!--end:Menu item-->
                    </div>
                    <!--end:Menu sub-->
                </div>
                <!--end:Menu item-->


                <!--begin:Menu item - Financials (Greyed Out with Sub-items)-->
                <div data-kt-menu-trigger="click" class="menu-item menu-accordion">
                    <!--begin:Menu link-->
                    <span class="menu-link disabled" data-bs-toggle="tooltip" data-bs-placement="right" title="{{ __('Coming Soon') }}">
                        <span class="menu-icon">{!! getIcon('dollar', 'fs-2 text-muted') !!}</span>
                        <span class="menu-title text-muted">{{ __('Financials') }}</span>
                        <span class="menu-arrow"></span>
                        <span class="menu-badge">
                            <span class="badge badge-light-secondary badge-circle">
                                {!! getIcon('lock', 'fs-7') !!}
                            </span>
                        </span>
                    </span>
                    <!--end:Menu link-->
                    <!--begin:Menu sub-->
                    <div class="menu-sub menu-sub-accordion">
                        <!--begin:Menu item-->
                        <div class="menu-item">
                            <span class="menu-link disabled" data-bs-toggle="tooltip" title="{{ __('Coming Soon') }}">
                                <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                <span class="menu-title text-muted">{{ __('Tuition fees') }}</span>
                                <span class="menu-badge"><span class="badge badge-light-secondary badge-circle">{!! getIcon('lock', 'fs-7') !!}</span></span>
                            </span>
                        </div>
                        <div class="menu-item">
                            <span class="menu-link disabled" data-bs-toggle="tooltip" title="{{ __('Coming Soon') }}">
                                <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                <span class="menu-title text-muted">{{ __('Payment history') }}</span>
                                <span class="menu-badge"><span class="badge badge-light-secondary badge-circle">{!! getIcon('lock', 'fs-7') !!}</span></span>
                            </span>
                        </div>
                        <div class="menu-item">
                            <span class="menu-link disabled" data-bs-toggle="tooltip" title="{{ __('Coming Soon') }}">
                                <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                <span class="menu-title text-muted">{{ __('Financial aid status') }}</span>
                                <span class="menu-badge"><span class="badge badge-light-secondary badge-circle">{!! getIcon('lock', 'fs-7') !!}</span></span>
                            </span>
                        </div>
                        <div class="menu-item">
                            <span class="menu-link disabled" data-bs-toggle="tooltip" title="{{ __('Coming Soon') }}">
                                <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                <span class="menu-title text-muted">{{ __('Online payments') }}</span>
                                <span class="menu-badge"><span class="badge badge-light-secondary badge-circle">{!! getIcon('lock', 'fs-7') !!}</span></span>
                            </span>
                        </div>
                        <!--end:Menu item-->
                    </div>
                    <!--end:Menu sub-->
                </div>
                <!--end:Menu item-->

                <!--begin:Menu item - Documents (Greyed Out with Sub-items)-->
                <div data-kt-menu-trigger="click" class="menu-item menu-accordion">
                    <!--begin:Menu link-->
                    <span class="menu-link disabled" data-bs-toggle="tooltip" data-bs-placement="right" title="{{ __('Coming Soon') }}">
                        <span class="menu-icon">{!! getIcon('folder', 'fs-2 text-muted') !!}</span>
                        <span class="menu-title text-muted">{{ __('Documents') }}</span>
                        <span class="menu-arrow"></span>
                        <span class="menu-badge">
                            <span class="badge badge-light-secondary badge-circle">
                                {!! getIcon('lock', 'fs-7') !!}
                            </span>
                        </span>
                    </span>
                    <!--end:Menu link-->
                    <!--begin:Menu sub-->
                    <div class="menu-sub menu-sub-accordion">
                        <div class="menu-item">
                            <span class="menu-link disabled" data-bs-toggle="tooltip" title="{{ __('Coming Soon') }}">
                                <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                <span class="menu-title text-muted">{{ __('Enrollment letters') }}</span>
                                <span class="menu-badge"><span class="badge badge-light-secondary badge-circle">{!! getIcon('lock', 'fs-7') !!}</span></span>
                            </span>
                        </div>
                        <div class="menu-item">
                            <span class="menu-link disabled" data-bs-toggle="tooltip" title="{{ __('Coming Soon') }}">
                                <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                <span class="menu-title text-muted">{{ __('Payment receipts') }}</span>
                                <span class="menu-badge"><span class="badge badge-light-secondary badge-circle">{!! getIcon('lock', 'fs-7') !!}</span></span>
                            </span>
                        </div>
                        <div class="menu-item">
                            <span class="menu-link disabled" data-bs-toggle="tooltip" title="{{ __('Coming Soon') }}">
                                <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                <span class="menu-title text-muted">{{ __('ID card') }}</span>
                                <span class="menu-badge"><span class="badge badge-light-secondary badge-circle">{!! getIcon('lock', 'fs-7') !!}</span></span>
                            </span>
                        </div>
                        <div class="menu-item">
                            <span class="menu-link disabled" data-bs-toggle="tooltip" title="{{ __('Coming Soon') }}">
                                <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                <span class="menu-title text-muted">{{ __('Transcript requests') }}</span>
                                <span class="menu-badge"><span class="badge badge-light-secondary badge-circle">{!! getIcon('lock', 'fs-7') !!}</span></span>
                            </span>
                        </div>
                        <div class="menu-item">
                            <span class="menu-link disabled" data-bs-toggle="tooltip" title="{{ __('Coming Soon') }}">
                                <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                <span class="menu-title text-muted">{{ __('Study permit support letters') }}</span>
                                <span class="menu-badge"><span class="badge badge-light-secondary badge-circle">{!! getIcon('lock', 'fs-7') !!}</span></span>
                            </span>
                        </div>
                    </div>
                    <!--end:Menu sub-->
                </div>
                <!--end:Menu item-->

                <!--begin:Menu item - Attendance Summary (Greyed Out with Sub-items)-->
                <div data-kt-menu-trigger="click" class="menu-item menu-accordion">
                    <!--begin:Menu link-->
                    <span class="menu-link disabled" data-bs-toggle="tooltip" data-bs-placement="right" title="{{ __('Coming Soon') }}">
                        <span class="menu-icon">{!! getIcon('calendar-tick', 'fs-2 text-muted') !!}</span>
                        <span class="menu-title text-muted">{{ __('Attendance Summary') }}</span>
                        <span class="menu-arrow"></span>
                        <span class="menu-badge">
                            <span class="badge badge-light-secondary badge-circle">
                                {!! getIcon('lock', 'fs-7') !!}
                            </span>
                        </span>
                    </span>
                    <!--end:Menu link-->
                    <!--begin:Menu sub-->
                    <div class="menu-sub menu-sub-accordion">
                        <div class="menu-item">
                            <span class="menu-link disabled" data-bs-toggle="tooltip" title="{{ __('Coming Soon') }}">
                                <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                <span class="menu-title text-muted">{{ __('Attendance per course') }}</span>
                                <span class="menu-badge"><span class="badge badge-light-secondary badge-circle">{!! getIcon('lock', 'fs-7') !!}</span></span>
                            </span>
                        </div>
                        <div class="menu-item">
                            <span class="menu-link disabled" data-bs-toggle="tooltip" title="{{ __('Coming Soon') }}">
                                <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                <span class="menu-title text-muted">{{ __('Reports') }}</span>
                                <span class="menu-badge"><span class="badge badge-light-secondary badge-circle">{!! getIcon('lock', 'fs-7') !!}</span></span>
                            </span>
                        </div>
                    </div>
                    <!--end:Menu sub-->
                </div>
                <!--end:Menu item-->

                <!--begin:Menu item - Official Grades (Greyed Out with Sub-items)-->
                <div data-kt-menu-trigger="click" class="menu-item menu-accordion">
                    <!--begin:Menu link-->
                    <span class="menu-link disabled" data-bs-toggle="tooltip" data-bs-placement="right" title="{{ __('Coming Soon') }}">
                        <span class="menu-icon">{!! getIcon('medal-star', 'fs-2 text-muted') !!}</span>
                        <span class="menu-title text-muted">{{ __('Official Grades') }}</span>
                        <span class="menu-arrow"></span>
                        <span class="menu-badge">
                            <span class="badge badge-light-secondary badge-circle">
                                {!! getIcon('lock', 'fs-7') !!}
                            </span>
                        </span>
                    </span>
                    <!--end:Menu link-->
                    <!--begin:Menu sub-->
                    <div class="menu-sub menu-sub-accordion">
                        <div class="menu-item">
                            <span class="menu-link disabled" data-bs-toggle="tooltip" title="{{ __('Coming Soon') }}">
                                <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                <span class="menu-title text-muted">{{ __('Official term grades') }}</span>
                                <span class="menu-badge"><span class="badge badge-light-secondary badge-circle">{!! getIcon('lock', 'fs-7') !!}</span></span>
                            </span>
                        </div>
                        <div class="menu-item">
                            <span class="menu-link disabled" data-bs-toggle="tooltip" title="{{ __('Coming Soon') }}">
                                <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                <span class="menu-title text-muted">{{ __('GPA calculation') }}</span>
                                <span class="menu-badge"><span class="badge badge-light-secondary badge-circle">{!! getIcon('lock', 'fs-7') !!}</span></span>
                            </span>
                        </div>
                        <div class="menu-item">
                            <span class="menu-link disabled" data-bs-toggle="tooltip" title="{{ __('Coming Soon') }}">
                                <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                <span class="menu-title text-muted">{{ __('Transcript previews') }}</span>
                                <span class="menu-badge"><span class="badge badge-light-secondary badge-circle">{!! getIcon('lock', 'fs-7') !!}</span></span>
                            </span>
                        </div>
                    </div>
                    <!--end:Menu sub-->
                </div>
                <!--end:Menu item-->

                <!--begin:Menu item - Schedule / Timetable (Greyed Out with Sub-items)-->
                <div data-kt-menu-trigger="click" class="menu-item menu-accordion">
                    <!--begin:Menu link-->
                    <span class="menu-link disabled" data-bs-toggle="tooltip" data-bs-placement="right" title="{{ __('Coming Soon') }}">
                        <span class="menu-icon">{!! getIcon('calendar-2', 'fs-2 text-muted') !!}</span>
                        <span class="menu-title text-muted">{{ __('Schedule / Timetable') }}</span>
                        <span class="menu-arrow"></span>
                        <span class="menu-badge">
                            <span class="badge badge-light-secondary badge-circle">
                                {!! getIcon('lock', 'fs-7') !!}
                            </span>
                        </span>
                    </span>
                    <!--end:Menu link-->
                    <!--begin:Menu sub-->
                    <div class="menu-sub menu-sub-accordion">
                        <div class="menu-item">
                            <span class="menu-link disabled" data-bs-toggle="tooltip" title="{{ __('Coming Soon') }}">
                                <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                <span class="menu-title text-muted">{{ __('Weekly class schedule') }}</span>
                                <span class="menu-badge"><span class="badge badge-light-secondary badge-circle">{!! getIcon('lock', 'fs-7') !!}</span></span>
                            </span>
                        </div>
                        <div class="menu-item">
                            <span class="menu-link disabled" data-bs-toggle="tooltip" title="{{ __('Coming Soon') }}">
                                <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                <span class="menu-title text-muted">{{ __('Room numbers') }}</span>
                                <span class="menu-badge"><span class="badge badge-light-secondary badge-circle">{!! getIcon('lock', 'fs-7') !!}</span></span>
                            </span>
                        </div>
                        <div class="menu-item">
                            <span class="menu-link disabled" data-bs-toggle="tooltip" title="{{ __('Coming Soon') }}">
                                <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                <span class="menu-title text-muted">{{ __('Online meeting links') }}</span>
                                <span class="menu-badge"><span class="badge badge-light-secondary badge-circle">{!! getIcon('lock', 'fs-7') !!}</span></span>
                            </span>
                        </div>
                    </div>
                    <!--end:Menu sub-->
                </div>
                <!--end:Menu item-->
            @endif

            {{-- Instructor Menu Items (visible only for instructors) --}}
            @if(auth()->user()->isInstructor())
                <!--begin:Menu item - Instructor Home-->
                <div class="menu-item">
                    <!--begin:Menu link-->
                    <a class="menu-link {{ request()->routeIs('dashboard') ? 'active' : '' }}"
                        href="{{ route('dashboard') }}">
                        <span class="menu-icon">{!! getIcon('element-11', 'fs-2') !!}</span>
                        <span class="menu-title">{{ __('Home') }}</span>
                    </a>
                    <!--end:Menu link-->
                </div>
                <!--end:Menu item-->

                <!--begin:Menu item - Notifications-->
                <div class="menu-item">
                    <!--begin:Menu link-->
                    <a class="menu-link {{ request()->routeIs('notifications.*') ? 'active' : '' }}"
                        href="{{ route('notifications.index') }}">
                        <span class="menu-icon">{!! getIcon('notification-bing', 'fs-2') !!}</span>
                        <span class="menu-title">{{ __('Notifications') }}</span>
                        @if(auth()->user()->unreadNotifications->count() > 0)
                            <span class="menu-badge">
                                <span class="badge badge-sm badge-circle badge-danger">{{ auth()->user()->unreadNotifications->count() }}</span>
                            </span>
                        @endif
                    </a>
                    <!--end:Menu link-->
                </div>
                <!--end:Menu item-->


            @endif

            {{-- Agent Menu Items (visible only for agents) --}}
            @if(auth()->user()->isAgent())
                <!--begin:Menu item - Agent Home-->
                <div class="menu-item">
                    <!--begin:Menu link-->
                    <a class="menu-link {{ request()->routeIs('dashboard') ? 'active' : '' }}"
                        href="{{ route('dashboard') }}">
                        <span class="menu-icon">{!! getIcon('element-11', 'fs-2') !!}</span>
                        <span class="menu-title">{{ __('Home') }}</span>
                    </a>
                    <!--end:Menu link-->
                </div>
                <!--end:Menu item-->

                <!--begin:Menu item - Notifications-->
                <div class="menu-item">
                    <!--begin:Menu link-->
                    <a class="menu-link {{ request()->routeIs('notifications.*') ? 'active' : '' }}"
                        href="{{ route('notifications.index') }}">
                        <span class="menu-icon">{!! getIcon('notification-bing', 'fs-2') !!}</span>
                        <span class="menu-title">{{ __('Notifications') }}</span>
                        @if(auth()->user()->unreadNotifications->count() > 0)
                            <span class="menu-badge">
                                <span class="badge badge-sm badge-circle badge-danger">{{ auth()->user()->unreadNotifications->count() }}</span>
                            </span>
                        @endif
                    </a>
                    <!--end:Menu link-->
                </div>
                <!--end:Menu item-->

                <!--begin:Menu item - Applications Section Header-->
                <div class="menu-item pt-5">
                    <!--begin:Menu content-->
                    <div class="menu-content">
                        <span class="menu-heading fw-bold text-uppercase fs-7">{{ __('Applications') }}</span>
                    </div>
                    <!--end:Menu content-->
                </div>
                <!--end:Menu item-->

                <!--begin:Menu item - My Applications-->
                <div class="menu-item">
                    <!--begin:Menu link-->
                    <a class="menu-link {{ request()->routeIs('agent.applications.index') || request()->routeIs('agent.applications.show') ? 'active' : '' }}"
                        href="{{ route('agent.applications.index') }}">
                        <span class="menu-icon">{!! getIcon('document', 'fs-2') !!}</span>
                        <span class="menu-title">{{ __('My Applications') }}</span>
                    </a>
                    <!--end:Menu link-->
                </div>
                <!--end:Menu item-->

                <!--begin:Menu item - Create Application-->
                <div class="menu-item">
                    <!--begin:Menu link-->
                    <a class="menu-link {{ request()->routeIs('agent.applications.create') ? 'active' : '' }}"
                        href="{{ route('agent.applications.create') }}">
                        <span class="menu-icon">{!! getIcon('plus-circle', 'fs-2') !!}</span>
                        <span class="menu-title">{{ __('Create Application') }}</span>
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
                        <span class="menu-title">{{ __('Home') }}</span>
                    </a>
                    <!--end:Menu link-->
                </div>
                <!--end:Menu item-->

                <!--begin:Menu item - Notifications-->
                <div class="menu-item">
                    <!--begin:Menu link-->
                    <a class="menu-link {{ request()->routeIs('notifications.*') ? 'active' : '' }}"
                        href="{{ route('notifications.index') }}">
                        <span class="menu-icon">{!! getIcon('notification-bing', 'fs-2') !!}</span>
                        <span class="menu-title">{{ __('Notifications') }}</span>
                        @if(auth()->user()->unreadNotifications->count() > 0)
                            <span class="menu-badge">
                                <span class="badge badge-sm badge-circle badge-danger">{{ auth()->user()->unreadNotifications->count() }}</span>
                            </span>
                        @endif
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
                        {{-- Admins - Only for Super Admins --}}
                        @if(auth()->user()->isSuperAdmin())
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
                        @endif
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
                        <!--begin:Menu item-->
                        <div class="menu-item">
                            <!--begin:Menu link-->
                            <a class="menu-link {{ request()->routeIs('admin.agents.*') ? 'active' : '' }}"
                                href="{{ route('admin.agents.index') }}">
                                <span class="menu-bullet">
                                    <span class="bullet bullet-dot"></span>
                                </span>
                                <span class="menu-title">{{ __('Agents') }}</span>
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

                <!--begin:Menu item - Contract Templates-->
                <div class="menu-item">
                    <!--begin:Menu link-->
                    <a class="menu-link {{ request()->routeIs('admin.contract-templates.*') ? 'active' : '' }}"
                        href="{{ route('admin.contract-templates.index') }}">
                        <span class="menu-icon">{!! getIcon('clipboard', 'fs-2') !!}</span>
                        <span class="menu-title">{{ __('Contract Templates') }}</span>
                    </a>
                    <!--end:Menu link-->
                </div>
                <!--end:Menu item-->

                <!--begin:Menu item - System Announcements (Admin)-->
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
