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
        Schema::table('users', function (Blueprint $table) {

            $table->string('employee_code')->nullable()->unique()->after('id');

            $table->string('username')->unique()->after('name');

            $table->string('phone')->nullable()->after('email');

            $table->string('photo')->nullable()->after('password');

            $table->boolean('status')
                  ->default(true)
                  ->after('photo');

            $table->timestamp('last_login_at')
                  ->nullable()
                  ->after('status');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {

            $table->dropColumn([
                'employee_code',
                'username',
                'phone',
                'photo',
                'status',
                'last_login_at'
            ]);

        });
    }
};