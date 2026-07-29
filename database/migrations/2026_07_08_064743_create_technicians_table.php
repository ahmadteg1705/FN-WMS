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
        Schema::create('technicians', function (Blueprint $table) {

            $table->id();

            $table->string('nama');

            $table->string('nik')->unique();

            $table->string('username')->unique();

            $table->string('password');

            $table->string('telepon');

            $table->string('email')->nullable();

            $table->string('alamat')->nullable();

            $table->string('jabatan')->default('Teknisi');

            $table->foreignId('team_id')
                  ->constrained()
                  ->cascadeOnDelete();

            $table->boolean('status')->default(true);

            $table->string('foto')->nullable();

            $table->date('tanggal_masuk')->nullable();

            $table->text('keterangan')->nullable();

            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('technicians');
    }
};