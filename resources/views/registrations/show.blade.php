@extends('layouts.admin')

@section('page-title', 'Detail Registrasi')

@section('content')
@php
    $registration->loadMissing([
        'package',
        'odp',
        'marketing.user',
        'histories.user',
        'workOrder.nocActivation.handler',
    ]);

    $isAdmin = auth()->user()->hasAnyRole([
        'Super User',
        'Super Admin',
        'Admin',
        'Administrator',
    ]);

    $timeline = collect();

    foreach ($registration->histories as $history) {
        $timeline->push([
            'time' => $history->created_at,
            'title' => $history->status_baru,
            'from' => $history->status_lama,
            'officer' => optional($history->user)->name ?? 'System',
            'note' => $history->catatan,
            'source' => 'Registrasi / Teknisi',
        ]);
    }

    $nocActivation = optional($registration->workOrder)->nocActivation;

    if ($nocActivation) {
        if ($nocActivation->accepted_at) {
            $timeline->push([
                'time' => $nocActivation->accepted_at,
                'title' => 'Diterima NOC',
                'from' => 'Menunggu Aktivasi',
                'officer' => optional($nocActivation->handler)->name ?? 'NOC',
                'note' => 'Tugas aktivasi diterima oleh petugas NOC.',
                'source' => 'Aktivasi NOC',
            ]);
        }

        if ($nocActivation->started_at) {
            $timeline->push([
                'time' => $nocActivation->started_at,
                'title' => 'Proses Aktivasi',
                'from' => 'Diterima NOC',
                'officer' => optional($nocActivation->handler)->name ?? 'NOC',
                'note' => 'Petugas NOC mulai melakukan proses aktivasi.',
                'source' => 'Aktivasi NOC',
            ]);
        }

        if ($nocActivation->activated_at) {
            $timeline->push([
                'time' => $nocActivation->activated_at,
                'title' => 'Menunggu Verifikasi Admin',
                'from' => 'Proses Aktivasi',
                'officer' => optional($nocActivation->handler)->name ?? 'NOC',
                'note' => $nocActivation->activation_result
                    ?: 'Aktivasi NOC selesai dan menunggu verifikasi Admin.',
                'source' => 'Aktivasi NOC',
            ]);
        }

        if ($nocActivation->status === \App\Models\NocActivation::STATUS_SUCCESS) {
            $timeline->push([
                'time' => $nocActivation->updated_at,
                'title' => 'Aktivasi Berhasil',
                'from' => 'Menunggu Verifikasi Admin',
                'officer' => 'Administrator',
                'note' => $nocActivation->activation_result
                    ?: 'Aktivasi telah diverifikasi Admin.',
                'source' => 'Verifikasi Admin',
            ]);
        }

        if ($nocActivation->status === \App\Models\NocActivation::STATUS_FAILED) {
            $timeline->push([
                'time' => $nocActivation->failed_at ?? $nocActivation->updated_at,
                'title' => 'Aktivasi Gagal',
                'from' => 'Proses Aktivasi',
                'officer' => optional($nocActivation->handler)->name ?? 'NOC',
                'note' => $nocActivation->activation_result ?: 'Aktivasi gagal.',
                'source' => 'Aktivasi NOC',
            ]);
        }
    }

    $timeline = $timeline
        ->filter(fn ($item) => filled($item['time']))
        ->sortByDesc('time')
        ->values();
@endphp

