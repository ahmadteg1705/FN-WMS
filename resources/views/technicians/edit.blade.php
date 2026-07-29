@extends('layouts.admin')

@section('content')

{{-- Header --}}
<div class="flex justify-between items-center mb-6">
    <div>
        <h1 class="text-4xl font-bold text-gray-800">
            Edit Teknisi
        </h1>
        <p class="text-gray-500">
            Perbarui data Teknisi Fahasa Net
        </p>
    </div>

    <a href="{{ route('technicians.index') }}"
       class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-3 rounded-lg transition">
        ← Kembali
    </a>
</div>

{{-- Form Card --}}
<div class="bg-white rounded-xl shadow p-8">
    <form action="{{ route('technicians.update', $technician->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        {{-- INFORMASI AKUN --}}
        <h2 class="text-xl font-bold text-gray-800 border-b pb-2 mb-6">
            Informasi Akun
        </h2>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            
            {{-- NIK --}}
            <div>
                <label class="block mb-2 font-semibold text-gray-700">
                    NIK *
                </label>
                <input
                    type="text"
                    name="nik"
                    value="{{ old('nik', $technician->nik) }}"
                    class="w-full border @error('nik') border-red-500 @else border-gray-300 @enderror rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500">
                @error('nik')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            <div>
    <label class="block mb-2 font-semibold text-gray-700">
        User Login *
    </label>

    <select
        name="user_id"
        class="w-full border @error('user_id') border-red-500 @else border-gray-300 @enderror rounded-lg px-4 py-3">

        @foreach($users as $user)

            <option
                value="{{ $user->id }}"
                {{ old('user_id', $technician->user_id) == $user->id ? 'selected' : '' }}>

                {{ $user->name }}
                ({{ $user->username }}) -
                {{ $user->roleName() }}

            </option>

        @endforeach

    </select>

    @error('user_id')
        <p class="text-red-500 text-sm mt-1">
            {{ $message }}
        </p>
    @enderror

</div>
        </div>

        {{-- INFORMASI KONTAK --}}
        <h2 class="text-xl font-bold text-gray-800 border-b pb-2 mb-6">
            Informasi Kontak
        </h2>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">

            {{-- Nomor HP --}}
            <div>
                <label class="block mb-2 font-semibold text-gray-700">
                    Nomor HP *
                </label>
                <input
                    type="text"
                    name="telepon"
                    value="{{ old('telepon', $technician->telepon) }}"
                    class="w-full border @error('telepon') border-red-500 @else border-gray-300 @enderror rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500">
                @error('telepon')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Alamat --}}
            <div class="md:col-span-2">
                <label class="block mb-2 font-semibold text-gray-700">
                    Alamat
                </label>
                <textarea
                    name="alamat"
                    rows="3"
                    class="w-full border @error('alamat') border-red-500 @else border-gray-300 @enderror rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('alamat', $technician->alamat) }}</textarea>
                @error('alamat')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        {{-- INFORMASI PEKERJAAN --}}
        <h2 class="text-xl font-bold text-gray-800 border-b pb-2 mb-6">
            Informasi Pekerjaan
        </h2>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            {{-- Jabatan --}}
            <div>
                <label class="block mb-2 font-semibold text-gray-700">
                    Jabatan *
                </label>
                <select
                    name="position_id"
                    class="w-full border @error('position_id') border-red-500 @else border-gray-300 @enderror rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">-- Pilih Jabatan --</option>
                    @foreach($positions as $position)
                        <option value="{{ $position->id }}"
    {{ old('position_id', $technician->position_id) == $position->id ? 'selected' : '' }}>
                            {{ $position->nama }}
                        </option>
                    @endforeach
                </select>
                @error('position_id')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Team --}}
            <div>
                <label class="block mb-2 font-semibold text-gray-700">
                    Team *
                </label>
                <select
                    name="team_id"
                    class="w-full border @error('team_id') border-red-500 @else border-gray-300 @enderror rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">-- Pilih Team --</option>
                    @foreach($teams as $team)
                        <option value="{{ $team->id }}" {{ old('team_id', $technician->team_id) == $team->id ? 'selected' : '' }}>
                            {{ $team->nama }}
                        </option>
                    @endforeach
                </select>
                @error('team_id')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Status --}}
            <div>
                <label class="block mb-2 font-semibold text-gray-700">
                    Status *
                </label>
                <select
                    name="status"
                    class="w-full border @error('status') border-red-500 @else border-gray-300 @enderror rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="1" {{ old('status', $technician->status) == 1 ? 'selected' : '' }}>Aktif</option>
                    <option value="0" {{ old('status', $technician->status) == 0 ? 'selected' : '' }}>Nonaktif</option>
                </select>
                @error('status')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Tanggal Masuk --}}
            <div>
                <label class="block mb-2 font-semibold text-gray-700">
                    Tanggal Masuk
                </label>
                <input
                    type="date"
                    name="tanggal_masuk"
                    value="{{ old('tanggal_masuk', $technician->tanggal_masuk) }}"
                    class="w-full border @error('tanggal_masuk') border-red-500 @else border-gray-300 @enderror rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500">
                @error('tanggal_masuk')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Foto Profil --}}
            <div class="md:col-span-2">
                <label class="block mb-2 font-semibold text-gray-700">
                    Foto Profil
                </label>
                <input
                    type="file"
                    name="foto"
                    class="w-full border @error('foto') border-red-500 @else border-gray-300 @enderror rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500">
                @error('foto')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        {{-- KETERANGAN --}}
        <h2 class="text-xl font-bold text-gray-800 border-b pb-2 mb-6">
            Keterangan
        </h2>

        <div class="mb-8">
            <textarea
                name="keterangan"
                rows="4"
                class="w-full border @error('keterangan') border-red-500 @else border-gray-300 @enderror rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('keterangan', $technician->keterangan) }}</textarea>
            @error('keterangan')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- ACTION BUTTONS --}}
        <div class="flex gap-3">
            <button
                type="submit"
                class="bg-blue-600 hover:bg-blue-700 text-white font-medium px-8 py-3 rounded-lg shadow transition">
                💾 Update Teknisi
            </button>

            <a href="{{ route('technicians.index') }}"
               class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-medium px-8 py-3 rounded-lg transition">
                Batal
            </a>
        </div>

    </form>
</div>

@endsection