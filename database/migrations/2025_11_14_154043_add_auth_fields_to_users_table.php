<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            // Split name into first_name and last_name
            $table->string('first_name')->nullable()->after('id');
            $table->string('last_name')->nullable()->after('first_name');

            // Authentication security fields
            $table->integer('failed_login_attempts')->default(0)->after('password');
            $table->timestamp('locked_until')->nullable()->after('failed_login_attempts');
            $table->boolean('password_change_required')->default(false)->after('locked_until');

            // Update existing last_login_at if not present
            if (! Schema::hasColumn('users', 'last_login_at')) {
                $table->timestamp('last_login_at')->nullable()->after('password_change_required');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'first_name',
                'last_name',
                'failed_login_attempts',
                'locked_until',
                'password_change_required',
            ]);
        });
    }
};
