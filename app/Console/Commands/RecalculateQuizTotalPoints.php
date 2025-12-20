<?php

namespace App\Console\Commands;

use App\Models\Quiz;
use Illuminate\Console\Command;

class RecalculateQuizTotalPoints extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'quiz:recalculate-points {--quiz_id= : Specific quiz ID to recalculate}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Recalculate total_points for quizzes based on their questions';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $quizId = $this->option('quiz_id');

        if ($quizId) {
            // Recalculate specific quiz
            $quiz = Quiz::find($quizId);

            if (! $quiz) {
                $this->error("Quiz with ID {$quizId} not found.");

                return Command::FAILURE;
            }

            $this->recalculateQuiz($quiz);
        } else {
            // Recalculate all quizzes
            $this->info('Recalculating total_points for all quizzes...');

            $quizzes = Quiz::withCount('questions')->get();
            $bar = $this->output->createProgressBar($quizzes->count());
            $bar->start();

            foreach ($quizzes as $quiz) {
                $this->recalculateQuiz($quiz);
                $bar->advance();
            }

            $bar->finish();
            $this->newLine();
            $this->info("Successfully recalculated total_points for {$quizzes->count()} quizzes.");
        }

        return Command::SUCCESS;
    }

    /**
     * Recalculate total points for a single quiz.
     */
    private function recalculateQuiz(Quiz $quiz): void
    {
        $oldPoints = $quiz->total_points;
        $newPoints = $quiz->questions()->sum('points');

        $quiz->update(['total_points' => $newPoints]);

        $this->line("Quiz #{$quiz->id} '{$quiz->title}': {$oldPoints} → {$newPoints} points");
    }
}

