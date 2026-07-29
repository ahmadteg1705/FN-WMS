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
    Schema::create('routers', function (Blueprint $table) {

        $table->id();

        $table->string('nama');

        $table->string('kota');

        $table->string('hostname');

        $table->string('ip');

        $table->string('vlan');

        $table->string('vlan_profile');

        $table->string('tcont_profile');

        $table->string('onu_type');

        $table->string('security_mgmt');

        $table->boolean('status')->default(true);

        $table->timestamps();

    });
}
};
