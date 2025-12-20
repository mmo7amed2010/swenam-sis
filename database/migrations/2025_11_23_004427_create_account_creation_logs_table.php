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
        Schema::create('account_creation_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('student_id')->nullable()->constrained('students')->onDelete('set null');
            $table->foreignId('application_id')->constrained('student_applications')->onDelete('cascade');
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->string('student_number', 20)->nullable();
            $table->enum('status', ['success', 'failed'])->notNull();
            $table->text('error_message')->nullable();
            $table->timestamps();

            // Indexes
            $table->index('application_id', 'idx_account_creation_application');
            $table->index('user_id', 'idx_account_creation_user');
            $table->index('student_id', 'idx_account_creation_student');
            $table->index('created_at', 'idx_account_creation_created');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('account_creation_logs');
    }
};
