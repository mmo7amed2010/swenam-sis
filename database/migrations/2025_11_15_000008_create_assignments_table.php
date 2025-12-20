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
        Schema::create('assignments', function (Blueprint $table) {
            $table->id();

            // Relationship
            $table->foreignId('course_id')->constrained()->cascadeOnDelete();

            // Assignment can be attached to specific module/unit/lesson (optional polymorphic)
            $table->string('assignmentable_type', 50)->nullable();
            $table->unsignedBigInteger('assignmentable_id')->nullable();

            // Assignment Info
            $table->string('title');
            $table->longText('description')->nullable();
            $table->longText('instructions')->nullable();

            // Assignment Type
            $table->enum('assignment_type', ['file_upload', 'text_submission', 'quiz', 'external_link']);

            // Scoring
            $table->decimal('max_points', 6, 2)->default(100.00);
            $table->decimal('weight', 5, 2)->default(1.00); // Weight in final grade calculation

            // Grading Rubric
            $table->json('rubric')->nullable();

            // Deadlines
            $table->timestamp('available_from')->nullable();
            $table->timestamp('due_date');
            $table->boolean('late_submission_allowed')->default(false);
            $table->decimal('late_penalty_percentage', 5, 2)->default(0.00);

            // Settings
            $table->boolean('is_published')->default(false);
            $table->unsignedSmallInteger('attempts_allowed')->default(1);

            // Created By
            $table->foreignId('created_by_user_id')->constrained('users')->restrictOnDelete();

            // Timestamps
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('course_id', 'idx_course_id');
            $table->index(['assignmentable_type', 'assignmentable_id'], 'idx_assignmentable');
            $table->index('due_date', 'idx_due_date');
            $table->index('is_published', 'idx_is_published');
            $table->index('created_by_user_id', 'idx_created_by');
            $table->index(['course_id', 'due_date'], 'idx_course_due');
            $table->index(['is_published', 'due_date'], 'idx_published_due');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assignments');
    }
};
