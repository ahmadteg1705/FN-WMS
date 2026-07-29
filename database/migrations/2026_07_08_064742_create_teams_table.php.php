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
    Schema::create('teams', function (Blueprint $table) {

        $table->id();

        $table->string('nama');

        $table->string('leader')->nullable();

        $table->text('keterangan')->nullable();

        $table->boolean('status')->default(true);

        $table->timestamps();

    });
}
};
