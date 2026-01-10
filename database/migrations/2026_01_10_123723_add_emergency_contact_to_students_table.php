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
        Schema::table('students', function (Blueprint $table) {
            $table->string('emergency_first_name', 100)->nullable()->after('address');
            $table->string('emergency_last_name', 100)->nullable()->after('emergency_first_name');
            $table->string('emergency_phone', 50)->nullable()->after('emergency_last_name');
            $table->json('emergency_address')->nullable()->after('emergency_phone');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropColumn([
                'emergency_first_name',
                'emergency_last_name',
                'emergency_phone',
                'emergency_address',
            ]);
        });
    }
};
