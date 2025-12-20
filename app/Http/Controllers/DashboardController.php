<?php

namespace App\Http\Controllers;

use App\Models\StudentApplication;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // Route to appropriate dashboard based on user type
        $user = $request->user();

        return match ($user->user_type) {
            'student' => $this->studentDashboard($request),
            'instructor' => $this->instructorDashboard($request),
            'admin' => $this->adminDashboard($request),
            default => abort(403, 'Invalid user type'),
        };
    }

    /**
     * Student dashboard - SIS focused (application tracking, student info)
     * Course access is provided via "My Program" link to LMS
     */
    protected function studentDashboard(Request $request)
    {
        $user = $request->user();
        $student = $user->student;
        $application = $student?->studentApplication;

        // Check if application is pending (not yet approved)
        $isPendingApproval = $application && ! $application->isApproved();

        // Check if student has LMS account (courses are accessed via LMS, not SIS)
        $hasLmsAccess = $user->hasLmsAccount();

        return view('pages/dashboards.student', [
            'user' => $user,
            'student' => $student,
            'program' => $user->program,
            'application' => $application,
            'isPendingApproval' => $isPendingApproval,
            'hasLmsAccess' => $hasLmsAccess,
        ]);
    }

    /**
     * Instructor dashboard - SIS focused
     * Course management is handled via LMS
     */
    protected function instructorDashboard(Request $request)
    {
        $user = $request->user();

        return view('pages/dashboards.instructor', [
            'user' => $user,
        ]);
    }

    /**
     * Admin dashboard - system management focused
     */
    protected function adminDashboard(Request $request)
    {
        $kpis = $this->getAdminKpis();
        $charts = $this->getAdminCharts();

        return view('pages/dashboards.admin', [
            'kpis' => $kpis,
            'charts' => $charts,
        ]);
    }

    /**
     * Get KPIs for admin dashboard
     */
    protected function getAdminKpis(): array
    {
        $sevenDaysAgo = Carbon::now()->subDays(7);

        return [
            // User counts
            'total_users' => User::count(),
            'total_students' => User::where('user_type', 'student')->count(),
            'total_instructors' => User::where('user_type', 'instructor')->count(),
            'total_admins' => User::where('user_type', 'admin')->count(),

            // Application counts
            'pending_applications' => StudentApplication::where('status', 'pending')->count(),
            'approved_applications' => StudentApplication::where('status', 'approved')->count(),
            'rejected_applications' => StudentApplication::where('status', 'rejected')->count(),
            'total_applications' => StudentApplication::count(),
            'initial_approved_applications' => StudentApplication::where('status', 'initial_approved')->count(),
            'approved_last_7d' => StudentApplication::where('status', 'approved')
                ->where('updated_at', '>=', $sevenDaysAgo)
                ->count(),
        ];
    }

    /**
     * Get chart data for admin dashboard
     */
    protected function getAdminCharts(): array
    {
        return [
            'monthly_user_registrations' => $this->getMonthlyUserRegistrations(),
            'user_types' => $this->getUserTypesDistribution(),
            'applications_by_status' => $this->getApplicationsByStatus(),
        ];
    }

    /**
     * Get monthly user registrations for the last 6 months
     */
    protected function getMonthlyUserRegistrations(): array
    {
        $labels = [];
        $counts = [];

        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $labels[] = $date->format('M Y');
            $counts[] = User::whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->count();
        }

        return [
            'labels' => $labels,
            'counts' => $counts,
        ];
    }

    /**
     * Get user types distribution
     */
    protected function getUserTypesDistribution(): array
    {
        return [
            'Students' => User::where('user_type', 'student')->count(),
            'Instructors' => User::where('user_type', 'instructor')->count(),
            'Admins' => User::where('user_type', 'admin')->count(),
        ];
    }

    /**
     * Get applications by status for chart
     */
    protected function getApplicationsByStatus(): array
    {
        $labels = [];
        $counts = [];

        $statuses = StudentApplication::select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->get();

        foreach ($statuses as $status) {
            $labels[] = ucfirst(str_replace('_', ' ', $status->status));
            $counts[] = $status->count;
        }

        return [
            'labels' => $labels,
            'counts' => $counts,
        ];
    }
}
