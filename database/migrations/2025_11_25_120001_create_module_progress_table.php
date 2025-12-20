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
        Schema::create('module_progress', function (Blueprint $table) {
            $table->id();

            // Foreign keys
            $table->unsignedBigInteger('module_id');
            $table->unsignedBigInteger('student_id');

            // Status and progress tracking
            $table->enum('status', ['not_started', 'in_progress', 'completed', 'exam_failed', 'exam_locked'])
                ->default('not_started');

            // Exam-specific fields
            $table->timestamp('exam_passed_at')->nullable();
            $table->unsignedTinyInteger('exam_attempts_used')->default(0);
            $table->decimal('exam_first_score', 5, 2)->nullable();
            $table->decimal('exam_best_score', 5, 2)->nullable();

            // Timestamps
            $table->timestamps();

            // Indexes
            $table->unique(['student_id', 'module_id']);
            $table->index('status');
            $table->index(['module_id', 'student_id']);

            // Foreign key constraints
            $table->foreign('module_id')
                ->references('id')
                ->on('course_modules')
                ->onDelete('cascade');

            $table->foreign('student_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('module_progress');
    }
};
