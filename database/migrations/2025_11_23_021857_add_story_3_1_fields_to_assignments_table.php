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
            // Story 3.1: Add submission_type field (replaces assignment_type enum values)
            $table->enum('submission_type', ['file_upload', 'text_entry', 'url_submission', 'multiple'])->nullable()->after('assignment_type');

            // Story 3.1: Add file upload settings
            $table->integer('max_file_size_mb')->nullable()->default(10)->after('submission_type')->comment('Max file size in MB (default 10MB, max 50MB)');
            $table->json('allowed_file_types')->nullable()->after('max_file_size_mb')->comment('Array of allowed file types: pdf, docx, txt, zip, images');

            // Story 3.1: Update late policy (replaces late_submission_allowed boolean)
            $table->enum('late_policy', ['not_allowed', 'penalty', 'no_penalty'])->default('not_allowed')->after('late_submission_allowed');
            $table->decimal('late_penalty_per_day', 5, 2)->nullable()->after('late_policy')->comment('Percentage penalty per day');

            // Story 3.1: Rename max_points to total_points for consistency
            // Note: We'll keep max_points for now to avoid breaking existing code, but add total_points as alias
            $table->integer('total_points')->nullable()->after('max_points')->comment('Total points (1-1000), alias for max_points');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('assignments', function (Blueprint $table) {
            $table->dropColumn([
                'submission_type',
                'max_file_size_mb',
                'allowed_file_types',
                'late_policy',
                'late_penalty_per_day',
                'total_points',
            ]);
        });
    }
};
