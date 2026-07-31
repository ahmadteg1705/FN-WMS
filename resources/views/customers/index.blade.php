@extends('layouts.admin')

@section('content')
<div class="space-y-5">
    <div class="flex flex-col gap-3 xl:flex-row xl:items-center xl:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Data Pelanggan</h1>
            <p class="mt-1 text-sm text-slate-500">Kelola pelanggan aktif, data jaringan, PPPoE, dan dokumentasi.</p>
        </div>

        <div class="flex flex-wrap gap-2">
            <a href="{{ route('customers.template') }}"
               class="rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700">
                Template Import
            </a>

            <button type="button" onclick="document.getElementById('import-modal').classList.remove('hidden')"
                    class="rounded-lg bg-amber-500 px-4 py-2.5 text-sm font-bold text-white">
                Import
            </button>

            <a href="{{ route('customers.export', request()->query()) }}"
               class="rounded-lg bg-emerald-600 px-4 py-2.5 text-sm font-bold text-white">
                Export
            </a>

            <a href="{{ route('customers.create') }}"
               class="rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-bold text-white">
                + Tambah Pelanggan
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-emerald-800">
            {{ session('success') }}
        </div>
    @endif

    @if(session('import_errors'))
        <div class="rounded-xl border border-amber-200 bg-amber-50 p-4 text-sm text-amber-900">
            <p class="font-bold">Sebagian baris gagal:</p>
            @foreach(session('import_errors') as $error)
                <p class="mt-1">{{ $error }}</p>
            @endforeach
        </div>
    @endif

    <section class="grid grid-cols-2 gap-3 lg:grid-cols-4">
        <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
            <p class="text-xs text-slate-500">Total Pelanggan</p>
            <p class="mt-1 text-3xl font-black">{{ $statistics['total'] }}</p>
        </div>
        <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
            <p class="text-xs text-slate-500">Aktif</p>
            <p class="mt-1 text-3xl font-black text-emerald-600">{{ $statistics['active'] }}</p>
        </div>
        <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
            <p class="text-xs text-slate-500">Nonaktif</p>
            <p class="mt-1 text-3xl font-black text-red-600">{{ $statistics['inactive'] }}</p>
        </div>
        <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
            <p class="text-xs text-slate-500">Memiliki PPPoE</p>
            <p class="mt-1 text-3xl font-black text-blue-600">{{ $statistics['with_pppoe'] }}</p>
        </div>
    </section>

    <form method="GET" class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
        <div class="grid gap-3 md:grid-cols-2 xl:grid-cols-6">
            <input name="q" value="{{ $search }}"
                   class="rounded-lg border-slate-300 xl:col-span-2"
                   placeholder="Nama, nomor pelanggan, NIK, telepon, SN, PPPoE">

            <select name="status" class="rounded-lg border-slate-300">
                <option value="">Semua Status</option>
                @foreach($filters['statuses'] as $option)
                    <option value="{{ $option }}" @selected($status === $option)>{{ $option }}</option>
                @endforeach
            </select>

            <select name="nas" class="rounded-lg border-slate-300">
                <option value="">Semua NAS</option>
                @foreach($filters['nasList'] as $option)
                    <option value="{{ $option }}" @selected($nas === $option)>{{ $option }}</option>
                @endforeach
            </select>

            <select name="odp" class="rounded-lg border-slate-300">
                <option value="">Semua ODP</option>
                @foreach($filters['odpList'] as $option)
                    <option value="{{ $option }}" @selected($odp === $option)>{{ $option }}</option>
                @endforeach
            </select>

            <select name="paket" class="rounded-lg border-slate-300">
                <option value="">Semua Paket</option>
                @foreach($filters['packageList'] as $option)
                    <option value="{{ $option }}" @selected($paket === $option)>{{ $option }}</option>
                @endforeach
            </select>
        </div>

        <div class="mt-3 flex justify-end gap-2">
            <a href="{{ route('customers.index') }}"
               class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-semibold">
                Reset
            </a>
            <button class="rounded-lg bg-slate-900 px-5 py-2 text-sm font-bold text-white">
                Terapkan
            </button>
        </div>
    </form>

    <section class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-4 py-3 text-left">Pelanggan</th>
                        <th class="px-4 py-3 text-left">Layanan</th>
                        <th class="px-4 py-3 text-left">Jaringan</th>
                        <th class="px-4 py-3 text-left">PPPoE</th>
                        <th class="px-4 py-3 text-left">Status</th>
                        <th class="px-4 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($customers as $customer)
                        <tr class="hover:bg-slate-50">
                            <td class="px-4 py-4">
                                <p class="font-bold text-slate-800">{{ $customer->nama }}</p>
                                <p class="text-xs text-slate-500">{{ $customer->nomor_pelanggan }}</p>
                                <p class="text-xs text-slate-500">{{ $customer->telepon }}</p>
                            </td>
                            <td class="px-4 py-4">
                                <p class="font-semibold">{{ $customer->paket ?: '-' }}</p>
                                <p class="text-xs text-slate-500">{{ $customer->nas ?: '-' }}</p>
                            </td>
                            <td class="px-4 py-4">
                                <p class="font-semibold">{{ $customer->odp ?: '-' }}</p>
                                <p class="font-mono text-xs text-slate-500">
                                    ONU {{ $customer->onu_number ?: '-' }} · {{ $customer->sn_modem ?: '-' }}
                                </p>
                            </td>
                            <td class="px-4 py-4">
                                <p class="font-mono font-semibold">{{ $customer->pppoe_username ?: '-' }}</p>
                            </td>
                            <td class="px-4 py-4">
                                <span class="rounded-full px-3 py-1 text-xs font-bold
                                    {{ $customer->status === 'Aktif'
                                        ? 'bg-emerald-100 text-emerald-800'
                                        : 'bg-slate-100 text-slate-700' }}">
                                    {{ $customer->status ?: '-' }}
                                </span>
                            </td>
                            <td class="px-4 py-4">
                                <div class="flex justify-end gap-2">
                                    <a href="{{ route('customers.show', $customer) }}"
                                       class="rounded-lg bg-blue-50 px-3 py-2 text-xs font-bold text-blue-700">
                                        Detail
                                    </a>
                                    <a href="{{ route('customers.edit', $customer) }}"
                                       class="rounded-lg bg-amber-50 px-3 py-2 text-xs font-bold text-amber-700">
                                        Edit
                                    </a>
                                    <form method="POST" action="{{ route('customers.destroy', $customer) }}"
                                          onsubmit="return confirm('Hapus pelanggan ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="rounded-lg bg-red-50 px-3 py-2 text-xs font-bold text-red-700">
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-14 text-center text-slate-500">
                                Data pelanggan tidak ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($customers->hasPages())
            <div class="border-t border-slate-100 p-4">{{ $customers->links() }}</div>
        @endif
    </section>
</div>

<div id="import-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 p-4">
    <div class="mx-auto mt-24 max-w-lg rounded-xl bg-white p-6 shadow-xl">
        <div class="flex items-center justify-between">
            <h2 class="text-lg font-bold">Import Pelanggan</h2>
            <button type="button" onclick="document.getElementById('import-modal').classList.add('hidden')"
                    class="text-2xl text-slate-400">×</button>
        </div>

        <form method="POST" action="{{ route('customers.import') }}" enctype="multipart/form-data" class="mt-5">
            @csrf
            <input name="file" type="file" accept=".csv,.txt"
                   class="w-full rounded-lg border border-slate-300 p-3" required>
            <p class="mt-2 text-xs text-slate-500">Gunakan template CSV. Maksimum 5 MB.</p>

            <div class="mt-5 flex justify-end gap-2">
                <button type="button" onclick="document.getElementById('import-modal').classList.add('hidden')"
                        class="rounded-lg border border-slate-300 px-4 py-2 font-semibold">
                    Batal
                </button>
                <button class="rounded-lg bg-amber-500 px-5 py-2 font-bold text-white">
                    Mulai Import
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
