@extends('layouts.admin')

@section('content')

<div class="flex justify-between items-center mb-6">
    <div>
        <h1 class="text-4xl font-bold text-gray-800">
            Edit Router NAS
        </h1>
        <p class="text-gray-500">
            Ubah konfigurasi Router NAS Fahasa Net
        </p>
    </div>

    <a href="{{ route('routers.index') }}"
       class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-3 rounded-lg transition">
        ← Kembali
    </a>
</div>

<div class="bg-white rounded-xl shadow p-8">
    <form action="{{ route('routers.update', $router->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

            {{-- Nama Router --}}
            <div>
                <label class="block mb-2 font-semibold text-gray-700">
                    Nama Router
                </label>
                <input
                    type="text"
                    name="nama"
                    value="{{ old('nama', $router->nama) }}"
                    class="w-full border @error('nama') border-red-500 @else border-gray-300 @enderror rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500">
                @error('nama')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Kota --}}
            <div>
                <label class="block mb-2 font-semibold text-gray-700">
                    Kota
                </label>
                <input
                    type="text"
                    name="kota"
                    value="{{ old('kota', $router->kota) }}"
                    class="w-full border @error('kota') border-red-500 @else border-gray-300 @enderror rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500">
                @error('kota')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Hostname --}}
            <div>
                <label class="block mb-2 font-semibold text-gray-700">
                    Hostname
                </label>
                <input
                    type="text"
                    name="hostname"
                    value="{{ old('hostname', $router->hostname) }}"
                    class="w-full border @error('hostname') border-red-500 @else border-gray-300 @enderror rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500">
                @error('hostname')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- IP Router --}}
            <div>
                <label class="block mb-2 font-semibold text-gray-700">
                    IP Router
                </label>
                <input
                    type="text"
                    name="ip"
                    value="{{ old('ip', $router->ip) }}"
                    class="w-full border @error('ip') border-red-500 @else border-gray-300 @enderror rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500">
                @error('ip')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- VLAN --}}
            <div>
                <label class="block mb-2 font-semibold text-gray-700">
                    VLAN
                </label>
                <input
                    type="text"
                    name="vlan"
                    value="{{ old('vlan', $router->vlan) }}"
                    class="w-full border @error('vlan') border-red-500 @else border-gray-300 @enderror rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500">
                @error('vlan')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- VLAN Profile --}}
            <div>
                <label class="block mb-2 font-semibold text-gray-700">
                    VLAN Profile
                </label>
                <input
                    type="text"
                    name="vlan_profile"
                    value="{{ old('vlan_profile', $router->vlan_profile) }}"
                    class="w-full border @error('vlan_profile') border-red-500 @else border-gray-300 @enderror rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500">
                @error('vlan_profile')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- TCONT Profile --}}
            <div>
                <label class="block mb-2 font-semibold text-gray-700">
                    TCONT Profile
                </label>
                <input
                    type="text"
                    name="tcont_profile"
                    value="{{ old('tcont_profile', $router->tcont_profile) }}"
                    class="w-full border @error('tcont_profile') border-red-500 @else border-gray-300 @enderror rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500">
                @error('tcont_profile')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- ONU Type --}}
            <div>
                <label class="block mb-2 font-semibold text-gray-700">
                    ONU Type
                </label>
                <select
                    name="onu_type"
                    class="w-full border @error('onu_type') border-red-500 @else border-gray-300 @enderror rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="ALL-ONT" {{ old('onu_type', $router->onu_type) == 'ALL-ONT' ? 'selected' : '' }}>
                        ALL-ONT
                    </option>
                    <option value="ZTE" {{ old('onu_type', $router->onu_type) == 'ZTE' ? 'selected' : '' }}>
                        ZTE
                    </option>
                </select>
                @error('onu_type')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Security Mgmt --}}
            <div>
                <label class="block mb-2 font-semibold text-gray-700">
                    Security Mgmt
                </label>
                <input
                    type="text"
                    name="security_mgmt"
                    value="{{ old('security_mgmt', $router->security_mgmt) }}"
                    class="w-full border @error('security_mgmt') border-red-500 @else border-gray-300 @enderror rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500">
                @error('security_mgmt')
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
                    <option value="1" {{ old('status', $router->status) == 1 ? 'selected' : '' }}>
                        Aktif
                    </option>
                    <option value="0" {{ old('status', $router->status) == 0 ? 'selected' : '' }}>
                        Nonaktif
                    </option>
                </select>
                @error('status')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

        </div>

        {{-- Action Buttons --}}
        <div class="mt-8 flex gap-3">
            <button
                type="submit"
                class="bg-blue-600 hover:bg-blue-700 text-white font-medium px-8 py-3 rounded-lg shadow transition">
                💾 Update Router
            </button>

            <a href="{{ route('routers.index') }}"
               class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-medium px-8 py-3 rounded-lg transition">
                Batal
            </a>
        </div>

    </form>
</div>

@endsection