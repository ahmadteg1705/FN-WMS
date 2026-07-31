@extends('layouts.admin')
@section('page-title', 'Master ODP')

@section('content')
<div class="space-y-6">

    {{-- ALERT / NOTIFIKASI IMPORT & SUCCESS --}}
    @if(session()->has('success'))
        @php $data = session('success'); @endphp

        @if(is_array($data))
            <div class="rounded-2xl bg-emerald-50 border border-emerald-200 p-5 shadow-sm">
                <div class="flex items-start gap-3">
                    <div class="p-2 bg-emerald-500 text-white rounded-xl shrink-0 mt-0.5">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                    <div class="flex-1">
                        <h3 class="font-bold text-emerald-900 text-base">Import Data Selesai</h3>
                        <div class="mt-2 flex flex-wrap gap-4 text-sm font-medium">
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-emerald-100 text-emerald-800 rounded-lg">
                                <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                                {{ $data['success'] }} Berhasil
                            </span>
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-rose-100 text-rose-800 rounded-lg">
                                <span class="w-2 h-2 rounded-full bg-rose-500"></span>
                                {{ $data['failed'] }} Gagal
                            </span>
                        </div>

                        @if(count($data['errors']) > 0)
                            <div class="mt-4 pt-4 border-t border-emerald-200/60 space-y-2">
                                <p class="text-xs font-semibold text-emerald-800 uppercase tracking-wider">Rincian Gagal:</p>
                                <div class="max-h-40 overflow-y-auto space-y-1.5 pr-2">
                                    @foreach($data['errors'] as $error)
                                        <div class="flex items-center justify-between bg-white/80 p-2.5 rounded-lg border border-emerald-100 text-xs">
                                            <span class="font-bold text-slate-700">{{ $error['nama'] }}</span>
                                            <span class="text-rose-600 font-medium">{{ $error['pesan'] }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @else
            <div class="rounded-2xl bg-emerald-50 border border-emerald-200 p-4 text-emerald-800 flex items-center gap-3 shadow-sm">
                <div class="p-1.5 bg-emerald-500 text-white rounded-lg shrink-0">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
                <span class="text-sm font-semibold">{{ $data }}</span>
            </div>
        @endif
    @endif

    {{-- HEADER PAGE & STATS BRIEF (OPTIONAL LOOK) --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Master ODP</h1>
            <p class="text-sm text-slate-500 mt-0.5">Kelola data Optical Distribution Point, lokasi router, dan kapasitas port ONU.</p>
        </div>
    </div>
{{-- MAP --}}
<div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">

    <div class="px-6 py-4 border-b border-slate-200">

        <h2 class="text-lg font-bold text-slate-800">
            Peta Lokasi ODP
        </h2>

        <p class="text-sm text-slate-500">
            Menampilkan seluruh lokasi ODP berdasarkan koordinat Latitude & Longitude.
        </p>

    </div>

    <div
        id="odpMap"
        class="w-full"
        style="height:500px;">
    </div>

</div>
    {{-- TOOLBAR (SEARCH & ACTION BUTTONS) --}}
    <div class="bg-white p-4 rounded-2xl shadow-sm border border-slate-200/80 flex flex-col lg:flex-row lg:items-center justify-between gap-4">
        
        {{-- Search Input --}}
        <form action="{{ route('odps.index') }}" method="GET" class="w-full lg:w-80">
            <div class="relative">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none text-slate-400">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </span>
                <input
                    type="text"
                    name="keyword"
                    value="{{ request('keyword') }}"
                    placeholder="Cari nama ODP / Router..."
                    class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-700 placeholder-slate-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 transition-all">
            </div>
        </form>

        {{-- Button Group --}}
        <div class="flex items-center gap-2 flex-wrap sm:flex-nowrap">
            @can('odps.create')
            <a href="{{ route('odps.create') }}"
               class="inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-xl shadow-sm transition-all duration-150">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Tambah ODP
            </a>
            @endcan

            @can('odps.import')

            <button
                type="button"
                onclick="openImportModal()"
                class="inline-flex items-center justify-center gap-2 px-3.5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium rounded-xl shadow-sm transition-all duration-150">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                </svg>
                Import
            </button>

            @endcan

            @can('odps.export')

            <a href="{{ route('odps.export') }}"
               class="inline-flex items-center justify-center gap-2 px-3.5 py-2.5 bg-amber-500 hover:bg-amber-600 text-white text-sm font-medium rounded-xl shadow-sm transition-all duration-150">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                </svg>
                Export
            </a>

            @endcan

            @can('odps.export')

            <button
                type="button"
                onclick="window.print()"
                class="inline-flex items-center justify-center gap-2 px-3.5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm font-medium rounded-xl border border-slate-200 transition-all duration-150">
                <svg class="w-4 h-4 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                </svg>
                Print
            </button>

            @endcan
        </div>
    </div>

    {{-- DATA TABLE --}}
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600">
                <thead class="bg-slate-50/80 text-xs uppercase font-bold text-slate-500 tracking-wider border-b border-slate-200">
                    <tr>
                        <th class="px-5 py-4 text-center w-12">No</th>
                        <th class="px-5 py-4">Nama ODP</th>
                        <th class="px-5 py-4">Router NAS</th>
                        <th class="px-5 py-4 text-center">Card</th>
                        <th class="px-5 py-4 text-center">Rentang ONU</th>
                        <th class="px-5 py-4 text-center">ONU Terpakai</th>
                        <th class="px-5 py-4 text-center">Sisa Port</th>
                        <th class="px-5 py-4 text-center">Latitude</th>
                        <th class="px-5 py-4 text-center">Longitude</th>
                        <th class="px-5 py-4 text-center">Status</th>
                        <th class="px-3 py-4 text-center w-24">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($odps as $odp)
                        <tr class="hover:bg-slate-50/80 transition-colors duration-150">
                            <td class="px-5 py-4 text-center font-medium text-slate-400">
                                {{ $loop->iteration }}
                            </td>
                            <td class="px-5 py-4 font-semibold text-slate-800">
                                <span class="hover:text-blue-600 transition">{{ $odp->nama }}</span>
                            </td>
                            <td class="px-5 py-4">
                                <span class="inline-flex items-center px-2.5 py-1 bg-slate-100 text-slate-700 rounded-lg text-xs font-medium border border-slate-200/60">
                                    {{ $odp->router }}
                                </span>
                            </td>
                            <td class="px-5 py-4 text-center font-mono text-xs text-slate-600">
                                {{ $odp->card }}
                            </td>
                            <td class="px-5 py-4 text-center">
                                <span class="px-2.5 py-1 bg-slate-100 text-slate-700 font-mono text-xs rounded-md">
                                    {{ $odp->onu_awal }} – {{ $odp->onu_akhir }}
                                </span>
                            </td>
                            <td class="px-5 py-4 text-center">
                                <span class="font-bold text-slate-700">0</span>
                            </td>
                            <td class="px-5 py-4 text-center">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                    {{ $odp->kapasitas }} Port
                                </span>
                            </td>
                            <td class="px-5 py-4 text-center font-mono text-xs">
                                {{ $odp->latitude ?? '-' }}
                            </td>

                            <td class="px-5 py-4 text-center font-mono text-xs">
                                {{ $odp->longitude ?? '-' }}
                            </td>
                            <td class="px-5 py-4 text-center">
                                @if($odp->status)
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium bg-emerald-50 text-emerald-700 border border-emerald-200/80">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                        Normal
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium bg-rose-50 text-rose-700 border border-rose-200/80">
                                        <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                                        Nonaktif
                                    </span>
                                @endif
                            </td>
                            <td class="px-3 py-4 text-center">
                                <div class="flex items-center justify-center gap-2">

    {{-- Lihat --}}
    <button
        type="button"
        onclick="focusMarker({{ $odp->id }})"
        title="Lihat di Peta"
        class="w-8 h-8 flex items-center justify-center rounded-lg bg-blue-500 hover:bg-blue-600 text-white transition">

        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M15 12a3 3 0 11-6 0 3 3 0 016 0"/>

            <path stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M2.458 12C3.732 7.943 7.523 5
                     12 5c4.477 0 8.268 2.943
                     9.542 7-1.274 4.057-5.065
                     7-9.542 7-4.477
                     0-8.268-2.943-9.542-7z"/>
        </svg>

    </button>

    {{-- Edit --}}
    @can('odps.edit')
    <a href="{{ route('odps.edit',$odp->id) }}"
       title="Edit"
       class="w-8 h-8 flex items-center justify-center rounded-lg bg-amber-400 hover:bg-amber-500 text-white transition">

        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
        </svg>

    </a>
    @endcan

    {{-- Hapus --}}
    @can('odps.delete')
    <form action="{{ route('odps.destroy',$odp->id) }}"
          method="POST"
          class="inline">

        @csrf
        @method('DELETE')

        <button
            type="submit"
            onclick="return confirm('Yakin ingin menghapus ODP ini?')"
            title="Hapus"
            class="w-8 h-8 flex items-center justify-center rounded-lg bg-red-500 hover:bg-red-600 text-white transition">

            <svg class="w-4 h-4"
                 fill="none"
                 stroke="currentColor"
                 viewBox="0 0 24 24">

                <path stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
            </svg>

        </button>

    </form>
    @endcan

</div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="11" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="w-12 h-12 rounded-2xl bg-slate-100 flex items-center justify-center text-slate-400 mb-3">
                                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                                        </svg>
                                    </div>
                                    <p class="text-slate-800 font-semibold text-base">Belum Ada Data ODP</p>
                                    <p class="text-slate-400 text-xs mt-1">Gunakan tombol "Tambah ODP" atau "Import" untuk mengisikan data.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- PAGINATION --}}
        @if($odps->hasPages())
            <div class="px-6 py-4 border-t border-slate-200/80 bg-slate-50/50">
                {{ $odps->links() }}
            </div>
        @endif
    </div>

</div>

@can('odps.import')
<x-fn.import-modal />
@endcan
@push('scripts')

<script>

document.addEventListener("DOMContentLoaded", function () {

    const map = L.map('odpMap').setView(
        [-6.593245,110.671534],
        12
    );

    L.tileLayer(
        'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
        {
            maxZoom:19,
            attribution:'© OpenStreetMap'
        }
    ).addTo(map);

    let markers = {};

    let bounds = [];

    @foreach($mapOdps as $odp)

    @if($odp->latitude && $odp->longitude)

        markers[{{ $odp->id }}] = L.marker([
    {{ $odp->latitude }},
    {{ $odp->longitude }}
]).addTo(map);

markers[{{ $odp->id }}].bindPopup(`
    <b>{{ $odp->nama }}</b><br>
    Router : {{ $odp->router }}<br>
    Card : {{ $odp->card }}<br>
    ONU : {{ $odp->onu_awal }} - {{ $odp->onu_akhir }}
`);

bounds.push([
    {{ $odp->latitude }},
    {{ $odp->longitude }}
]);

        @endif

    @endforeach

    if(bounds.length>0){

        map.fitBounds(bounds,{
            padding:[40,40]
        });

    }

    window.focusMarker=function(id){

        if(markers[id]){

            map.flyTo(
                markers[id].getLatLng(),
                18
            );

            markers[id].openPopup();

            document
                .getElementById('odpMap')
                .scrollIntoView({
                    behavior:'smooth'
                });

        }

    }

});

</script>

@endpush
@endsection