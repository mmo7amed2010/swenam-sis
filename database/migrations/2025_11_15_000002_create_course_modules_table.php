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
        Schema::create('course_modules', function (Blueprint $table) {
            $table->id();

            // Relationship
            $table->foreignId('course_id')->constrained()->cascadeOnDelete();

            // Module Info
            $table->string('title');
            $table->text('description')->nullable();
            $table->unsignedSmallInteger('order_index')->default(1);

            // Content Settings
            $table->boolean('is_enabled')->default(true);
            $table->timestamp('release_date')->nullable();

            // Metadata
            $table->decimal('estimated_hours', 4, 1)->nullable();

            // Timestamps
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('course_id', 'idx_course_id');
            $table->index(['course_id', 'order_index'], 'idx_order');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('course_modules');
    }
};
