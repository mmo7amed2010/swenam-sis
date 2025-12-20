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
        Schema::table('grades', function (Blueprint $table) {
            // Story 3.7: Add late penalty override field
            $table->decimal('late_penalty_override', 5, 2)->nullable()->after('max_points')->comment('Override late penalty percentage (if null, auto-calculated)');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('grades', function (Blueprint $table) {
            $table->dropColumn('late_penalty_override');
        });
    }
};
