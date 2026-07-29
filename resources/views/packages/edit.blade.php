@extends('layouts.admin')

@section('content')

<div class="flex justify-between items-center mb-6">
    <div>
        <h1 class="text-4xl font-bold text-gray-800">
            Edit Paket Internet
        </h1>
        <p class="text-gray-500">
            Ubah data paket internet Fahasa Net
        </p>
    </div>

    <a href="{{ route('packages.index') }}"
       class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-3 rounded-lg transition">
        ← Kembali
    </a>
</div>

<div class="bg-white rounded-xl shadow p-8">
    <form action="{{ route('packages.update', $package->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

            {{-- Nama Paket --}}
            <div>
                <label class="block mb-2 font-semibold text-gray-700">
                    Nama Paket
                </label>
                <input
                    type="text"
                    name="nama"
                    value="{{ old('nama', $package->nama) }}"
                    class="w-full border @error('nama') border-red-500 @else border-gray-300 @enderror rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500">
                @error('nama')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Kecepatan --}}
            <div>
                <label class="block mb-2 font-semibold text-gray-700">
                    Kecepatan
                </label>
                <input
                    type="text"
                    name="kecepatan"
                    value="{{ old('kecepatan', $package->kecepatan) }}"
                    class="w-full border @error('kecepatan') border-red-500 @else border-gray-300 @enderror rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500">
                @error('kecepatan')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Harga --}}
            <div>
                <label class="block mb-2 font-semibold text-gray-700">
                    Harga
                </label>
                <input
                    type="number"
                    name="harga"
                    value="{{ old('harga', $package->harga) }}"
                    class="w-full border @error('harga') border-red-500 @else border-gray-300 @enderror rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500">
                @error('harga')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Status --}}
            <div>
                <label class="block mb-2 font-semibold text-gray-700">
                    Status
                </label>
                <select
                    name="status"
                    class="w-full border @error('status') border-red-500 @else border-gray-300 @enderror rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="1" {{ old('status', $package->status) == 1 ? 'selected' : '' }}>
                        Aktif
                    </option>
                    <option value="0" {{ old('status', $package->status) == 0 ? 'selected' : '' }}>
                        Nonaktif
                    </option>
                </select>
                @error('status')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

        </div>

        {{-- Keterangan --}}
        <div class="mt-6">
            <label class="block mb-2 font-semibold text-gray-700">
                Keterangan
            </label>
            <textarea
                name="keterangan"
                rows="4"
                class="w-full border @error('keterangan') border-red-500 @else border-gray-300 @enderror rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('keterangan', $package->keterangan) }}</textarea>
            @error('keterangan')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- Action Buttons --}}
        <div class="mt-8 flex gap-3">
            <button
                type="submit"
                class="bg-blue-600 hover:bg-blue-700 text-white font-medium px-8 py-3 rounded-lg shadow transition">
                💾 Update Paket
            </button>

            <a href="{{ route('packages.index') }}"
               class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-medium px-8 py-3 rounded-lg transition">
                Batal
            </a>
        </div>

    </form>
</div>

@endsection