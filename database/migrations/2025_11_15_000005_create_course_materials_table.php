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
        Schema::create('course_materials', function (Blueprint $table) {
            $table->id();

            // Polymorphic Relationship
            $table->string('materialable_type', 50);
            $table->unsignedBigInteger('materialable_id');

            // Material Info
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('material_type', ['document', 'presentation', 'video', 'image', 'link', 'other']);

            // File Info (if uploaded)
            $table->string('file_name')->nullable();
            $table->string('file_path', 500)->nullable();
            $table->unsignedBigInteger('file_size')->nullable(); // bytes
            $table->string('mime_type', 100)->nullable();

            // External Link (if link type)
            $table->string('external_url', 500)->nullable();

            // Access Control
            $table->boolean('is_downloadable')->default(true);
            $table->boolean('requires_completion')->default(false);

            // Uploaded By
            $table->foreignId('uploaded_by_user_id')->constrained('users')->restrictOnDelete();

            // Timestamps
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index(['materialable_type', 'materialable_id'], 'idx_materialable');
            $table->index('uploaded_by_user_id', 'idx_uploaded_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('course_materials');
    }
};
