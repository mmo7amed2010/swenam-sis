<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Modify the enum to include 'video_upload'
        // MySQL doesn't support ALTER ENUM directly, so we need to use raw SQL
        DB::statement("ALTER TABLE module_lessons MODIFY COLUMN content_type ENUM('text_html', 'video', 'pdf', 'external_link', 'video_upload') DEFAULT 'text_html'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove 'video_upload' from enum
        DB::statement("ALTER TABLE module_lessons MODIFY COLUMN content_type ENUM('text_html', 'video', 'pdf', 'external_link') DEFAULT 'text_html'");
        
        // Delete any lessons with video_upload content type
        DB::table('module_lessons')
            ->where('content_type', 'video_upload')
            ->delete();
    }
};
