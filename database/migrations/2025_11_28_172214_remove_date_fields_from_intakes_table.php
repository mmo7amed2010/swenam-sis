<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Simplifies the Intake model by removing date and capacity fields.
     * The intake will now only have: name, slug, is_active, description, sort_order.
     */
    public function up(): void
    {
        Schema::table('intakes', function (Blueprint $table) {
            // Drop indexes first
            $table->dropIndex(['is_active', 'start_date']);
            $table->dropIndex(['application_deadline']);

            // Drop the columns
            $table->dropColumn([
                'start_date',
                'end_date',
                'application_open_date',
                'application_deadline',
                'max_capacity',
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('intakes', function (Blueprint $table) {
            // Re-add the columns
            $table->date('start_date')->after('slug');
            $table->date('end_date')->nullable()->after('start_date');
            $table->date('application_open_date')->nullable()->after('end_date');
            $table->date('application_deadline')->nullable()->after('application_open_date');
            $table->unsignedInteger('max_capacity')->nullable()->after('description');

            // Re-add the indexes
            $table->index(['is_active', 'start_date']);
            $table->index('application_deadline');
        });
    }
};
