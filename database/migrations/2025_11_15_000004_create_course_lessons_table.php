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
        Schema::create('course_lessons', function (Blueprint $table) {
            $table->id();

            // Relationship
            $table->foreignId('unit_id')->constrained('course_units')->cascadeOnDelete();

            // Lesson Info
            $table->string('title');
            $table->longText('content')->nullable(); // HTML content
            $table->unsignedSmallInteger('order_index')->default(1);

            // Content Type
            $table->enum('lesson_type', ['text', 'video', 'interactive', 'quiz'])->default('text');

            // Content Settings
            $table->boolean('is_enabled')->default(true);
            $table->timestamp('release_date')->nullable();

            // Metadata
            $table->unsignedSmallInteger('estimated_minutes')->nullable();

            // Timestamps
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('course_lessons');
    }
};
