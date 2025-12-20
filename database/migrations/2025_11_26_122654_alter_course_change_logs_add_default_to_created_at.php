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
        Schema::table('course_change_logs', function (Blueprint $table) {
            // Alter the created_at column to have a default value of CURRENT_TIMESTAMP
            $table->timestamp('created_at')->useCurrent()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('course_change_logs', function (Blueprint $table) {
            // Remove the default value from created_at
            $table->timestamp('created_at')->change();
        });
    }
};
