<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Story 4.1: Create quizzes table
     */
    public function up(): void
    {
        Schema::create('quizzes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained()->cascadeOnDelete();
            $table->foreignId('created_by')->constrained('users')->onDelete('restrict');

            $table->string('title');
            $table->text('description')->nullable();
            $table->integer('total_points')->default(0)->comment('Calculated from questions');

            $table->timestamp('due_date')->nullable();
            $table->integer('time_limit')->nullable()->comment('Minutes, null = unlimited');
            $table->integer('max_attempts')->default(1)->comment('1-10 or -1 for unlimited');

            $table->boolean('shuffle_questions')->default(false);
            $table->boolean('shuffle_answers')->default(false);

            $table->enum('show_correct_answers', ['never', 'after_each_attempt', 'after_all_attempts', 'after_due_date'])->default('after_due_date');
            $table->integer('passing_score')->default(60)->comment('Percentage 0-100');

            $table->boolean('published')->default(false);

            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index(['course_id', 'published']);
            $table->index('created_by');
            $table->index('due_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quizzes');
    }
};
