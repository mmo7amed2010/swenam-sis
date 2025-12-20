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
        Schema::create('intakes', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);                          // e.g., "January 2025", "Fall 2025"
            $table->string('slug', 100)->unique();                // URL-friendly identifier
            $table->date('start_date');                           // When classes begin
            $table->date('end_date')->nullable();                 // When the term ends
            $table->date('application_open_date')->nullable();    // When applications open
            $table->date('application_deadline')->nullable();     // When applications close
            $table->boolean('is_active')->default(true);          // Whether visible/selectable
            $table->text('description')->nullable();              // Optional description
            $table->unsignedInteger('max_capacity')->nullable();  // Optional enrollment limit
            $table->unsignedInteger('sort_order')->default(0);    // Display order
            $table->timestamps();

            $table->index(['is_active', 'start_date']);
            $table->index('application_deadline');
        });

        // Update student_applications to reference intakes table
        Schema::table('student_applications', function (Blueprint $table) {
            // Add intake_id column
            $table->foreignId('intake_id')->nullable()->after('program_id')->constrained('intakes')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('student_applications', function (Blueprint $table) {
            $table->dropForeign(['intake_id']);
            $table->dropColumn('intake_id');
        });

        Schema::dropIfExists('intakes');
    }
};
