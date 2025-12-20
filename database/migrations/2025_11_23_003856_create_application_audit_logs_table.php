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
        Schema::create('application_audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('application_id')->constrained('student_applications')->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('action', 50)->notNull(); // 'submitted', 'approved', 'rejected', 'status_changed'
            $table->string('old_status', 50)->nullable();
            $table->string('new_status', 50)->notNull();
            $table->text('reason')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamps();

            // Indexes for performance
            $table->index('application_id', 'idx_application_id');
            $table->index('created_at', 'idx_created_at');
            $table->index(['application_id', 'created_at'], 'idx_app_created');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('application_audit_logs');
    }
};
