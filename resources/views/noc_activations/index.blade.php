<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="theme-color" content="#0f172a">
    <title>Dashboard Aktivasi NOC</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-100 text-slate-800">
<header class="sticky top-0 z-40 border-b border-slate-800 bg-slate-950 text-white shadow-lg">
    <div class="mx-auto flex max-w-7xl items-center gap-3 px-4 py-3 sm:px-6">
        <a href="{{ route('dashboard') }}" class="flex h-11 w-11 items-center justify-center rounded-xl bg-white/10 text-xl hover:bg-white/20">←</a>
        <div class="min-w-0 flex-1">
            <h1 class="truncate text-base font-bold sm:text-lg">NOC Activation Center</h1>
            <p class="truncate text-xs text-slate-400">Dashboard dan antrean aktivasi pelanggan</p>
        </div>
        <div class="rounded-full bg-cyan-400/15 px-3 py-1 text-xs font-semibold text-cyan-300">NOC</div>
    </div>
</header>
<main class="mx-auto max-w-7xl px-4 py-5 sm:px-6">
    @if(session('success'))
        <div class="mb-5 rounded-2xl border border-emerald-200 bg-emerald-50 p-4 text-sm text-emerald-800">✅ {{ session('success') }}</div>
    @endif

    <section class="mb-5 grid grid-cols-2 gap-3 lg:grid-cols-5">
        @php
            $cards = [
                ['label' => 'Menunggu', 'count' => $statistics['waiting'], 'status' => \App\Models\NocActivation::STATUS_WAITING, 'class' => 'border-amber-200 text-amber-700'],
                ['label' => 'Diterima', 'count' => $statistics['accepted'], 'status' => \App\Models\NocActivation::STATUS_ACCEPTED, 'class' => 'border-blue-200 text-blue-700'],
                ['label' => 'Diproses', 'count' => $statistics['processing'], 'status' => \App\Models\NocActivation::STATUS_PROCESSING, 'class' => 'border-violet-200 text-violet-700'],
                ['label' => 'Menunggu Admin', 'count' => $statistics['waiting_admin'], 'status' => \App\Models\NocActivation::STATUS_WAITING_ADMIN_VERIFICATION, 'class' => 'border-emerald-200 text-emerald-700'],
                ['label' => 'Gagal', 'count' => $statistics['failed'], 'status' => \App\Models\NocActivation::STATUS_FAILED, 'class' => 'border-red-200 text-red-700'],
            ];
        @endphp
        @foreach($cards as $card)
            <a href="{{ route('noc-activations.index', ['status' => $card['status']]) }}" class="rounded-2xl border bg-white p-4 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md {{ $card['class'] }}">
                <p class="text-xs font-semibold uppercase tracking-wide">{{ $card['label'] }}</p>
                <p class="mt-2 text-3xl font-black text-slate-900">{{ $card['count'] }}</p>
            </a>
        @endforeach
    </section>

    <section class="mb-5 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
        <form action="{{ route('noc-activations.index') }}" method="GET" class="grid gap-3 md:grid-cols-[1fr_240px_auto]">
            <input type="search" name="q" value="{{ $search }}" placeholder="Cari SN, PPPoE, nama atau nomor registrasi..." class="h-12 w-full rounded-xl border-slate-300 px-4 text-sm focus:border-cyan-500 focus:ring-cyan-500">
            <select name="status" class="h-12 w-full rounded-xl border-slate-300 px-3 text-sm focus:border-cyan-500 focus:ring-cyan-500">
                <option value="">Semua Status</option>
                @foreach($statuses as $statusOption)
                    <option value="{{ $statusOption }}" @selected($status === $statusOption)>{{ $statusOption }}</option>
                @endforeach
            </select>
            <button type="submit" class="h-12 rounded-xl bg-slate-900 px-6 text-sm font-bold text-white hover:bg-slate-800">Cari</button>
        </form>
    </section>

    <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="flex items-center justify-between border-b border-slate-200 px-4 py-4 sm:px-5">
            <div>
                <h2 class="font-bold text-slate-900">Antrean Aktivasi</h2>
                <p class="mt-1 text-xs text-slate-500">Setelah diterima, tombol Proses Aktivasi akan tersedia.</p>
            </div>
            <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600">{{ $activations->total() }} tugas</span>
        </div>

        @forelse($activations as $activation)
            @php
                $registration = $activation->workOrder?->registration;
                $installation = $activation->workOrder?->installation;
                $customerName = $registration?->nama ?? 'Nama belum tersedia';
                $customerNumber = $registration?->registration_number;
                $odpName = $registration?->odp?->nama ?? '-';
                $teamName = $activation->workOrder?->team?->nama ?? '-';
                $statusClasses = match($activation->status) {
                    \App\Models\NocActivation::STATUS_WAITING => 'bg-amber-100 text-amber-800',
                    \App\Models\NocActivation::STATUS_ACCEPTED => 'bg-blue-100 text-blue-800',
                    \App\Models\NocActivation::STATUS_PROCESSING => 'bg-violet-100 text-violet-800',
                    \App\Models\NocActivation::STATUS_WAITING_ADMIN_VERIFICATION => 'bg-emerald-100 text-emerald-800',
                    \App\Models\NocActivation::STATUS_FAILED => 'bg-red-100 text-red-800',
                    default => 'bg-slate-100 text-slate-700',
                };
            @endphp
            <article class="border-b border-slate-100 p-4 last:border-b-0 sm:p-5">
                <div class="grid gap-4 lg:grid-cols-[1fr_auto] lg:items-center">
                    <div class="min-w-0">
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="rounded-full px-2.5 py-1 text-xs font-semibold {{ $statusClasses }}">{{ $activation->status }}</span>
                            <span class="text-xs font-medium text-slate-500">WO #{{ $activation->work_order_id }}</span>
                            <span class="text-xs text-slate-400">{{ $activation->created_at?->format('d-m-Y H:i') }}</span>
                        </div>
                        <div class="mt-3 grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
                            <div><p class="text-xs uppercase tracking-wide text-slate-400">SN Modem</p><p class="mt-1 break-all font-mono text-sm font-bold text-slate-900">{{ $activation->sn_modem ?? $installation?->sn_modem ?? '-' }}</p></div>
                            <div><p class="text-xs uppercase tracking-wide text-slate-400">Pelanggan</p><p class="mt-1 text-sm font-semibold text-slate-900">{{ $customerName }}</p>@if($customerNumber)<p class="mt-0.5 text-xs text-slate-500">{{ $customerNumber }}</p>@endif</div>
                            <div><p class="text-xs uppercase tracking-wide text-slate-400">ODP</p><p class="mt-1 text-sm font-semibold text-slate-900">{{ $odpName }}</p></div>
                            <div><p class="text-xs uppercase tracking-wide text-slate-400">Tim Teknisi</p><p class="mt-1 text-sm font-semibold text-slate-900">{{ $teamName }}</p></div>
                        </div>
                        @if($activation->handler)
                            <p class="mt-3 text-xs text-slate-500">Ditangani oleh <strong class="text-slate-700">{{ $activation->handler->name }}</strong></p>
                        @endif
                    </div>

                    <div class="flex gap-2 lg:w-44 lg:flex-col">
                        @if($activation->status === \App\Models\NocActivation::STATUS_WAITING)
                            <form action="{{ route('noc-activations.accept', $activation) }}" method="POST" class="w-full" onsubmit="return confirm('Terima tugas aktivasi ini?')">
                                @csrf
                                <button type="submit" class="h-12 w-full rounded-xl bg-cyan-600 px-4 text-sm font-bold text-white hover:bg-cyan-700">Terima Tugas</button>
                            </form>
                        @elseif(in_array($activation->status, [\App\Models\NocActivation::STATUS_ACCEPTED, \App\Models\NocActivation::STATUS_PROCESSING], true))
                            @if($activation->handled_by === auth()->id() || auth()->user()->can('noc-activations.verify'))
                                <a href="{{ route('noc-activations.process', $activation) }}" class="flex h-12 w-full items-center justify-center rounded-xl bg-violet-600 px-4 text-sm font-bold text-white hover:bg-violet-700">Proses Aktivasi</a>
                            @else
                                <button type="button" disabled class="h-12 w-full cursor-not-allowed rounded-xl bg-slate-100 px-4 text-sm font-semibold text-slate-400">Ditangani NOC lain</button>
                            @endif
                        @else
                            <button type="button" disabled class="h-12 w-full cursor-not-allowed rounded-xl bg-slate-100 px-4 text-sm font-semibold text-slate-400">{{ $activation->status }}</button>
                        @endif
                    </div>
                </div>
            </article>
        @empty
            <div class="px-6 py-16 text-center">
                <div class="text-5xl">📡</div>
                <h3 class="mt-4 font-bold text-slate-900">Belum ada antrean aktivasi</h3>
                <p class="mt-2 text-sm text-slate-500">Tugas baru akan muncul setelah teknisi menyimpan SN modem.</p>
            </div>
        @endforelse

        @if($activations->hasPages())
            <div class="border-t border-slate-200 px-4 py-4">{{ $activations->links() }}</div>
        @endif
    </section>
</main>
</body>
</html>
