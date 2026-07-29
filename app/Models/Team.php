<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Technician;

class Team extends Model
{
    protected $fillable = [
        'nama',
        'leader',
        'keterangan',
        'status'
    ];

    public function technicians()
    {
        return $this->hasMany(Technician::class);
    }
    public function workOrders()
{
    return $this->hasMany(WorkOrder::class);
}
}