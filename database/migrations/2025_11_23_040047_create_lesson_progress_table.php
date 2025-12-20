<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Story 2.13: Create lesson_progress table for tracking student lesson completion
     */
    public function up(): void
    {
        Schema::create('lesson_progress', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->foreignId('lesson_id')->constrained('module_lessons')->onDelete('cascade');
            $table->foreignId('course_id')->constrained('courses')->onDelete('cascade');
            $table->timestamp('completed_at')->useCurrent();
            $table->timestamps();

            // Unique constraint: one progress record per student per lesson
            $table->unique(['student_id', 'lesson_id'], 'unique_student_lesson');

            // Indexes for performance
            $table->index(['student_id', 'course_id'], 'idx_student_course');
            $table->index(['course_id', 'completed_at'], 'idx_course_completed');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lesson_progress');
    }
};
