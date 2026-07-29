<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NocActivation extends Model
{
    public const STATUS_WAITING = 'Menunggu Aktivasi';
    public const STATUS_ACCEPTED = 'Diterima NOC';
    public const STATUS_PROCESSING = 'Proses Aktivasi';
    public const STATUS_WAITING_ADMIN_VERIFICATION = 'Menunggu Verifikasi Admin';
    public const STATUS_SUCCESS = 'Aktivasi Berhasil';
    public const STATUS_FAILED = 'Aktivasi Gagal';

    protected $fillable = [
        'work_order_id',
        'handled_by',
        'status',
        'sn_modem',
        'router_name',
        'odp_name',
        'olt_interface',
        'onu_number',
        'pppoe_username',
        'pppoe_password',
        'package_name',
        'provisioning_script',
        'activation_result',
        'noc_notes',
        'accepted_at',
        'started_at',
        'activated_at',
        'failed_at',
    ];

    protected function casts(): array
    {
        return [
            'onu_number' => 'integer',
            'accepted_at' => 'datetime',
            'started_at' => 'datetime',
            'activated_at' => 'datetime',
            'failed_at' => 'datetime',
        ];
    }

    public function workOrder(): BelongsTo
    {
        return $this->belongsTo(WorkOrder::class);
    }

    public function handler(): BelongsTo
    {
        return $this->belongsTo(User::class, 'handled_by');
    }

    public function isWaiting(): bool
    {
        return $this->status === self::STATUS_WAITING;
    }

    public function isAccepted(): bool
    {
        return $this->status === self::STATUS_ACCEPTED;
    }

    public function isProcessing(): bool
    {
        return $this->status === self::STATUS_PROCESSING;
    }

    public function isCompleted(): bool
    {
        return in_array($this->status, [
            self::STATUS_WAITING_ADMIN_VERIFICATION,
            self::STATUS_SUCCESS,
            self::STATUS_FAILED,
        ], true);
    }
}
