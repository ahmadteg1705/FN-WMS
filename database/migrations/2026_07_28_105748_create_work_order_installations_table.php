<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('work_order_installations', function (Blueprint $table) {
            $table->id();

            $table->foreignId('work_order_id')
                ->unique()
                ->constrained()
                ->cascadeOnDelete();

            /*
            |--------------------------------------------------------------------------
            | Informasi Modem
            |--------------------------------------------------------------------------
            */

            $table->string('sn_modem')->nullable();

            /*
            |--------------------------------------------------------------------------
            | Dokumentasi Foto
            |--------------------------------------------------------------------------
            | Dibuat nullable agar teknisi bisa menyimpan bertahap.
            */

            $table->string('foto_sn_modem')->nullable();
            $table->string('foto_speedtest')->nullable();
            $table->string('foto_rumah_depan')->nullable();
            $table->string('foto_form_registrasi')->nullable();
            $table->string('foto_redaman_modem')->nullable();

            /*
            |--------------------------------------------------------------------------
            | Checklist Pekerjaan
            |--------------------------------------------------------------------------
            */

            $table->boolean('modem_terpasang')->default(false);
            $table->boolean('onu_online')->default(false);
            $table->boolean('internet_normal')->default(false);
            $table->boolean('speedtest_berhasil')->default(false);
            $table->boolean('pelanggan_menerima')->default(false);

            /*
            |--------------------------------------------------------------------------
            | Lokasi Teknisi
            |--------------------------------------------------------------------------
            */

            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();

            /*
            |--------------------------------------------------------------------------
            | Catatan Teknisi
            |--------------------------------------------------------------------------
            */

            $table->text('catatan_teknisi')->nullable();

            /*
            |--------------------------------------------------------------------------
            | Penanda Proses
            |--------------------------------------------------------------------------
            */

            // Terisi ketika data SN modem pertama kali disimpan.
            $table->timestamp('sn_disimpan_at')->nullable();

            // Terisi ketika semua bukti lengkap dan dikirim untuk verifikasi.
            $table->timestamp('dikirim_verifikasi_at')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('work_order_installations');
    }
};