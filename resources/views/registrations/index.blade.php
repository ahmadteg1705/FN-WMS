@extends('layouts.admin')

@section('page-title', 'Registrasi Pelanggan')

@section('content')

{{-- Alert Message --}}
@if(session('success'))
<div class="mb-6 flex items-center gap-3 rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-emerald-800 shadow-sm">
    <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-emerald-100 text-emerald-600">
        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
        </svg>
    </div>
    <div>
        <h4 class="font-semibold text-emerald-900">Berhasil</h4>
        <p class="text-sm text-emerald-700">{{ session('success') }}</p>
    </div>
</div>
@endif

{{-- Header Toolbar --}}
<div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
    <div>
        <h1 class="text-2xl font-bold text-slate-800">Registrasi Pelanggan</h1>
        <p class="text-sm text-slate-500">Kelola pendaftaran dan verifikasi calon pelanggan baru.</p>
    </div>

    <div class="flex items-center gap-3">
        <a href="{{ route('registrations.create') }}" class="inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500/20">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Registrasi Baru
        </a>
        <a href="{{ route('reports.registrations.excel', request()->query()) }}"
   class="inline-flex items-center rounded-xl bg-emerald-600 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-700">

    Export Excel

</a>
    </div>
</div>
{{-- Filter & Search Section --}}
<div class="mb-6 rounded-2xl border border-slate-200/80 bg-white p-5 shadow-sm">
    <form method="GET">
        <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-8 items-end">
            
            {{-- Input Search (2 kolom) --}}
            <div class="lg:col-span-2">
                <label class="mb-1 block text-xs font-semibold uppercase tracking-wider text-slate-500">Cari Pelanggan</label>
                <div class="relative">
                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                        <svg class="h-4 w-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                    <input
                        type="text"
                        name="search"
                        value="{{ request('search') }}"
                        placeholder="Nama / No Reg / Telepon..."
                        class="w-full rounded-xl border border-slate-200 pl-9 pr-3 py-2 text-sm text-slate-800 placeholder-slate-400 transition focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20"
                    >
                </div>
            </div>

            {{-- Filter Status --}}
            <div>
                <label class="mb-1 block text-xs font-semibold uppercase tracking-wider text-slate-500">Status</label>
                <select name="status" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm text-slate-800 transition focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20">
                    <option value="">Semua Status</option>
                    <option value="Registrasi Baru" {{ request('status')=='Registrasi Baru'?'selected':'' }}>Registrasi Baru</option>
                    <option value="Survey" {{ request('status')=='Survey'?'selected':'' }}>Survey</option>
                    <option value="Instalasi" {{ request('status')=='Instalasi'?'selected':'' }}>Instalasi</option>
                    <option value="Pelanggan Aktif" {{ request('status')=='Pelanggan Aktif'?'selected':'' }}>Pelanggan Aktif</option>
                </select>
            </div>

            {{-- Filter Marketing --}}
            <div>
                <label class="mb-1 block text-xs font-semibold uppercase tracking-wider text-slate-500">Marketing</label>
                <select name="marketing_id" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm text-slate-800 transition focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20">
                    <option value="">Semua Marketing</option>
                    @foreach($marketings as $marketing)
                        <option value="{{ $marketing->id }}" {{ request('marketing_id')==$marketing->id?'selected':'' }}>
                            {{ optional($marketing->user)->name ?? '-' }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Filter Paket --}}
            <div>
                <label class="mb-1 block text-xs font-semibold uppercase tracking-wider text-slate-500">Paket</label>
                <select name="package_id" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm text-slate-800 transition focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20">
                    <option value="">Semua Paket</option>
                    @foreach($packages as $package)
                        <option value="{{ $package->id }}" {{ request('package_id')==$package->id?'selected':'' }}>
                            {{ $package->nama }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Filter Tanggal Dari --}}
            <div>
                <label class="mb-1 block text-xs font-semibold uppercase tracking-wider text-slate-500">Dari Tanggal</label>
                <input
                    type="date"
                    name="tanggal_dari"
                    value="{{ request('tanggal_dari') }}"
                    class="w-full rounded-xl border border-slate-200 px-2.5 py-2 text-sm text-slate-800 transition focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20"
                >
            </div>

            {{-- Filter Tanggal Sampai (Sudah Masuk Baris Utama) --}}
            <div>
                <label class="mb-1 block text-xs font-semibold uppercase tracking-wider text-slate-500">Sampai Tanggal</label>
                <input
                    type="date"
                    name="tanggal_sampai"
                    value="{{ request('tanggal_sampai') }}"
                    class="w-full rounded-xl border border-slate-200 px-2.5 py-2 text-sm text-slate-800 transition focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20"
                >
            </div>

            {{-- Tombol Aksi (Cari & Reset Sejajar di Baris Utama) --}}
            <div class="flex items-center gap-1.5">
                <button
                    type="submit"
                    class="w-full inline-flex items-center justify-center gap-1.5 rounded-xl bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-700 active:scale-95">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    Cari
                </button>

                <a
                    href="{{ route('registrations.index') }}"
                    title="Reset Filter"
                    class="inline-flex items-center justify-center rounded-xl border border-slate-200 bg-white p-2 text-sm font-semibold text-slate-600 shadow-sm transition hover:bg-slate-50">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                </a>
            </div>

        </div>
    </form>
