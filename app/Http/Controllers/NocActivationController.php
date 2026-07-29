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

        $status = trim((string) $request->query('status', ''));
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
                            ->orWhere('registration_number', 'like', "%{$search}%");
                    });
            });
        }

        $activations = $query->paginate(15)->withQueryString();

        $statistics = [
            'waiting' => NocActivation::where('status', NocActivation::STATUS_WAITING)->count(),
            'accepted' => NocActivation::where('status', NocActivation::STATUS_ACCEPTED)->count(),
            'processing' => NocActivation::where('status', NocActivation::STATUS_PROCESSING)->count(),
            'waiting_admin' => NocActivation::where('status', NocActivation::STATUS_WAITING_ADMIN_VERIFICATION)->count(),
            'failed' => NocActivation::where('status', NocActivation::STATUS_FAILED)->count(),
        ];

        $statuses = [
            NocActivation::STATUS_WAITING,
            NocActivation::STATUS_ACCEPTED,
            NocActivation::STATUS_PROCESSING,
            NocActivation::STATUS_WAITING_ADMIN_VERIFICATION,
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
            ->with('success', 'Tugas aktivasi berhasil diterima. Silakan proses aktivasi.');
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

                abort_unless(
            $nocActivation->workOrder?->account
                && filled($nocActivation->workOrder->account->username)
                && filled($nocActivation->workOrder->account->password),
            422,
            'Akun PPPoE belum diisi oleh Admin. Silakan minta Admin melengkapi username dan password pada Detail Work Order.'
        );


        $registration = $nocActivation->workOrder?->registration;
        $odp = $registration?->odp;
        $router = $this->resolveRouter($odp?->router);

        $data = [
            'nas' => $router?->nama ?? $odp?->router ?? '-',
            'customer_name' => $registration?->nama ?? '-',
            'sn' => $nocActivation->sn_modem
                ?? $nocActivation->workOrder?->installation?->sn_modem
                ?? '-',
            'odp' => $odp?->nama ?? '-',
            'username' => $nocActivation->workOrder?->account?->username ?? '-',
            'password' => $nocActivation->workOrder?->account?->password ?? '-',
            'card' => $odp?->card ?? '',
            'onu_type' => $router?->onu_type ?? 'ALL-ONT',
            'vlan' => $router?->vlan ?? '',
            'vlan_profile' => $router?->vlan_profile ?? '',
            'tcont_profile' => $router?->tcont_profile ?? '',
            'security_mgmt' => $router?->security_mgmt ?? '',
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
        ], [
            'onu_number.required' => 'Nomor ONU wajib diisi.',
            'onu_number.unique' => 'Nomor ONU tersebut sudah digunakan pada ODP yang sama.',
            'provisioning_script.required' => 'Klik Generate terlebih dahulu sebelum menyimpan.',
        ]);

        DB::transaction(function () use ($request, $nocActivation, $validated) {
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
                'Aktivasi ini tidak dapat disimpan karena statusnya sudah berubah.'
            );

            $workOrder = $activation->workOrder;
            $registration = $workOrder?->registration;
            $odp = $registration?->odp;
            $router = $this->resolveRouter($odp?->router);
            $account = $workOrder?->account;
            $installation = $workOrder?->installation;

            abort_unless($workOrder && $registration, 422, 'Data Work Order atau Registrasi tidak ditemukan.');
            abort_unless($account, 422, 'Akun PPPoE Work Order belum tersedia.');
            abort_unless($installation?->sn_modem, 422, 'SN modem belum tersedia.');

            $odpName = $odp?->nama ?? '-';
            $routerName = $router?->nama ?? $odp?->router ?? '-';
            $packageName = $registration->package?->nama ?? $registration->package?->name ?? '-';

            $activation->update([
                'status' => NocActivation::STATUS_WAITING_ADMIN_VERIFICATION,
                'sn_modem' => $installation->sn_modem,
                'router_name' => $routerName,
                'odp_name' => $odpName,
                'olt_interface' => $odp?->card,
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

            Customer::updateOrCreate(
                ['nik' => $registration->nik],
                [
                    'nama' => $registration->nama,
                    'nik' => $registration->nik,
                    'alamat' => $registration->alamat,
                    'telepon' => $registration->telepon,
                    'paket' => $packageName,
                    'nomor_pelanggan' => $registration->registration_number,
                    'odp' => $odpName,
                    'sn_modem' => $installation->sn_modem,
                    'nas' => $routerName,
                    'onu_number' => $validated['onu_number'],
                    'pppoe_username' => $account->username,
                    'pppoe_password' => $account->password,
                    'latitude' => $registration->latitude,
                    'longitude' => $registration->longitude,
                    'tanggal_registrasi' => $registration->created_at?->toDateString() ?? now()->toDateString(),
                    'status' => 'Menunggu Verifikasi Admin',
                    'foto_ktp' => $registration->foto_ktp,
                    'catatan' => 'Teknisi selesai dan NOC selesai. Menunggu verifikasi Admin.',
                ]
            );

            $workOrder->update([
                'status' => 'Menunggu Verifikasi',
            ]);

            $registration->update([
                'status' => 'Menunggu Verifikasi',
            ]);
        });

        return redirect()
            ->route('noc-activations.index')
            ->with('success', 'Aktivasi NOC selesai. Data disimpan dan sekarang menunggu verifikasi Admin.');
    }

    private function ensureHandlerMayProcess(Request $request, NocActivation $nocActivation): void
    {
        abort_unless(
            $nocActivation->handled_by === $request->user()->id
                || $request->user()->can('noc-activations.verify'),
            403,
            'Aktivasi ini sedang ditangani oleh petugas NOC lain.'
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
