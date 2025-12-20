<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('quizzes', function (Blueprint $table) {
            // Add assessment type to distinguish between quizzes and exams
            $table->enum('assessment_type', ['quiz', 'exam'])->default('quiz')->after('description');

            // Add scope to distinguish between lesson-level and module-level assessments
            $table->enum('scope', ['lesson', 'module'])->default('lesson')->after('assessment_type');

            // Add module_id for module-level assessments (exams)
            $table->unsignedBigInteger('module_id')->nullable()->after('course_id');

            // Add indexes for performance
            $table->index('assessment_type');
            $table->index('scope');
            $table->index('module_id');

            // Add foreign key constraint for module_id
            $table->foreign('module_id')
                ->references('id')
                ->on('course_modules')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('quizzes', function (Blueprint $table) {
            // Drop foreign key constraint first
            $table->dropForeign(['module_id']);

            // Drop indexes
            $table->dropIndex(['assessment_type']);
            $table->dropIndex(['scope']);
            $table->dropIndex(['module_id']);

            // Drop columns
            $table->dropColumn(['assessment_type', 'scope', 'module_id']);
        });
    }
};
