<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkOrderAccount extends Model
{
    protected $fillable = [
        'work_order_id',
        'username',
        'password',
        'created_by',
        'updated_by',
    ];

    public function workOrder()
    {
        return $this->belongsTo(WorkOrder::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}