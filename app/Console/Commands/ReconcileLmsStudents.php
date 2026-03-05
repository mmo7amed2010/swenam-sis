<?php

namespace App\Console\Commands;

use App\Models\LmsSyncFailure;
use App\Models\User;
use App\Services\LmsAdmissionTypeResolver;
use App\Services\LmsApiService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ReconcileLmsStudents extends Command
{
    protected $signature = 'sync:reconcile-lms {--dry-run : Show what would be synced without making changes} {--limit=100 : Maximum number of students to process}';

    protected $description = 'Reconcile LMS student states with SIS (handles missed syncs and drift)';

    public function handle(): int
    {
        $dryRun = $this->option('dry-run');
        $limit = (int) $this->option('limit');
        $lmsApiService = app(LmsApiService::class);

        $stats = ['synced' => 0, 'skipped' => 0, 'errors' => 0, 'resolved' => 0];

        // 1. Process unresolved sync failures
        $failures = LmsSyncFailure::unresolved()
            ->with('user')
            ->take($limit)
            ->get();

        if ($failures->isNotEmpty()) {
            $this->info("Processing {$failures->count()} unresolved sync failures...");
        }

        foreach ($failures as $failure) {
            $user = $failure->user;
            if (! $user || ! $user->lms_user_id) {
                $stats['skipped']++;
                continue;
            }

            $admissionType = LmsAdmissionTypeResolver::resolve($user);
            $payload = [
                'admission_type' => $admissionType,
                'is_suspended' => $user->is_suspended,
            ];

            if ($dryRun) {
                $this->line("  [DRY RUN] Failure #{$failure->id}: User #{$user->id} → " . json_encode($payload));
                $stats['synced']++;
                continue;
            }

            try {
                $result = $lmsApiService->updateStudent($user->lms_user_id, $payload);

                if ($result['success']) {
                    $failure->update(['resolved_at' => now()]);
                    $stats['resolved']++;
                    $stats['synced']++;
                } else {
                    $stats['errors']++;
                }
            } catch (\Exception $e) {
                $this->error("  Error reconciling user #{$user->id}: {$e->getMessage()}");
                $stats['errors']++;
            }

            usleep(100_000); // 100ms rate limit
        }

        // 2. Process recently modified students (last 30 minutes)
        $remaining = $limit - $failures->count();
        if ($remaining > 0) {
            $recentUsers = User::whereNotNull('lms_user_id')
                ->where('user_type', 'student')
                ->where('updated_at', '>=', now()->subMinutes(30))
                ->with('student.studentApplication')
                ->take($remaining)
                ->get();

            if ($recentUsers->isNotEmpty()) {
                $this->info("Processing {$recentUsers->count()} recently modified students...");
            }

            foreach ($recentUsers as $user) {
                $admissionType = LmsAdmissionTypeResolver::resolve($user);
                $payload = [
                    'admission_type' => $admissionType,
                    'is_suspended' => $user->is_suspended,
                ];

                if ($dryRun) {
                    $this->line("  [DRY RUN] User #{$user->id} (LMS #{$user->lms_user_id}) → " . json_encode($payload));
                    $stats['synced']++;
                    continue;
                }

                try {
                    $result = $lmsApiService->updateStudent($user->lms_user_id, $payload);

                    if ($result['success']) {
                        $stats['synced']++;
                    } else {
                        $stats['errors']++;
                    }
                } catch (\Exception $e) {
                    $this->error("  Error syncing user #{$user->id}: {$e->getMessage()}");
                    $stats['errors']++;
                }

                usleep(100_000); // 100ms rate limit
            }
        }

        $this->newLine();
        $this->info('Reconciliation complete:');
        $this->table(
            ['Metric', 'Count'],
            [
                ['Synced', $stats['synced']],
                ['Failures resolved', $stats['resolved']],
                ['Skipped', $stats['skipped']],
                ['Errors', $stats['errors']],
            ]
        );

        if ($dryRun) {
            $this->warn('This was a dry run. No changes were made.');
        }

        Log::info('LMS reconciliation completed', $stats);

        return self::SUCCESS;
    }
}
