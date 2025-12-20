<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Removes unused course fields per client requirements:
     * - start_date, end_date, difficulty_level, max_enrollment
     * - enrollment_open, is_public, duration_weeks
     */
    public function up(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->dropColumn([
                'start_date',
                'end_date',
                'difficulty_level',
                'max_enrollment',
                'enrollment_open',
                'is_public',
                'duration_weeks',
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->date('start_date')->nullable()->after('description');
            $table->date('end_date')->nullable()->after('start_date');
            $table->enum('difficulty_level', ['beginner', 'intermediate', 'advanced'])->default('beginner')->after('end_date');
            $table->unsignedInteger('max_enrollment')->nullable()->after('difficulty_level');
            $table->boolean('enrollment_open')->default(true)->after('max_enrollment');
            $table->boolean('is_public')->default(false)->after('enrollment_open');
            $table->unsignedInteger('duration_weeks')->nullable()->after('is_public');
        });
    }
};
