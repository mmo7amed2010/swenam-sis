<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Adds last_accessed_at column for "Continue Learning" feature.
     * This tracks when a student last accessed a lesson.
     */
    public function up(): void
    {
        Schema::table('lesson_progress', function (Blueprint $table) {
            $table->timestamp('last_accessed_at')->nullable()->after('completed_at');
            $table->index(['student_id', 'last_accessed_at'], 'lesson_progress_student_last_accessed_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lesson_progress', function (Blueprint $table) {
            $table->dropIndex('lesson_progress_student_last_accessed_idx');
            $table->dropColumn('last_accessed_at');
        });
    }
};
