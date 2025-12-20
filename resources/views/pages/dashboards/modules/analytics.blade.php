    {{-- System Analytics Overview --}}
    <div class="row g-5 g-xl-10 mb-5 mb-xl-10">
        <div class="col-xl-12">
            <div class="card card-flush h-xl-100">
                <div class="card-header pt-5">
                    <h3 class="card-title align-items-start flex-column">
                        <span class="card-label fw-bold text-gray-900 fs-3">📊 {{ __('System Analytics Overview') }}</span>
                        <span class="text-gray-500 mt-1 fw-semibold fs-6">{{ __('Comprehensive system utilization metrics for') }} {{ ucfirst($connection) }}</span>
                    </h3>
                </div>
                <div class="card-body pt-0">
                    <div class="row g-0">
                        {{-- Recent Exams --}}
                        <div class="col-6 col-md-3">
                            <div class="border border-dashed border-gray-300 card-rounded p-6 mb-6">
                                <div class="d-flex align-items-center">
                                    <div class="symbol symbol-45px me-5">
                                        <div class="symbol-label bg-light-primary">
                                            <i class="ki-duotone ki-notepad-bookmark fs-2x text-primary">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                                <span class="path3"></span>
                                            </i>
                                        </div>
                                    </div>
                                    <div class="d-flex flex-column flex-grow-1">
                                        <span class="fs-2hx fw-bold text-gray-800">{{ number_format($data['overview']['recent_exams'] ?? 0) }}</span>
                                        <span class="fw-semibold text-gray-500 fs-6">{{ __('Recent Exams (7d)') }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Recent Questions --}}
                        <div class="col-6 col-md-3">
                            <div class="border border-dashed border-gray-300 card-rounded p-6 mb-6">
                                <div class="d-flex align-items-center">
                                    <div class="symbol symbol-45px me-5">
                                        <div class="symbol-label bg-light-success">
                                            <i class="ki-duotone ki-questionnaire-tablet fs-2x text-success">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                                <span class="path3"></span>
                                            </i>
                                        </div>
                                    </div>
                                    <div class="d-flex flex-column flex-grow-1">
                                        <span class="fs-2hx fw-bold text-gray-800">{{ number_format($data['overview']['recent_questions'] ?? 0) }}</span>
                                        <span class="fw-semibold text-gray-500 fs-6">{{ __('Recent Questions (7d)') }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Recent Students --}}
                        <div class="col-6 col-md-3">
                            <div class="border border-dashed border-gray-300 card-rounded p-6 mb-6">
                                <div class="d-flex align-items-center">
                                    <div class="symbol symbol-45px me-5">
                                        <div class="symbol-label bg-light-warning">
                                            <i class="ki-duotone ki-people fs-2x text-warning">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                                <span class="path3"></span>
                                            </i>
                                        </div>
                                    </div>
                                    <div class="d-flex flex-column flex-grow-1">
                                        <span class="fs-2hx fw-bold text-gray-800">{{ number_format($data['overview']['recent_students'] ?? 0) }}</span>
                                        <span class="fw-semibold text-gray-500 fs-6">{{ __('New Students (7d)') }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Recent Attempts --}}
                        <div class="col-6 col-md-3">
                            <div class="border border-dashed border-gray-300 card-rounded p-6 mb-6">
                                <div class="d-flex align-items-center">
                                    <div class="symbol symbol-45px me-5">
                                        <div class="symbol-label bg-light-info">
                                            <i class="ki-duotone ki-rocket fs-2x text-info">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                            </i>
                                        </div>
                                    </div>
                                    <div class="d-flex flex-column flex-grow-1">
                                        <span class="fs-2hx fw-bold text-gray-800">{{ number_format($data['overview']['recent_attempts'] ?? 0) }}</span>
                                        <span class="fw-semibold text-gray-500 fs-6">{{ __('Recent Attempts (7d)') }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- System Utilization Analytics --}}
    <div class="row g-5 g-xl-10 mb-5 mb-xl-10">
        {{-- Monthly Growth --}}
        <div class="col-xl-6">
            <div class="card card-flush h-xl-100">
                <div class="card-header pt-5">
                    <h3 class="card-title align-items-start flex-column">
                        <span class="card-label fw-bold text-gray-900">📈 {{ __('Monthly User Growth') }}</span>
                        <span class="text-gray-500 mt-1 fw-semibold fs-6">{{ __('User registration trends') }}</span>
                    </h3>
                </div>
                <div class="card-body pt-0">
                    <div id="Monthly_Growth_Chart" style="height: 400px; min-height: 400px;"></div>
                </div>
            </div>
        </div>

        {{-- Feature Usage --}}
        <div class="col-xl-6">
            <div class="card card-flush h-xl-100">
                <div class="card-header pt-5">
                    <h3 class="card-title align-items-start flex-column">
                        <span class="card-label fw-bold text-gray-900">⚡ {{ __('Feature Usage') }}</span>
                        <span class="text-gray-500 mt-1 fw-semibold fs-6">{{ __('System feature utilization') }}</span>
                    </h3>
                </div>
                <div class="card-body pt-0">
                    <div id="Feature_Usage_Chart" style="height: 400px; min-height: 400px;"></div>
                </div>
            </div>
        </div>
    </div>

    {{-- User Engagement Analytics --}}
    <div class="row g-5 g-xl-10 mb-5 mb-xl-10">
        {{-- User Types --}}
        <div class="col-xl-6">
            <div class="card card-flush h-xl-100">
                <div class="card-header pt-5">
                    <h3 class="card-title align-items-start flex-column">
                        <span class="card-label fw-bold text-gray-900">👥 {{ __('User Types') }}</span>
                        <span class="text-gray-500 mt-1 fw-semibold fs-6">{{ __('Distribution by user type') }}</span>
                    </h3>
                </div>
                <div class="card-body pt-0">
                    <div id="User_Types_Chart" style="height: 400px; min-height: 400px;"></div>
                </div>
            </div>
        </div>

        {{-- Accessibility Distribution --}}
        <div class="col-xl-6">
            <div class="card card-flush h-xl-100">
                <div class="card-header pt-5">
                    <h3 class="card-title align-items-start flex-column">
                        <span class="card-label fw-bold text-gray-900">♿ {{ __('Accessibility Distribution') }}</span>
                        <span class="text-gray-500 mt-1 fw-semibold fs-6">{{ __('Users by accessibility needs') }}</span>
                    </h3>
                </div>
                <div class="card-body pt-0">
                    <div id="Disability_Distribution_Chart" style="height: 400px; min-height: 400px;"></div>
                </div>
            </div>
        </div>
    </div>

    {{-- Login Activity Timeline --}}
    <div class="row g-5 g-xl-10 mb-5 mb-xl-10">
        <div class="col-xl-12">
            <div class="card card-flush h-xl-100">
                <div class="card-header pt-5">
                    <h3 class="card-title align-items-start flex-column">
                        <span class="card-label fw-bold text-gray-900">🔐 {{ __('Login Activity Timeline') }}</span>
                        <span class="text-gray-500 mt-1 fw-semibold fs-6">{{ __('User login patterns and peak times') }}</span>
                    </h3>
                </div>
                <div class="card-body pt-0">
                    <div id="Login_Activity_Chart" style="height: 400px; min-height: 400px;"></div>
                </div>
            </div>
        </div>
    </div>

    {{-- Administrative Analytics --}}
    <div class="row g-5 g-xl-10 mb-5 mb-xl-10">
        {{-- Content Approval Status --}}
        <div class="col-xl-12">
            <div class="card card-flush h-xl-100">
                <div class="card-header pt-5">
                    <h3 class="card-title align-items-start flex-column">
                        <span class="card-label fw-bold text-gray-900">✅ {{ __('Content Approval Status') }}</span>
                        <span class="text-gray-500 mt-1 fw-semibold fs-6">{{ __('Content moderation and approval workflow') }}</span>
                    </h3>
                </div>
                <div class="card-body pt-0">
                    <div id="Content_Approval_Chart" style="height: 400px; min-height: 400px;"></div>
                </div>
            </div>
        </div>
    </div>

    {{-- Admin Activity Overview --}}
    <div class="row g-5 g-xl-10 mb-5 mb-xl-10">
        <div class="col-xl-12">
            <div class="card card-flush h-xl-100">
                <div class="card-header pt-5">
                    <h3 class="card-title align-items-start flex-column">
                        <span class="card-label fw-bold text-gray-900">⚙️ {{ __('Admin Activity Overview') }}</span>
                        <span class="text-gray-500 mt-1 fw-semibold fs-6">{{ __('Administrative actions and system management') }}</span>
                    </h3>
                </div>
                <div class="card-body pt-0">
                    <div id="Admin_Activity_Chart" style="height: 400px; min-height: 400px;"></div>
                </div>
            </div>
        </div>
    </div>

    {{-- Comparative Analytics --}}
    <div class="row g-5 g-xl-10 mb-5 mb-xl-10">
        {{-- Performance Trends --}}
        <div class="col-xl-6">
            <div class="card card-flush h-xl-100">
                <div class="card-header pt-5">
                    <h3 class="card-title align-items-start flex-column">
                        <span class="card-label fw-bold text-gray-900">📈 {{ __('Performance Trends') }}</span>
                        <span class="text-gray-500 mt-1 fw-semibold fs-6">{{ __('System performance over time') }}</span>
                    </h3>
                </div>
                <div class="card-body pt-0">
                    <div id="Performance_Trends_Chart" style="height: 400px; min-height: 400px;"></div>
                </div>
            </div>
        </div>

        {{-- System Health Metrics --}}
        <div class="col-xl-6">
            <div class="card card-flush h-xl-100">
                <div class="card-header pt-5">
                    <h3 class="card-title align-items-start flex-column">
                        <span class="card-label fw-bold text-gray-900">💚 {{ __('System Health Metrics') }}</span>
                        <span class="text-gray-500 mt-1 fw-semibold fs-6">{{ __('Overall system performance indicators') }}</span>
                    </h3>
                </div>
                <div class="card-body pt-0">
                    <div id="System_Health_Chart" style="height: 400px; min-height: 400px;"></div>
                </div>
            </div>
            </div>
