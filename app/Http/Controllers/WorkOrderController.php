<?php

namespace App\Http\Controllers;

use App\Models\Registration;
use App\Models\Team;
use App\Models\Technician;
use App\Models\WorkOrder;
use App\Services\WorkOrderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WorkOrderController extends Controller
{
    /**
     * Daftar seluruh Work Order
     */
    public function index()
    {
        $query = WorkOrder::with([
            'registration',
            'team',
            'technician.user',
            'assignedBy',
            'account',
        ]);

        // ===========================
        // SUPER ADMIN & ADMIN
        // ===========================
        if (auth()->user()->hasAnyRole(['Super Admin', 'Admin'])) {
            // Melihat semua Work Order
        }

        // ===========================
        // TEKNISI
        // ===========================
        elseif (auth()->user()->hasRole('Teknisi')) {

            $technician = auth()->user()->technician;

            if ($technician) {
                $query->where(function ($q) use ($technician) {

                    // WO yang ditugaskan langsung
                    $q->where('technician_id', $technician->id);

                    // atau WO berdasarkan tim
                    if ($technician->team_id) {
                        $q->orWhere('team_id', $technician->team_id);
                    }

                });
            } else {
                $query->whereRaw('1 = 0');
            }
        }

        // ===========================
        // NOC
        // ===========================
        elseif (auth()->user()->hasRole('NOC')) {

            $query->whereIn('status', [
                'Diterima Teknisi',
                'Persiapan',
                'Menuju Lokasi',
                'Di Lokasi',
                'Instalasi',
                'Menunggu Verifikasi',
            ]);
        }

        $workOrders = $query
            ->latest()
            ->paginate(15);

        $stats = [
            'total' => (clone $query)->count(),

            'pending' => (clone $query)
                ->where('status', 'Menunggu Diterima Teknisi')
                ->count(),

            'progress' => (clone $query)
                ->whereNotIn('status', [
                    'Draft',
                    'Selesai',
                    'Dibatalkan',
                ])
                ->count(),

            'completed' => (clone $query)
                ->where('status', 'Selesai')
                ->count(),
        ];

        return view(
            'work_orders.index',
            compact(
                'workOrders',
                'stats'
            )
        );
    }

    /**
     * Form penjadwalan teknisi
     */
    public function create(Request $request)
    {
        $registration = Registration::with([
                'package',
                'odp',
                'marketing.user',
            ])
            ->findOrFail($request->registration);

        $teams = Team::where('status', '1')
            ->orderBy('nama')
            ->get();

        return view(
            'work_orders.create',
            compact(
                'registration',
                'teams'
            )
        );
    }
        /**
     * Simpan Work Order baru
     */
    public function store(
        Request $request,
        WorkOrderService $service
    ) {
        $validated = $request->validate([
            'registration_id' => ['required', 'exists:registrations,id'],
            'team_id'         => ['required', 'exists:teams,id'],
            'tanggal'         => ['required', 'date'],
            'jam'             => ['required'],
            'prioritas'       => ['required', 'in:Rendah,Normal,Tinggi,Urgent'],
            'catatan'         => ['nullable', 'string'],
        ]);

        $workOrder = $service->create($validated);

        return redirect()
            ->route('registrations.show', $workOrder->registration_id)
            ->with(
                'success',
                'Work Order berhasil dibuat.'
            );
    }
        /**
     * Detail Work Order
     */
    public function show(WorkOrder $workOrder)
    {
        $workOrder->load([
            'registration.package',
            'registration.odp',
            'registration.marketing.user',
            'team',
            'team.technicians.user',
            'technician.user',
            'assignedBy',
            'parent',
            'children.technician.user',
            'account',
        ]);

        return view(
            'work_orders.show',
            compact('workOrder')
        );
    }

    /**
     * Form Edit Work Order
     */
    public function edit(WorkOrder $workOrder)
    {
        $workOrder->load([
            'registration.package',
            'registration.odp',
            'registration.marketing.user',
        ]);

        $teams = Team::where('status', 'Aktif')
            ->orderBy('nama')
            ->get();

        $technicians = Technician::with('user')
            ->where('team_id', $workOrder->team_id)
            ->where('status', 'Aktif')
            ->get();

        return view(
            'work_orders.edit',
            compact(
                'workOrder',
                'teams',
                'technicians'
            )
        );
    }

    /**
     * Update Work Order
     */
    public function update(
        Request $request,
        WorkOrder $workOrder
    ) {
        $validated = $request->validate([
            'team_id' => ['required', 'exists:teams,id'],
            'technician_id' => ['required', 'exists:technicians,id'],
            'tanggal' => ['required', 'date'],
            'jam' => ['required'],
            'prioritas' => ['required', 'in:Rendah,Normal,Tinggi,Urgent'],
            'status' => ['required'],
            'catatan' => ['nullable', 'string'],
        ]);

        $technician = Technician::where('id', $validated['technician_id'])
            ->where('team_id', $validated['team_id'])
            ->first();

        if (!$technician) {
            return back()
                ->withInput()
                ->withErrors([
                    'technician_id' => 'Teknisi tidak termasuk dalam tim yang dipilih.',
                ]);
        }

        $workOrder->update($validated);

        return redirect()
            ->route('work-orders.show', $workOrder)
            ->with(
                'success',
                'Work Order berhasil diperbarui.'
            );
    }
/**
 * Mengambil Leader dan Anggota Tim
 */
public function getTeamMembers(Team $team)
{
    $technicians = Technician::with('user')
        ->where('team_id', $team->id)
        ->where('status', 1)
        ->get();

    return response()->json([
        'leader' => $team->leader,
        'members' => $technicians->map(function ($technician) {
            return $technician->user->name;
        })->values(),
    ]);
}

    /**
     * Hapus Work Order
     */
    public function destroy(WorkOrder $workOrder)
    {
        $workOrder->delete();

        return redirect()
            ->route('work-orders.index')
            ->with(
                'success',
                'Work Order berhasil dihapus.'
            );
    }
   public function accept(WorkOrder $workOrder)
    {
        $statusLama = $workOrder->registration->status;

        $workOrder->update([
            'status' => 'Diterima Teknisi',
        ]);

        $workOrder->registration->update([
            'status' => 'Diterima Teknisi',
        ]);

        \App\Models\RegistrationHistory::create([
            'registration_id' => $workOrder->registration->id,
            'status_lama'     => $statusLama,
            'status_baru'     => 'Diterima Teknisi',
            'catatan'         => 'Work Order diterima oleh Teknisi.',
            'user_id'         => auth()->id(),
        ]);

        return redirect()
            ->route('work-orders.show', $workOrder)
            ->with('success', 'Work Order berhasil diterima.');
    }
public function preparation(WorkOrder $workOrder)
{
    $statusLama = $workOrder->registration->status;

    $workOrder->update([
        'status' => 'Persiapan',
    ]);

    $workOrder->registration->update([
        'status' => 'Persiapan',
    ]);

    \App\Models\RegistrationHistory::create([
        'registration_id' => $workOrder->registration->id,
        'status_lama'     => $statusLama,
        'status_baru'     => 'Persiapan',
        'catatan'         => 'Teknisi sedang melakukan persiapan.',
        'user_id'         => auth()->id(),
    ]);

    return redirect()
        ->route('work-orders.show', $workOrder)
        ->with('success', 'Teknisi sedang melakukan persiapan.');
}
public function depart(WorkOrder $workOrder)
{
    $statusLama = $workOrder->registration->status;

    $workOrder->update([
        'status' => 'Menuju Lokasi',
    ]);

    $workOrder->registration->update([
        'status' => 'Menuju Lokasi',
    ]);

    \App\Models\RegistrationHistory::create([
        'registration_id' => $workOrder->registration->id,
        'status_lama'     => $statusLama,
        'status_baru'     => 'Menuju Lokasi',
        'catatan'         => 'Teknisi menuju lokasi pelanggan.',
        'user_id'         => auth()->id(),
    ]);

    return redirect()
        ->route('work-orders.show', $workOrder)
        ->with('success', 'Status berhasil diubah menjadi Menuju Lokasi.');
}
public function arrive(WorkOrder $workOrder)
{
    $statusLama = $workOrder->registration->status;

    $workOrder->update([
        'status' => 'Di Lokasi',
    ]);

    $workOrder->registration->update([
        'status' => 'Di Lokasi',
    ]);

    \App\Models\RegistrationHistory::create([
        'registration_id' => $workOrder->registration->id,
        'status_lama'     => $statusLama,
        'status_baru'     => 'Di Lokasi',
        'catatan'         => 'Teknisi telah sampai di lokasi pelanggan.',
        'user_id'         => auth()->id(),
    ]);

    return redirect()
        ->route('work-orders.show', $workOrder)
        ->with('success', 'Status berhasil diubah menjadi Di Lokasi.');
}
public function customerNotFound(WorkOrder $workOrder)
{
    $statusLama = $workOrder->registration->status;

    $workOrder->update([
        'status' => 'Pelanggan Tidak Ditemui',
    ]);

    $workOrder->registration->update([
        'status' => 'Pelanggan Tidak Ditemui',
    ]);

    \App\Models\RegistrationHistory::create([
        'registration_id' => $workOrder->registration->id,
        'status_lama'     => $statusLama,
        'status_baru'     => 'Pelanggan Tidak Ditemui',
        'catatan'         => 'Teknisi telah sampai di lokasi, tetapi pelanggan tidak dapat ditemui.',
        'user_id'         => auth()->id(),
    ]);

    return redirect()
        ->route('work-orders.show', $workOrder)
        ->with('success', 'Status diubah menjadi Pelanggan Tidak Ditemui.');
}
public function rescheduleRequest(WorkOrder $workOrder)
{
    $statusLama = $workOrder->registration->status;

    $workOrder->update([
        'status' => 'Dijadwalkan Ulang',
    ]);

    $workOrder->registration->update([
        'status' => 'Dijadwalkan Ulang',
    ]);

    \App\Models\RegistrationHistory::create([
        'registration_id' => $workOrder->registration->id,
        'status_lama'     => $statusLama,
        'status_baru'     => 'Dijadwalkan Ulang',
        'catatan'         => 'Pelanggan meminta penjadwalan ulang.',
        'user_id'         => auth()->id(),
    ]);

    return redirect()
        ->route('work-orders.show', $workOrder)
        ->with('success', 'Permintaan penjadwalan ulang berhasil dikirim.');
}
public function reschedule(WorkOrder $workOrder)
{
    abort_unless(
        auth()->user()->hasAnyRole(['Super Admin', 'Admin']),
        403
    );

    abort_unless(
        in_array($workOrder->status, [
            'Pelanggan Tidak Ditemui',
            'Dijadwalkan Ulang',
        ]),
        403
    );

    $teams = \App\Models\Team::with('technicians.user')
        ->orderBy('nama')
        ->get();

    return view('work_orders.reschedule', compact(
        'workOrder',
        'teams'
    ));
}

public function updateSchedule(
    \Illuminate\Http\Request $request,
    WorkOrder $workOrder
) {
    abort_unless(
        auth()->user()->hasAnyRole(['Super Admin', 'Admin']),
        403
    );

    $data = $request->validate([
        'tanggal'      => ['required', 'date', 'after_or_equal:today'],
        'jam'          => ['required', 'date_format:H:i'],
        'team_id'      => ['required', 'exists:teams,id'],
        'technician_id'=> ['nullable', 'exists:technicians,id'],
        'catatan'      => ['nullable', 'string', 'max:2000'],
    ]);

    $statusLama = $workOrder->registration->status;

    $workOrder->update([
        'tanggal'       => $data['tanggal'],
        'jam'           => $data['jam'],
        'team_id'       => $data['team_id'],
        'technician_id' => null,
        'catatan'       => $data['catatan'] ?? $workOrder->catatan,
        'status'        => 'Menunggu Diterima Teknisi',
        'accepted_at'   => null,
        'started_at'    => null,
        'finished_at'   => null,
    ]);

    $workOrder->registration->update([
        'status' => 'Menunggu Diterima Teknisi',
    ]);

    \App\Models\RegistrationHistory::create([
        'registration_id' => $workOrder->registration->id,
        'status_lama'     => $statusLama,
        'status_baru'     => 'Menunggu Diterima Teknisi',
        'catatan'         => 'Admin menjadwalkan ulang pekerjaan pada '
            . $data['tanggal']
            . ' pukul '
            . $data['jam']
            . '. '
            . ($data['catatan'] ?? ''),
        'user_id'         => auth()->id(),
    ]);

    return redirect()
        ->route('work-orders.show', $workOrder)
        ->with('success', 'Work Order berhasil dijadwalkan ulang.');
}
public function installation(WorkOrder $workOrder)
{
    $statusLama = $workOrder->registration->status;

    $workOrder->update([
        'status' => 'Instalasi',
    ]);

    $workOrder->registration->update([
        'status' => 'Instalasi',
    ]);

    \App\Models\RegistrationHistory::create([
        'registration_id' => $workOrder->registration->id,
        'status_lama'     => $statusLama,
        'status_baru'     => 'Instalasi',
        'catatan'         => 'Teknisi mulai melakukan instalasi.',
        'user_id'         => auth()->id(),
    ]);

    return redirect()
        ->route('work-order-installation.edit', $workOrder)
        ->with('success', 'Silakan lengkapi dokumentasi instalasi.');
}
public function waitingVerification(WorkOrder $workOrder)
{
    $statusLama = $workOrder->registration->status;

    $workOrder->update([
        'status' => 'Menunggu Verifikasi',
    ]);

    $workOrder->registration->update([
        'status' => 'Menunggu Verifikasi',
    ]);

    \App\Models\RegistrationHistory::create([
        'registration_id' => $workOrder->registration->id,
        'status_lama'     => $statusLama,
        'status_baru'     => 'Menunggu Verifikasi',
        'catatan'         => 'Instalasi selesai dan menunggu verifikasi Admin.',
        'user_id'         => auth()->id(),
    ]);

    return redirect()
        ->route('work-orders.show', $workOrder)
        ->with('success', 'Instalasi selesai. Menunggu verifikasi Admin.');
}
public function complete(WorkOrder $workOrder)
{
    $statusLama = $workOrder->registration->status;

    $workOrder->update([
        'status' => 'Selesai',
    ]);

    $workOrder->registration->update([
        'status' => 'Selesai',
    ]);

    \App\Models\RegistrationHistory::create([
        'registration_id' => $workOrder->registration->id,
        'status_lama'     => $statusLama,
        'status_baru'     => 'Selesai',
        'catatan'         => 'Work Order telah diverifikasi dan diselesaikan oleh Admin.',
        'user_id'         => auth()->id(),
    ]);

    return redirect()
        ->route('work-orders.show', $workOrder)
        ->with('success', 'Work Order berhasil diverifikasi dan diselesaikan.');
}
}