<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lms_sync_failures', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->unsignedBigInteger('lms_user_id')->nullable();
            $table->string('action');
            $table->json('payload');
            $table->text('error');
            $table->unsignedInteger('attempts')->default(0);
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();

            $table->index(['resolved_at', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lms_sync_failures');
    }
};
