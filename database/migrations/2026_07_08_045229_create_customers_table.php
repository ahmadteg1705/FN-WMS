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
        Schema::create('customers', function (Blueprint $table) {

            $table->id();

            $table->string('nama');
            $table->string('nik')->nullable();
            $table->text('alamat');

            $table->string('telepon');
            $table->string('email')->nullable();

            $table->string('paket');
            $table->string('nomor_pelanggan')->nullable();

            $table->string('odp');
            $table->string('sn_modem')->nullable();

            $table->decimal('latitude',10,7)->nullable();
            $table->decimal('longitude',10,7)->nullable();

            $table->date('tanggal_registrasi')->nullable();

            $table->string('status')->default('Calon');

            $table->string('foto_ktp')->nullable();
            $table->string('foto_rumah')->nullable();

            $table->text('catatan')->nullable();

            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
