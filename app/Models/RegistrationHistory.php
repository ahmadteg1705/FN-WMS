<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RegistrationHistory extends Model
{
    protected $fillable = [
        'registration_id',
        'status_lama',
        'status_baru',
        'catatan',
        'user_id',
    ];

    public function registration()
    {
        return $this->belongsTo(Registration::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}