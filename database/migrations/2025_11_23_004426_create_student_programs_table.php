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
        Schema::create('student_programs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->foreignId('program_id')->constrained('programs')->onDelete('restrict');
            $table->date('enrollment_date');
            $table->date('expected_graduation')->nullable();
            $table->enum('status', ['enrolled', 'completed', 'withdrawn'])->default('enrolled');
            $table->timestamps();

            // Unique constraint: one student can only be enrolled in a program once
            $table->unique(['student_id', 'program_id'], 'unique_student_program');

            // Indexes
            $table->index('student_id', 'idx_student_programs_student');
            $table->index('program_id', 'idx_student_programs_program');
            $table->index('status', 'idx_student_programs_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_programs');
    }
};
