<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Remove foreign key constraints from student_applications table.
 *
 * Programs and intakes are now managed from LMS (master system).
 * SIS stores the IDs as references but doesn't enforce FK constraints
 * since the data comes from an external API.
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('student_applications', function (Blueprint $table) {
            // Drop foreign key constraint on program_id
            $table->dropForeign(['program_id']);
        });

        // Drop intake_id foreign key separately (it may or may not exist)
        Schema::table('student_applications', function (Blueprint $table) {
            try {
                $table->dropForeign(['intake_id']);
            } catch (\Exception $e) {
                // Constraint might not exist, continue silently
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Re-add foreign key constraints
        Schema::table('student_applications', function (Blueprint $table) {
            $table->foreign('program_id')->references('id')->on('programs');
        });

        Schema::table('student_applications', function (Blueprint $table) {
            $table->foreign('intake_id')->references('id')->on('intakes')->nullOnDelete();
        });
    }
};
