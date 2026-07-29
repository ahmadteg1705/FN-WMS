@extends('layouts.admin')
@section('page-title', 'Tambah ODP')

@section('content')
<div class="space-y-6 w-full">

    {{-- HEADER PAGE & BACK BUTTON --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Tambah ODP</h1>
            <p class="text-sm text-slate-500 mt-0.5">Tambahkan data ODP baru ke dalam sistem Fahasa Net.</p>
        </div>
        <a href="{{ route('odps.index') }}"
           class="inline-flex items-center gap-2 px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm font-medium rounded-xl border border-slate-200/80 transition-all duration-150 self-start sm:self-auto">
            <svg class="w-4 h-4 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Kembali
        </a>
    </div>

    {{-- ERROR VALIDATION ALERT --}}
    @if ($errors->any())
        <div class="rounded-2xl bg-rose-50 border border-rose-200 p-5 shadow-sm">
            <div class="flex items-start gap-3">
                <div class="p-2 bg-rose-500 text-white rounded-xl shrink-0 mt-0.5">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
                <div>
                    <h3 class="font-bold text-rose-900 text-base">Terjadi Kesalahan Input</h3>
                    <ul class="mt-2 space-y-1 text-sm text-rose-700 list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

    {{-- FORM CONTAINER --}}
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 p-6 md:p-8">
        <form action="{{ route('odps.store') }}" method="POST">
            @csrf

            <div class="space-y-6">
                
                {{-- GRID FORM --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                    {{-- Nama ODP --}}
                    <div>
                        <label for="nama" class="block text-sm font-semibold text-slate-700 mb-2">
                            Nama ODP <span class="text-rose-500">*</span>
                        </label>
                        <input
                            type="text"
                            id="nama"
                            name="nama"
                            value="{{ old('nama') }}"
                            class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-700 placeholder-slate-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 transition-all"
                            placeholder="Contoh: ODP-JPR-01"
                            required>
                    </div>

                    {{-- Router NAS --}}
                    <div>
                        <label for="router" class="block text-sm font-semibold text-slate-700 mb-2">
                            Router NAS <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative">
                            <select
                                id="router"
                                name="router"
                                class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-700 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 transition-all appearance-none pr-10">
                                @foreach($routers as $router)
                                    <option
                                        value="{{ $router->nama }}"
                                        {{ old('router') == $router->nama ? 'selected' : '' }}>
                                        {{ $router->nama }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="absolute inset-y-0 right-0 flex items-center px-3.5 pointer-events-none text-slate-400">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </div>
                        </div>
                    </div>

                    {{-- Card / PON --}}
                    <div>
                        <label for="card" class="block text-sm font-semibold text-slate-700 mb-2">
                            Card / PON
                        </label>
                        <input
                            type="text"
                            id="card"
                            name="card"
                            value="{{ old('card') }}"
                            class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-700 placeholder-slate-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 transition-all"
                            placeholder="Contoh: 1/6/11">
                    </div>

                    {{-- Kapasitas ONU (Readonly / Calculated) --}}
                    <div>
                        <label for="kapasitas" class="block text-sm font-semibold text-slate-700 mb-2">
                            Kapasitas ONU <span class="text-xs font-normal text-slate-400">(Kalkulasi Otomatis)</span>
                        </label>
                        <input
                            type="number"
                            id="kapasitas"
                            name="kapasitas"
                            value="{{ old('kapasitas', 8) }}"
                            readonly
                            class="w-full px-4 py-2.5 bg-slate-100 border border-slate-200 rounded-xl text-sm font-semibold font-mono text-slate-600 cursor-not-allowed">
                    </div>

                    {{-- ONU Awal --}}
                    <div>
                        <label for="onu_awal" class="block text-sm font-semibold text-slate-700 mb-2">
                            ONU Awal
                        </label>
                        <input
                            type="number"
                            id="onu_awal"
                            name="onu_awal"
                            value="{{ old('onu_awal') }}"
                            class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-700 placeholder-slate-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 transition-all"
                            placeholder="Contoh: 1">
                    </div>

                    {{-- ONU Akhir --}}
                    <div>
                        <label for="onu_akhir" class="block text-sm font-semibold text-slate-700 mb-2">
                            ONU Akhir
                        </label>
                        <input
                            type="number"
                            id="onu_akhir"
                            name="onu_akhir"
                            value="{{ old('onu_akhir') }}"
                            class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-700 placeholder-slate-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 transition-all"
                            placeholder="Contoh: 8">
                    </div>
                    {{-- Latitude --}}
                    <div>
                        <label for="latitude" class="block text-sm font-semibold text-slate-700 mb-2">
                            Latitude
                        </label>

                        <input
                            type="text"
                            id="latitude"
                            name="latitude"
                            value="{{ old('latitude') }}"
                            class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-700 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600"
                            placeholder="-6.593245">
                    </div>

                    {{-- Longitude --}}
                    <div>
                        <label for="longitude" class="block text-sm font-semibold text-slate-700 mb-2">
                            Longitude
                        </label>

                        <input
                            type="text"
                            id="longitude"
                            name="longitude"
                            value="{{ old('longitude') }}"
                            class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-700 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600"
                            placeholder="110.671534">
                    </div>
                </div>

                {{-- Keterangan --}}
                <div>
                    <label for="keterangan" class="block text-sm font-semibold text-slate-700 mb-2">
                        Keterangan
                    </label>
                    <textarea
                        id="keterangan"
                        name="keterangan"
                        rows="4"
                        class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-700 placeholder-slate-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 transition-all"
                        placeholder="Tambahkan catatan khusus mengenai ODP ini jika ada...">{{ old('keterangan') }}</textarea>
                </div>

                {{-- ACTION BUTTONS --}}
                <div class="pt-6 border-t border-slate-100 flex items-center justify-end gap-3">
                    <a href="{{ route('odps.index') }}"
                       class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm font-medium rounded-xl transition-all duration-150">
                        Batal
                    </a>
                    <button
                        type="submit"
                        class="inline-flex items-center gap-2 px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-xl shadow-sm transition-all duration-150">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" />
                        </svg>
                        Simpan ODP
                    </button>
                </div>

            </div>
        </form>
    </div>

</div>

{{-- SCRIPT JAVASCRIPT UNTUK HITUNG KAPASITAS --}}
<script>
function hitungKapasitas() {
    let awal = parseInt(document.getElementById('onu_awal').value) || 0;
    let akhir = parseInt(document.getElementById('onu_akhir').value) || 0;

    let kapasitas = 0;

    if (akhir >= awal && awal > 0) {
        kapasitas = (akhir - awal) + 1;
    }

    document.getElementById('kapasitas').value = kapasitas;
}

document.getElementById('onu_awal').addEventListener('input', hitungKapasitas);
document.getElementById('onu_akhir').addEventListener('input', hitungKapasitas);

// Hitung saat halaman dibuka
hitungKapasitas();
</script>
@endsection