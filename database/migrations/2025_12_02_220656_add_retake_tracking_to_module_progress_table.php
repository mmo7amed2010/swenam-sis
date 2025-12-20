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
        Schema::table('module_progress', function (Blueprint $table) {
            $table->boolean('primary_exam_failed')
                ->default(false)
                ->after('exam_attempts_used')
                ->comment('True if student failed the primary exam');

            $table->boolean('retake_exam_failed')
                ->default(false)
                ->after('primary_exam_failed')
                ->comment('True if student failed the retake exam');

            $table->timestamp('retake_unlocked_at')
                ->nullable()
                ->after('retake_exam_failed')
                ->comment('When retake exam became available');

            $table->timestamp('retake_passed_at')
                ->nullable()
                ->after('retake_unlocked_at')
                ->comment('When student passed the retake exam');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('module_progress', function (Blueprint $table) {
            $table->dropColumn([
                'primary_exam_failed',
                'retake_exam_failed',
                'retake_unlocked_at',
                'retake_passed_at',
            ]);
        });
    }
};
