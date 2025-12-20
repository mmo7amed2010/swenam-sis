<x-default-layout>

    @section('title')
        {{ __('Admin Dashboard') }}
    @endsection

    @section('breadcrumbs')
        {{ Breadcrumbs::render('dashboard') }}
    @endsection

    @php
        // Ensure Metronic vendor assets required by this page are loaded
        addVendors(['echarts']);
    @endphp

    {{-- KPI Overview --}}
    <div class="row g-5 mb-6">
        <x-stat-card
            icon="profile-user"
            :label="__('Total Users')"
            :value="number_format($kpis['total_users'] ?? 0)"
            color="primary"
            col-class="col-md-6 col-xl-3"
        />
        <x-stat-card
            icon="people"
            :label="__('Students')"
            :value="number_format($kpis['total_students'] ?? 0)"
            color="success"
            col-class="col-md-6 col-xl-3"
        />
        <x-stat-card
            icon="teacher"
            :label="__('Instructors')"
            :value="number_format($kpis['total_instructors'] ?? 0)"
            color="warning"
            col-class="col-md-6 col-xl-3"
        />
        <x-stat-card
            icon="shield"
            :label="__('Admins')"
            :value="number_format($kpis['total_admins'] ?? 0)"
            color="danger"
            col-class="col-md-6 col-xl-3"
        />
    </div>

    <div class="row g-5 mb-6">
        <x-stat-card
            icon="category"
            :label="__('Programs')"
            :value="number_format($kpis['total_programs'] ?? 0)"
            color="primary"
            col-class="col-md-6 col-xl-3"
            :tooltip="__('Active: ') . number_format($kpis['active_programs'] ?? 0)"
        />
        <x-stat-card
            icon="book"
            :label="__('Courses')"
            :value="number_format($kpis['total_courses'] ?? 0)"
            color="info"
            col-class="col-md-6 col-xl-3"
            :tooltip="__('Active: ') . number_format($kpis['active_courses'] ?? 0)"
        />
        <x-stat-card
            icon="send"
            :label="__('Submissions (7d)')"
            :value="number_format($kpis['submissions_last_7d'] ?? 0)"
            color="info"
            col-class="col-md-6 col-xl-3"
        />
        <x-stat-card
            icon="rocket"
            :label="__('Quiz Attempts (7d)')"
            :value="number_format($kpis['quiz_attempts_last_7d'] ?? 0)"
            color="primary"
            col-class="col-md-6 col-xl-3"
            :tooltip="__('Pending applications: ') . number_format($kpis['pending_applications'] ?? 0)"
        />
    </div>

    <div class="row g-5 g-xl-10 mb-5 mb-xl-10">
        {{-- Charts --}}
        <div class="col-xl-8">
            <x-cards.section
                :title="__('Analytics')"
                :subtitle="__('High-level trends and activity')"
                flush="true"
                class="mb-5 mb-xl-10"
            >
                <div class="row g-5">
                    <div class="col-xl-12">
                        <div class="card card-flush h-xl-100">
                            <div class="card-header pt-5">
                                <h3 class="card-title align-items-start flex-column">
                                    <span class="card-label fw-bold text-gray-900">{{ __('User Registrations (Last 6 Months)') }}</span>
                                    <span class="text-gray-500 mt-1 fw-semibold fs-6">{{ __('New users created') }}</span>
                                </h3>
                            </div>
                            <div class="card-body pt-0">
                                <div id="Admin_Monthly_Users_Chart" style="height: 350px; min-height: 350px;"></div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-6">
                        <div class="card card-flush h-xl-100">
                            <div class="card-header pt-5">
                                <h3 class="card-title align-items-start flex-column">
                                    <span class="card-label fw-bold text-gray-900">{{ __('Activity (Last 14 Days)') }}</span>
                                    <span class="text-gray-500 mt-1 fw-semibold fs-6">{{ __('Submissions and quiz attempts') }}</span>
                                </h3>
                            </div>
                            <div class="card-body pt-0">
                                <div id="Admin_Activity_By_Day_Chart" style="height: 350px; min-height: 350px;"></div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-6">
                        <div class="card card-flush h-xl-100">
                            <div class="card-header pt-5">
                                <h3 class="card-title align-items-start flex-column">
                                    <span class="card-label fw-bold text-gray-900">{{ __('User Types') }}</span>
                                    <span class="text-gray-500 mt-1 fw-semibold fs-6">{{ __('Distribution by role') }}</span>
                                </h3>
                            </div>
                            <div class="card-body pt-0">
                                <div id="Admin_User_Types_Chart" style="height: 350px; min-height: 350px;"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </x-cards.section>
        </div>

        {{-- Sidebar --}}
        <div class="col-xl-4">
            <x-cards.section
                :title="__('Quick Status')"
                flush="true"
                class="mb-5 mb-xl-10"
            >
                <div class="d-flex flex-column gap-4">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center">
                            <span class="symbol symbol-35px me-3">
                                <span class="symbol-label bg-light-warning">
                                    <i class="ki-duotone ki-notepad-bookmark fs-3 text-warning">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                        <span class="path3"></span>
                                    </i>
                                </span>
                            </span>
                            <span class="fw-semibold text-gray-700">{{ __('Pending Applications') }}</span>
                        </div>
                        <span class="badge badge-light-warning fw-bold">{{ number_format($kpis['pending_applications'] ?? 0) }}</span>
                    </div>

                    <div class="separator separator-dashed"></div>

                    <div class="d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center">
                            <span class="symbol symbol-35px me-3">
                                <span class="symbol-label bg-light-info">
                                    <i class="ki-duotone ki-send fs-3 text-info">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                </span>
                            </span>
                            <span class="fw-semibold text-gray-700">{{ __('Submissions (7d)') }}</span>
                        </div>
                        <span class="badge badge-light-info fw-bold">{{ number_format($kpis['submissions_last_7d'] ?? 0) }}</span>
                    </div>

                    <div class="separator separator-dashed"></div>

                    <div class="d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center">
                            <span class="symbol symbol-35px me-3">
                                <span class="symbol-label bg-light-primary">
                                    <i class="ki-duotone ki-rocket fs-3 text-primary">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                </span>
                            </span>
                            <span class="fw-semibold text-gray-700">{{ __('Quiz Attempts (7d)') }}</span>
                        </div>
                        <span class="badge badge-light-primary fw-bold">{{ number_format($kpis['quiz_attempts_last_7d'] ?? 0) }}</span>
                    </div>
                </div>
            </x-cards.section>

            {{-- Announcements Widget --}}
            <x-dashboard.announcements-widget />
        </div>
    </div>

    @php
        // Expose chart datasets for ECharts init
        $adminDashboardCharts = $charts ?? [];
    @endphp

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Defensive: ECharts may not be loaded on some layouts/pages
                if (typeof echarts === 'undefined') {
                    console.warn('ECharts not found: admin dashboard charts will not render.');
                    return;
                }

                const charts = @json($adminDashboardCharts);
                renderAdminMonthlyUsersChart(charts?.monthly_user_registrations);
                renderAdminActivityByDayChart(charts?.activity_by_day);
                renderAdminUserTypesChart(charts?.user_types);
            });

            function renderAdminMonthlyUsersChart(data) {
                const el = document.getElementById('Admin_Monthly_Users_Chart');
                if (!el) return;

                const labels = data?.labels || [];
                const counts = data?.counts || [];

                const chart = echarts.init(el);
                chart.setOption({
                    tooltip: { trigger: 'axis' },
                    grid: { left: 40, right: 20, top: 20, bottom: 40 },
                    xAxis: { type: 'category', data: labels, axisLabel: { rotate: 30 } },
                    yAxis: { type: 'value' },
                    series: [
                        {
                            name: 'Users',
                            type: 'bar',
                            data: counts,
                            itemStyle: { color: '#009EF7' }
                        }
                    ]
                });

                window.addEventListener('resize', function() {
                    chart.resize();
                });
            }

            function renderAdminActivityByDayChart(data) {
                const el = document.getElementById('Admin_Activity_By_Day_Chart');
                if (!el) return;

                const labels = data?.labels || [];
                const submissions = data?.submissions || [];
                const attempts = data?.quiz_attempts || [];

                const chart = echarts.init(el);
                chart.setOption({
                    tooltip: { trigger: 'axis' },
                    legend: { data: ['Submissions', 'Quiz Attempts'], bottom: 0 },
                    grid: { left: 40, right: 20, top: 20, bottom: 60 },
                    xAxis: { type: 'category', data: labels, axisLabel: { rotate: 30 } },
                    yAxis: { type: 'value' },
                    series: [
                        {
                            name: 'Submissions',
                            type: 'line',
                            smooth: true,
                            data: submissions,
                            itemStyle: { color: '#1BC5BD' },
                            areaStyle: { opacity: 0.15 }
                        },
                        {
                            name: 'Quiz Attempts',
                            type: 'line',
                            smooth: true,
                            data: attempts,
                            itemStyle: { color: '#FFA800' },
                            areaStyle: { opacity: 0.10 }
                        }
                    ]
                });

                window.addEventListener('resize', function() {
                    chart.resize();
                });
            }

            function renderAdminUserTypesChart(data) {
                const el = document.getElementById('Admin_User_Types_Chart');
                if (!el) return;

                const seriesData = [];
                if (data && typeof data === 'object') {
                    Object.keys(data).forEach(function(key, idx) {
                        if (key === 'unknown' && Number(data[key] || 0) === 0) return;
                        seriesData.push({
                            name: key,
                            value: Number(data[key] || 0),
                            itemStyle: { color: ['#009EF7', '#50CD89', '#FFA800', '#7E8299'][idx % 4] }
                        });
                    });
                }

                const chart = echarts.init(el);
                chart.setOption({
                    tooltip: { trigger: 'item', formatter: '{b}: {c} ({d}%)' },
                    legend: { orient: 'horizontal', bottom: 0 },
                    series: [
                        {
                            type: 'pie',
                            radius: ['45%', '75%'],
                            center: ['50%', '45%'],
                            data: seriesData
                        }
                    ]
                });

                window.addEventListener('resize', function() {
                    chart.resize();
                });
            }
        </script>
    @endpush

</x-default-layout>
