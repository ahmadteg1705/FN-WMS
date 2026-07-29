<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('technicians', function (Blueprint $table) {

            $table->foreignId('position_id')
                ->nullable()
                ->after('team_id')
                ->constrained('positions')
                ->nullOnDelete();

        });
    }

    public function down(): void
    {
        Schema::table('technicians', function (Blueprint $table) {

            $table->dropForeign(['position_id']);

            $table->dropColumn('position_id');

        });
    }
};
