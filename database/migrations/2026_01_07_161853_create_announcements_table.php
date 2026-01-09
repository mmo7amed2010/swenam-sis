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
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // Creator
            $table->string('title');
            $table->text('content');
            $table->enum('type', ['system'])->default('system');
            $table->enum('priority', ['low', 'medium', 'high'])->default('medium');
            $table->enum('target_audience', ['all', 'students', 'admins', 'program'])->default('all');
            $table->foreignId('program_id')->nullable()->constrained('programs')->onDelete('cascade');
            $table->boolean('is_published')->default(true);
            $table->boolean('send_email')->default(false);
            $table->timestamps();

            // Indexes for performance
            $table->index('type');
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
