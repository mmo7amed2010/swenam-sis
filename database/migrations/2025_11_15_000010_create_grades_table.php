<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('grades', function (Blueprint $table) {
            $table->id();

            // Relationship
            $table->foreignId('submission_id')->constrained()->cascadeOnDelete();

            // Grading Info
            $table->decimal('points_awarded', 6, 2);
            $table->decimal('max_points', 6, 2);
            // percentage will be added as generated column below

            // Feedback
            $table->longText('feedback')->nullable();
            $table->json('rubric_scores')->nullable(); // Scores per rubric criterion

            // Annotated Files
            $table->string('annotated_file_path', 500)->nullable();

            // Grading Metadata
            $table->foreignId('graded_by_user_id')->constrained('users')->restrictOnDelete();
            $table->timestamp('graded_at')->useCurrent();

            // Status
            $table->boolean('is_published')->default(false);
            $table->timestamp('published_at')->nullable();

            // Version Control
            $table->unsignedSmallInteger('version')->default(1);

            // Timestamps
            $table->timestamps();

            // Indexes
            $table->unique(['submission_id', 'version'], 'unique_submission_grade');
            $table->index('graded_by_user_id', 'idx_graded_by');
            $table->index('is_published', 'idx_is_published');
        });

        // Add generated column for percentage (MySQL 5.7.6+)
        DB::statement('ALTER TABLE grades ADD COLUMN percentage DECIMAL(5,2) GENERATED ALWAYS AS ((points_awarded / max_points) * 100) STORED');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('grades');
    }
};
