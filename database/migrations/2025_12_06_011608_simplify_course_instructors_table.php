<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Simplify course_instructors table by removing co-instructor functionality.
 *
 * Removes:
 * - role column (no lead/co-instructor distinction needed)
 * - permissions JSON column (all instructors have full course access)
 *
 * Keeps:
 * - course_id, user_id (the assignment relationship)
 * - assigned_by_admin_id, assigned_at (audit trail)
 * - removed_at (soft delete for unassignment)
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('course_instructors', function (Blueprint $table) {
            // Remove role column (no lead/co-instructor distinction)
            $table->dropColumn('role');

            // Remove permissions JSON column (no granular permissions)
            $table->dropColumn('permissions');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('course_instructors', function (Blueprint $table) {
            // Re-add role column with default
            $table->enum('role', ['lead', 'co-instructor'])->default('co-instructor')->after('user_id');

            // Re-add permissions JSON column
            $table->json('permissions')->nullable()->after('role');
        });
    }
};
