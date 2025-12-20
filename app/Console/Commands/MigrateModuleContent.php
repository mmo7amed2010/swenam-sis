<?php

namespace App\Console\Commands;

use App\Models\CourseModule;
use App\Models\ModuleItem;
use App\Models\Quiz;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MigrateModuleContent extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'module:migrate-content
                            {--dry-run : Show what would be migrated without making changes}
                            {--module= : Migrate only a specific module ID}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrate existing lessons, quizzes, and assignments into the unified module_items table';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $dryRun = $this->option('dry-run');
        $moduleId = $this->option('module');

        if ($dryRun) {
            $this->info('DRY RUN MODE - No changes will be made');
        }

        $this->info('Starting module content migration...');

        $stats = [
            'lessons' => 0,
            'quizzes' => 0,
            'assignments' => 0,
            'skipped' => 0,
        ];

        try {
            DB::beginTransaction();

            // Get modules to process
            $modulesQuery = CourseModule::query();
            if ($moduleId) {
                $modulesQuery->where('id', $moduleId);
            }

            $modules = $modulesQuery->with(['lessons', 'assignments'])->get();

            $this->output->progressStart($modules->count());

            foreach ($modules as $module) {
                $position = 0;

                // 1. Migrate ModuleLessons (preserve their order_number)
                $lessons = $module->lessons()->orderBy('order_number')->get();
                foreach ($lessons as $lesson) {
                    if ($this->itemExists('App\\Models\\ModuleLesson', $lesson->id)) {
                        $stats['skipped']++;

                        continue;
                    }

                    if (! $dryRun) {
                        ModuleItem::create([
                            'module_id' => $module->id,
                            'itemable_type' => 'App\\Models\\ModuleLesson',
                            'itemable_id' => $lesson->id,
                            'order_position' => $position,
                            'is_required' => false,
                            'release_date' => null,
                        ]);
                    }
                    $position++;
                    $stats['lessons']++;
                }

                // 2. Migrate Quizzes (module-level only)
                $quizzes = Quiz::where('module_id', $module->id)
                    ->where('scope', 'module')
                    ->get();

                foreach ($quizzes as $quiz) {
                    if ($this->itemExists('App\\Models\\Quiz', $quiz->id)) {
                        $stats['skipped']++;

                        continue;
                    }

                    if (! $dryRun) {
                        ModuleItem::create([
                            'module_id' => $module->id,
                            'itemable_type' => 'App\\Models\\Quiz',
                            'itemable_id' => $quiz->id,
                            'order_position' => $position,
                            'is_required' => $quiz->assessment_type === 'exam',
                            'release_date' => $quiz->start_date ?? null,
                        ]);
                    }
                    $position++;
                    $stats['quizzes']++;
                }

                // 3. Migrate Assignments (module-level only via polymorphic)
                $assignments = $module->assignments;
                foreach ($assignments as $assignment) {
                    if ($this->itemExists('App\\Models\\Assignment', $assignment->id)) {
                        $stats['skipped']++;

                        continue;
                    }

                    if (! $dryRun) {
                        ModuleItem::create([
                            'module_id' => $module->id,
                            'itemable_type' => 'App\\Models\\Assignment',
                            'itemable_id' => $assignment->id,
                            'order_position' => $position,
                            'is_required' => false,
                            'release_date' => $assignment->available_from ?? null,
                        ]);
                    }
                    $position++;
                    $stats['assignments']++;
                }

                $this->output->progressAdvance();
            }

            $this->output->progressFinish();

            if (! $dryRun) {
                DB::commit();
            } else {
                DB::rollBack();
            }

            $this->newLine();
            $this->info('Migration completed successfully!');
            $this->table(
                ['Type', 'Count'],
                [
                    ['Lessons', $stats['lessons']],
                    ['Quizzes', $stats['quizzes']],
                    ['Assignments', $stats['assignments']],
                    ['Skipped (already exists)', $stats['skipped']],
                ]
            );

            Log::info('Module content migration completed', $stats);

            return Command::SUCCESS;

        } catch (\Exception $e) {
            DB::rollBack();
            $this->error('Migration failed: '.$e->getMessage());
            Log::error('Module content migration failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return Command::FAILURE;
        }
    }

    /**
     * Check if an item already exists in module_items table.
     */
    private function itemExists(string $type, int $id): bool
    {
        return ModuleItem::where('itemable_type', $type)
            ->where('itemable_id', $id)
            ->exists();
    }
}
