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
        Schema::create('odps', function (Blueprint $table) {

            $table->id();

            $table->string('nama')->unique();
            $table->string('router');
            $table->string('card');

            $table->integer('onu_awal');
            $table->integer('onu_akhir');

            $table->integer('kapasitas')->default(8);

            $table->boolean('status')->default(true);

            $table->text('keterangan')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('odps');
    }
};