<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Registration extends Model
{
    protected $fillable = [
        'registration_number',
        'nama',
        'nik',
        'telepon',
        'alamat',
        'foto_ktp',
        'latitude',
        'longitude',
        'package_id',
        'odp_id',
        'marketing_id',
        'status',
        'keterangan',
    ];

    public function package()
    {
        return $this->belongsTo(Package::class);
    }

    public function odp()
    {
        return $this->belongsTo(Odp::class);
    }
    public function marketing()
{
    return $this->belongsTo(Marketing::class);
}
public function histories()
{
    return $this->hasMany(RegistrationHistory::class)
                ->latest();
}
public function workOrder()
{
    return $this->hasOne(\App\Models\WorkOrder::class);
}
}