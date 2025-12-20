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
        Schema::table('course_modules', function (Blueprint $table) {
            $table->boolean('requires_exam_pass')
                ->default(false)
                ->after('status')
                ->comment('If true, students must pass module exam to access next module');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('course_modules', function (Blueprint $table) {
            $table->dropColumn('requires_exam_pass');
        });
    }
};
