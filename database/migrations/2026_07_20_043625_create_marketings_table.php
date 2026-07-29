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
    Schema::create('marketings', function (Blueprint $table) {

       $table->id();

        $table->foreignId('user_id')
            ->constrained()
            ->cascadeOnDelete();

        $table->foreignId('position_id')
            ->constrained()
            ->cascadeOnDelete();

        $table->string('telepon')->nullable();

        $table->string('wilayah')->nullable();

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
        Schema::dropIfExists('marketings');
    }
};
