@extends('layouts.admin')

@section('page-title', 'Detail Registrasi')

@section('content')
<div class="mx-auto max-w-7xl space-y-6">

    {{-- Header --}}
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-xl font-bold text-slate-800">Detail Registrasi</h1>
            <p class="text-xs text-slate-500">Informasi lengkap calon pelanggan #{{ $registration->registration_number }}</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('registrations.index') }}" class="rounded-lg border border-slate-200 bg-white px-4 py-2 text-xs font-semibold text-slate-600 transition hover:bg-slate-50">
                ← Kembali
            </a>
            <a href="{{ route('registrations.status.edit', $registration) }}"
            class="rounded-xl bg-amber-500 px-5 py-2.5 text-sm font-semibold text-white hover:bg-amber-600">

                Ubah Status

</a>
            <a href="{{ route('registrations.edit', $registration) }}" class="rounded-lg bg-indigo-600 px-4 py-2 text-xs font-semibold text-white transition hover:bg-indigo-700">
                ✏ Edit
            </a>
        </div>
    </div>

    {{-- Main Grid --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Kolom Kiri: Detail Informasi Pelanggan & Layanan --}}
        <div class="lg:col-span-2 space-y-6">
            <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
                
                {{-- Data Pelanggan --}}
                <div class="mb-6 pb-6 border-b border-slate-100">
                    <h2 class="text-sm font-bold uppercase tracking-wider text-indigo-600 mb-4">Informasi Pelanggan</h2>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-y-4 gap-x-6 text-sm">
                        <div>
                            <span class="block text-xs font-medium text-slate-400">Nama Pelanggan</span>
                            <span class="font-semibold text-slate-800">{{ $registration->nama }}</span>
                        </div>
                        <div>
                            <span class="block text-xs font-medium text-slate-400">Nomor HP / Whatsapp</span>
                            <span class="text-slate-700">{{ $registration->telepon }}</span>
                        </div>
                        <div>
                            <span class="block text-xs font-medium text-slate-400">NIK</span>
                            <span class="text-slate-700">{{ $registration->nik ?: '-' }}</span>
                        </div>
                        <div>
                            <span class="block text-xs font-medium text-slate-400">Tanggal Registrasi</span>
                            <span class="text-slate-700">{{ $registration->created_at ? $registration->created_at->format('d M Y, H:i') : '-' }}</span>
                        </div>
                        <div class="sm:col-span-2">
                            <span class="block text-xs font-medium text-slate-400">Alamat Lengkap</span>
                            <span class="text-slate-700 leading-relaxed whitespace-pre-line">{{ $registration->alamat ?: '-' }}</span>
                        </div>
                    </div>
                </div>

                {{-- Data Layanan --}}
                <div class="mb-6 pb-6 border-b border-slate-100">
                    <h2 class="text-sm font-bold uppercase tracking-wider text-indigo-600 mb-4">Layanan & Status</h2>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-y-4 gap-x-6 text-sm">
                        <div>
                            <span class="block text-xs font-medium text-slate-400">Paket Internet</span>
                            <span class="font-semibold text-slate-800">{{ optional($registration->package)->nama ?? '-' }}</span>
                        </div>
                        <div>
                            <span class="block text-xs font-medium text-slate-400">Status Registrasi</span>
                            <div class="mt-1">
                                @if($registration->status == 'Registrasi Baru')
                                    <span class="inline-flex items-center gap-1.5 rounded-md bg-amber-50 px-2.5 py-1 text-xs font-medium text-amber-700 border border-amber-200">
                                        🟡 {{ $registration->status }}
                                    </span>
                                @elseif($registration->status == 'Diverifikasi')
                                    <span class="inline-flex items-center gap-1.5 rounded-md bg-blue-50 px-2.5 py-1 text-xs font-medium text-blue-700 border border-blue-200">
                                        🔵 {{ $registration->status }}
                                    </span>
                                @elseif($registration->status == 'Pelanggan Aktif')
                                    <span class="inline-flex items-center gap-1.5 rounded-md bg-emerald-50 px-2.5 py-1 text-xs font-medium text-emerald-700 border border-emerald-200">
                                        🟢 {{ $registration->status }}
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 rounded-md bg-slate-100 px-2.5 py-1 text-xs font-medium text-slate-700 border border-slate-200">
                                        {{ $registration->status }}
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div>
                            <span class="block text-xs font-medium text-slate-400">Marketing</span>
                            <span class="text-slate-700">{{ optional(optional($registration->marketing)->user)->name ?? '-' }}</span>
                        </div>
                        <div>
                            <span class="block text-xs font-medium text-slate-400">ODP Terhubung</span>
                            <span class="text-slate-700">{{ optional($registration->odp)->nama ?? '-' }}</span>
                        </div>
                    </div>
                </div>

                {{-- Keterangan --}}
                <div>
                    <h2 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-2">Catatan / Keterangan</h2>
                    <p class="text-sm text-slate-600 bg-slate-50 rounded-lg p-3 border border-slate-100 italic">
                        {{ $registration->keterangan ?: 'Tidak ada keterangan tambahan.' }}
                    </p>
                </div>
{{-- Riwayat Status --}}
<div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">

    <h3 class="mb-6 flex items-center gap-2 border-b border-slate-100 pb-3 text-base font-bold text-slate-800">

        🕒 Riwayat Status

    </h3>

    @forelse($registration->histories as $history)

        <div class="relative pl-10 pb-8">

            {{-- Garis Timeline --}}
            @unless($loop->last)
                <div class="absolute left-4 top-8 h-full w-0.5 bg-slate-300"></div>
            @endunless

            {{-- Titik Timeline --}}
            <div class="absolute left-0 top-1 flex h-8 w-8 items-center justify-center rounded-full bg-indigo-600 text-white">

                ✔

            </div>

            <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">

                <div class="flex flex-col md:flex-row md:justify-between">

                    <div>

                        <h4 class="font-semibold text-indigo-700">

                            {{ $history->status_baru }}

                        </h4>

                        @if($history->status_lama)

                            <div class="text-sm text-slate-500 mt-1">

                                Dari :
                                <strong>{{ $history->status_lama }}</strong>

                            </div>

                        @endif

                    </div>

                    <div class="text-sm text-slate-500">

                        {{ $history->created_at->format('d M Y H:i') }}

                    </div>

                </div>

                <div class="mt-3 text-sm">

                    <strong>Petugas :</strong>

                    {{ optional($history->user)->name ?? 'System' }}

                </div>

                @if($history->catatan)

                    <div class="mt-2 rounded-lg bg-white p-3 text-sm">

                        {{ $history->catatan }}

                    </div>

                @endif

            </div>

        </div>

    @empty

        <div class="text-center text-slate-400 py-8">

            Belum ada riwayat perubahan status.

        </div>

    @endforelse

