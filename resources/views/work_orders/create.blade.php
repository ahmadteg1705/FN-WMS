@extends('layouts.admin')

@section('page-title', 'Buat Work Order')

@section('content')

<div class="max-w-7xl mx-auto py-6">

    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800">
            Penjadwalan Teknisi
        </h1>

        <p class="text-sm text-gray-500">
            Buat penugasan teknisi untuk registrasi pelanggan.
        </p>
    </div>

    @if ($errors->any())
        <div class="mb-6 rounded-lg border border-red-200 bg-red-50 p-4">

            <div class="font-semibold text-red-700 mb-2">
                Terjadi kesalahan.
            </div>

            <ul class="list-disc ml-5 text-red-600">

                @foreach ($errors->all() as $error)

                    <li>{{ $error }}</li>

                @endforeach

            </ul>

        </div>
    @endif

    <form action="{{ route('work-orders.store') }}" method="POST">

        @csrf

        <input
            type="hidden"
            name="registration_id"
            value="{{ $registration->id }}"
        >

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2">

                <div class="bg-white rounded-xl shadow">

                    <div class="px-6 py-4 border-b">

                        <h2 class="text-lg font-semibold">
                            Informasi Registrasi
                        </h2>

                    </div>

                    <div class="p-6">

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                            <div>

                                <label class="text-sm text-gray-500">
                                    Nomor Registrasi
                                </label>

                                <div class="font-semibold">
                                    {{ $registration->registration_number }}
                                </div>

                            </div>

                            <div>

                                <label class="text-sm text-gray-500">
                                    Status
                                </label>

                                <div>

                                    <span class="px-3 py-1 rounded-full bg-blue-100 text-blue-700 text-sm">

                                        {{ $registration->status }}

                                    </span>

                                </div>

                            </div>

                            <div>

                                <label class="text-sm text-gray-500">
                                    Nama Pelanggan
                                </label>

                                <div class="font-semibold">

                                    {{ $registration->nama }}

                                </div>

                            </div>

                            <div>

                                <label class="text-sm text-gray-500">
                                    Nomor HP
                                </label>

                                <div>

                                    {{ $registration->telepon }}

                                </div>

                            </div>

                            <div>

                                <label class="text-sm text-gray-500">
                                    Marketing
                                </label>

                                <div>

                                    {{ optional($registration->marketing->user)->name }}

                                </div>

                            </div>

                            <div>

                                <label class="text-sm text-gray-500">
                                    Paket
                                </label>

                                <div>

                                    {{ optional($registration->package)->nama }}

                                </div>

                            </div>

                            <div class="md:col-span-2">

                                <label class="text-sm text-gray-500">
                                    Alamat
                                </label>

                                <div>

                                    {{ $registration->alamat }}

                                </div>

                            </div>

                            <div>

                                <label class="text-sm text-gray-500">
                                    ODP
                                </label>

                                <div>

                                    {{ optional($registration->odp)->nama }}

                                </div>

                            </div>

                        </div>

                    </div>

                </div>

            </div>
                        {{-- Card Penjadwalan --}}
            <div class="lg:col-span-1">

                <div class="bg-white rounded-xl shadow">

                    <div class="px-6 py-4 border-b">

                        <h2 class="text-lg font-semibold">
                            Penjadwalan Teknisi
                        </h2>

                    </div>

                    <div class="p-6 space-y-5">

                        {{-- Team --}}
                        <div>

                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Tim Teknisi
                            </label>

                            <select
                                name="team_id"
                                id="team_id"
                                class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                                required
                            >

                                <option value="">
                                    -- Pilih Tim --
                                </option>

                                @foreach($teams as $team)

                                    <option
                                        value="{{ $team->id }}"
                                        {{ old('team_id') == $team->id ? 'selected' : '' }}
                                    >
                                        {{ $team->nama }}
                                    </option>

                                @endforeach

                            </select>

                        </div>

                        {{-- Teknisi --}}
                        <div>

                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Teknisi
                            </label>

                            <div id="team-info" class="hidden rounded-lg border border-blue-200 bg-blue-50 p-4">

                                <div class="mb-3">
                                    <div class="text-xs font-semibold text-slate-500">
                                        Leader
                                    </div>

                                    <div
                                        id="leader-name"
                                        class="font-semibold text-slate-800">
                                        -
                                    </div>
                                </div>

                                <hr class="my-3">

                                <div>

                                    <div class="text-xs font-semibold text-slate-500 mb-2">
                                        Anggota Tim
                                    </div>

                                    <ul
                                        id="team-members"
                                        class="list-disc list-inside text-sm text-slate-700">

                                    </ul>

                                </div>

                            </div>

                        </div>

                        {{-- Tanggal --}}
                        <div>

                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Tanggal Pengerjaan
                            </label>

                            <input
                                type="date"
                                name="tanggal"
                                value="{{ old('tanggal', now()->format('Y-m-d')) }}"
                                class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                                required
                            >

                        </div>

                        {{-- Jam --}}
                        <div>

                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Jam
                            </label>

                            <input
                                type="time"
                                name="jam"
                                value="{{ old('jam') }}"
                                class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                                required
                            >

                        </div>

                        {{-- Prioritas --}}
                        <div>

                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Prioritas
                            </label>

                            <select
                                name="prioritas"
                                class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                                required
                            >

                                <option value="Normal" selected>
                                    Normal
                                </option>

                                <option value="Rendah">
                                    Rendah
                                </option>

                                <option value="Tinggi">
                                    Tinggi
                                </option>

                                <option value="Urgent">
                                    Urgent
                                </option>

                            </select>

                        </div>

                        {{-- Catatan --}}
                        <div>

                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Catatan
                            </label>

                            <textarea
                                name="catatan"
                                rows="4"
                                class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                            >{{ old('catatan') }}</textarea>

                        </div>

                        {{-- Tombol --}}
                        <div class="pt-4 flex justify-between">

                            <a
                                href="{{ route('registrations.show', $registration) }}"
                                class="px-5 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-lg"
                            >
                                Batal
                            </a>

                            <button
                                type="submit"
                                class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg"
                            >
                                Simpan Work Order
                            </button>

                        </div>

                    </div>

                </div>

            </div>

        </div>
    </form>

</div>

@push('scripts')
<script>

document.addEventListener('DOMContentLoaded', function () {

    const teamSelect = document.getElementById('team_id');
    const teamInfo = document.getElementById('team-info');
    const leaderName = document.getElementById('leader-name');
    const memberList = document.getElementById('team-members');

    function loadTeam(teamId)
    {
        if (!teamId) {

            teamInfo.classList.add('hidden');
            leaderName.textContent = '-';
            memberList.innerHTML = '';

            return;
        }

        fetch(`/teams/${teamId}/members`)
            .then(response => response.json())
            .then(data => {

                leaderName.textContent = data.leader ?? '-';

                memberList.innerHTML = '';

                data.members.forEach(function(member){

                    const li = document.createElement('li');
                    li.textContent = member;
                    memberList.appendChild(li);

                });

                teamInfo.classList.remove('hidden');

            })
            .catch(function(){

                leaderName.textContent = '-';
                memberList.innerHTML = '';
                teamInfo.classList.add('hidden');

            });

    }

    teamSelect.addEventListener('change', function () {

        loadTeam(this.value);

    });

    if(teamSelect.value){

        loadTeam(teamSelect.value);

    }

});

</script>
@endpush

@endsection