</div>

{{-- Enhanced User Engagement Analytics --}}
<div class="row g-5 g-xl-10 mb-5 mb-xl-10">
    {{-- User Engagement Chart --}}
    <div class="col-xl-6">
        <div class="card card-flush h-xl-100">
            <div class="card-header pt-5">
                <h3 class="card-title align-items-start flex-column">
                    <span class="card-label fw-bold text-gray-900">👥 {{ __('User Engagement Analytics') }}</span>
                    <span class="text-gray-500 mt-1 fw-semibold fs-6">{{ __('User types and login patterns') }}</span>
                </h3>
            </div>
            <div class="card-body pt-0">
                <div id="User_Engagement_Chart" style="height: 400px; min-height: 400px;"></div>
            </div>
        </div>
    </div>

    {{-- Time-based Analytics Chart --}}
    <div class="col-xl-6">
        <div class="card card-flush h-xl-100">
            <div class="card-header pt-5">
                <h3 class="card-title align-items-start flex-column">
                    <span class="card-label fw-bold text-gray-900">⏰ {{ __('Time-based Analytics') }}</span>
                    <span class="text-gray-500 mt-1 fw-semibold fs-6">{{ __('Activity patterns over time') }}</span>
                </h3>
            </div>
            <div class="card-body pt-0">
                <div id="Time_Based_Analytics_Chart" style="height: 400px; min-height: 400px;"></div>
            </div>
        </div>
    </div>
