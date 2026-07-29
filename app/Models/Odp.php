<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Odp extends Model
{
    protected $fillable = [

        'nama',
        'router',
        'card',
        'onu_awal',
        'onu_akhir',
        'kapasitas',
        'latitude',
        'longitude',
        'status',
        'keterangan'

    ];
}