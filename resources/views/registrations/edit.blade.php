@extends('layouts.admin')

@section('page-title', 'Registrasi Pelanggan')

@section('content')

<div class="w-full space-y-6">
    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Edit Registrasi</h1>
            <p class="text-sm text-slate-500">Perbarui data registrasi pelanggan.</p>
        </div>
        <a href="{{ route('registrations.index') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-slate-600 hover:text-slate-900 transition">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Kembali
        </a>
    </div>

    <form action="{{ route('registrations.update', $registration) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf
        @method('PUT')

        {{-- Section 1: Data Pelanggan --}}
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <h3 class="text-base font-bold text-slate-800 border-b border-slate-100 pb-3 mb-5 flex items-center gap-2">
                <svg class="h-5 w-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
                Data Pelanggan
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">
                        Nama Pelanggan <span class="text-rose-500">*</span>
                    </label>
                    <input 
                        type="text" 
                        name="nama" 
                        value="{{ old('nama', $registration->nama) }}" 
                        class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-700 transition focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 @error('nama') border-rose-500 @enderror"
                    >
                    @error('nama')
                        <p class="mt-1.5 text-xs text-rose-500">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">NIK</label>
                    <input 
                        type="text" 
                        name="nik" 
                        value="{{ old('nik', $registration->nik) }}" 
                        class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-700 transition focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20"
                    >
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">
                        Nomor HP <span class="text-rose-500">*</span>
                    </label>
                    <input 
                        type="text" 
                        name="telepon" 
                        value="{{ old('telepon', $registration->telepon) }}" 
                        class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-700 transition focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 @error('telepon') border-rose-500 @enderror"
                    >
                    @error('telepon')
                        <p class="mt-1.5 text-xs text-rose-500">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Foto KTP</label>
                    
                    @if($registration->foto_ktp)
                    <div class="mb-3">
                        <img src="{{ asset('storage/' . $registration->foto_ktp) }}" class="h-32 rounded-xl border border-slate-200 object-cover shadow-sm">
                    </div>
                    @endif

                    <input 
                        type="file" 
                        name="foto_ktp" 
                        accept="image/*" 
                        class="w-full rounded-xl border border-slate-200 bg-white text-sm text-slate-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-l-xl file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 transition"
                    >
                    <p class="text-xs text-slate-400 mt-1.5">Format: JPG, PNG, WEBP (Maks. 2 MB)</p>
                    @error('foto_ktp')
                        <p class="mt-1.5 text-xs text-rose-500">{{ $message }}</p>
                    @enderror
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Alamat Lengkap</label>
                    <textarea 
                        name="alamat" 
                        rows="3" 
                        class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-700 transition focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 @error('alamat') border-rose-500 @enderror"
                    >{{ old('alamat', $registration->alamat) }}</textarea>
                    @error('alamat')
                        <p class="mt-1.5 text-xs text-rose-500">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        {{-- Section 2: Data Layanan --}}
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <h3 class="text-base font-bold text-slate-800 border-b border-slate-100 pb-3 mb-5 flex items-center gap-2">
                <svg class="h-5 w-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                </svg>
                Layanan
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Paket Internet</label>
                    <select name="package_id" class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-700 transition focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20">
                        <option value="">-- Pilih Paket --</option>
                        @foreach($packages as $package)
                            <option value="{{ $package->id }}" {{ old('package_id', $registration->package_id) == $package->id ? 'selected' : '' }}>
                                {{ $package->nama }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">ODP</label>
                    <select id="odp_id" name="odp_id" class="w-full">
                        <option value="">-- Pilih ODP --</option>
                        @foreach($odps as $odp)
                            <option value="{{ $odp->id }}" {{ old('odp_id', $registration->odp_id) == $odp->id ? 'selected' : '' }}>
                                {{ $odp->nama }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    {{-- marketing-auto-current-user --}}
@role('Marketing')
    <label class="mb-1 block text-sm font-medium text-slate-700">Marketing</label>

    @if(auth()->user()->marketing)
    <input type="hidden" name="marketing_id" value="{{ auth()->user()->marketing->id }}">
@else
    <div class="rounded-lg border border-red-200 bg-red-50 px-3 py-2 text-sm font-semibold text-red-700">
        Akun belum terhubung ke data Marketing. Hubungi Admin.
    </div>
@endif

    <div class="w-full rounded-lg border border-slate-300 bg-slate-100 px-3 py-2.5 text-slate-700">
        {{ auth()->user()->name }}
    </div>

    <p class="mt-1 text-xs text-slate-500">
        Otomatis menggunakan akun Marketing yang sedang login.
    </p>
@else
<label class="block text-sm font-semibold text-slate-700 mb-2">Marketing</label>

<select
    name="marketing_id"
    class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-700 transition focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20"
>
    <option value="">-- Pilih Marketing --</option>

    @foreach($marketings as $marketing)
        <option value="{{ $marketing->id }}"
            {{ old('marketing_id', $registration->marketing_id) == $marketing->id ? 'selected' : '' }}>
            {{ optional($marketing->user)->name ?? '-' }}
        </option>
    @endforeach
</select>
@endrole
                </div>
            </div>
        </div>

        {{-- Section 3: Lokasi --}}
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <h3 class="text-base font-bold text-slate-800 border-b border-slate-100 pb-3 mb-5 flex items-center gap-2">
                <svg class="h-5 w-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                Lokasi Pemasangan
            </h3>

            <div class="mb-5">
                <div id="map" class="overflow-hidden rounded-xl border border-slate-200 shadow-inner" style="height:400px;"></div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Latitude</label>
                    <input 
                        type="text" 
                        name="latitude" 
                        value="{{ old('latitude', $registration->latitude) }}" 
                        class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-700 transition focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20"
                    >
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Longitude</label>
                    <input 
                        type="text" 
                        name="longitude" 
                        value="{{ old('longitude', $registration->longitude) }}" 
                        class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-700 transition focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20"
                    >
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Keterangan Tambahan</label>
                    <textarea 
                        name="keterangan" 
                        rows="3" 
                        class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-700 transition focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20"
                    >{{ old('keterangan', $registration->keterangan) }}</textarea>
                </div>
            </div>
        </div>

        {{-- Footer Actions --}}
        <div class="flex items-center justify-end gap-3 pt-2">
            <a href="{{ route('registrations.index') }}" class="rounded-xl border border-slate-200 bg-white px-5 py-2.5 text-sm font-semibold text-slate-600 transition hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-slate-500/20">
                Batal
            </a>
            <button type="submit" class="rounded-xl bg-indigo-600 px-6 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500/20">
                Update Registrasi
            </button>
        </div>
    </form>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function(){
    if (document.querySelector('#odp_id')) {
        new TomSelect('#odp_id',{
            create: false,
            maxOptions: 1000,
            placeholder: 'Cari ODP...',
        });
    }
});
</script>
@endpush