</div>

{{-- Exam Completion Analytics --}}
<div class="row g-5 g-xl-10 mb-5 mb-xl-10">
    <div class="col-xl-12">
        <div class="card card-flush h-xl-100">
            <div class="card-header pt-5">
                <h3 class="card-title align-items-start flex-column">
                    <span class="card-label fw-bold text-gray-900">📊 {{ __('Exam Completion Analytics') }}</span>
                    <span class="text-gray-500 mt-1 fw-semibold fs-6">{{ __('Completion rates and duration analysis') }}</span>
                </h3>
            </div>
            <div class="card-body pt-0">
                <div id="Exam_Completion_Stats_Chart" style="height: 400px; min-height: 400px;"></div>
            </div>
        </div>
    </div>
</div>

{{-- Behavioral Analytics Section --}}
<div class="row g-5 g-xl-10 mb-5 mb-xl-10">
    {{-- User Action Distribution --}}
    <div class="col-xl-6">
        <div class="card card-flush h-xl-100">
            <div class="card-header pt-5">
                <h3 class="card-title align-items-start flex-column">
                    <span class="card-label fw-bold text-gray-900">🎯 {{ __('User Action Distribution') }}</span>
                    <span class="text-gray-500 mt-1 fw-semibold fs-6">{{ __('Distribution of user behaviors and actions') }}</span>
                </h3>
            </div>
            <div class="card-body pt-0">
                <div id="User_Action_Distribution_Chart" style="height: 400px; min-height: 400px;"></div>
            </div>
        </div>
    </div>

    {{-- Device Usage Patterns --}}
    <div class="col-xl-6">
        <div class="card card-flush h-xl-100">
            <div class="card-header pt-5">
                <h3 class="card-title align-items-start flex-column">
                    <span class="card-label fw-bold text-gray-900">💻 {{ __('Device Usage Patterns') }}</span>
                    <span class="text-gray-500 mt-1 fw-semibold fs-6">{{ __('Device type distribution and usage statistics') }}</span>
                </h3>
            </div>
            <div class="card-body pt-0">
                <div id="Device_Usage_Chart" style="height: 400px; min-height: 400px;"></div>
            </div>
        </div>
    </div>
</div>

{{-- Security Events and Top Users Tables --}}
<div class="row g-5 g-xl-10 mb-5 mb-xl-10">
    {{-- Recent Security Events --}}
    <div class="col-xl-6">
        <div class="card card-flush h-xl-100">
            <div class="card-header pt-5">
                <h3 class="card-title align-items-start flex-column">
                    <span class="card-label fw-bold text-gray-900">🚨 {{ __('Recent Security Events') }}</span>
                    <span class="text-gray-500 mt-1 fw-semibold fs-6">{{ __('Security warnings and flagged activities') }}</span>
                </h3>
            </div>
            <div class="card-body pt-0">
                @if (isset($data['tables']['recent_security_events']['recent_warnings']) && count($data['tables']['recent_security_events']['recent_warnings']) > 0)
                    <div class="table-responsive">
                        <table class="table table-row-dashed align-middle gs-0 gy-3 my-0">
                            <thead>
                                <tr class="fw-bold text-muted">
                                    <th class="min-w-150px">{{ __('Student') }}</th>
                                    <th class="min-w-120px">{{ __('Exam') }}</th>
                                    <th class="min-w-100px">{{ __('Reason') }}</th>
                                    <th class="min-w-100px">{{ __('Date') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($data['tables']['recent_security_events']['recent_warnings'] as $warning)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="symbol symbol-30px me-3">
                                                <div class="symbol-label bg-light-warning">
                                                    <i class="ki-duotone ki-warning-2 fs-6 text-warning">
                                                        <span class="path1"></span>
                                                        <span class="path2"></span>
                                                        <span class="path3"></span>
                                                    </i>
                                                </div>
                                            </div>
                                            <div class="d-flex flex-column">
                                                <span class="text-gray-800 fw-bold">{{ $warning->student_name ?? __('Unknown') }}</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="text-gray-600">{{ $warning->exam_title ?? __('N/A') }}</span>
                                    </td>
                                    <td>
                                        <span class="badge badge-light-warning fs-7">{{ Str::limit($warning->reason ?? __('Security Alert'), 20) }}</span>
                                    </td>
                                    <td>
                                        <span class="text-gray-500 fs-7">{{ date('M j, Y', strtotime($warning->created_at)) }}</span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-10">
                        <div class="symbol symbol-100px mx-auto mb-7">
                            <div class="symbol-label bg-light-success">
                                <i class="ki-duotone ki-shield-tick fs-3x text-success">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                            </div>
                        </div>
                        <h3 class="text-gray-800 fw-bold mb-3">{{ __('No Security Issues') }}</h3>
                        <p class="text-gray-500 fs-6">{{ __('All systems operating normally with no security alerts') }}</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Top Active Users --}}
    <div class="col-xl-6">
        <div class="card card-flush h-xl-100">
            <div class="card-header pt-5">
                <h3 class="card-title align-items-start flex-column">
                    <span class="card-label fw-bold text-gray-900">⭐ {{ __('Top Active Users') }}</span>
                    <span class="text-gray-500 mt-1 fw-semibold fs-6">{{ __('Most active users by engagement') }}</span>
                </h3>
            </div>
            <div class="card-body pt-0">
                @if (isset($data['tables']['top_active_users']) && count($data['tables']['top_active_users']) > 0)
                    <div class="table-responsive">
                        <table class="table table-row-dashed align-middle gs-0 gy-3 my-0">
                            <thead>
                                <tr class="fw-bold text-muted">
                                    <th class="min-w-200px">{{ __('User') }}</th>
                                    <th class="min-w-100px">{{ __('Actions') }}</th>
                                    <th class="min-w-100px">{{ __('Exams') }}</th>
                                    <th class="min-w-120px">{{ __('Last Activity') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($data['tables']['top_active_users'] as $index => $user)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="symbol symbol-30px me-3">
                                                <div class="symbol-label bg-light-{{ $index < 3 ? 'primary' : 'secondary' }}">
                                                    @if ($index === 0)
                                                        <i class="ki-duotone ki-crown fs-6 text-primary">
                                                            <span class="path1"></span>
                                                            <span class="path2"></span>
                                                        </i>
                                                    @else
                                                        <span class="text-{{ $index < 3 ? 'primary' : 'secondary' }} fw-bold">{{ $index + 1 }}</span>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="d-flex flex-column">
                                                <span class="text-gray-800 fw-bold fs-6">{{ Str::limit($user->name ?? __('Unknown'), 25) }}</span>
                                                <span class="text-gray-500 fs-7">{{ $user->email ?? __('N/A') }}</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge badge-light-primary fs-7">{{ number_format($user->total_actions ?? 0) }}</span>
                                    </td>
                                    <td>
                                        <span class="text-gray-600 fw-bold">{{ number_format($user->exams_involved ?? 0) }}</span>
                                    </td>
                                    <td>
                                        <span class="text-gray-500 fs-7">{{ date('M j, Y', strtotime($user->last_activity)) }}</span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-10">
                        <div class="symbol symbol-100px mx-auto mb-7">
                            <div class="symbol-label bg-light-primary">
                                <i class="ki-duotone ki-user fs-3x text-primary">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                            </div>
                        </div>
                        <h3 class="text-gray-800 fw-bold mb-3">{{ __('No User Data') }}</h3>
                        <p class="text-gray-500 fs-6">{{ __('User activity data will appear here') }}</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- System Alerts --}}
