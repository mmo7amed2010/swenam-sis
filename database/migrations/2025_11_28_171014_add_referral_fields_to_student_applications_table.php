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
        Schema::table('student_applications', function (Blueprint $table) {
            $table->boolean('has_referral')->default(false)->after('intake_id');
            $table->string('referral_agency_name', 255)->nullable()->after('has_referral');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('student_applications', function (Blueprint $table) {
            $table->dropColumn(['has_referral', 'referral_agency_name']);
        });
    }
};
