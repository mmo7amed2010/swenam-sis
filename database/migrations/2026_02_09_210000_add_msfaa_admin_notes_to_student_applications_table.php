<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('student_applications', function (Blueprint $table) {
            $table->text('msfaa_admin_notes')->nullable()->after('msfaa_rejection_reason');
            $table->unsignedBigInteger('msfaa_rejected_by')->nullable()->after('msfaa_rejection_reason');
            $table->foreign('msfaa_rejected_by')->references('id')->on('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('student_applications', function (Blueprint $table) {
            $table->dropForeign(['msfaa_rejected_by']);
            $table->dropColumn(['msfaa_admin_notes', 'msfaa_rejected_by']);
        });
    }
};
