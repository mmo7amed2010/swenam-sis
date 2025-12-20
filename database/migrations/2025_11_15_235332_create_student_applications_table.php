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
        Schema::create('student_applications', function (Blueprint $table) {
            $table->id();
            $table->string('reference_number', 20)->unique();
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');

            // Program Information
            $table->foreignId('program_id')->constrained('programs');
            $table->string('preferred_intake', 50);

            // Personal Information
            $table->string('first_name', 100);
            $table->string('last_name', 100);
            $table->string('email', 255);
            $table->string('phone', 50);
            $table->date('date_of_birth');
            $table->string('country_of_citizenship', 100);
            $table->string('residency_status', 100);
            $table->string('primary_language', 50);
            $table->string('address_line1', 255);
            $table->string('address_line2', 255)->nullable();
            $table->string('city', 100);
            $table->string('state_province', 100);
            $table->string('postal_code', 20);
            $table->string('country', 100);

            // Education History
            $table->string('highest_education_level', 255);
            $table->string('education_field', 255);
            $table->string('institution_name', 255);
            $table->enum('education_completed', ['yes', 'no', 'still_studying']);
            $table->string('education_country', 100);
            $table->boolean('has_disciplinary_action')->default(false);

            // Work History
            $table->boolean('has_work_experience')->default(false);
            $table->string('position_level', 100)->nullable();
            $table->string('position_title', 255)->nullable();
            $table->string('organization_name', 255)->nullable();
            $table->date('work_start_date')->nullable();
            $table->date('work_end_date')->nullable();
            $table->string('years_of_experience', 50)->nullable();

            // Supporting Documents
            $table->string('degree_certificate_path', 500)->nullable();
            $table->string('transcripts_path', 500)->nullable();
            $table->string('cv_path', 500)->nullable();
            $table->string('english_test_path', 500)->nullable();

            // Review/Approval
            $table->foreignId('reviewed_by')->nullable()->constrained('users');
            $table->timestamp('reviewed_at')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->text('admin_notes')->nullable();
            $table->foreignId('created_user_id')->nullable()->constrained('users');

            $table->timestamps();

            // Indexes
            $table->index('status');
            $table->index('email');
            $table->index('reference_number');
            $table->index('program_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_applications');
    }
};
