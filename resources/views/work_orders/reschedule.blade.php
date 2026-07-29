@extends('layouts.admin')

@section('page-title', 'Jadwalkan Ulang Work Order')

@section('content')

<div class="max-w-3xl mx-auto py-6">

    <div class="mb-6">
        <a href="{{ route('work-orders.show', $workOrder) }}"
           class="text-blue-600 hover:underline">
            ← Kembali ke Detail Work Order
        </a>
    </div>

    <div class="bg-white rounded-lg border border-slate-200 shadow-sm p-6">

        <h1 class="text-xl font-bold text-slate-800 mb-2">
            📅 Jadwalkan Ulang Work Order
        </h1>

        <p class="text-sm text-slate-500 mb-6">
            {{ $workOrder->work_order_no }} —
            {{ $workOrder->registration->nama ?? '-' }}
        </p>

        @if ($errors->any())

            <div class="mb-5 rounded-lg bg-red-50 border border-red-200 p-4">

                <ul class="text-sm text-red-700 list-disc list-inside">

                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach

                </ul>

            </div>

        @endif

        <form method="POST"
              action="{{ route('work-orders.reschedule.update', $workOrder) }}"
              class="space-y-5">

            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">
                        Tanggal Baru
                    </label>

                    <input
                        type="date"
                        name="tanggal"
                        value="{{ old('tanggal', optional($workOrder->tanggal)->format('Y-m-d')) }}"
                        min="{{ now()->format('Y-m-d') }}"
                        class="w-full rounded-lg border border-slate-300 px-3 py-2"
                        required>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">
                        Jam Baru
                    </label>

                    <input
                        type="time"
                        name="jam"
                        value="{{ old('jam', \Carbon\Carbon::parse($workOrder->jam)->format('H:i')) }}"
                        class="w-full rounded-lg border border-slate-300 px-3 py-2"
                        required>
                </div>

            </div>

            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">
                    Tim Teknisi
                </label>

                <select
                    id="team_id"
                    name="team_id"
                    class="w-full rounded-lg border border-slate-300 px-3 py-2"
                    onchange="showTeamInfo()"
                    required>

                    <option value="">-- Pilih Tim Teknisi --</option>

                    @foreach($teams as $team)

                        <option
                            value="{{ $team->id }}"
                            data-name="{{ $team->nama }}"
                            data-leader="{{ $team->leader ?? '-' }}"
                            data-members="{{ $team->technicians
                                ->map(fn($technician) => $technician->user->name ?? 'Teknisi')
                                ->implode(', ') }}"
                            @selected(old('team_id', $workOrder->team_id) == $team->id)>

                            {{ $team->nama }}

                        </option>

                    @endforeach

                </select>
            </div>

            <div
                id="teamInfo"
                class="hidden rounded-lg border border-blue-200 bg-blue-50 p-4">

                <h3 class="font-semibold text-blue-900 mb-3">
                    👷 Informasi Tim Teknisi
                </h3>

                <div class="space-y-2 text-sm">

                    <div class="flex justify-between gap-4">
                        <span class="text-slate-500">Nama Tim</span>
                        <span id="teamName" class="font-semibold text-slate-800 text-right">-</span>
                    </div>

                    <div class="flex justify-between gap-4">
                        <span class="text-slate-500">Leader</span>
                        <span id="teamLeader" class="font-semibold text-slate-800 text-right">-</span>
                    </div>

                    <div class="flex justify-between gap-4">
                        <span class="text-slate-500">Anggota</span>
                        <span id="teamMembers" class="font-semibold text-slate-800 text-right">-</span>
                    </div>

                </div>
            </div>

            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">
                    Catatan Penjadwalan Ulang
                </label>

                <textarea
                    name="catatan"
                    rows="4"
                    class="w-full rounded-lg border border-slate-300 px-3 py-2"
                    placeholder="Contoh: Pelanggan meminta dijadwalkan ulang hari Senin sore.">{{ old('catatan') }}</textarea>
            </div>

            <div class="flex flex-col-reverse sm:flex-row justify-end gap-3 pt-4">

                <a href="{{ route('work-orders.show', $workOrder) }}"
                   class="rounded-lg border border-slate-300 px-5 py-2.5 text-center text-slate-700 hover:bg-slate-50">

                    Batal

                </a>

                <button
                    type="submit"
                    class="rounded-lg bg-amber-500 hover:bg-amber-600 text-white px-5 py-2.5 font-semibold">

                    📅 Simpan Jadwal Baru

                </button>

            </div>

        </form>

    </div>

</div>
<script>
    function showTeamInfo() {
        const select = document.getElementById('team_id');
        const option = select.options[select.selectedIndex];
        const info = document.getElementById('teamInfo');

        if (!option.value) {
            info.classList.add('hidden');
            return;
        }

        document.getElementById('teamName').textContent =
            option.dataset.name || '-';

        document.getElementById('teamLeader').textContent =
            option.dataset.leader || '-';

        document.getElementById('teamMembers').textContent =
            option.dataset.members || 'Belum ada anggota';

        info.classList.remove('hidden');
    }

    document.addEventListener('DOMContentLoaded', function () {
        showTeamInfo();
    });
</script>
@endsection