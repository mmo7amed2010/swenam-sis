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
        Schema::create('module_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('module_id')->constrained('course_modules')->cascadeOnDelete();
            $table->morphs('itemable'); // itemable_type and itemable_id
            $table->unsignedInteger('order_position')->default(0);
            $table->boolean('is_required')->default(false);
            $table->timestamp('release_date')->nullable();
            $table->timestamps();

            // Indexes for performance
            $table->index(['module_id', 'order_position'], 'idx_module_order');
            $table->unique(['itemable_type', 'itemable_id'], 'idx_itemable_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('module_items');
    }
};
