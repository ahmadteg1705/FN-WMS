<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('marketings', function (Blueprint $table) {

            // Hapus kolom lama
            $table->dropColumn([
                'kode',
                'nama',
                'email',
            ]);

            // Tambah kolom baru
            $table->foreignId('user_id')
                ->after('id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('position_id')
                ->after('user_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('foto')->nullable()->after('status');

            $table->date('tanggal_masuk')->nullable()->after('foto');

        });
    }

    public function down(): void
    {
        Schema::table('marketings', function (Blueprint $table) {

            $table->dropForeign(['user_id']);
            $table->dropForeign(['position_id']);

            $table->dropColumn([
                'user_id',
                'position_id',
                'foto',
                'tanggal_masuk',
            ]);

            $table->string('kode')->unique();
            $table->string('nama');
            $table->string('email')->nullable();

        });
    }
};