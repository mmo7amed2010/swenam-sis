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
        Schema::create('course_audit_log', function (Blueprint $table) {
            $table->id();

            // What Changed
            $table->string('auditable_type', 50);
            $table->unsignedBigInteger('auditable_id');

            // Change Details
            $table->enum('event_type', ['created', 'updated', 'deleted', 'published', 'archived', 'restored']);
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();

            // Who & When
            $table->foreignId('user_id')->constrained()->restrictOnDelete();
            $table->enum('user_type', ['admin', 'instructor', 'system']);
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();

            // Metadata
            $table->text('description')->nullable();

            // Timestamp
            $table->timestamp('created_at')->useCurrent();

            // Indexes
            $table->index(['auditable_type', 'auditable_id'], 'idx_auditable');
            $table->index('user_id', 'idx_user_id');
            $table->index('event_type', 'idx_event_type');
            $table->index('created_at', 'idx_created_at');
            $table->index(['auditable_type', 'auditable_id', 'created_at'], 'idx_auditable_created');
            $table->index(['user_id', 'created_at'], 'idx_user_created');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('course_audit_log');
    }
};
