<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;

    /**
     * Mass Assignable
     */
    protected $fillable = [
        'employee_code',
        'name',
        'username',
        'email',
        'phone',
        'password',
        'photo',
        'status',
        'last_login_at',
    ];

    /**
     * Hidden Attributes
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Attribute Casting
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'last_login_at'     => 'datetime',
            'password'          => 'hashed',
            'status'            => 'boolean',
        ];
    }

    /**
     * Scope User Aktif
     */
    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    /**
     * Scope User Nonaktif
     */
    public function scopeInactive($query)
    {
        return $query->where('status', false);
    }

    /**
     * Nama Role
     */
    public function roleName()
    {
        return $this->roles->first()?->name ?? '-';
    }

    /**
     * URL Foto Profil
     */
    public function photoUrl()
    {
        if ($this->photo) {
            return asset('storage/' . $this->photo);
        }

        return asset('images/default-avatar.webp');
    }
    public function technician()
    {
    return $this->hasOne(Technician::class);
    }
    public function marketing()
    {
    return $this->hasOne(\App\Models\Marketing::class);
    }
    public function assignedWorkOrders()
{
    return $this->hasMany(WorkOrder::class, 'assigned_by');
}
}