<div class="mx-auto max-w-7xl space-y-6">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-xl font-bold text-slate-800">Detail Registrasi</h1>
            <p class="text-xs text-slate-500">
                Informasi lengkap calon pelanggan #{{ $registration->registration_number }}
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route('registrations.index') }}"
               class="rounded-lg border border-slate-200 bg-white px-4 py-2 text-xs font-semibold text-slate-600 transition hover:bg-slate-50">
                ← Kembali
            </a>

            @if($isAdmin)
                <a href="{{ route('registrations.status.edit', $registration) }}"
                   class="rounded-lg bg-amber-500 px-4 py-2 text-xs font-semibold text-white hover:bg-amber-600">
                    Ubah Status
                </a>

                <a href="{{ route('registrations.edit', $registration) }}"
                   class="rounded-lg bg-indigo-600 px-4 py-2 text-xs font-semibold text-white hover:bg-indigo-700">
                    ✏ Edit
                </a>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <div class="space-y-6 lg:col-span-2">
            <section class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
                <div class="mb-6 border-b border-slate-100 pb-6">
                    <h2 class="mb-4 text-sm font-bold uppercase tracking-wider text-indigo-600">
                        Informasi Pelanggan
                    </h2>

                    <div class="grid grid-cols-1 gap-x-6 gap-y-4 text-sm sm:grid-cols-2">
                        <div>
                            <span class="block text-xs font-medium text-slate-400">Nama Pelanggan</span>
                            <span class="font-semibold text-slate-800">{{ $registration->nama }}</span>
                        </div>

                        <div>
                            <span class="block text-xs font-medium text-slate-400">Nomor HP / WhatsApp</span>
                            <span class="text-slate-700">{{ $registration->telepon ?: '-' }}</span>
                        </div>

                        <div>
                            <span class="block text-xs font-medium text-slate-400">NIK</span>
                            <span class="text-slate-700">{{ $registration->nik ?: '-' }}</span>
                        </div>

                        <div>
                            <span class="block text-xs font-medium text-slate-400">Tanggal Registrasi</span>
                            <span class="text-slate-700">
                                {{ $registration->created_at ? $registration->created_at->format('d M Y, H:i') : '-' }}
                            </span>
                        </div>

                        <div class="sm:col-span-2">
                            <span class="block text-xs font-medium text-slate-400">Alamat Lengkap</span>
                            <span class="whitespace-pre-line leading-relaxed text-slate-700">
                                {{ $registration->alamat ?: '-' }}
                            </span>
                        </div>
                    </div>
                </div>

                <div class="mb-6 border-b border-slate-100 pb-6">
                    <h2 class="mb-4 text-sm font-bold uppercase tracking-wider text-indigo-600">
                        Layanan & Status
                    </h2>

                    <div class="grid grid-cols-1 gap-x-6 gap-y-4 text-sm sm:grid-cols-2">
                        <div>
                            <span class="block text-xs font-medium text-slate-400">Paket Internet</span>
                            <span class="font-semibold text-slate-800">
                                {{ optional($registration->package)->nama ?? '-' }}
                            </span>
                        </div>

                        <div>
                            <span class="block text-xs font-medium text-slate-400">Status Registrasi</span>
                            <span class="mt-1 inline-flex rounded-md border border-slate-200 bg-slate-100 px-2.5 py-1 text-xs font-medium text-slate-700">
                                {{ $registration->status }}
                            </span>
                        </div>

                        <div>
                            <span class="block text-xs font-medium text-slate-400">Marketing</span>
                            <span class="text-slate-700">
                                {{ optional(optional($registration->marketing)->user)->name ?? '-' }}
                            </span>
                        </div>

                        <div>
                            <span class="block text-xs font-medium text-slate-400">ODP Terhubung</span>
                            <span class="text-slate-700">
                                {{ optional($registration->odp)->nama ?? '-' }}
                            </span>
                        </div>
                    </div>
                </div>

                <div>
                    <h2 class="mb-2 text-xs font-bold uppercase tracking-wider text-slate-400">
                        Catatan / Keterangan
                    </h2>

                    <p class="rounded-lg border border-slate-100 bg-slate-50 p-3 text-sm italic text-slate-600">
                        {{ $registration->keterangan ?: 'Tidak ada keterangan tambahan.' }}
                    </p>
                </div>
            </section>

            <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <h3 class="mb-6 flex items-center gap-2 border-b border-slate-100 pb-3 text-base font-bold text-slate-800">
                    🕒 Riwayat Status Lengkap
                </h3>

                @forelse($timeline as $event)
                    <div class="relative pb-8 pl-10">
                        @unless($loop->last)
                            <div class="absolute left-4 top-8 h-full w-0.5 bg-slate-300"></div>
                        @endunless

                        <div class="absolute left-0 top-1 flex h-8 w-8 items-center justify-center rounded-full text-white
                            {{ $event['source'] === 'Aktivasi NOC'
                                ? 'bg-violet-600'
                                : ($event['source'] === 'Verifikasi Admin'
                                    ? 'bg-emerald-600'
                                    : 'bg-indigo-600') }}">
                            ✔
                        </div>

                        <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                            <div class="flex flex-col gap-2 md:flex-row md:justify-between">
                                <div>
                                    <div class="flex flex-wrap items-center gap-2">
                                        <h4 class="font-semibold text-indigo-700">
                                            {{ $event['title'] }}
                                        </h4>

                                        <span class="rounded-full bg-white px-2 py-0.5 text-[10px] font-bold uppercase tracking-wide text-slate-500">
                                            {{ $event['source'] }}
                                        </span>
                                    </div>

                                    @if($event['from'])
                                        <div class="mt-1 text-sm text-slate-500">
                                            Dari: <strong>{{ $event['from'] }}</strong>
                                        </div>
                                    @endif
                                </div>

                                <div class="text-sm text-slate-500">
                                    {{ \Carbon\Carbon::parse($event['time'])->format('d M Y H:i') }}
                                </div>
                            </div>

                            <div class="mt-3 text-sm">
                                <strong>Petugas:</strong> {{ $event['officer'] }}
                            </div>

                            @if($event['note'])
                                <div class="mt-2 rounded-lg bg-white p-3 text-sm">
                                    {{ $event['note'] }}
                                </div>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="py-8 text-center text-slate-400">
                        Belum ada riwayat perubahan status.
                    </div>
                @endforelse
            </section>
        </div>

        <aside class="space-y-6">
            <section class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                <h3 class="mb-3 text-xs font-bold uppercase tracking-wider text-slate-400">
                    Kapasitas ODP
                </h3>

                <div class="grid grid-cols-3 gap-2 text-center">
                    <div class="rounded-lg bg-slate-50 p-2">
                        <span class="block text-xs text-slate-400">Total</span>
                        <span class="text-sm font-semibold text-slate-700">
                            {{ optional($registration->odp)->kapasitas ?? 0 }}
                        </span>
                    </div>

                    <div class="rounded-lg bg-slate-50 p-2">
                        <span class="block text-xs text-slate-400">Terpakai</span>
                        <span class="text-sm font-semibold text-amber-600">
                            {{ optional($registration->odp)->terpakai ?? 0 }}
                        </span>
                    </div>

                    <div class="rounded-lg bg-slate-50 p-2">
                        <span class="block text-xs text-slate-400">Sisa</span>
                        <span class="text-sm font-semibold text-emerald-600">
                            {{ (optional($registration->odp)->kapasitas ?? 0) - (optional($registration->odp)->terpakai ?? 0) }}
                        </span>
                    </div>
                </div>
            </section>

            <section class="space-y-3 rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                <div class="flex items-center justify-between">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400">
                        Lokasi Pemasangan
                    </h3>

                    @if($registration->latitude && $registration->longitude)
                        <a href="https://www.google.com/maps?q={{ $registration->latitude }},{{ $registration->longitude }}"
                           target="_blank"
                           class="text-xs font-medium text-indigo-600 hover:underline">
                            Buka Maps ↗
                        </a>
                    @endif
                </div>

                <div id="showMap"
                     class="overflow-hidden rounded-lg border border-slate-200"
                     style="height: 200px;"></div>

                <div class="flex justify-between pt-1 text-xs text-slate-500">
                    <span>Lat: {{ $registration->latitude ?: '-' }}</span>
                    <span>Long: {{ $registration->longitude ?: '-' }}</span>
                </div>
            </section>

            <section class="space-y-3 rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400">
                    Dokumen KTP
                </h3>

                @if($registration->foto_ktp)
                    <a href="{{ asset('storage/'.$registration->foto_ktp) }}"
                       target="_blank"
                       class="block overflow-hidden rounded-lg border border-slate-200 transition hover:opacity-90">
                        <img src="{{ asset('storage/'.$registration->foto_ktp) }}"
                             class="h-36 w-full object-cover">
                    </a>
                @else
                    <div class="rounded-lg border border-dashed border-slate-200 p-6 text-center text-xs text-slate-400">
                        Foto KTP tidak tersedia
                    </div>
                @endif
            </section>

            @if($isAdmin)
                <section class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                    @if($registration->status === 'Registrasi Baru')
                        <form action="{{ route('registrations.verify', $registration) }}"
                              method="POST">
                            @csrf

                            <button type="submit"
                                    onclick="return confirm('Verifikasi registrasi ini?')"
                                    class="w-full rounded-lg bg-emerald-600 px-4 py-3 text-sm font-semibold text-white hover:bg-emerald-700">
                                ✓ Verifikasi
                            </button>
                        </form>
                    @elseif($registration->status === 'Diverifikasi')
                        @if(!$registration->workOrder)
                            <a href="{{ route('work-orders.create', ['registration' => $registration->id]) }}"
                               class="inline-flex w-full items-center justify-center rounded-lg bg-blue-600 px-4 py-3 text-sm font-semibold text-white shadow hover:bg-blue-700">
                                📅 Jadwalkan Teknisi
                            </a>
                        @else
                            <a href="{{ route('work-orders.show', $registration->workOrder) }}"
                               class="inline-flex w-full items-center justify-center rounded-lg bg-emerald-600 px-4 py-3 text-sm font-semibold text-white shadow hover:bg-emerald-700">
                                👷 Lihat Work Order
                            </a>
                        @endif
                    @endif
                </section>
            @endif
        </aside>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const lat = {{ $registration->latitude ?: -6.5937 }};
    const lng = {{ $registration->longitude ?: 110.6673 }};

    const map = L.map('showMap', {
        zoomControl: false
    }).setView([lat, lng], 14);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap'
    }).addTo(map);

    L.marker([lat, lng]).addTo(map);
});
</script>
@endpush
