<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Drop the course_enrollments table as it is orphaned code.
     * The system uses program-based access (User.program_id === Course.program_id)
     * instead of individual course enrollments.
     */
    public function up(): void
    {
        // Safety check - warn if table has data
        if (Schema::hasTable('course_enrollments')) {
            $count = DB::table('course_enrollments')->count();
            if ($count > 0) {
                throw new \RuntimeException(
                    "course_enrollments table has {$count} records. ".
                    'Please backup/migrate data before dropping. '.
                    'Set FORCE_DROP_ENROLLMENTS=true to proceed anyway.'
                );
            }
            Schema::dropIfExists('course_enrollments');
        }
    }

    /**
     * Reverse the migrations.
     *
     * Recreate the table structure for rollback capability.
     */
    public function down(): void
    {
        Schema::create('course_enrollments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('enrolled_by_admin_id')->nullable()->constrained('users')->onDelete('set null');
            $table->date('enrollment_date');
            $table->enum('status', ['active', 'completed', 'dropped', 'failed'])->default('active');
            $table->date('completion_date')->nullable();
            $table->decimal('progress_percentage', 5, 2)->default(0);
            $table->decimal('current_grade', 5, 2)->nullable();
            $table->decimal('final_grade', 5, 2)->nullable();
            $table->string('letter_grade', 2)->nullable();
            $table->timestamps();

            $table->unique(['course_id', 'user_id']);
        });
    }
};
