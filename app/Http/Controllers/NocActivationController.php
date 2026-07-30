<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\NocActivation;
use App\Models\Router;
use App\Models\WorkOrder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class NocActivationController extends Controller
{
    public function index(Request $request): View
    {
        $this->createPendingActivations();

        return $this->renderList($request, 'queue');
    }

    public function processingIndex(Request $request): View
    {
        $this->createPendingActivations();

        return $this->renderList($request, 'processing');
    }

    private function renderList(Request $request, string $mode): View
    {
        $search = trim((string) $request->query('q', ''));

        $query = NocActivation::query()
            ->with([
                'handler:id,name',
                'workOrder.registration.odp',
                'workOrder.registration.package',
                'workOrder.team',
                'workOrder.installation',
                'workOrder.account',
            ])
            ->latest('created_at');

        if ($mode === 'queue') {
            $query->where('status', NocActivation::STATUS_WAITING);
        } else {
            $query->whereIn('status', [
                NocActivation::STATUS_ACCEPTED,
                NocActivation::STATUS_PROCESSING,
                NocActivation::STATUS_WAITING_ADMIN_VERIFICATION,
            ]);

            if (!$request->user()->can('noc-activations.verify')) {
                $query->where('handled_by', $request->user()->id);
            }
        }

        if ($search !== '') {
            $query->where(function ($activationQuery) use ($search) {
                $activationQuery
                    ->where('sn_modem', 'like', "%{$search}%")
                    ->orWhere('pppoe_username', 'like', "%{$search}%")
                    ->orWhereHas('workOrder.registration', function ($registrationQuery) use ($search) {
                        $registrationQuery
                            ->where('nama', 'like', "%{$search}%")
                            ->orWhere('registration_number', 'like', "%{$search}%");
                    });
            });
        }

        $activations = $query->paginate(15)->withQueryString();

        $statistics = [
            'waiting' => NocActivation::where('status', NocActivation::STATUS_WAITING)->count(),
            'processing' => NocActivation::whereIn('status', [
                NocActivation::STATUS_ACCEPTED,
                NocActivation::STATUS_PROCESSING,
            ])->count(),
            'waiting_admin' => NocActivation::where(
                'status',
                NocActivation::STATUS_WAITING_ADMIN_VERIFICATION
            )->count(),
            'success' => NocActivation::where('status', NocActivation::STATUS_SUCCESS)->count(),
        ];

        return view('noc_activations.index', compact(
            'activations',
            'statistics',
            'search',
            'mode'
        ));
    }

    public function accept(Request $request, NocActivation $nocActivation): RedirectResponse
    {
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
            ->route('noc-activations.process', $nocActivation)
            ->with('success', 'Tugas aktivasi berhasil diterima.');
    }

    public function process(Request $request, NocActivation $nocActivation): View
    {
        $this->ensureHandlerMayProcess($request, $nocActivation);

        $nocActivation->load([
            'handler:id,name',
            'workOrder.registration.odp',
            'workOrder.registration.package',
            'workOrder.installation',
            'workOrder.account',
        ]);

        abort_if(
            $nocActivation->status === NocActivation::STATUS_WAITING,
            422,
            'Tugas harus diterima terlebih dahulu.'
        );

        abort_if(
            $nocActivation->isCompleted(),
            422,
            'Proses aktivasi ini sudah selesai.'
        );

        if ($nocActivation->status === NocActivation::STATUS_ACCEPTED) {
            $nocActivation->update([
                'status' => NocActivation::STATUS_PROCESSING,
                'started_at' => $nocActivation->started_at ?? now(),
            ]);
            $nocActivation->refresh();
        }

        $registration = $nocActivation->workOrder?->registration;
        $odp = $registration?->odp;
        $router = $this->resolveRouter($odp?->router);
        $account = $nocActivation->workOrder?->account;

        abort_unless($odp, 422, 'Master ODP belum tersedia pada Registrasi.');
        abort_unless($router, 422, 'Router NAS pada Master ODP tidak ditemukan.');
        abort_unless($account && filled($account->username) && filled($account->password), 422,
            'Akun PPPoE belum diisi oleh Admin.');

        foreach ([
            'card' => $odp->card,
            'onu_type' => $router->onu_type,
            'vlan' => $router->vlan,
            'vlan_profile' => $router->vlan_profile,
            'tcont_profile' => $router->tcont_profile,
            'security_mgmt' => $router->security_mgmt,
            'service_command' => $router->service_command,
            'wan_ethuni' => $router->wan_ethuni,
            'wan_ssid' => $router->wan_ssid,
            'wan_service' => $router->wan_service,
        ] as $field => $value) {
            abort_unless(filled($value), 422,
                "Data {$field} belum lengkap pada Router NAS/Master ODP.");
        }

        $data = [
            'nas' => $router->nama,
            'customer_name' => $registration?->nama ?? '-',
            'sn' => $nocActivation->sn_modem
                ?? $nocActivation->workOrder?->installation?->sn_modem
                ?? '-',
            'odp' => $odp->nama,
            'username' => $account->username,
            'password' => $account->password,

            // Seluruh parameter generate berasal dari Router NAS dan Master ODP.
            'card' => $odp->card,
            'onu_start' => $odp->onu_awal,
            'onu_end' => $odp->onu_akhir,
            'onu_type' => $router->onu_type,
            'vlan' => $router->vlan,
            'vlan_profile' => $router->vlan_profile,
            'tcont_profile' => $router->tcont_profile,
            'security_mgmt' => $router->security_mgmt,
            'service_command' => $router->service_command,
            'wan_ethuni' => $router->wan_ethuni,
            'wan_ssid' => $router->wan_ssid,
            'wan_service' => $router->wan_service,
        ];

        return view('noc_activations.process', compact('nocActivation', 'data'));
    }

    public function complete(Request $request, NocActivation $nocActivation): RedirectResponse
    {
        $this->ensureHandlerMayProcess($request, $nocActivation);

        $validated = $request->validate([
            'onu_number' => [
                'required',
                'integer',
                'min:1',
                'max:128',
                Rule::unique('noc_activations', 'onu_number')
                    ->where(fn ($query) => $query
                        ->where('odp_name', (string) $request->input('odp_name'))
                        ->whereNotNull('odp_name'))
                    ->ignore($nocActivation->id),
            ],
            'provisioning_script' => ['required', 'string'],
            'noc_notes' => ['nullable', 'string', 'max:2000'],
        ]);

        DB::transaction(function () use ($nocActivation, $validated) {
            $activation = NocActivation::query()
                ->lockForUpdate()
                ->with([
                    'workOrder.registration.odp',
                    'workOrder.registration.package',
                    'workOrder.installation',
                    'workOrder.account',
                ])
                ->findOrFail($nocActivation->id);

            abort_unless(
                in_array($activation->status, [
                    NocActivation::STATUS_ACCEPTED,
                    NocActivation::STATUS_PROCESSING,
                ], true),
                422,
                'Status aktivasi sudah berubah.'
            );

            $workOrder = $activation->workOrder;
            $registration = $workOrder?->registration;
            $odp = $registration?->odp;
            $router = $this->resolveRouter($odp?->router);
            $account = $workOrder?->account;
            $installation = $workOrder?->installation;

            abort_unless($workOrder && $registration && $odp && $router && $account, 422,
                'Data Work Order, Registrasi, ODP, Router NAS, atau PPPoE belum lengkap.');
            abort_unless($installation?->sn_modem, 422, 'SN modem belum tersedia.');

            $packageName = $registration->package?->nama
                ?? $registration->package?->name
                ?? '-';

            $activation->update([
                'status' => NocActivation::STATUS_WAITING_ADMIN_VERIFICATION,
                'sn_modem' => $installation->sn_modem,
                'router_name' => $router->nama,
                'odp_name' => $odp->nama,
                'olt_interface' => $odp->card,
                'onu_number' => $validated['onu_number'],
                'pppoe_username' => $account->username,
                'pppoe_password' => $account->password,
                'package_name' => $packageName,
                'provisioning_script' => $validated['provisioning_script'],
                'activation_result' => 'Aktivasi NOC selesai. Menunggu verifikasi Admin.',
                'noc_notes' => $validated['noc_notes'] ?? null,
                'activated_at' => now(),
                'failed_at' => null,
            ]);

            $workOrder->update(['status' => 'Menunggu Verifikasi']);
            $registration->update(['status' => 'Menunggu Verifikasi']);
        });

        return redirect()
            ->route('noc-activations.processing')
            ->with('success', 'Aktivasi selesai dan dikirim ke Verifikasi Admin.');
    }

    public function verifyAdmin(Request $request, NocActivation $nocActivation): RedirectResponse
    {
        abort_unless(
            $request->user()->can('noc-activations.verify'),
            403,
            'Anda tidak memiliki izin Verifikasi Admin.'
        );

        DB::transaction(function () use ($nocActivation) {
            $activation = NocActivation::query()
                ->lockForUpdate()
                ->with([
                    'workOrder.registration.odp',
                    'workOrder.registration.package',
                    'workOrder.installation',
                    'workOrder.account',
                ])
                ->findOrFail($nocActivation->id);

            abort_unless(
                $activation->status === NocActivation::STATUS_WAITING_ADMIN_VERIFICATION,
                422,
                'Aktivasi belum siap diverifikasi Admin.'
            );

            $workOrder = $activation->workOrder;
            $registration = $workOrder?->registration;
            $installation = $workOrder?->installation;
            $account = $workOrder?->account;

            abort_unless($workOrder && $registration && $installation && $account, 422,
                'Data pelanggan belum lengkap.');

            Customer::updateOrCreate(
                ['nik' => $registration->nik],
                [
                    'nama' => $registration->nama,
                    'nik' => $registration->nik,
                    'alamat' => $registration->alamat,
                    'telepon' => $registration->telepon,
                    'paket' => $activation->package_name,
                    'nomor_pelanggan' => $registration->registration_number,
                    'odp' => $activation->odp_name,
                    'sn_modem' => $activation->sn_modem,
                    'nas' => $activation->router_name,
                    'onu_number' => $activation->onu_number,
                    'pppoe_username' => $activation->pppoe_username,
                    'pppoe_password' => $activation->pppoe_password,
                    'latitude' => $registration->latitude,
                    'longitude' => $registration->longitude,
                    'tanggal_registrasi' => $registration->created_at?->toDateString()
                        ?? now()->toDateString(),
                    'status' => 'Aktif',
                    'foto_ktp' => $registration->foto_ktp,
                    'catatan' => 'Diverifikasi Admin dari Aktivasi NOC.',
                ]
            );

            $activation->update([
                'status' => NocActivation::STATUS_SUCCESS,
                'activation_result' => 'Aktivasi berhasil dan telah diverifikasi Admin.',
            ]);

            $workOrder->update(['status' => 'Selesai']);
            $registration->update(['status' => 'Selesai']);
        });

        return redirect()
            ->route('noc-activations.processing')
            ->with('success', 'Verifikasi berhasil. Data telah masuk ke database pelanggan.');
    }

    private function ensureHandlerMayProcess(Request $request, NocActivation $nocActivation): void
    {
        abort_unless(
            $nocActivation->handled_by === $request->user()->id
                || $request->user()->can('noc-activations.verify'),
            403,
            'Aktivasi ini sedang ditangani petugas NOC lain.'
        );
    }

    private function resolveRouter(?string $routerName): ?Router
    {
        if (!$routerName) {
            return null;
        }

        return Router::query()
            ->where('nama', $routerName)
            ->orWhere('hostname', $routerName)
            ->first();
    }

    private function createPendingActivations(): void
    {
        WorkOrder::query()
            ->whereHas('installation', fn ($query) => $query
                ->whereNotNull('sn_modem')
                ->whereNotNull('sn_disimpan_at'))
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
