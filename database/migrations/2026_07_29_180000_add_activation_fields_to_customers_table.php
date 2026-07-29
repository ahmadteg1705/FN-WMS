<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->string('nas', 150)->nullable()->after('sn_modem');
            $table->unsignedInteger('onu_number')->nullable()->after('nas');
            $table->string('pppoe_username', 150)->nullable()->after('onu_number');
            $table->string('pppoe_password')->nullable()->after('pppoe_username');

            $table->index(['odp', 'onu_number']);
            $table->index('pppoe_username');
        });
    }

    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropIndex(['odp', 'onu_number']);
            $table->dropIndex(['pppoe_username']);
            $table->dropColumn([
                'nas',
                'onu_number',
                'pppoe_username',
                'pppoe_password',
            ]);
        });
    }
};
