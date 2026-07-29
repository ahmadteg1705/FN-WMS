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
        Schema::create('registrations', function (Blueprint $table) {

    $table->id();

    // Nomor Registrasi
    $table->string('registration_number')->unique();

    // Data Calon Pelanggan
    $table->string('nama');
    $table->string('nik',25)->nullable();
    $table->string('telepon',20);
    $table->text('alamat');

    // Lokasi
    $table->string('latitude')->nullable();
    $table->string('longitude')->nullable();

    // Relasi Master
    $table->foreignId('package_id')->nullable()->constrained()->nullOnDelete();
    $table->foreignId('odp_id')->nullable()->constrained()->nullOnDelete();

    // Sales/Admin
    $table->foreignId('marketing_id')
      ->nullable()
      ->constrained()
      ->nullOnDelete();

    // Workflow
    $table->enum('status',[
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
    ])->default('Menunggu Diterima Teknisi');

    // Catatan
    $table->text('keterangan')->nullable();

    $table->timestamps();

});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('registrations');
    }
};
