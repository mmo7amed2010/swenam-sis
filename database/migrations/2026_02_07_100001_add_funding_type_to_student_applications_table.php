<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('student_applications', function (Blueprint $table) {
            $table->enum('funding_type', ['self_funded', 'government_funded'])
                ->default('self_funded')
                ->after('referral_agency_name');
        });
    }

    public function down(): void
    {
        Schema::table('student_applications', function (Blueprint $table) {
            $table->dropColumn('funding_type');
        });
    }
};
