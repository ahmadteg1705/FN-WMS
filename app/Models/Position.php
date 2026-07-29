<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Technician;

class Position extends Model
{
    protected $fillable = [

        'nama',

        'status',

        'keterangan'

    ];

    public function technicians()
{
    return $this->hasMany(Technician::class, 'jabatan', 'nama');
}
    
}