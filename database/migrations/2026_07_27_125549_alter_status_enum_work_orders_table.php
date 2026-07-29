<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("
            ALTER TABLE work_orders
            MODIFY status ENUM(
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
                'Dibatalkan'
            )
            DEFAULT 'Menunggu Diterima Teknisi'
        ");
    }

    public function down(): void
    {
        DB::statement("
            ALTER TABLE work_orders
            MODIFY status ENUM(
                'Pending',
                'Accepted',
                'On Progress',
                'Completed',
                'Cancelled'
            )
            DEFAULT 'Pending'
        ");
    }
};