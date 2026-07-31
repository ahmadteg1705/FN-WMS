<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $fillable = [
        'nama',
        'nik',
        'alamat',
        'telepon',
        'email',
        'paket',
        'nomor_pelanggan',
        'odp',
        'sn_modem',
        'nas',
        'onu_number',
        'pppoe_username',
        'pppoe_password',
        'latitude',
        'longitude',
        'tanggal_registrasi',
        'status',
        'foto_ktp',
        'foto_rumah',
        'catatan',
    ];

    protected $casts = [
        'tanggal_registrasi' => 'date',
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
    ];
}
