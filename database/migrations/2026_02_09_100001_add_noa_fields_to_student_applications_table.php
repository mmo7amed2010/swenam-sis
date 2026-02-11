<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('student_applications', function (Blueprint $table) {
            $table->string('noa_status')->nullable()->after('payment_rejection_reason');
            $table->timestamp('noa_requested_at')->nullable()->after('noa_status');
            $table->unsignedBigInteger('noa_requested_by')->nullable()->after('noa_requested_at');
            $table->timestamp('noa_uploaded_at')->nullable()->after('noa_requested_by');
            $table->string('noa_document_path')->nullable()->after('noa_uploaded_at');
            $table->timestamp('noa_approved_at')->nullable()->after('noa_document_path');
            $table->unsignedBigInteger('noa_approved_by')->nullable()->after('noa_approved_at');
            $table->text('noa_rejection_reason')->nullable()->after('noa_approved_by');
            $table->boolean('noa_skipped')->default(false)->after('noa_rejection_reason');
        });
    }

    public function down(): void
    {
        Schema::table('student_applications', function (Blueprint $table) {
            $table->dropColumn([
                'noa_status',
                'noa_requested_at',
                'noa_requested_by',
                'noa_uploaded_at',
                'noa_document_path',
                'noa_approved_at',
                'noa_approved_by',
                'noa_rejection_reason',
                'noa_skipped',
            ]);
        });
    }
};
