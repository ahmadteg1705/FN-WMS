<?php

namespace App\Models;

use App\Models\WorkOrderInstallation;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class WorkOrder extends Model
{
    protected $fillable = [

        'work_order_no',

        'registration_id',

        'parent_id',

        'team_id',

        'technician_id',

        'assigned_by',

        'tanggal',

        'jam',

        'prioritas',

        'status',

        'reschedule_count',

        'catatan',

        'cancel_reason',

        'accepted_at',

        'started_at',

        'finished_at',
    ];

    protected $casts = [

        'tanggal' => 'date',

        'accepted_at' => 'datetime',

        'started_at' => 'datetime',

        'finished_at' => 'datetime',
    ];

    public function registration()
    {
        return $this->belongsTo(Registration::class);
    }

    public function parent()
    {
        return $this->belongsTo(
            WorkOrder::class,
            'parent_id'
        );
    }

    public function children()
    {
        return $this->hasMany(
            WorkOrder::class,
            'parent_id'
        );
    }

    public function technician()
    {
        return $this->belongsTo(Technician::class);
    }

    public function team()
    {
        return $this->belongsTo(Team::class);
    }

    public function assignedBy()
    {
        return $this->belongsTo(
            User::class,
            'assigned_by'
        );
    }
    public function account()
{
    return $this->hasOne(WorkOrderAccount::class);
}
public function installation()
{
    return $this->hasOne(WorkOrderInstallation::class);
}
public function nocActivation(): HasOne
{
    return $this->hasOne(NocActivation::class);
}
}