@if (isset($data['tables']['system_alerts']) && count($data['tables']['system_alerts']) > 0)
<div class="row g-5 g-xl-10 mb-5 mb-xl-10">
    <div class="col-xl-12">
        <div class="card card-flush h-xl-100">
            <div class="card-header pt-5">
                <h3 class="card-title align-items-start flex-column">
                    <span class="card-label fw-bold text-gray-900">🔔 {{ __('System Alerts') }}</span>
                    <span class="text-gray-500 mt-1 fw-semibold fs-6">{{ __('Recent system notifications and status updates') }}</span>
                </h3>
            </div>
            <div class="card-body pt-0">
                <div class="table-responsive">
                    <table class="table table-row-dashed align-middle gs-0 gy-3 my-0">
                        <thead>
                            <tr class="fw-bold text-muted">
                                <th class="min-w-100px">{{ __('Type') }}</th>
                                <th class="min-w-300px">{{ __('Message') }}</th>
                                <th class="min-w-200px">{{ __('Details') }}</th>
                                <th class="min-w-150px">{{ __('Timestamp') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($data['tables']['system_alerts'] as $alert)
                            <tr>
                                <td>
                                    @if ($alert->type === 'success')
                                        <span class="badge badge-light-success">{{ __('Success') }}</span>
                                    @elseif ($alert->type === 'warning')
                                        <span class="badge badge-light-warning">{{ __('Warning') }}</span>
                                    @elseif ($alert->type === 'error')
                                        <span class="badge badge-light-danger">{{ __('Error') }}</span>
                                    @else
                                        <span class="badge badge-light-info">{{ __('Info') }}</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="text-gray-800 fw-bold">{{ $alert->message ?? __('N/A') }}</span>
                                </td>
                                <td>
                                    <span class="text-gray-600">{{ Str::limit($alert->details ?? __('No details'), 50) }}</span>
                                </td>
                                <td>
                                    <span class="text-gray-500 fs-7">{{ date('M j, Y H:i', strtotime($alert->timestamp)) }}</span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endif

{{-- Chart Initialization Scripts --}}
    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                if (dashboardData.module === 'analytics' && dashboardData.moduleData) {
                    initializeAnalyticsCharts(dashboardData.moduleData);
                }
            });

            function initializeAnalyticsCharts(data) {
                if (data.charts && data.charts.monthly_growth) {
                    renderMonthlyGrowthChart(data.charts.monthly_growth);
                }
                if (data.charts && data.charts.feature_usage) {
                    renderFeatureUsageChart(data.charts.feature_usage);
                }
                if (data.charts && data.charts.user_types) {
                    renderUserTypesChart(data.charts.user_types);
                }

                console.log('data.charts.disability_distribution', data.charts.disability_distribution);

                if (data.charts && data.charts.disability_distribution) {
                    renderDisabilityDistributionChart(data.charts.disability_distribution);
                }
                if (data.charts && data.charts.login_activity) {
                    renderLoginActivityChart(data.charts.login_activity);
                }
                if (data.charts && data.charts.content_approval) {
                    renderContentApprovalChart(data.charts.content_approval);
                }
                if (data.charts && data.charts.admin_activity) {
                    renderAdminActivityChart(data.charts.admin_activity);
                }
                if (data.charts && data.charts.performance_trends) {
                    renderPerformanceTrendsChart(data.charts.performance_trends);
                }
                if (data.charts && data.charts.system_health) {
                    renderSystemHealthChart(data.charts.system_health);
                }

                // New analytics charts
                if (data.charts && data.charts.user_engagement) {
                    renderUserEngagementChart(data.charts.user_engagement);
                }
                if (data.charts && data.charts.time_based_analytics) {
                    renderTimeBasedAnalyticsChart(data.charts.time_based_analytics);
                }
                if (data.charts && data.charts.exam_completion_stats) {
                    renderExamCompletionStatsChart(data.charts.exam_completion_stats);
                }

                // Behavioral analytics charts
                if (data.charts && data.charts.behavioral_analytics) {
                    renderUserActionDistributionChart(data.charts.behavioral_analytics);
                }
                if (data.charts && data.charts.usage_distribution) {
                    renderDeviceUsageChart(data.charts.usage_distribution);
                }
            }

            function renderMonthlyGrowthChart(data) {
                var chartDom = document.getElementById('Monthly_Growth_Chart');
                if (!chartDom) return;

                var myChart = echarts.init(chartDom);
                var option = {
                    title: {
                        text: Lang.get('User Growth Trends'),
                        left: 'center',
                        textStyle: {
                            fontSize: 16,
                            fontWeight: 'bold'
                        }
                    },
                    tooltip: {
                        trigger: 'axis'
                    },
                    legend: {
                        data: ['New Users', 'Active Users'],
                        top: 30
                    },
                    xAxis: {
                        type: 'category',
                        data: data.months || []
                    },
                    yAxis: {
                        type: 'value',
                        name: 'Number of Users'
                    },
                    series: [{
                            name: 'New Users',
                            type: 'line',
                            smooth: true,
                            data: data.new_users || [],
                            itemStyle: {
                                color: '#3699FF'
                            },
                            areaStyle: {
                                opacity: 0.3
                            }
                        },
                        {
                            name: 'Active Users',
                            type: 'line',
                            smooth: true,
                            data: data.active_users || [],
                            itemStyle: {
                                color: '#50CD89'
                            },
                            areaStyle: {
                                opacity: 0.3
                            }
                        }
                    ]
                };

                myChart.setOption(option);
                window.addEventListener('resize', function() {
                    myChart.resize();
                });
            }

            function renderFeatureUsageChart(data) {
                var chartDom = document.getElementById('Feature_Usage_Chart');
                if (!chartDom) return;

                var myChart = echarts.init(chartDom);
                var option = {
                    title: {
                        text: Lang.get('Feature Utilization'),
                        left: 'center',
                        textStyle: {
                            fontSize: 16,
                            fontWeight: 'bold'
                        }
                    },
                    tooltip: {
                        trigger: 'axis',
                        axisPointer: {
                            type: 'shadow'
                        }
                    },
                    xAxis: {
                        type: 'value',
                        name: 'Usage Count'
                    },
                    yAxis: {
                        type: 'category',
                        data: Object.keys(data).reverse()
                    },
                    series: [{
                        type: 'bar',
                        data: Object.values(data).reverse(),
                        itemStyle: {
                            color: new echarts.graphic.LinearGradient(1, 0, 0, 0, [{
                                    offset: 0,
                                    color: '#50CD89'
                                },
                                {
                                    offset: 1,
                                    color: '#009EF7'
                                }
                            ])
                        },
                        label: {
                            show: true,
                            position: 'right'
                        }
                    }]
                };

                myChart.setOption(option);
                window.addEventListener('resize', function() {
                    myChart.resize();
                });
            }

            function renderUserTypesChart(data) {
                var chartDom = document.getElementById('User_Types_Chart');
                if (!chartDom) return;

                var myChart = echarts.init(chartDom);
                var option = {
                    title: {
                        text: Lang.get('User Type Distribution'),
                        left: 'center',
                        textStyle: {
                            fontSize: 16,
                            fontWeight: 'bold'
                        }
                    },
                    tooltip: {
                        trigger: 'item',
                        formatter: '{b}: {c} ({d}%)'
                    },
                    legend: {
                        orient: 'horizontal',
                        bottom: 10
                    },
                    series: [{
                        type: 'pie',
                        radius: '70%',
                        center: ['50%', '45%'],
                        data: Object.keys(data).map((key, index) => ({
                            value: data[key],
                            name: key,
                            itemStyle: {
                                color: ['#3699FF', '#50CD89', '#FFA800', '#F1416C', '#7239EA'][index % 5]
                            }
                        }))
                    }]
                };

                myChart.setOption(option);
                window.addEventListener('resize', function() {
                    myChart.resize();
                });
            }

            function renderDisabilityDistributionChart(data) {
                var chartDom = document.getElementById('Disability_Distribution_Chart');
                if (!chartDom) return;

                var myChart = echarts.init(chartDom);
                var option = {
                    title: {
                        text: Lang.get('Accessibility Needs'),
                        left: 'center',
                        textStyle: {
                            fontSize: 16,
                            fontWeight: 'bold'
                        }
                    },
                    tooltip: {
                        trigger: 'item'
                    },
                    series: [{
                        type: 'pie',
                        radius: ['40%', '70%'],
                        data: Object.keys(data).map((key, index) => ({
                            value: data[key],
                            name: key.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase()),
                            itemStyle: {
                                color: ['#1BC5BD', '#009EF7', '#FFC700', '#FF9500', '#7239EA'][index % 5]
                            }
                        }))
                    }]
                };

                myChart.setOption(option);
                window.addEventListener('resize', function() {
                    myChart.resize();
                });
            }

            function renderLoginActivityChart(data) {
                var chartDom = document.getElementById('Login_Activity_Chart');
                if (!chartDom) return;

                var myChart = echarts.init(chartDom);
                var option = {
                    title: {
                        text: Lang.get('Daily Login Activity'),
                        left: 'center',
                        textStyle: {
                            fontSize: 16,
                            fontWeight: 'bold'
                        }
                    },
                    tooltip: {
                        trigger: 'axis'
                    },
                    xAxis: {
                        type: 'category',
                        data: data.dates || []
                    },
                    yAxis: {
                        type: 'value',
                        name: Lang.get('Login Count')
                    },
                    series: [{
                        type: 'line',
                        smooth: true,
                        data: data.counts || [],
                        itemStyle: {
                            color: '#009EF7'
                        },
                        areaStyle: {
                            color: new echarts.graphic.LinearGradient(0, 0, 0, 1, [{
                                    offset: 0,
                                    color: 'rgba(0, 158, 247, 0.3)'
                                },
                                {
                                    offset: 1,
                                    color: 'rgba(0, 158, 247, 0.05)'
                                }
                            ])
                        }
                    }]
                };

                myChart.setOption(option);
                window.addEventListener('resize', function() {
                    myChart.resize();
                });
            }

            function renderContentApprovalChart(data) {
                var chartDom = document.getElementById('Content_Approval_Chart');
                if (!chartDom) return;

                var myChart = echarts.init(chartDom);
                var option = {
                    title: {
                        text: Lang.get('Content Approval Workflow'),
                        left: 'center',
                        textStyle: {
                            fontSize: 16,
                            fontWeight: 'bold'
                        }
                    },
                    tooltip: {
                        trigger: 'axis',
                        axisPointer: {
                            type: 'shadow'
                        }
                    },
                    legend: {
                        data: [Lang.get('Approved'), Lang.get('Pending'), Lang.get('Rejected')],
                        top: 30
                    },
                    xAxis: {
                        type: 'category',
                        data: data.content_types || []
                    },
                    yAxis: {
                        type: 'value',
                        name: Lang.get('Content Count')
                    },
                    series: [{
                            name: Lang.get('Approved'),
                            type: 'bar',
                            stack: 'approval',
                            data: data.approved || [],
                            itemStyle: {
                                color: '#50CD89'
                            }
                        },
                        {
                            name: Lang.get('Pending'),
                            type: 'bar',
                            stack: 'approval',
                            data: data.pending || [],
                            itemStyle: {
                                color: '#FFA800'
                            }
                        },
                        {
                            name: Lang.get('Rejected'),
                            type: 'bar',
                            stack: 'approval',
                            data: data.rejected || [],
                            itemStyle: {
                                color: '#F1416C'
                            }
                        }
                    ]
                };

                myChart.setOption(option);
                window.addEventListener('resize', function() {
                    myChart.resize();
                });
            }

            function renderAdminActivityChart(data) {
                var chartDom = document.getElementById('Admin_Activity_Chart');
                if (!chartDom) return;

                var myChart = echarts.init(chartDom);
                var option = {
                    title: {
                        text: Lang.get('Administrative Actions'),
                        left: 'center',
                        textStyle: {
                            fontSize: 16,
                            fontWeight: 'bold'
                        }
                    },
                    tooltip: {
                        trigger: 'item'
                    },
                    series: [{
                        type: 'bar',
                        data: Object.values(data),
                        itemStyle: {
                            color: new echarts.graphic.LinearGradient(0, 0, 0, 1, [{
                                    offset: 0,
                                    color: '#009EF7'
                                },
                                {
                                    offset: 1,
                                    color: '#1BC5BD'
                                }
                            ])
                        },
                        label: {
                            show: true,
                            position: 'top'
                        }
                    }],
                    xAxis: {
                        type: 'category',
                        data: Object.keys(data).map(key => key.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase())),
                        axisLabel: {
                            rotate: 45
                        }
                    },
                    yAxis: {
                        type: 'value',
                        name: Lang.get('Activity Count')
                    }
                };

                myChart.setOption(option);
                window.addEventListener('resize', function() {
                    myChart.resize();
                });
            }

            function renderPerformanceTrendsChart(data) {
                var chartDom = document.getElementById('Performance_Trends_Chart');
                if (!chartDom) return;

                var myChart = echarts.init(chartDom);
                var option = {
                    title: {
                        text: Lang.get('System Performance'),
                        left: 'center',
                        textStyle: {
                            fontSize: 16,
                            fontWeight: 'bold'
                        }
                    },
                    tooltip: {
                        trigger: 'axis'
                    },
                    legend: {
                        data: [Lang.get('Response Time'), Lang.get('Throughput')],
                        top: 30
                    },
                    xAxis: {
                        type: 'category',
                        data: data.dates || []
                    },
                    yAxis: [{
                            type: 'value',
                            name: Lang.get('Response Time (ms)')
                        },
                        {
                            type: 'value',
                            name: Lang.get('Throughput (req/s)')
                        }
                    ],
                    series: [{
                            name: Lang.get('Response Time'),
                            type: 'line',
                            data: data.response_times || [],
                            itemStyle: {
                                color: '#F1416C'
                            }
                        },
                        {
                            name: Lang.get('Throughput'),
                            type: 'line',
                            yAxisIndex: 1,
                            data: data.throughput || [],
                            itemStyle: {
                                color: '#50CD89'
                            }
                        }
                    ]
                };

                myChart.setOption(option);
                window.addEventListener('resize', function() {
                    myChart.resize();
                });
            }

            function renderSystemHealthChart(data) {
                var chartDom = document.getElementById('System_Health_Chart');
                if (!chartDom) return;

                var myChart = echarts.init(chartDom);
                var healthScore = data.health_score || 85;
                var remainingScore = 100 - healthScore;

                var option = {
                    title: {
                        text: Lang.get('System Health Score'),
                        left: 'center',
                        textStyle: {
                            fontSize: 16,
                            fontWeight: 'bold'
                        }
                    },
                    tooltip: {
                        trigger: 'item',
                        formatter: function(params) {
                            if (params.name === 'Health') {
                                return Lang.get('System Health') + ': ' + params.value + '%';
                            }
                            return '';
                        }
                    },
                    series: [{
                        type: 'pie',
                        radius: ['50%', '80%'],
                        center: ['50%', '60%'],
                        startAngle: 180,
                        endAngle: 360,
                        data: [
                            {
                                value: healthScore,
                                name: Lang.get('Health'),
                                itemStyle: {
                                    color: healthScore >= 80 ? '#50CD89' : healthScore >= 60 ? '#FFA800' : '#F1416C'
                                }
                            },
                            {
                                value: remainingScore,
                                name: Lang.get('Remaining'),
                                itemStyle: {
                                    color: '#E4E6EF',
                                    opacity: 0.3
                                },
                                label: {
                                    show: false
                                }
                            }
                        ],
                        label: {
                            show: false
                        },
                        emphasis: {
                            scale: false
                        }
                    }],
                    graphic: [{
                        type: 'text',
                        left: 'center',
                        top: 'middle',
                        style: {
                            text: healthScore + '%',
                            fontSize: 48,
                            fontWeight: 'bold',
                            fill: healthScore >= 80 ? '#50CD89' : healthScore >= 60 ? '#FFA800' : '#F1416C'
                        }
                    }, {
                        type: 'text',
                        left: 'center',
                        top: 'middle',
                        style: {
                            text: Lang.get('System Health'),
                            fontSize: 14,
                            fill: '#7E8299',
                            y: 30
                        }
                    }]
                };

                myChart.setOption(option);
                window.addEventListener('resize', function() {
                    myChart.resize();
                });
            }

            function renderUserEngagementChart(data) {
                var chartDom = document.getElementById('User_Engagement_Chart');
                if (!chartDom) return;

                var myChart = echarts.init(chartDom);

                // Process user types data
                var userTypesData = [];
                if (data.userTypes && Object.keys(data.userTypes).length > 0) {
                    userTypesData = Object.keys(data.userTypes).map((type, index) => ({
                        value: data.userTypes[type],
                        name: type,
                        itemStyle: {
                            color: ['#009EF7', '#50CD89', '#FFA800', '#F1416C', '#7239EA'][index % 5]
                        }
                    }));
                }

                var option = {
                    title: {
                        text: Lang.get('User Engagement Analytics'),
                        left: 'center',
                        textStyle: {
                            fontSize: 16,
                            fontWeight: 'bold'
                        }
                    },
                    tooltip: {
                        trigger: 'item',
                        formatter: function(params) {
                            return params.name + ': ' + params.value + ' users (' + params.percent + '%)';
                        }
                    },
                    legend: {
                        orient: 'horizontal',
                        bottom: 10,
                        data: userTypesData.map(item => item.name)
                    },
                    series: [
                        {
                            name: 'User Types',
                            type: 'pie',
                            radius: ['40%', '70%'],
                            center: ['50%', '45%'],
                            data: userTypesData,
                            emphasis: {
                                itemStyle: {
                                    shadowBlur: 10,
                                    shadowOffsetX: 0,
                                    shadowColor: 'rgba(0, 0, 0, 0.5)'
                                }
                            },
                            label: {
                                formatter: function(params) {
                                    return params.name + '\n' + params.value + ' users';
                                }
                            }
                        }
                    ]
                };

                myChart.setOption(option);
                window.addEventListener('resize', function() {
                    myChart.resize();
                });
            }

            function renderTimeBasedAnalyticsChart(data) {
                var chartDom = document.getElementById('Time_Based_Analytics_Chart');
                if (!chartDom) return;

                var myChart = echarts.init(chartDom);
                var option = {
                    title: {
                        text: Lang.get('Time-based Analytics'),
                        left: 'center',
                        textStyle: {
                            fontSize: 16,
                            fontWeight: 'bold'
                        }
                    },
                    tooltip: {
                        trigger: 'axis',
                        axisPointer: {
                            type: 'cross'
                        }
                    },
                    legend: {
                        data: [Lang.get('Exams Taken'), Lang.get('Avg Score'), Lang.get('Unique Students')],
                        bottom: 10
                    },
                    xAxis: {
                        type: 'category',
                        data: data.monthlyExams && data.monthlyExams.labels ? data.monthlyExams.labels : [],
                        axisLabel: {
                            rotate: 45
                        }
                    },
                    yAxis: [
                        {
                            type: 'value',
                            name: Lang.get('Count'),
                            position: 'left'
                        },
                        {
                            type: 'value',
                            name: Lang.get('Score (%)'),
                            max: 100,
                            position: 'right'
                        }
                    ],
                    series: [
                        {
                            name: Lang.get('Exams Taken'),
                            type: 'bar',
                            data: data.monthlyExams && data.monthlyExams.examsTaken ? data.monthlyExams.examsTaken : [],
                            itemStyle: {
                                color: new echarts.graphic.LinearGradient(0, 0, 0, 1, [{
                                    offset: 0,
                                    color: '#009EF7'
                                }, {
                                    offset: 1,
                                    color: '#1BC5BD'
                                }])
                            }
                        },
                        {
                            name: Lang.get('Avg Score'),
                            type: 'line',
                            yAxisIndex: 1,
                            data: data.monthlyExams && data.monthlyExams.avgScores ? data.monthlyExams.avgScores : [],
                            itemStyle: {
                                color: '#50CD89'
                            },
                            lineStyle: {
                                width: 3
                            },
                            symbol: 'circle',
                            symbolSize: 8
                        },
                        {
                            name: Lang.get('Unique Students'),
                            type: 'line',
                            data: data.monthlyExams && data.monthlyExams.uniqueStudents ? data.monthlyExams.uniqueStudents : [],
                            itemStyle: {
                                color: '#FFA800'
                            },
                            lineStyle: {
                                width: 3
                            },
                            symbol: 'diamond',
                            symbolSize: 8
                        }
                    ]
                };

                myChart.setOption(option);
                window.addEventListener('resize', function() {
                    myChart.resize();
                });
            }

            function renderExamCompletionStatsChart(data) {
                var chartDom = document.getElementById('Exam_Completion_Stats_Chart');
                if (!chartDom) return;

                var myChart = echarts.init(chartDom);
                var option = {
                    title: {
                        text: Lang.get('Exam Completion Statistics'),
                        left: 'center',
                        textStyle: {
                            fontSize: 16,
                            fontWeight: 'bold'
                        }
                    },
                    tooltip: {
                        trigger: 'item',
                        formatter: function(params) {
                            if (params.seriesName === 'Completion Status') {
                                return params.name + ': ' + params.value + ' exams (' + params.percent + '%)';
                            }
                            return params.name + ': ' + params.value + ' minutes';
                        }
                    },
                    legend: {
                        data: ['Completion Status'],
                        bottom: 10
                    },
                    series: [
                        {
                            name: 'Completion Status',
                            type: 'pie',
                            radius: '70%',
                            center: ['50%', '45%'],
                            data: data.statusDistribution && data.statusDistribution.statuses ?
                                data.statusDistribution.statuses.map((status, index) => ({
                                    value: data.statusDistribution.counts[index],
                                    name: status,
                                    itemStyle: {
                                        color: ['#50CD89', '#FFA800', '#F1416C'][index % 3]
                                    }
                                })) : []
                        }
                    ],
                    graphic: [{
                        type: 'text',
                        left: 'center',
                        bottom: 20,
                        style: {
                            text: Lang.get('Avg Duration: ') + (data.averageDuration || 0) + Lang.get(' minutes'),
                            fontSize: 14,
                            fontWeight: 'bold',
                            fill: '#7E8299'
                        }
                    }]
                };

                myChart.setOption(option);
                window.addEventListener('resize', function() {
                    myChart.resize();
                });
            }

            function renderUserActionDistributionChart(data) {
                var chartDom = document.getElementById('User_Action_Distribution_Chart');
                if (!chartDom) return;

                var myChart = echarts.init(chartDom);

                // Process action distribution data
                var actionData = [];
                if (data.action_distribution && Array.isArray(data.action_distribution)) {
                    actionData = data.action_distribution.map((item, index) => ({
                        value: item.count,
                        name: item.action.charAt(0).toUpperCase() + item.action.slice(1),
                        itemStyle: {
                            color: ['#3699FF', '#50CD89', '#FFA800', '#F1416C', '#7239EA'][index % 5]
                        }
                    }));
                }

                var option = {
                    title: {
                        text: Lang.get('User Action Distribution'),
                        left: 'center',
                        textStyle: {
                            fontSize: 16,
                            fontWeight: 'bold'
                        }
                    },
                    tooltip: {
                        trigger: 'item',
                        formatter: function(params) {
                            return params.name + ': ' + params.value + ' actions (' + params.percent + '%)';
                        }
                    },
                    legend: {
                        orient: 'horizontal',
                        bottom: 10
                    },
                    series: [{
                        type: 'pie',
                        radius: ['30%', '70%'],
                        center: ['50%', '45%'],
                        data: actionData,
                        emphasis: {
                            itemStyle: {
                                shadowBlur: 10,
                                shadowOffsetX: 0,
                                shadowColor: 'rgba(0, 0, 0, 0.5)'
                            }
                        },
                        label: {
                            formatter: function(params) {
                                return params.name + '\n' + params.value;
                            }
                        }
                    }]
                };

                myChart.setOption(option);
                window.addEventListener('resize', function() {
                    myChart.resize();
                });
            }

            function renderDeviceUsageChart(data) {
                var chartDom = document.getElementById('Device_Usage_Chart');
                if (!chartDom) return;

                var myChart = echarts.init(chartDom);

                // Process device usage data
                var deviceData = [];
                if (data.device_patterns && Array.isArray(data.device_patterns)) {
                    deviceData = data.device_patterns.map((item, index) => ({
                        value: item.usage_count,
                        name: item.device_type,
                        itemStyle: {
                            color: ['#009EF7', '#50CD89', '#FFA800', '#F1416C'][index % 4]
                        }
                    }));
                }

                var option = {
                    title: {
                        text: Lang.get('Device Usage Patterns'),
                        left: 'center',
                        textStyle: {
                            fontSize: 16,
                            fontWeight: 'bold'
                        }
                    },
                    tooltip: {
                        trigger: 'item',
                        formatter: function(params) {
                            return params.name + ': ' + params.value.toLocaleString() + ' sessions (' + params.percent + '%)';
                        }
                    },
                    legend: {
                        orient: 'horizontal',
                        bottom: 10
                    },
                    series: [{
                        type: 'pie',
                        radius: '60%',
                        center: ['50%', '45%'],
                        data: deviceData,
                        emphasis: {
                            itemStyle: {
                                shadowBlur: 10,
                                shadowOffsetX: 0,
                                shadowColor: 'rgba(0, 0, 0, 0.5)'
                            }
                        },
                        label: {
                            formatter: function(params) {
                                return params.name + '\n' + params.value.toLocaleString() + ' sessions';
                            }
                        }
                    }]
                };

                myChart.setOption(option);
                window.addEventListener('resize', function() {
                    myChart.resize();
                });
            }
        </script>
    @endpush
