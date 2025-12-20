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
        Schema::create('courses', function (Blueprint $table) {
            $table->id();

            // Course Identity
            $table->string('course_code', 20);
            $table->string('name');
            $table->text('description')->nullable();
            $table->unsignedSmallInteger('version')->default(1);

            // Course Details
            $table->decimal('credits', 3, 1)->default(3.0);
            $table->unsignedSmallInteger('duration_weeks')->default(16);
            $table->enum('difficulty_level', ['beginner', 'intermediate', 'advanced'])->default('intermediate');

            // Schedule
            $table->string('semester', 50)->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();

            // Categorization
            $table->string('department', 100)->nullable();
            $table->string('program', 100)->nullable();

            // Content Settings
            $table->text('syllabus')->nullable();
            $table->text('prerequisites')->nullable(); // Free text prerequisites

            // Prerequisites (JSON array of course IDs)
            $table->json('prerequisite_course_ids')->nullable();

            // Learning Objectives (JSON array of strings)
            $table->json('learning_objectives')->nullable();

            // Enrollment Settings
            $table->unsignedSmallInteger('max_enrollment')->nullable();
            $table->boolean('enrollment_open')->default(true);
            $table->boolean('is_public')->default(true);

            // Lifecycle State
            $table->enum('status', ['draft', 'published', 'active', 'archived'])->default('draft');
            $table->timestamp('published_at')->nullable();
            $table->timestamp('archived_at')->nullable();

            // Ownership & Audit
            $table->foreignId('created_by_admin_id')->constrained('users')->restrictOnDelete();

            // Timestamps
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->unique(['course_code', 'department', 'version'], 'unique_course_dept_version');
            $table->index('status', 'idx_status');
            $table->index('department', 'idx_department');
            $table->index('program', 'idx_program');
            $table->index('semester', 'idx_semester');
            $table->index('created_by_admin_id', 'idx_created_by');
            $table->index('published_at', 'idx_published_at');
            $table->index(['status', 'department'], 'idx_status_dept');
            $table->index(['status', 'program'], 'idx_status_program');
            $table->index(['semester', 'status'], 'idx_semester_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('courses');
    }
};
