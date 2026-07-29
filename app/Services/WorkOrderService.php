<?php

namespace App\Services;

use App\Models\Registration;
use App\Models\RegistrationHistory;
use App\Models\WorkOrder;
use Illuminate\Support\Facades\DB;

class WorkOrderService
{
    /**
     * Generate Nomor Work Order
     */
    public function generateNumber(): string
    {
        $prefix = 'WO-' . now()->format('Ymd');

        $last = WorkOrder::whereDate(
                'created_at',
                today()
            )
            ->latest('id')
            ->first();

        $number = $last
            ? ((int) substr($last->work_order_no, -4)) + 1
            : 1;

        return sprintf(
            '%s-%04d',
            $prefix,
            $number
        );
    }
    public function create(array $data): WorkOrder
{
    return DB::transaction(function () use ($data) {

        $registration = Registration::findOrFail(
            $data['registration_id']
        );
        $existing = WorkOrder::where(
            'registration_id',
            $registration->id
        )->first();

        if ($existing) {
            return $existing;
        }
        $workOrder = WorkOrder::create([

            'work_order_no' => $this->generateNumber(),

            'registration_id' => $registration->id,

            'team_id' => $data['team_id'],

            'technician_id' => null,

            'assigned_by' => auth()->id(),

            'tanggal' => $data['tanggal'],

            'jam' => $data['jam'],

            'prioritas' => $data['prioritas'],

            'status' => 'Menunggu Diterima Teknisi',

            'catatan' => $data['catatan'] ?? null,

        ]);

        $statusLama = $registration->status;

        $registration->update([
            'status' => 'Menunggu Diterima Teknisi',
        ]);

        RegistrationHistory::create([

            'registration_id' => $registration->id,

            'status_lama' => $statusLama,

            'status_baru' => 'Dijadwalkan',

            'catatan' =>
                "Tim Teknisi telah dijadwalkan.\n\n" .
                "Tim Teknisi : {$workOrder->team->nama}\n" .
                "Leader : {$workOrder->team->leader}",

            'user_id' => auth()->id(),

        ]);

        return $workOrder;
    });
}
public function store(
    Request $request,
    WorkOrderService $service
)
{
    $request->validate([
        'registration_id' => 'required',
        'team_id' => 'required',
        'technician_id' => 'required',
        'tanggal' => 'required|date',
        'jam' => 'required',
        'prioritas' => 'required',
    ]);

    $workOrder = $service->create(
        $request->all()
    );

    return redirect()
        ->route(
            'registrations.show',
            $workOrder->registration
        )
        ->with(
            'success',
            'Work Order berhasil dibuat.'
        );
}
}