<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Team;
use App\Models\User;
use App\Models\Position;

class Technician extends Model
{
    protected $fillable = [

    'user_id',

    'nik',

    'telepon',

    'alamat',

    'team_id',

    'position_id',

    'status',

    'foto',

    'tanggal_masuk',

    'keterangan',

];

    public function team()
    {
        return $this->belongsTo(Team::class);
    }
    public function user()
{
    return $this->belongsTo(User::class);
}
public function position()
{
    return $this->belongsTo(Position::class);
}
public function workOrders()
{
    return $this->hasMany(WorkOrder::class);
}
}