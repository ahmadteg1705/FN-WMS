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
        'latitude',
        'longitude',
        'tanggal_registrasi',
        'status',
        'foto_ktp',
        'foto_rumah',
        'catatan'
    ];
}