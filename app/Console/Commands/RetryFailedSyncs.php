<?php

namespace App\Console\Commands;

use App\Jobs\SyncStudentStatusToLmsJob;
use App\Models\LmsSyncFailure;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class RetryFailedSyncs extends Command
{
    protected $signature = 'sync:retry-failed {--limit=50 : Maximum number of failures to retry}';

    protected $description = 'Retry unresolved LMS sync failures by re-dispatching sync jobs';

    public function handle(): int
    {
        $limit = (int) $this->option('limit');

        $failures = LmsSyncFailure::unresolved()
            ->with('user')
            ->oldest()
            ->take($limit)
            ->get();

        if ($failures->isEmpty()) {
            $this->info('No unresolved sync failures found.');
            return self::SUCCESS;
        }

        $this->info("Retrying {$failures->count()} failed syncs...");

        $dispatched = 0;
        $skipped = 0;

        foreach ($failures as $failure) {
            $user = $failure->user;

            if (! $user || ! $user->lms_user_id) {
                $failure->update(['resolved_at' => now(), 'error' => $failure->error . ' | Auto-resolved: user or LMS account no longer exists']);
                $skipped++;
                continue;
            }

            SyncStudentStatusToLmsJob::dispatch($user, 'retry_' . $failure->action);

            // Mark as resolved since a new job will create a new failure record if it fails again
            $failure->update(['resolved_at' => now()]);

            $dispatched++;
        }

        $this->info("Dispatched: {$dispatched}, Skipped: {$skipped}");

        Log::info('Retry failed syncs completed', [
            'dispatched' => $dispatched,
            'skipped' => $skipped,
        ]);

        return self::SUCCESS;
    }
}
