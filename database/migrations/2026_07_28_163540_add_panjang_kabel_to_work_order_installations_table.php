<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('work_order_installations', function (Blueprint $table) {
            $table
                ->unsignedInteger('panjang_kabel')
                ->nullable()
                ->after('sn_modem')
                ->comment('Panjang kabel yang digunakan dalam meter');
        });
    }

    public function down(): void
    {
        Schema::table('work_order_installations', function (Blueprint $table) {
            $table->dropColumn('panjang_kabel');
        });
    }
};