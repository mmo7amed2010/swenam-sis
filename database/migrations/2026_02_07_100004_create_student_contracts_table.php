<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('student_contracts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('application_id');
            $table->unsignedBigInteger('contract_template_id');
            $table->json('admin_field_values')->nullable();
            $table->longText('rendered_body');
            $table->string('generated_pdf_path')->nullable();
            $table->string('signed_pdf_path')->nullable();
            $table->unsignedBigInteger('issued_by')->nullable();
            $table->timestamp('issued_at')->nullable();
            $table->timestamp('signed_at')->nullable();
            $table->timestamps();

            $table->foreign('application_id')->references('id')->on('student_applications')->onDelete('cascade');
            $table->foreign('contract_template_id')->references('id')->on('contract_templates')->onDelete('restrict');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_contracts');
    }
};
