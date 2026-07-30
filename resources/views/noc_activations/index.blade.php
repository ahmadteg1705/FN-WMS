<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $mode === 'queue' ? 'Aktivasi NOC' : 'Proses Aktivasi' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-100 text-slate-800">
<header class="border-b border-slate-800 bg-slate-950 text-white">
    <div class="mx-auto flex max-w-7xl items-center gap-3 px-4 py-4 sm:px-6">
        <a href="{{ route('dashboard') }}" class="rounded-xl bg-white/10 px-4 py-3">←</a>
        <div class="flex-1">
            <h1 class="font-bold">{{ $mode === 'queue' ? 'Aktivasi NOC' : 'Proses Aktivasi' }}</h1>
            <p class="text-xs text-slate-400">
                {{ $mode === 'queue' ? 'Antrean baru yang belum diterima NOC' : 'Tugas diterima, sedang diproses, dan menunggu verifikasi Admin' }}
            </p>
        </div>
    </div>
</header>

<main class="mx-auto max-w-7xl px-4 py-5 sm:px-6">
    @if(session('success'))
        <div class="mb-4 rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-emerald-800">
            {{ session('success') }}
        </div>
    @endif

    <section class="mb-5 grid grid-cols-2 gap-3 lg:grid-cols-4">
        <a href="{{ route('noc-activations.index') }}" class="rounded-xl bg-white p-4 shadow">
            <p class="text-xs text-slate-500">Antrean NOC</p>
            <p class="text-3xl font-black">{{ $statistics['waiting'] }}</p>
        </a>
        <a href="{{ route('noc-activations.processing') }}" class="rounded-xl bg-white p-4 shadow">
            <p class="text-xs text-slate-500">Sedang Diproses</p>
            <p class="text-3xl font-black">{{ $statistics['processing'] }}</p>
        </a>
        <a href="{{ route('noc-activations.processing') }}" class="rounded-xl bg-white p-4 shadow">
            <p class="text-xs text-slate-500">Verifikasi Admin</p>
            <p class="text-3xl font-black">{{ $statistics['waiting_admin'] }}</p>
        </a>
        <div class="rounded-xl bg-white p-4 shadow">
            <p class="text-xs text-slate-500">Aktif</p>
            <p class="text-3xl font-black">{{ $statistics['success'] }}</p>
        </div>
    </section>

    <form method="GET" class="mb-5 flex gap-3 rounded-xl bg-white p-4 shadow">
        <input name="q" value="{{ $search }}" class="h-11 flex-1 rounded-lg border-slate-300"
               placeholder="Cari pelanggan, SN, atau PPPoE">
        <button class="rounded-lg bg-slate-900 px-5 font-bold text-white">Cari</button>
    </form>

    <section class="overflow-hidden rounded-xl bg-white shadow">
        @forelse($activations as $activation)
            @php
                $registration = $activation->workOrder?->registration;
                $installation = $activation->workOrder?->installation;
            @endphp
            <article class="border-b border-slate-100 p-5 last:border-0">
                <div class="grid gap-4 lg:grid-cols-[1fr_190px] lg:items-center">
                    <div>
                        <div class="flex flex-wrap gap-2">
                            <span class="rounded-full bg-blue-100 px-3 py-1 text-xs font-bold text-blue-800">
                                {{ $activation->status }}
                            </span>
                            <span class="text-xs text-slate-500">WO #{{ $activation->work_order_id }}</span>
                        </div>
                        <div class="mt-4 grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
                            <div><p class="text-xs text-slate-400">PELANGGAN</p><p class="font-bold">{{ $registration?->nama ?? '-' }}</p></div>
                            <div><p class="text-xs text-slate-400">SN MODEM</p><p class="font-mono font-bold">{{ $activation->sn_modem ?? $installation?->sn_modem ?? '-' }}</p></div>
                            <div><p class="text-xs text-slate-400">ODP</p><p class="font-bold">{{ $registration?->odp?->nama ?? '-' }}</p></div>
                            <div><p class="text-xs text-slate-400">PETUGAS NOC</p><p class="font-bold">{{ $activation->handler?->name ?? '-' }}</p></div>
                        </div>
                    </div>

                    <div class="space-y-2">
                        @if($mode === 'queue')
                            <form method="POST" action="{{ route('noc-activations.accept', $activation) }}">
                                @csrf
                                <button class="h-11 w-full rounded-lg bg-cyan-600 font-bold text-white">
                                    Terima Tugas
                                </button>
                            </form>
                        @elseif(in_array($activation->status, [
                            \App\Models\NocActivation::STATUS_ACCEPTED,
                            \App\Models\NocActivation::STATUS_PROCESSING
                        ], true))
                            <a href="{{ route('noc-activations.process', $activation) }}"
                               class="flex h-11 items-center justify-center rounded-lg bg-violet-600 font-bold text-white">
                                Buka Proses
                            </a>
                        @elseif($activation->status === \App\Models\NocActivation::STATUS_WAITING_ADMIN_VERIFICATION)
                            @can('noc-activations.verify')
                                <form method="POST"
                                      action="{{ route('noc-activations.verify-admin', $activation) }}"
                                      onsubmit="return confirm('Verifikasi dan masukkan data ke database pelanggan?')">
                                    @csrf
                                    <button class="h-11 w-full rounded-lg bg-emerald-600 px-3 font-bold text-white">
                                        Verifikasi Admin
                                    </button>
                                </form>
                            @else
                                <div class="rounded-lg bg-amber-50 p-3 text-center text-xs font-bold text-amber-800">
                                    Menunggu Admin
                                </div>
                            @endcan
                        @endif
                    </div>
                </div>
            </article>
        @empty
            <div class="p-14 text-center text-slate-500">
                Tidak ada data pada halaman ini.
            </div>
        @endforelse

        @if($activations->hasPages())
            <div class="border-t p-4">{{ $activations->links() }}</div>
        @endif
    </section>
</main>
</body>
</html>
