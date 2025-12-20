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
        Schema::create('notification_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained('users')->onDelete('cascade');

            // Email notification preferences (all default to true)
            $table->boolean('course_announcements_email')->default(true);
            $table->boolean('system_notifications_email')->default(true);
            $table->boolean('assignment_reminders_email')->default(true);
            $table->boolean('grade_notifications_email')->default(true);
            $table->boolean('application_updates_email')->default(true);
            $table->boolean('quiz_notifications_email')->default(true);

            $table->timestamps();

            // Index for quick lookups
            $table->index('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notification_settings');
    }
};
