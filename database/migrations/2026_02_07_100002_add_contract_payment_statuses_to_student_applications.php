<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Expand status ENUM to include contract and payment statuses
        DB::statement("ALTER TABLE student_applications MODIFY COLUMN status ENUM('pending', 'initial_approved', 'contract_sent', 'contract_uploaded', 'contract_approved', 'payment_pending', 'payment_uploaded', 'payment_approved', 'approved', 'rejected') DEFAULT 'pending'");

        Schema::table('student_applications', function (Blueprint $table) {
            // Contract tracking columns
            $table->timestamp('contract_sent_at')->nullable()->after('initial_approved_by');
            $table->unsignedBigInteger('contract_sent_by')->nullable()->after('contract_sent_at');
            $table->timestamp('contract_uploaded_at')->nullable()->after('contract_sent_by');
            $table->string('signed_contract_path')->nullable()->after('contract_uploaded_at');
            $table->timestamp('contract_approved_at')->nullable()->after('signed_contract_path');
            $table->unsignedBigInteger('contract_approved_by')->nullable()->after('contract_approved_at');

            // Payment tracking columns
            $table->timestamp('payment_uploaded_at')->nullable()->after('contract_approved_by');
            $table->string('payment_receipt_path')->nullable()->after('payment_uploaded_at');
            $table->timestamp('payment_approved_at')->nullable()->after('payment_receipt_path');
            $table->unsignedBigInteger('payment_approved_by')->nullable()->after('payment_approved_at');
        });
    }

    public function down(): void
    {
        Schema::table('student_applications', function (Blueprint $table) {
            $table->dropColumn([
                'contract_sent_at',
                'contract_sent_by',
                'contract_uploaded_at',
                'signed_contract_path',
                'contract_approved_at',
                'contract_approved_by',
                'payment_uploaded_at',
                'payment_receipt_path',
                'payment_approved_at',
                'payment_approved_by',
            ]);
        });

        DB::statement("ALTER TABLE student_applications MODIFY COLUMN status ENUM('pending', 'initial_approved', 'approved', 'rejected') DEFAULT 'pending'");
    }
};
