@extends('layouts.admin')
@section('page-title','Dashboard')
@section('content')
<div class="space-y-6">
    <div class="flex flex-col gap-4 xl:flex-row xl:items-end xl:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">
                {{ $mode === 'administrator' ? 'Dashboard Operasional' : ($mode === 'technician' ? 'Pekerjaan Teknisi' : ($mode === 'marketing' ? 'Registrasi Marketing' : ($mode === 'noc' ? 'Pekerjaan Aktivasi NOC' : 'Dashboard'))) }}
            </h1>
            <p class="mt-1 text-sm text-slate-500">Filter awal otomatis menampilkan pekerjaan hari ini.</p>
        </div>
        <form method="GET" class="grid gap-3 rounded-xl border bg-white p-4 shadow-sm sm:grid-cols-[180px_220px_auto]">
            <div><label class="mb-1 block text-xs font-bold text-slate-500">TANGGAL</label><input type="date" name="date" value="{{ $selectedDate }}" class="h-11 w-full rounded-lg border-slate-300"></div>
            <div><label class="mb-1 block text-xs font-bold text-slate-500">STATUS</label><select name="status" class="h-11 w-full rounded-lg border-slate-300"><option value="">Semua Status</option>@foreach($statuses as $s)<option value="{{ $s }}" @selected($status===$s)>{{ $s }}</option>@endforeach</select></div>
            <button class="h-11 self-end rounded-lg bg-slate-900 px-5 font-bold text-white">Terapkan</button>
        </form>
    </div>

    <section class="grid grid-cols-1 gap-4 sm:grid-cols-3 {{ $mode==='administrator' ? 'xl:grid-cols-5' : '' }}">
        @if($mode==='administrator')
            <div class="rounded-xl border bg-white p-5 shadow-sm"><p class="text-xs font-bold text-slate-400">PELANGGAN</p><p class="mt-2 text-3xl font-black text-blue-600">{{ $summary['customers'] }}</p></div>
            <div class="rounded-xl border bg-white p-5 shadow-sm"><p class="text-xs font-bold text-slate-400">PELANGGAN AKTIF</p><p class="mt-2 text-3xl font-black text-emerald-600">{{ $summary['active_customers'] }}</p></div>
        @endif
        <div class="rounded-xl border bg-white p-5 shadow-sm"><p class="text-xs font-bold text-slate-400">PEKERJAAN TANGGAL INI</p><p class="mt-2 text-3xl font-black">{{ $summary['today_total'] }}</p></div>
        <div class="rounded-xl border bg-white p-5 shadow-sm"><p class="text-xs font-bold text-slate-400">SELESAI</p><p class="mt-2 text-3xl font-black text-emerald-600">{{ $summary['today_completed'] }}</p></div>
        <div class="rounded-xl border bg-white p-5 shadow-sm"><p class="text-xs font-bold text-slate-400">BELUM SELESAI</p><p class="mt-2 text-3xl font-black text-amber-600">{{ $summary['today_pending'] }}</p></div>
    </section>

    <section class="overflow-hidden rounded-xl border bg-white shadow-sm">
        <div class="flex items-center justify-between border-b px-5 py-4"><div><h2 class="font-bold">{{ $selectedDate===now()->toDateString() ? 'Pekerjaan Hari Ini' : 'Pekerjaan '.\Carbon\Carbon::parse($selectedDate)->format('d M Y') }}</h2><p class="text-xs text-slate-500">{{ $items->count() }} data ditemukan.</p></div>@if($selectedDate!==now()->toDateString() || $status!=='')<a href="{{ route('dashboard') }}" class="rounded-lg border px-3 py-2 text-xs font-semibold">Reset Hari Ini</a>@endif</div>
        @forelse($items as $item)
            <article class="border-b p-5 last:border-0">
                @if(in_array($mode,['administrator','technician']))
                    <div class="grid gap-4 lg:grid-cols-[1fr_auto] lg:items-center"><div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-5"><div><p class="text-xs text-slate-400">WORK ORDER</p><p class="font-bold">{{ $item->work_order_no ?? '#'.$item->id }}</p></div><div><p class="text-xs text-slate-400">PELANGGAN</p><p class="font-semibold">{{ $item->registration?->nama ?? '-' }}</p></div><div><p class="text-xs text-slate-400">JADWAL</p><p class="font-semibold">{{ optional($item->tanggal)->format('d-m-Y') }} {{ $item->jam ? substr((string)$item->jam,0,5) : '' }}</p></div><div><p class="text-xs text-slate-400">TIM</p><p class="font-semibold">{{ $item->team?->nama ?? $item->technician?->user?->name ?? '-' }}</p></div><div><p class="text-xs text-slate-400">STATUS</p><span class="rounded-full bg-blue-50 px-3 py-1 text-xs font-bold text-blue-700">{{ $item->status }}</span></div></div>@can('work-orders.view')<a href="{{ route('work-orders.show',$item) }}" class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-bold text-white">Detail</a>@endcan</div>
                @elseif($mode==='marketing')
                    <div class="grid gap-4 lg:grid-cols-[1fr_auto] lg:items-center"><div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-5"><div><p class="text-xs text-slate-400">NO REGISTRASI</p><p class="font-bold">{{ $item->registration_number }}</p></div><div><p class="text-xs text-slate-400">PELANGGAN</p><p class="font-semibold">{{ $item->nama }}</p></div><div><p class="text-xs text-slate-400">PAKET</p><p class="font-semibold">{{ $item->package?->nama ?? '-' }}</p></div><div><p class="text-xs text-slate-400">ODP</p><p class="font-semibold">{{ $item->odp?->nama ?? '-' }}</p></div><div><p class="text-xs text-slate-400">STATUS</p><span class="rounded-full bg-indigo-50 px-3 py-1 text-xs font-bold text-indigo-700">{{ $item->status }}</span></div></div>@can('registrations.view')<a href="{{ route('registrations.show',$item) }}" class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-bold text-white">Detail</a>@endcan</div>
                @elseif($mode==='noc')
                    <div class="grid gap-4 lg:grid-cols-[1fr_auto] lg:items-center"><div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4"><div><p class="text-xs text-slate-400">WORK ORDER</p><p class="font-bold">{{ $item->workOrder?->work_order_no ?? '#'.$item->work_order_id }}</p></div><div><p class="text-xs text-slate-400">PELANGGAN</p><p class="font-semibold">{{ $item->workOrder?->registration?->nama ?? '-' }}</p></div><div><p class="text-xs text-slate-400">SN MODEM</p><p class="font-mono font-bold">{{ $item->sn_modem ?? '-' }}</p></div><div><p class="text-xs text-slate-400">STATUS</p><span class="rounded-full bg-violet-50 px-3 py-1 text-xs font-bold text-violet-700">{{ $item->status }}</span></div></div><a href="{{ $item->status===\App\Models\NocActivation::STATUS_WAITING ? route('noc-activations.index') : route('noc-activations.processing') }}" class="rounded-lg bg-violet-600 px-4 py-2 text-sm font-bold text-white">Buka</a></div>
                @endif
            </article>
        @empty
            <div class="px-6 py-16 text-center text-slate-500">Tidak ada pekerjaan untuk tanggal dan status yang dipilih.</div>
        @endforelse
    </section>
</div>
@endsection
