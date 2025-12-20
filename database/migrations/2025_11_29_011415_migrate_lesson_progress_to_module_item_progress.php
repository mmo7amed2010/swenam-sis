<?php

use App\Models\ModuleItem;
use App\Models\ModuleLesson;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

/**
 * Data migration to transfer lesson_progress records to module_item_progress.
 * This is part of the Student Experience Overhaul that unifies progress tracking
 * for all module item types (lessons, quizzes, assignments).
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Only run if both tables exist
        if (! Schema::hasTable('lesson_progress') || ! Schema::hasTable('module_item_progress')) {
            Log::info('Skipping lesson_progress migration: Required tables do not exist');

            return;
        }

        $migrated = 0;
        $skipped = 0;

        // Get all lesson progress records
        $lessonProgressRecords = DB::table('lesson_progress')->get();

        foreach ($lessonProgressRecords as $progress) {
            // Find the corresponding ModuleItem for this lesson
            $moduleItem = DB::table('module_items')
                ->where('itemable_type', ModuleLesson::class)
                ->where('itemable_id', $progress->lesson_id)
                ->first();

            if (! $moduleItem) {
                Log::warning('No ModuleItem found for lesson', [
                    'lesson_id' => $progress->lesson_id,
                    'student_id' => $progress->student_id,
                ]);
                $skipped++;

                continue;
            }

            // Check if record already exists
            $exists = DB::table('module_item_progress')
                ->where('student_id', $progress->student_id)
                ->where('module_item_id', $moduleItem->id)
                ->exists();

            if ($exists) {
                $skipped++;

                continue;
            }

            // Insert the new record
            DB::table('module_item_progress')->insert([
                'student_id' => $progress->student_id,
                'module_item_id' => $moduleItem->id,
                'course_id' => $progress->course_id,
                'completed_at' => $progress->completed_at,
                'last_accessed_at' => $progress->last_accessed_at,
                'created_at' => $progress->created_at ?? now(),
                'updated_at' => now(),
            ]);

            $migrated++;
        }

        Log::info('Lesson progress migration completed', [
            'migrated' => $migrated,
            'skipped' => $skipped,
            'total_records' => $lessonProgressRecords->count(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Only remove records that were migrated from lesson_progress
        // by deleting module_item_progress records where the module_item is a lesson
        if (Schema::hasTable('module_item_progress') && Schema::hasTable('module_items')) {
            $lessonModuleItemIds = DB::table('module_items')
                ->where('itemable_type', ModuleLesson::class)
                ->pluck('id');

            DB::table('module_item_progress')
                ->whereIn('module_item_id', $lessonModuleItemIds)
                ->delete();

            Log::info('Rolled back lesson progress migration');
        }
    }
};
