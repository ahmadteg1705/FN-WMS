<?php

namespace App\Http\Controllers;

use App\Models\NocActivation;
use App\Models\WorkOrder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class NocActivationController extends Controller
{
    public function index(Request $request): View
    {
        $this->createPendingActivations();

        $status = trim((string) $request->query('status', ''));
        $search = trim((string) $request->query('q', ''));

        $query = NocActivation::query()
            ->with([
                'handler:id,name',
                'workOrder.registration',
                'workOrder.team',
                'workOrder.installation',
            ])
            ->latest('created_at');

        if ($status !== '') {
            $query->where('status', $status);
        }

        if ($search !== '') {
            $query->where(function ($activationQuery) use ($search) {
                $activationQuery
                    ->where('sn_modem', 'like', "%{$search}%")
                    ->orWhere('pppoe_username', 'like', "%{$search}%")
                    ->orWhereHas('workOrder.registration', function ($registrationQuery) use ($search) {
                        $registrationQuery
                            ->where('nama', 'like', "%{$search}%")
                            ->orWhere('nama_pelanggan', 'like', "%{$search}%")
                            ->orWhere('no_pelanggan', 'like', "%{$search}%");
                    });
            });
        }

        $activations = $query->paginate(15)->withQueryString();

        $statistics = [
            'waiting' => NocActivation::where('status', NocActivation::STATUS_WAITING)->count(),
            'accepted' => NocActivation::where('status', NocActivation::STATUS_ACCEPTED)->count(),
            'processing' => NocActivation::where('status', NocActivation::STATUS_PROCESSING)->count(),
            'success_today' => NocActivation::where('status', NocActivation::STATUS_SUCCESS)
                ->whereDate('activated_at', today())
                ->count(),
            'failed' => NocActivation::where('status', NocActivation::STATUS_FAILED)->count(),
        ];

        $statuses = [
            NocActivation::STATUS_WAITING,
            NocActivation::STATUS_ACCEPTED,
            NocActivation::STATUS_PROCESSING,
            NocActivation::STATUS_SUCCESS,
            NocActivation::STATUS_FAILED,
        ];

        return view('noc_activations.index', compact(
            'activations',
            'statistics',
            'statuses',
            'status',
            'search'
        ));
    }

    public function accept(
        Request $request,
        NocActivation $nocActivation
    ): RedirectResponse {
        DB::transaction(function () use ($request, $nocActivation) {
            $activation = NocActivation::query()
                ->lockForUpdate()
                ->findOrFail($nocActivation->id);

            abort_unless(
                $activation->status === NocActivation::STATUS_WAITING,
                422,
                'Tugas ini sudah diambil atau tidak lagi berada dalam antrean.'
            );

            $activation->update([
                'handled_by' => $request->user()->id,
                'status' => NocActivation::STATUS_ACCEPTED,
                'accepted_at' => now(),
            ]);
        });

        return redirect()
            ->route('noc-activations.index')
            ->with('success', 'Tugas aktivasi berhasil diterima.');
    }

    private function createPendingActivations(): void
    {
        WorkOrder::query()
            ->whereHas('installation', function ($query) {
                $query
                    ->whereNotNull('sn_modem')
                    ->whereNotNull('sn_disimpan_at');
            })
            ->whereDoesntHave('nocActivation')
            ->with('installation')
            ->chunkById(100, function ($workOrders) {
                foreach ($workOrders as $workOrder) {
                    NocActivation::firstOrCreate(
                        ['work_order_id' => $workOrder->id],
                        [
                            'status' => NocActivation::STATUS_WAITING,
                            'sn_modem' => $workOrder->installation?->sn_modem,
                        ]
                    );
                }
            });
    }
}
