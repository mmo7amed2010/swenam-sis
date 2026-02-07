<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('student_applications', function (Blueprint $table) {
            $table->text('contract_rejection_reason')->nullable()->after('contract_approved_by');
            $table->text('payment_rejection_reason')->nullable()->after('payment_approved_by');
        });
    }

    public function down(): void
    {
        Schema::table('student_applications', function (Blueprint $table) {
            $table->dropColumn(['contract_rejection_reason', 'payment_rejection_reason']);
        });
    }
};
