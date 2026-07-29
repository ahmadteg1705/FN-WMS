<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkOrderInstallation extends Model
{
    protected $fillable = [
        'work_order_id',
        'sn_modem',
        'panjang_kabel',
        'foto_sn_modem',
        'foto_speedtest',
        'foto_rumah_depan',
        'foto_form_registrasi',
        'foto_redaman_modem',
        'modem_terpasang',
        'onu_online',
        'internet_normal',
        'speedtest_berhasil',
        'pelanggan_menerima',
        'latitude',
        'longitude',
        'catatan_teknisi',
        'sn_disimpan_at',
        'dikirim_verifikasi_at',
    ];

    protected $casts = [
        'panjang_kabel' => 'integer',
        'modem_terpasang' => 'boolean',
        'onu_online' => 'boolean',
        'internet_normal' => 'boolean',
        'speedtest_berhasil' => 'boolean',
        'pelanggan_menerima' => 'boolean',
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
        'sn_disimpan_at' => 'datetime',
        'dikirim_verifikasi_at' => 'datetime',
    ];

    public function workOrder()
    {
        return $this->belongsTo(WorkOrder::class);
    }
}