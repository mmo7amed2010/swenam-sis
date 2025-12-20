<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('announcements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->nullable()->constrained('courses')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // Creator
            $table->string('title');
            $table->text('content');
            $table->enum('type', ['course', 'system'])->default('course');
            $table->enum('priority', ['low', 'medium', 'high'])->default('medium');
            $table->boolean('is_published')->default(true);
            $table->timestamp('publish_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->boolean('send_email')->default(false);
            $table->timestamps();

            // Indexes for performance
            $table->index('course_id');
            $table->index('type');
            $table->index('publish_at');
            $table->index('created_at');
            $table->index(['type', 'is_published', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('announcements');
    }
};
