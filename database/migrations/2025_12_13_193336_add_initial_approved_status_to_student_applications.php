<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Modify the status enum to include 'initial_approved'
        DB::statement("ALTER TABLE student_applications MODIFY COLUMN status ENUM('pending', 'initial_approved', 'approved', 'rejected') DEFAULT 'pending'");

        // Add new columns for tracking initial approval
        Schema::table('student_applications', function (Blueprint $table) {
            $table->timestamp('initial_approved_at')->nullable()->after('reviewed_at');
            $table->foreignId('initial_approved_by')->nullable()->after('initial_approved_at')->constrained('users')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // First, update any 'initial_approved' status back to 'pending'
        DB::statement("UPDATE student_applications SET status = 'pending' WHERE status = 'initial_approved'");

        // Revert the enum to original values
        DB::statement("ALTER TABLE student_applications MODIFY COLUMN status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending'");

        // Remove the new columns
        Schema::table('student_applications', function (Blueprint $table) {
            $table->dropForeign(['initial_approved_by']);
            $table->dropColumn(['initial_approved_at', 'initial_approved_by']);
        });
    }
};
