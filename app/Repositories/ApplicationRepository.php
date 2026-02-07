<?php

namespace App\Repositories;

use App\Models\StudentApplication;
use Illuminate\Support\Facades\Cache;

class ApplicationRepository
{
    /**
     * Get application statistics.
     *
     * @param  int  $cacheMinutes  Cache duration in minutes
     * @return array Statistics array
     */
    public function getStats(int $cacheMinutes = 5): array
    {
        $userId = auth()->id() ?? 'guest';
        $userType = auth()->user()?->user_type ?? 'guest';
        $cacheKey = "application_stats_{$userId}_{$userType}";

        return [
            'total' => StudentApplication::count(),
            'pending' => StudentApplication::where('status', 'pending')->count(),
            'initial_approved' => StudentApplication::where('status', 'initial_approved')->count(),
            'contract_sent' => StudentApplication::where('status', 'contract_sent')->count(),
            'contract_uploaded' => StudentApplication::where('status', 'contract_uploaded')->count(),
            'contract_approved' => StudentApplication::where('status', 'contract_approved')->count(),
            'payment_pending' => StudentApplication::where('status', 'payment_pending')->count(),
            'payment_uploaded' => StudentApplication::where('status', 'payment_uploaded')->count(),
            'payment_approved' => StudentApplication::where('status', 'payment_approved')->count(),
            'approved' => StudentApplication::where('status', 'approved')->count(),
            'rejected' => StudentApplication::where('status', 'rejected')->count(),
        ];
    }

    /**
     * Get applications with filters.
     *
     * @param  string  $status  Status filter (all, pending, approved, rejected)
     * @param  string|null  $from  Start date
     * @param  string|null  $to  End date
     * @return \Illuminate\Database\Eloquent\Collection Applications
     */
    public function getFilteredApplications(string $status = 'all', ?string $from = null, ?string $to = null)
    {
        $query = StudentApplication::with(['reviewer']);

        if ($status !== 'all') {
            $query->byStatus($status);
        }

        if ($from && $to) {
            $query->byDateRange($from, $to);
        }

        return $query->orderBy('created_at', 'desc')->get();
    }
}
