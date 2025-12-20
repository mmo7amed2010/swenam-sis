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
        Schema::table('login_logs', function (Blueprint $table) {
            // Index for time-based queries
            $table->index('created_at');

            // Composite index for user-specific failure tracking
            $table->index(['user_id', 'status']);

            // Composite index for IP-based rate limiting
            $table->index(['ip_address', 'created_at']);

            // Composite index for login analytics
            $table->index(['email', 'status', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('login_logs', function (Blueprint $table) {
            $table->dropIndex(['created_at']);
            $table->dropIndex(['user_id', 'status']);
            $table->dropIndex(['ip_address', 'created_at']);
            $table->dropIndex(['email', 'status', 'created_at']);
        });
    }
};
