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
        Schema::create('course_analytics_snapshot', function (Blueprint $table) {
            $table->id();

            // Relationship
            $table->foreignId('course_id')->constrained()->cascadeOnDelete();

            // Snapshot Type
            $table->enum('snapshot_type', ['daily', 'weekly', 'monthly', 'final']);
            $table->date('snapshot_date');

            // Enrollment Metrics
            $table->unsignedSmallInteger('total_enrollments')->default(0);
            $table->unsignedSmallInteger('active_enrollments')->default(0);
            $table->unsignedSmallInteger('completed_enrollments')->default(0);
            $table->unsignedSmallInteger('dropped_enrollments')->default(0);

            // Engagement Metrics
            $table->decimal('avg_progress_percentage', 5, 2)->default(0.00);
            $table->decimal('avg_last_access_days', 6, 2)->default(0.00);
            $table->unsignedSmallInteger('total_submissions')->default(0);

            // Performance Metrics
            $table->decimal('avg_grade', 5, 2)->nullable();
            $table->decimal('median_grade', 5, 2)->nullable();
            $table->json('grade_distribution')->nullable();

            // Completion Metrics
            $table->decimal('avg_completion_days', 6, 2)->nullable();
            $table->decimal('completion_rate', 5, 2)->default(0.00);

            // Timestamp
            $table->timestamp('created_at')->useCurrent();

            // Indexes
            $table->unique(['course_id', 'snapshot_type', 'snapshot_date'], 'unique_course_snapshot');
            $table->index('snapshot_date', 'idx_snapshot_date');
            $table->index('snapshot_type', 'idx_snapshot_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('course_analytics_snapshot');
    }
};
