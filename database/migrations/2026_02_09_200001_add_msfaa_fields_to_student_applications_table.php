<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('student_applications', function (Blueprint $table) {
            $table->string('msfaa_status')->nullable()->after('noa_skipped');
            $table->timestamp('msfaa_requested_at')->nullable()->after('msfaa_status');
            $table->unsignedBigInteger('msfaa_requested_by')->nullable()->after('msfaa_requested_at');
            $table->timestamp('msfaa_confirmed_at')->nullable()->after('msfaa_requested_by');
            $table->timestamp('msfaa_approved_at')->nullable()->after('msfaa_confirmed_at');
            $table->unsignedBigInteger('msfaa_approved_by')->nullable()->after('msfaa_approved_at');
            $table->text('msfaa_rejection_reason')->nullable()->after('msfaa_approved_by');

            $table->foreign('msfaa_requested_by')->references('id')->on('users')->nullOnDelete();
            $table->foreign('msfaa_approved_by')->references('id')->on('users')->nullOnDelete();
            $table->index('msfaa_status');
        });
    }

    public function down(): void
    {
        Schema::table('student_applications', function (Blueprint $table) {
            $table->dropForeign(['msfaa_requested_by']);
            $table->dropForeign(['msfaa_approved_by']);
            $table->dropIndex(['msfaa_status']);
            $table->dropColumn([
                'msfaa_status',
                'msfaa_requested_at',
                'msfaa_requested_by',
                'msfaa_confirmed_at',
                'msfaa_approved_at',
                'msfaa_approved_by',
                'msfaa_rejection_reason',
            ]);
        });
    }
};
