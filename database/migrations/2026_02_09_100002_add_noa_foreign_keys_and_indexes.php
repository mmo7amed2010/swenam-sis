<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('student_applications', function (Blueprint $table) {
            $table->foreign('noa_requested_by')->references('id')->on('users')->nullOnDelete();
            $table->foreign('noa_approved_by')->references('id')->on('users')->nullOnDelete();
            $table->index('noa_status');
        });
    }

    public function down(): void
    {
        Schema::table('student_applications', function (Blueprint $table) {
            $table->dropForeign(['noa_requested_by']);
            $table->dropForeign(['noa_approved_by']);
            $table->dropIndex(['noa_status']);
        });
    }
};
