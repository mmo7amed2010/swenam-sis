<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Phase 3: Standardize module_item_progress to use User.id instead of Student.id
     */
    public function up(): void
    {
        // Step 1: Create temporary column for new user_id values
        Schema::table('module_item_progress', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id_new')->nullable()->after('student_id');
        });

        // Step 2: Migrate data - convert student.id to user.id
        DB::statement('
            UPDATE module_item_progress mip
            JOIN students s ON mip.student_id = s.id
            SET mip.user_id_new = s.user_id
        ');

        // Step 3: Drop old column and constraints
        Schema::table('module_item_progress', function (Blueprint $table) {
            // Drop old foreign key and indexes
            $table->dropForeign(['student_id']);
            $table->dropUnique('unique_student_item');
            $table->dropIndex('idx_student_course');
            $table->dropColumn('student_id');
        });

        // Step 4: Rename new column to user_id
        Schema::table('module_item_progress', function (Blueprint $table) {
            $table->renameColumn('user_id_new', 'user_id');
        });

        // Step 5: Add new foreign key and indexes
        Schema::table('module_item_progress', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->unique(['user_id', 'module_item_id'], 'unique_user_item');
            $table->index(['user_id', 'course_id'], 'idx_user_course');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Step 1: Create temporary column for old student_id values
        Schema::table('module_item_progress', function (Blueprint $table) {
            $table->unsignedBigInteger('student_id_old')->nullable()->after('user_id');
        });

        // Step 2: Reverse data migration - convert user.id back to student.id
        DB::statement('
            UPDATE module_item_progress mip
            JOIN students s ON mip.user_id = s.user_id
            SET mip.student_id_old = s.id
        ');

        // Step 3: Drop new column and constraints
        Schema::table('module_item_progress', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropUnique('unique_user_item');
            $table->dropIndex('idx_user_course');
            $table->dropColumn('user_id');
        });

        // Step 4: Rename old column back to student_id
        Schema::table('module_item_progress', function (Blueprint $table) {
            $table->renameColumn('student_id_old', 'student_id');
        });

        // Step 5: Restore old foreign key and indexes
        Schema::table('module_item_progress', function (Blueprint $table) {
            $table->foreign('student_id')->references('id')->on('students')->onDelete('cascade');
            $table->unique(['student_id', 'module_item_id'], 'unique_student_item');
            $table->index(['student_id', 'course_id'], 'idx_student_course');
        });
    }
};
