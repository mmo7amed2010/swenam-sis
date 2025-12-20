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
        Schema::create('submissions', function (Blueprint $table) {
            $table->id();

            // Relationship
            $table->foreignId('assignment_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            // Submission Info
            $table->enum('submission_type', ['file', 'text', 'quiz_answers', 'link']);

            // Content
            $table->longText('text_content')->nullable();
            $table->string('file_path', 500)->nullable();
            $table->string('file_name')->nullable();
            $table->unsignedBigInteger('file_size')->nullable();
            $table->string('external_url', 500)->nullable();

            // Quiz Answers (if applicable)
            $table->json('quiz_answers')->nullable();

            // Submission Metadata
            $table->unsignedSmallInteger('attempt_number')->default(1);
            $table->timestamp('submitted_at')->useCurrent();
            $table->boolean('is_late')->default(false);
            $table->unsignedSmallInteger('late_days')->default(0);

            // Status
            $table->enum('status', ['draft', 'submitted', 'graded', 'returned'])->default('draft');

            // Timestamps
            $table->timestamps();

            // Indexes
            $table->index('assignment_id', 'idx_assignment_id');
            $table->index('user_id', 'idx_user_id');
            $table->index('status', 'idx_status');
            $table->index('submitted_at', 'idx_submitted_at');
            $table->unique(['assignment_id', 'user_id', 'attempt_number'], 'unique_submission_attempt');
            $table->index(['assignment_id', 'status'], 'idx_assignment_status');
            $table->index(['user_id', 'assignment_id'], 'idx_user_assignment');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('submissions');
    }
};
