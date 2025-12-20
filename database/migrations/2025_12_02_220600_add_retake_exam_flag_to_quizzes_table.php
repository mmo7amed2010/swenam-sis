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
        Schema::table('quizzes', function (Blueprint $table) {
            $table->boolean('is_retake_exam')
                ->default(false)
                ->after('scope')
                ->index()
                ->comment('If true, this exam is a retake exam that appears after failing primary');

            $table->foreignId('primary_exam_id')
                ->nullable()
                ->after('is_retake_exam')
                ->constrained('quizzes')
                ->nullOnDelete()
                ->comment('Reference to the primary exam this retake is linked to');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('quizzes', function (Blueprint $table) {
            $table->dropForeign(['primary_exam_id']);
            $table->dropColumn(['is_retake_exam', 'primary_exam_id']);
        });
    }
};
