<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('noc_activations', function (Blueprint $table) {
            $table->id();

            $table
                ->foreignId('work_order_id')
                ->unique()
                ->constrained()
                ->cascadeOnDelete();

            $table
                ->foreignId('handled_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            /*
            |--------------------------------------------------------------------------
            | Status proses NOC
            |--------------------------------------------------------------------------
            */

            $table
                ->string('status', 50)
                ->default('Menunggu Aktivasi');

            /*
            |--------------------------------------------------------------------------
            | Data perangkat
            |--------------------------------------------------------------------------
            */

            $table
                ->string('sn_modem', 100)
                ->nullable();

            $table
                ->string('router_name', 150)
                ->nullable();

            $table
                ->string('odp_name', 150)
                ->nullable();

            $table
                ->string('olt_interface', 100)
                ->nullable();

            $table
                ->unsignedInteger('onu_number')
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | Akun PPPoE
            |--------------------------------------------------------------------------
            */

            $table
                ->string('pppoe_username', 150)
                ->nullable();

            $table
                ->string('pppoe_password', 255)
                ->nullable();

            $table
                ->string('package_name', 150)
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | Hasil provisioning
            |--------------------------------------------------------------------------
            */

            $table
                ->longText('provisioning_script')
                ->nullable();

            $table
                ->text('activation_result')
                ->nullable();

            $table
                ->text('noc_notes')
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | Timestamp proses
            |--------------------------------------------------------------------------
            */

            $table
                ->timestamp('accepted_at')
                ->nullable();

            $table
                ->timestamp('started_at')
                ->nullable();

            $table
                ->timestamp('activated_at')
                ->nullable();

            $table
                ->timestamp('failed_at')
                ->nullable();

            $table->timestamps();

            $table->index('status');
            $table->index('handled_by');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('noc_activations');
    }
};