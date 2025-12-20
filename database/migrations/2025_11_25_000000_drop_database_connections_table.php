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
        if (Schema::hasTable('database_connections')) {
            Schema::drop('database_connections');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::create('database_connections', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('description')->nullable();
            $table->string('host');
            $table->unsignedInteger('port')->default(3306);
            $table->string('database');
            $table->string('username');
            $table->text('password')->nullable();
            $table->boolean('is_active')->default(false);
            $table->boolean('is_connected')->default(false);
            $table->text('last_error')->nullable();
            $table->timestamp('last_connected_at')->nullable();
            $table->json('connection_options')->nullable();
            $table->timestamps();
        });
    }
};
