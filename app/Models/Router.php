<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Router extends Model
{
    protected $fillable = [
        'nama',
        'kota',
        'hostname',
        'ip',
        'vlan',
        'vlan_profile',
        'tcont_profile',
        'onu_type',
        'security_mgmt',
        'service_command',
        'wan_ethuni',
        'wan_ssid',
        'wan_service',
        'status',
    ];
}
