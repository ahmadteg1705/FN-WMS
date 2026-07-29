<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("
            ALTER TABLE registrations
            MODIFY status ENUM(
                'Registrasi Baru',
                'Diverifikasi',
                'Dijadwalkan',
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
                'Batal'
            )
            DEFAULT 'Registrasi Baru'
        ");
    }

    public function down(): void
    {
        DB::statement("
            ALTER TABLE registrations
            MODIFY status ENUM(
                'Menunggu Diterima Teknisi',
                'Diterima Teknisi',
                'Persiapan',
                'Menuju Lokasi',
                'Di Lokasi',
                'Pelanggan Tidak Ditemui',
                'Dijadwalkan Ulang',
                'Instalasi',
                'Menunggu Verifikasi',
                'Selesai'
            )
            DEFAULT 'Menunggu Diterima Teknisi'
        ");
    }
};