<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Story 2.7: Create module_lessons table
     */
    public function up(): void
    {
        Schema::create('module_lessons', function (Blueprint $table) {
            $table->id();

            // Relationship
            $table->foreignId('module_id')->constrained('course_modules')->cascadeOnDelete();

            // Lesson Info
            $table->string('title');
            $table->enum('content_type', ['text_html', 'video', 'pdf', 'external_link'])->default('text_html');

            // Content (varies by type)
            $table->longText('content')->nullable(); // HTML content for text_html
            $table->string('content_url', 500)->nullable(); // URL for video, pdf, external_link
            $table->string('file_path')->nullable(); // Storage path for PDF files

            // Metadata
            $table->unsignedSmallInteger('order_number')->default(1);
            $table->enum('status', ['draft', 'published'])->default('draft');
            $table->unsignedSmallInteger('estimated_duration')->nullable(); // Minutes

            // External link settings
            $table->boolean('open_new_tab')->default(false); // For external_link type

            // Timestamps
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('module_id', 'idx_module_id');
            $table->index(['module_id', 'order_number'], 'idx_module_order');
            $table->index(['module_id', 'status'], 'idx_module_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('module_lessons');
    }
};
