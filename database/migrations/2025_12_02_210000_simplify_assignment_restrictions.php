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
        Schema::table('assignments', function (Blueprint $table) {
            // Remove deadline-related fields for self-paced online courses
            $table->dropColumn([
                'available_from',
                'due_date',
                'late_submission_allowed',
                'late_penalty_percentage',
                'late_policy',
                'late_penalty_per_day'
            ]);

            // Remove complex file upload restrictions
            $table->dropColumn('allowed_file_types');

            // Remove indexes related to dates (no longer needed)

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('assignments', function (Blueprint $table) {
            // Restore deadline fields
            $table->timestamp('available_from')->nullable()->after('instructions');
            $table->timestamp('due_date')->after('available_from');
            $table->boolean('late_submission_allowed')->default(false)->after('due_date');
            $table->decimal('late_penalty_percentage', 5, 2)->default(0.00)->after('late_submission_allowed');

            // Restore late policy fields (Story 3.1)
            $table->enum('late_policy', ['not_allowed', 'penalty', 'no_penalty'])->default('not_allowed')->after('late_submission_allowed');
            $table->decimal('late_penalty_per_day', 5, 2)->nullable()->after('late_policy');

            // Restore file upload restrictions
            $table->json('allowed_file_types')->nullable()->after('max_file_size_mb');

            // Restore date-related indexes

        });
    }
};
