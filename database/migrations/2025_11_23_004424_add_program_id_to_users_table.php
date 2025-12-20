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
        Schema::table('users', function (Blueprint $table) {
            // Add program_id foreign key (nullable, only used for user_type='student')
            $table->foreignId('program_id')->nullable()->after('user_type')->constrained('programs')->onDelete('set null');
            $table->index('program_id', 'idx_users_program_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['program_id']);
            $table->dropIndex('idx_users_program_id');
            $table->dropColumn('program_id');
        });
    }
};
