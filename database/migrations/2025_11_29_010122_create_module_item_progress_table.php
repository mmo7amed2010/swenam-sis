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
        Schema::create('module_item_progress', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('student_id');
            $table->unsignedBigInteger('module_item_id');
            $table->unsignedBigInteger('course_id');
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('last_accessed_at')->nullable();
            $table->timestamps();

            // Unique constraint: one progress record per student per item
            $table->unique(['student_id', 'module_item_id'], 'unique_student_item');

            // Performance indexes
            $table->index(['student_id', 'course_id'], 'idx_student_course');
            $table->index(['course_id', 'completed_at'], 'idx_course_completed');

            // Foreign keys
            $table->foreign('student_id')->references('id')->on('students')->onDelete('cascade');
            $table->foreign('module_item_id')->references('id')->on('module_items')->onDelete('cascade');
            $table->foreign('course_id')->references('id')->on('courses')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('module_item_progress');
    }
};
