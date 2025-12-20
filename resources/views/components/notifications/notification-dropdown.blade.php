{{--
* Notification Dropdown Component
*
* Bell icon with unread count badge and dropdown showing recent notifications.
* Displays in header navbar.
*
* Uses Metronic design patterns.
--}}

@php
    $unreadCount = auth()->user()->unreadNotifications->count();
    $recentNotifications = auth()->user()->notifications()->latest()->limit(10)->get();
@endphp

<div class="app-navbar-item ms-1 ms-md-4">
    <!--begin::Menu wrapper-->
    <div class="position-relative" id="kt_header_notifications_toggle">
        <!--begin::Menu toggle-->
        <div class="btn btn-icon btn-custom btn-icon-muted btn-active-light btn-active-color-primary w-35px h-35px"
            data-kt-menu-trigger="{default: 'click', lg: 'hover'}" data-kt-menu-attach="parent"
            data-kt-menu-placement="bottom-end">
            {!! getIcon('notification-bing', 'fs-2') !!}

            @if($unreadCount > 0)
                <span class="badge badge-circle badge-danger position-absolute top-0 start-100 translate-middle"
                    style="font-size: 0.65rem; padding: 0.35rem 0.5rem;">
                    {{ $unreadCount > 99 ? '99+' : $unreadCount }}
                </span>
            @endif
        </div>
        <!--end::Menu toggle-->

        <!--begin::Menu-->
        <div class="menu menu-sub menu-sub-dropdown menu-column w-350px w-lg-375px" data-kt-menu="true">
            <!--begin::Heading-->
            <div class="d-flex flex-column bgi-no-repeat rounded-top"
                style="background-image:url('{{ asset('assets/media/misc/menu-header-bg.jpg') }}')">
                <!--begin::Title-->
                <h3 class="text-white fw-semibold px-9 mt-10 mb-6">
                    Notifications
                    @if($unreadCount > 0)
                        <span class="fs-8 opacity-75 ps-3">{{ $unreadCount }} unread</span>
                    @endif
                </h3>
                <!--end::Title-->

                <!--begin::Tabs-->
                <ul class="nav nav-line-tabs nav-line-tabs-2x nav-stretch fw-semibold px-9">
                    <li class="nav-item">
                        <a class="nav-link text-white opacity-75 opacity-state-100 pb-4 active" data-bs-toggle="tab"
                            href="#kt_topbar_notifications_all">
                            All
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white opacity-75 opacity-state-100 pb-4" data-bs-toggle="tab"
                            href="#kt_topbar_notifications_unread">
                            Unread ({{ $unreadCount }})
                        </a>
                    </li>
                </ul>
                <!--end::Tabs-->
            </div>
            <!--end::Heading-->

            <!--begin::Tab content-->
            <div class="tab-content">
                <!--begin::Tab panel (All)-->
                <div class="tab-pane fade show active" id="kt_topbar_notifications_all" role="tabpanel">
                    <!--begin::Items-->
                    <div class="scroll-y mh-325px my-5 px-8">
                        @forelse($recentNotifications as $notification)
                            <x-notifications.notification-item :notification="$notification" />
                        @empty
                            <div class="text-center py-10">
                                <div class="text-gray-400 fs-6">
                                    {!! getIcon('notification', 'fs-3x text-gray-300 mb-3') !!}
                                    <div>No notifications yet</div>
                                </div>
                            </div>
                        @endforelse
                    </div>
                    <!--end::Items-->

                    <!--begin::View more-->
                    @if($recentNotifications->count() > 0)
                        <div class="py-3 text-center border-top">
                            <a href="{{ route('notifications.index') }}"
                                class="btn btn-color-gray-600 btn-active-color-primary">
                                View All Notifications
                                {!! getIcon('arrow-right', 'fs-5 ms-1') !!}
                            </a>
                        </div>
                    @endif
                    <!--end::View more-->
                </div>
                <!--end::Tab panel-->

                <!--begin::Tab panel (Unread)-->
                <div class="tab-pane fade" id="kt_topbar_notifications_unread" role="tabpanel">
                    <!--begin::Items-->
                    <div class="scroll-y mh-325px my-5 px-8">
                        @forelse(auth()->user()->unreadNotifications as $notification)
                            <x-notifications.notification-item :notification="$notification" />
                        @empty
                            <div class="text-center py-10">
                                <div class="text-gray-400 fs-6">
                                    {!! getIcon('check-circle', 'fs-3x text-success mb-3') !!}
                                    <div>All caught up!</div>
                                </div>
                            </div>
                        @endforelse
                    </div>
                    <!--end::Items-->
                </div>
                <!--end::Tab panel-->
            </div>
            <!--end::Tab content-->

            <!--begin::Actions-->
            @if($unreadCount > 0)
                <div class="py-3 text-center border-top">
                    <form action="{{ route('notifications.mark-all-read') }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-light-primary">
                            {!! getIcon('check', 'fs-6 me-1') !!}
                            Mark All as Read
                        </button>
                    </form>
                </div>
            @endif
            <!--end::Actions-->
        </div>
        <!--end::Menu-->
    </div>
    <!--end::Menu wrapper-->
</div>