<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Position;

class Marketing extends Model
{
    protected $fillable = [

        'user_id',

        'position_id',

        'telepon',

        'wilayah',

        'status',

        'foto',

        'tanggal_masuk',

        'keterangan',

    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function position()
    {
        return $this->belongsTo(Position::class);
    }
}