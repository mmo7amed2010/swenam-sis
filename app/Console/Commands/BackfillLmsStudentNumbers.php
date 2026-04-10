<?php

namespace App\Console\Commands;

use App\Models\LmsSyncFailure;
use App\Models\User;
use App\Services\LmsApiService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

/**
 * One-time backfill: push the SIS student_number to the LMS for every student
 * that already has an LMS account, so both systems share the same
 * human-readable student ID.
 *
 * Safe to re-run: the LMS update endpoint uses a unique constraint that
 * ignores the target student's own row, so applying the same value twice is
 * a no-op. Genuine conflicts (two different students holding the same
 * STU-YYYY-##### across systems) are recorded in lms_sync_failures for manual
 * review.
 */
class BackfillLmsStudentNumbers extends Command
{
    protected $signature = 'students:backfill-lms-student-numbers {--dry-run : Show what would be synced without making changes}';

    protected $description = 'Backfill SIS student_number into LMS for all students with an existing LMS account';

    public function handle(): int
    {
        $dryRun = (bool) $this->option('dry-run');
        $lmsApiService = app(LmsApiService::class);

        $query = User::whereNotNull('lms_user_id')
            ->where('user_type', 'student')
            ->with('student');

        $total = (clone $query)->count();

        if ($total === 0) {
            $this->info('No students with LMS accounts found. Nothing to backfill.');

            return self::SUCCESS;
        }

        $this->info("Found {$total} students with LMS accounts.");

        $stats = ['updated' => 0, 'skipped' => 0, 'failed' => 0];

        $bar = $this->output->createProgressBar($total);
        $bar->start();

        $query->chunkById(200, function ($users) use ($lmsApiService, $dryRun, &$stats, $bar) {
            foreach ($users as $user) {
                $student = $user->student;

                if (! $student || ! $student->student_number) {
                    $this->newLine();
                    $this->warn("  Skipping user #{$user->id} (LMS #{$user->lms_user_id}): no SIS student record or student_number.");
                    $stats['skipped']++;
                    $bar->advance();

                    continue;
                }

                if ($dryRun) {
                    $this->newLine();
                    $this->line("  [DRY RUN] User #{$user->id} (LMS #{$user->lms_user_id}) → {$student->student_number}");
                    $stats['updated']++;
                    $bar->advance();

                    continue;
                }

                try {
                    $result = $lmsApiService->updateStudent($user->lms_user_id, [
                        'student_number' => $student->student_number,
                    ]);

                    if ($result['success']) {
                        $stats['updated']++;
                    } else {
                        $this->recordFailure($user, $student->student_number, $result['error'] ?? 'Unknown error');
                        $stats['failed']++;
                    }
                } catch (\Exception $e) {
                    $this->recordFailure($user, $student->student_number, $e->getMessage());
                    $stats['failed']++;
                }

                $bar->advance();
            }
        });

        $bar->finish();
        $this->newLine(2);

        $this->info('Backfill complete:');
        $this->table(
            ['Result', 'Count'],
            [
                ['Updated', $stats['updated']],
                ['Skipped (no SIS student record)', $stats['skipped']],
                ['Failed (recorded in lms_sync_failures)', $stats['failed']],
            ]
        );

        if ($dryRun) {
            $this->warn('This was a dry run. No changes were made. Remove --dry-run to apply.');
        }

        Log::info('LMS student_number backfill completed', $stats + ['dry_run' => $dryRun]);

        return self::SUCCESS;
    }

    private function recordFailure(User $user, string $studentNumber, string $error): void
    {
        $this->newLine();
        $this->error("  Failed to sync user #{$user->id} (LMS #{$user->lms_user_id}): {$error}");

        LmsSyncFailure::create([
            'user_id' => $user->id,
            'lms_user_id' => $user->lms_user_id,
            'action' => 'backfill_student_number',
            'payload' => ['student_number' => $studentNumber],
            'error' => $error,
            'attempts' => 1,
        ]);
    }
}
