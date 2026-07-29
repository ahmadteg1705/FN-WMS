<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('work_orders', function (Blueprint $table) {

            $table->id();

            $table->foreignId('registration_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('team_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->foreignId('technician_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->foreignId('assigned_by')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->date('tanggal');

            $table->time('jam');

            $table->enum('prioritas', [
                'Rendah',
                'Normal',
                'Tinggi',
                'Darurat'
            ])->default('Normal');

            $table->enum('status', [
                'Draft',
                'Menunggu Diterima Teknisi',
                'Diterima Teknisi',
                'Persiapan',
                'Menuju Lokasi',
                'Di Lokasi',
                'Pelanggan Tidak Ditemui',
                'Dijadwalkan Ulang',
                'Instalasi',
                'Menunggu Verifikasi',
                'Selesai',
                'Dibatalkan',
            ])->default('Menunggu Diterima Teknisi');

            $table->text('catatan')->nullable();

            $table->timestamp('accepted_at')->nullable();

            $table->timestamp('started_at')->nullable();

            $table->timestamp('finished_at')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('work_orders');
    }
};