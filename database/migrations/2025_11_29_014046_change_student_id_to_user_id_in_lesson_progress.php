<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Phase 3: Standardize lesson_progress to use User.id instead of Student.id
     */
    public function up(): void
    {
        // Step 1: Create temporary column for new user_id values
        Schema::table('lesson_progress', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id_new')->nullable()->after('student_id');
        });

        // Step 2: Migrate data - convert student.id to user.id
        DB::statement('
            UPDATE lesson_progress lp
            JOIN students s ON lp.student_id = s.id
            SET lp.user_id_new = s.user_id
        ');

        // Step 3: Drop old column and constraints
        Schema::table('lesson_progress', function (Blueprint $table) {
            // Drop old foreign key and index
            $table->dropForeign(['student_id']);
            $table->dropUnique('unique_student_lesson');
            $table->dropIndex('idx_student_course');
            $table->dropColumn('student_id');
        });

        // Step 4: Rename new column to user_id
        Schema::table('lesson_progress', function (Blueprint $table) {
            $table->renameColumn('user_id_new', 'user_id');
        });

        // Step 5: Add new foreign key and indexes
        Schema::table('lesson_progress', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->unique(['user_id', 'lesson_id'], 'unique_user_lesson');
            $table->index(['user_id', 'course_id'], 'idx_user_course');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Step 1: Create temporary column for old student_id values
        Schema::table('lesson_progress', function (Blueprint $table) {
            $table->unsignedBigInteger('student_id_old')->nullable()->after('user_id');
        });

        // Step 2: Reverse data migration - convert user.id back to student.id
        DB::statement('
            UPDATE lesson_progress lp
            JOIN students s ON lp.user_id = s.user_id
            SET lp.student_id_old = s.id
        ');

        // Step 3: Drop new column and constraints
        Schema::table('lesson_progress', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropUnique('unique_user_lesson');
            $table->dropIndex('idx_user_course');
            $table->dropColumn('user_id');
        });

        // Step 4: Rename old column back to student_id
        Schema::table('lesson_progress', function (Blueprint $table) {
            $table->renameColumn('student_id_old', 'student_id');
        });

        // Step 5: Restore old foreign key and indexes
        Schema::table('lesson_progress', function (Blueprint $table) {
            $table->foreign('student_id')->references('id')->on('students')->onDelete('cascade');
            $table->unique(['student_id', 'lesson_id'], 'unique_student_lesson');
            $table->index(['student_id', 'course_id'], 'idx_student_course');
        });
    }
};
