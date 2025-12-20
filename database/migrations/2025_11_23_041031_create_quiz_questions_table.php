<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Story 4.2: Create quiz_questions table (MCQ and True/False only, no short_answer)
     */
    public function up(): void
    {
        Schema::create('quiz_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quiz_id')->constrained()->cascadeOnDelete();

            $table->enum('question_type', ['mcq', 'true_false'])->comment('Multiple Choice or True/False');
            $table->text('question_text');
            $table->integer('points')->default(1)->comment('Points for correct answer (1-100)');
            $table->integer('order_number')->default(0);

            // JSON structure for answers
            // MCQ: [{"text": "Option 1", "is_correct": false}, {"text": "Option 2", "is_correct": true}, ...]
            // True/False: {"correct": true} or {"correct": false}
            $table->json('answers_json')->nullable();

            // Settings for question behavior
            $table->json('settings_json')->nullable()->comment('Additional settings like randomize_answers');

            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index(['quiz_id', 'order_number']);
            $table->index(['quiz_id', 'deleted_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quiz_questions');
    }
};
