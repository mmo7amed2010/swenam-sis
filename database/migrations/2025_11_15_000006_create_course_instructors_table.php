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
        Schema::create('course_instructors', function (Blueprint $table) {
            $table->id();

            // Relationship
            $table->foreignId('course_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            // Role
            $table->enum('role', ['lead', 'co-instructor'])->default('co-instructor');

            // Permissions (JSON for flexibility)
            $table->json('permissions')->nullable();

            // Assignment Info
            $table->foreignId('assigned_by_admin_id')->constrained('users')->restrictOnDelete();
            $table->timestamp('assigned_at')->useCurrent();
            $table->timestamp('removed_at')->nullable();

            // Timestamps
            $table->timestamps();

            // Indexes
            $table->unique(['course_id', 'user_id'], 'unique_course_instructor');
            $table->index('user_id', 'idx_user_id');
            $table->index(['course_id', 'role'], 'idx_role');
            $table->index('assigned_by_admin_id', 'idx_assigned_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('course_instructors');
    }
};
