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
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained('users')->onDelete('cascade');
            $table->string('student_number', 20)->unique();
            $table->string('first_name', 255);
            $table->string('last_name', 255);
            $table->string('email', 255);
            $table->string('phone', 20)->nullable();
            $table->date('date_of_birth')->nullable();
            $table->json('address')->nullable();
            $table->enum('enrollment_status', ['active', 'suspended', 'withdrawn', 'graduated'])->default('active');
            $table->timestamps();

            // Indexes
            $table->index('student_number', 'idx_student_number');
            $table->index('email', 'idx_students_email');
            $table->index('enrollment_status', 'idx_enrollment_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
