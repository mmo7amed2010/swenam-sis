<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Story 4.4, 4.5, 4.6: Create quiz_attempts table
     */
    public function up(): void
    {
        Schema::create('quiz_attempts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quiz_id')->constrained()->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('users')->cascadeOnDelete();

            $table->integer('attempt_number');
            $table->timestamp('start_time');
            $table->timestamp('end_time')->nullable();

            $table->enum('status', ['in_progress', 'submitted', 'graded'])->default('in_progress');

            $table->decimal('score', 5, 2)->nullable()->comment('Points earned');
            $table->decimal('percentage', 5, 2)->nullable()->comment('Percentage score');

            // JSON structure: [{"question_id": 1, "answer": "...", "answered_at": "timestamp"}, ...]
            $table->json('answers_json')->nullable();

            // Store question order for this attempt (if shuffled)
            $table->json('questions_order')->nullable();

            $table->timestamps();

            // Indexes
            $table->index(['student_id', 'quiz_id']);
            $table->index(['quiz_id', 'status']);
            $table->unique(['student_id', 'quiz_id', 'attempt_number'], 'unique_student_quiz_attempt');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quiz_attempts');
    }
};