</div>
{{-- Data Table --}}
<div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm text-slate-600">
            <thead class="border-b border-slate-200 bg-slate-50/70 text-xs font-semibold uppercase tracking-wider text-slate-500">
                <tr>
                    <th scope="col" class="px-6 py-4">No Registrasi</th>
                    <th scope="col" class="px-6 py-4">Nama Pelanggan</th>
                    <th scope="col" class="px-6 py-4">Paket</th>
                    <th scope="col" class="px-6 py-4">telepon</th>
                    <th scope="col" class="px-6 py-4">ODP</th>
                    <th scope="col" class="px-6 py-4">Marketing</th>
                    <th scope="col" class="px-6 py-4">Tanggal Reg</th>
                    <th scope="col" class="px-6 py-4 text-center">Status</th>
                    <th scope="col" class="w-36 px-6 py-4 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200">
                @forelse($registrations as $registration)
                <tr class="transition hover:bg-slate-50/50">
                    <td class="px-6 py-4 font-mono text-xs font-semibold text-indigo-600">
                        {{ $registration->registration_number }}
                    </td>
                    <td class="px-6 py-4 font-semibold text-slate-800">
                        {{ $registration->nama }}
                    </td>
                    <td class="px-6 py-4 text-slate-600">
                        {{ optional($registration->package)->nama ?? '-' }}
                    </td>
                    <td class="px-6 py-4">
                        {{ $registration->telepon }}
                    </td>
                    <td class="px-6 py-4 text-slate-600">
                        {{ optional($registration->odp)->nama ?? '-' }}
                    </td>
                    <td class="px-6 py-4 text-slate-600 font-medium">
                        {{ optional(optional($registration->marketing)->user)->name ?? '-' }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        {{ $registration->created_at->format('d-m-Y') }}
                    </td>
                    <td class="px-6 py-4 text-center">
                        @if($registration->status == "Registrasi Baru")
                            <span class="inline-flex items-center gap-1.5 rounded-full bg-amber-50 px-3 py-1 text-xs font-semibold text-amber-700 border border-amber-200">
                                <span class="h-1.5 w-1.5 rounded-full bg-amber-500"></span>
                                {{ $registration->status }}
                            </span>
                        @elseif($registration->status == "Diverifikasi")
                            <span class="inline-flex items-center gap-1.5 rounded-full bg-blue-50 px-3 py-1 text-xs font-semibold text-blue-700 border border-blue-200">
                                <span class="h-1.5 w-1.5 rounded-full bg-blue-500"></span>
                                {{ $registration->status }}
                            </span>
                        @elseif($registration->status == "Pelanggan Aktif")
                            <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-700 border border-emerald-200">
                                <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                                {{ $registration->status }}
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1.5 rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-700 border border-slate-200">
                                <span class="h-1.5 w-1.5 rounded-full bg-slate-400"></span>
                                {{ $registration->status }}
                            </span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-center whitespace-nowrap">
                        <div class="flex items-center justify-center gap-2">
                            <a href="{{ route('registrations.show', $registration) }}"
                            class="inline-flex items-center gap-1.5 rounded-xl border border-emerald-200 bg-emerald-50 px-3 py-1.5 text-xs font-semibold text-emerald-600 transition hover:bg-emerald-100 hover:text-emerald-700">

                                <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M2.458 12C3.732 7.943 7.523 5 12 5s8.268 2.943 9.542 7c-1.274 4.057-5.065 7-9.542 7S3.732 16.057 2.458 12z"/>
                                </svg>
                                Lihat
                            </a>
                            <a href="{{ route('registrations.edit', $registration) }}" 
                               class="inline-flex items-center gap-1.5 rounded-xl border border-blue-200 bg-blue-50 px-3 py-1.5 text-xs font-semibold text-blue-600 transition hover:bg-blue-100 hover:text-blue-700">
                                <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                                Edit
                            </a>

                            <form action="{{ route('registrations.destroy', $registration) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" 
                                        onclick="return confirm('Hapus registrasi ini?')" 
                                        class="inline-flex items-center gap-1.5 rounded-xl border border-rose-200 bg-rose-50 px-3 py-1.5 text-xs font-semibold text-rose-600 transition hover:bg-rose-100 hover:text-rose-700">
                                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                    Hapus
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-12 text-center text-slate-400">
                        <svg class="mx-auto h-12 w-12 text-slate-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                        <p class="font-medium text-slate-600">Belum ada data registrasi</p>
                        <p class="text-sm text-slate-400 mt-1">Silakan tambahkan data pendaftaran pelanggan baru.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
<div class="border-t bg-white px-6 py-4">
    {{ $registrations->links() }}
</div>
    </div>
</div>

@endsection