</div>
            </div>
        </div>

        {{-- Kolom Kanan: Peta, ODP & Foto KTP --}}
        <div class="space-y-6">

            {{-- Stat ODP Ringkas --}}
            <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-3">Kapasitas ODP</h3>
                <div class="grid grid-cols-3 gap-2 text-center">
                    <div class="p-2 bg-slate-50 rounded-lg">
                        <span class="block text-xs text-slate-400">Total</span>
                        <span class="text-sm font-semibold text-slate-700">{{ optional($registration->odp)->kapasitas ?? 0 }}</span>
                    </div>
                    <div class="p-2 bg-slate-50 rounded-lg">
                        <span class="block text-xs text-slate-400">Terpakai</span>
                        <span class="text-sm font-semibold text-amber-600">{{ optional($registration->odp)->terpakai ?? 0 }}</span>
                    </div>
                    <div class="p-2 bg-slate-50 rounded-lg">
                        <span class="block text-xs text-slate-400">Sisa</span>
                        <span class="text-sm font-semibold text-emerald-600">
                            {{ (optional($registration->odp)->kapasitas ?? 0) - (optional($registration->odp)->terpakai ?? 0) }}
                        </span>
                    </div>
                </div>
            </div>

            {{-- Peta --}}
            <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm space-y-3">
                <div class="flex justify-between items-center">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400">Lokasi Pemasangan</h3>
                    @if($registration->latitude && $registration->longitude)
                        <a href="https://www.google.com/maps?q={{ $registration->latitude }},{{ $registration->longitude }}" target="_blank" class="text-xs font-medium text-indigo-600 hover:underline">
                            Buka Maps ↗
                        </a>
                    @endif
                </div>

                <div id="showMap" class="overflow-hidden rounded-lg border border-slate-200" style="height:200px;"></div>

                <div class="flex justify-between text-xs text-slate-500 pt-1">
                    <span>Lat: {{ $registration->latitude ?: '-' }}</span>
                    <span>Long: {{ $registration->longitude ?: '-' }}</span>
                </div>
            </div>

            {{-- Foto KTP --}}
            <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm space-y-3">
                <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400">Dokumen KTP</h3>
                @if($registration->foto_ktp)
                    <a href="{{ asset('storage/'.$registration->foto_ktp) }}" target="_blank" class="block overflow-hidden rounded-lg border border-slate-200 hover:opacity-90 transition">
                        <img src="{{ asset('storage/'.$registration->foto_ktp) }}" class="w-full h-36 object-cover">
                    </a>
                @else
                    <div class="rounded-lg border border-dashed border-slate-200 p-6 text-center text-xs text-slate-400">
                        Foto KTP tidak tersedia
                    </div>
                @endif
            </div>

            {{-- Action Registrasi --}}
            <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">

                @if($registration->status == 'Registrasi Baru')

                    <form
                        action="{{ route('registrations.verify', $registration) }}"
                        method="POST"
                    >
                        @csrf

                        <button
                            type="submit"
                            onclick="return confirm('Verifikasi registrasi ini?')"
                            class="w-full rounded-lg bg-emerald-600 px-4 py-3 text-sm font-semibold text-white hover:bg-emerald-700"
                        >
                            ✓ Verifikasi
                        </button>
                    </form>

                @elseif($registration->status == 'Diverifikasi')

                    @if($registration->status == 'Diverifikasi')

                        @if(!$registration->workOrder)

                            <a
                                href="{{ route('work-orders.create', ['registration' => $registration->id]) }}"
                                class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg shadow">

                                📅 Jadwalkan Teknisi

                            </a>

                        @else

                            <a
                                href="{{ route('work-orders.show', $registration->workOrder) }}"
                                class="inline-flex items-center px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg shadow">

                                👷 Lihat Work Order

                            </a>

                        @endif

                    @endif
                @endif

            </div>

        </div>

    </div>

</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function(){
    let lat = {{ $registration->latitude ?: -6.5937 }};
    let lng = {{ $registration->longitude ?: 110.6673 }};

    const map = L.map('showMap', {
        zoomControl: false // Menyederhanakan kontrol peta
    }).setView([lat, lng], 14);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap'
    }).addTo(map);

    L.marker([lat, lng]).addTo(map);
});
</script>
@endpush