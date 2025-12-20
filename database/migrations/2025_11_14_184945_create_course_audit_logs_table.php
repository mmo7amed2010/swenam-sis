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
        Schema::create('course_audit_logs', function (Blueprint $table) {
            $table->id();

            // Polymorphic relationship to any auditable model
            $table->string('auditable_type');
            $table->unsignedBigInteger('auditable_id');
            $table->index(['auditable_type', 'auditable_id']);

            // Event information
            $table->string('event_type', 50); // created, updated, deleted, published, etc.
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();

            // User who performed the action
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('user_type', 20)->nullable(); // admin, instructor, student
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');

            // Request metadata
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();

            // Additional context
            $table->text('description')->nullable();

            $table->timestamp('created_at');

            // Indexes for common queries
            $table->index('event_type');
            $table->index('user_id');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('course_audit_logs');
    }
};
