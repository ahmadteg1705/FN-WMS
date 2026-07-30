<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('routers', function (Blueprint $table) {
            $table->string('service_command', 150)
                ->default('service 1 gemport 1 cos 0 vlan {vlan}')
                ->after('security_mgmt');
            $table->string('wan_ethuni', 50)->default('1,2,3,4')->after('service_command');
            $table->string('wan_ssid', 30)->default('1')->after('wan_ethuni');
            $table->string('wan_service', 50)->default('internet')->after('wan_ssid');
        });

        // Data awal dipindahkan ke database Router NAS.
        DB::table('routers')
            ->where(function ($query) {
                $query->whereRaw('LOWER(kota) = ?', ['kudus'])
                    ->orWhereRaw('LOWER(nama) LIKE ?', ['%kudus%']);
            })
            ->update([
                'service_command' => 'service 1 gemport 1 vlan {vlan}',
                'wan_ethuni' => '1,2,3,4',
                'wan_ssid' => '1',
                'wan_service' => 'internet',
            ]);

        DB::table('routers')
            ->where(function ($query) {
                $query->whereRaw('LOWER(kota) = ?', ['jepara'])
                    ->orWhereRaw('LOWER(nama) LIKE ?', ['%jepara%']);
            })
            ->update([
                'service_command' => 'service 1 gemport 1 cos 0 vlan {vlan}',
                'wan_ethuni' => '1,2,3,4',
                'wan_ssid' => '1',
                'wan_service' => 'internet',
            ]);
    }

    public function down(): void
    {
        Schema::table('routers', function (Blueprint $table) {
            $table->dropColumn([
                'service_command',
                'wan_ethuni',
                'wan_ssid',
                'wan_service',
            ]);
        });
    }
};
