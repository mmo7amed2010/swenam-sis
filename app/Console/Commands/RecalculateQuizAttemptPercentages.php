<?php

namespace App\Console\Commands;

use App\Models\QuizAttempt;
use Illuminate\Console\Command;

class RecalculateQuizAttemptPercentages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'quiz:recalculate-percentages {--attempt_id= : Specific attempt ID to recalculate}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Recalculate percentages for quiz attempts based on current quiz total_points';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $attemptId = $this->option('attempt_id');

        if ($attemptId) {
            // Recalculate specific attempt
            $attempt = QuizAttempt::with('quiz')->find($attemptId);

            if (! $attempt) {
                $this->error("Attempt with ID {$attemptId} not found.");

                return Command::FAILURE;
            }

            $this->recalculateAttempt($attempt);
        } else {
            // Recalculate all attempts
            $this->info('Recalculating percentages for all quiz attempts...');

            $attempts = QuizAttempt::with('quiz')->whereIn('status', ['submitted', 'graded'])->get();
            $bar = $this->output->createProgressBar($attempts->count());
            $bar->start();

            foreach ($attempts as $attempt) {
                $this->recalculateAttempt($attempt);
                $bar->advance();
            }

            $bar->finish();
            $this->newLine();
            $this->info("Successfully recalculated percentages for {$attempts->count()} attempts.");
        }

        return Command::SUCCESS;
    }

    /**
     * Recalculate percentage for a single attempt.
     */
    private function recalculateAttempt(QuizAttempt $attempt): void
    {
        $quiz = $attempt->quiz;
        $oldPercentage = $attempt->percentage;

        if ($quiz->total_points > 0) {
            $newPercentage = ($attempt->score / $quiz->total_points) * 100;
        } else {
            $newPercentage = 0;
        }

        $attempt->update(['percentage' => $newPercentage]);

        $this->line(sprintf(
            "Attempt #%d (Quiz: %s): %.1f%% → %.1f%% (Score: %.1f/%d)",
            $attempt->id,
            $quiz->title,
            $oldPercentage,
            $newPercentage,
            $attempt->score,
            $quiz->total_points
        ));
    }
}

