<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Services\LmsAdmissionTypeResolver;
use App\Services\LmsApiService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SyncAdmissionTypesToLms extends Command
{
    protected $signature = 'sync:admission-types {--dry-run : Show what would be synced without making changes}';

    protected $description = 'Sync admission_type to LMS for all existing students with LMS accounts';

    public function handle(): int
    {
        $dryRun = $this->option('dry-run');
        $lmsApiService = app(LmsApiService::class);

        // Get all SIS users that have LMS accounts
        $users = User::whereNotNull('lms_user_id')
            ->where('user_type', 'student')
            ->with('student')
            ->get();

        $this->info("Found {$users->count()} students with LMS accounts.");

        $stats = ['approved' => 0, 'bypass' => 0, 'rejected' => 0, 'suspended' => 0, 'errors' => 0];

        $bar = $this->output->createProgressBar($users->count());
        $bar->start();

        foreach ($users as $user) {
            $admissionType = LmsAdmissionTypeResolver::resolve($user);

            if ($dryRun) {
                $this->newLine();
                $this->line("  [DRY RUN] User #{$user->id} (LMS #{$user->lms_user_id}) → ".($admissionType ?? 'null'));
            } else {
                try {
                    $result = $lmsApiService->updateStudent($user->lms_user_id, [
                        'admission_type' => $admissionType,
                        'is_suspended' => $user->is_suspended,
                    ]);

                    if (! $result['success']) {
                        $this->newLine();
                        $this->error("  Failed to sync user #{$user->id} (LMS #{$user->lms_user_id}): ".($result['error'] ?? 'Unknown'));
                        $stats['errors']++;
                        $bar->advance();

                        continue;
                    }
                } catch (\Exception $e) {
                    $this->newLine();
                    $this->error("  Error syncing user #{$user->id}: {$e->getMessage()}");
                    $stats['errors']++;
                    $bar->advance();

                    continue;
                }
            }

            if ($user->isSuspended()) {
                $stats['suspended']++;
            } elseif ($admissionType === null) {
                $stats['rejected']++;
            } else {
                $stats[$admissionType]++;
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        $this->info('Sync complete:');
        $this->table(
            ['Type', 'Count'],
            [
                ['Approved', $stats['approved']],
                ['Bypass', $stats['bypass']],
                ['Rejected (set to null)', $stats['rejected']],
                ['Suspended (set to null)', $stats['suspended']],
                ['Errors', $stats['errors']],
            ]
        );

        if ($dryRun) {
            $this->warn('This was a dry run. No changes were made. Remove --dry-run to apply.');
        }

        Log::info('Admission type sync to LMS completed', $stats);

        return self::SUCCESS;
    }

}
