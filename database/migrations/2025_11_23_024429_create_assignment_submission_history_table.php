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
        Schema::create('assignment_submission_history', function (Blueprint $table) {
            $table->id();

            // Reference to original submission (Story 3.5 AC #3)
            $table->foreignId('submission_id')->constrained('submissions')->cascadeOnDelete();

            // Relationship
            $table->foreignId('assignment_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            // Submission Info (archived copy)
            $table->enum('submission_type', ['file', 'text', 'quiz_answers', 'link']);

            // Content (archived)
            $table->longText('text_content')->nullable();
            $table->string('file_path', 500)->nullable();
            $table->string('file_name')->nullable();
            $table->unsignedBigInteger('file_size')->nullable();
            $table->string('external_url', 500)->nullable();

            // Quiz Answers (if applicable)
            $table->json('quiz_answers')->nullable();

            // Submission Metadata (archived)
            $table->unsignedSmallInteger('attempt_number')->default(1);
            $table->timestamp('submitted_at');
            $table->boolean('is_late')->default(false);
            $table->unsignedSmallInteger('late_days')->default(0);
            $table->enum('status', ['draft', 'submitted', 'graded', 'returned'])->default('draft');

            // Archive metadata
            $table->timestamp('archived_at')->useCurrent();
            $table->foreignId('archived_by_user_id')->nullable()->constrained('users')->nullOnDelete();

            // Timestamps
            $table->timestamps();

            // Indexes
            $table->index('submission_id', 'idx_submission_id');
            $table->index('assignment_id', 'idx_assignment_id');
            $table->index('user_id', 'idx_user_id');
            $table->index('archived_at', 'idx_archived_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assignment_submission_history');
    }
};
