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
        Schema::create('course_enrollments', function (Blueprint $table) {
            $table->id();

            // Relationship
            $table->foreignId('course_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            // Enrollment Info
            $table->foreignId('enrolled_by_admin_id')->constrained('users')->restrictOnDelete();
            $table->timestamp('enrollment_date')->useCurrent();

            // Status
            $table->enum('status', ['active', 'completed', 'dropped', 'failed'])->default('active');
            $table->timestamp('completion_date')->nullable();

            // Progress Tracking
            $table->decimal('progress_percentage', 5, 2)->default(0.00); // 0.00 to 100.00
            $table->timestamp('last_accessed_at')->nullable();

            // Grade Info
            $table->decimal('current_grade', 5, 2)->nullable(); // 0.00 to 100.00
            $table->decimal('final_grade', 5, 2)->nullable();
            $table->char('letter_grade', 2)->nullable(); // A, B+, C-, etc.

            // Metadata
            $table->text('notes')->nullable(); // Admin/instructor notes

            // Timestamps
            $table->timestamps();

            // Indexes
            $table->unique(['course_id', 'user_id'], 'unique_course_enrollment');
            $table->index('user_id', 'idx_user_id');
            $table->index('status', 'idx_status');
            $table->index('enrolled_by_admin_id', 'idx_enrolled_by');
            $table->index('enrollment_date', 'idx_enrollment_date');
            $table->index('last_accessed_at', 'idx_last_accessed');
            $table->index(['course_id', 'status'], 'idx_course_status');
            $table->index(['user_id', 'status'], 'idx_user_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('course_enrollments');
    }
};
