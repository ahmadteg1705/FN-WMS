@extends('layouts.admin')

@section('content')
<div class="space-y-5">
    <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
        <div>
            <div class="flex flex-wrap items-center gap-2">
                <h1 class="text-2xl font-bold text-slate-800">{{ $customer->nama }}</h1>
                <span class="rounded-full px-3 py-1 text-xs font-bold
                    {{ $customer->status === 'Aktif'
                        ? 'bg-emerald-100 text-emerald-800'
                        : 'bg-slate-100 text-slate-700' }}">
                    {{ $customer->status }}
                </span>
            </div>
            <p class="mt-1 text-sm text-slate-500">{{ $customer->nomor_pelanggan }}</p>
        </div>

        <div class="flex gap-2">
            <a href="{{ route('customers.index') }}"
               class="rounded-lg border border-slate-300 bg-white px-4 py-2.5 font-semibold">
                Kembali
            </a>
            <a href="{{ route('customers.edit', $customer) }}"
               class="rounded-lg bg-amber-500 px-4 py-2.5 font-bold text-white">
                Edit
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-emerald-800">
            {{ session('success') }}
        </div>
    @endif

    <div class="grid gap-5 xl:grid-cols-2">
        <section class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
            <h2 class="mb-4 text-lg font-bold">Informasi Pelanggan</h2>
            <dl class="space-y-3">
                @foreach([
                    ['Nomor Pelanggan', $customer->nomor_pelanggan],
                    ['Nama', $customer->nama],
                    ['NIK', $customer->nik],
                    ['Telepon', $customer->telepon],
                    ['Email', $customer->email],
                    ['Tanggal Registrasi', optional($customer->tanggal_registrasi)->format('d-m-Y') ?? $customer->tanggal_registrasi],
                    ['Alamat', $customer->alamat],
                ] as [$label, $value])
                    <div class="grid gap-1 border-b border-slate-100 pb-3 sm:grid-cols-[170px_1fr]">
                        <dt class="text-sm text-slate-500">{{ $label }}</dt>
                        <dd class="font-semibold text-slate-800">{{ $value ?: '-' }}</dd>
                    </div>
                @endforeach
            </dl>

            @if($customer->latitude && $customer->longitude)
                <a target="_blank"
                   href="https://www.google.com/maps?q={{ $customer->latitude }},{{ $customer->longitude }}"
                   class="mt-4 inline-flex rounded-lg bg-blue-50 px-4 py-2 font-bold text-blue-700">
                    Buka Google Maps
                </a>
            @endif
        </section>

        <section class="rounded-xl border border-blue-200 bg-blue-50/50 p-5 shadow-sm">
            <h2 class="mb-4 text-lg font-bold text-blue-900">Informasi Layanan dan PPPoE</h2>
            <dl class="space-y-3">
                @foreach([
                    ['Paket Internet', $customer->paket],
                    ['Router NAS', $customer->nas],
                    ['ODP', $customer->odp],
                    ['Nomor ONU', $customer->onu_number],
                    ['SN Modem', $customer->sn_modem],
                    ['Username PPPoE', $customer->pppoe_username],
                ] as [$label, $value])
                    <div class="grid gap-1 border-b border-blue-100 pb-3 sm:grid-cols-[170px_1fr]">
                        <dt class="text-sm text-blue-700">{{ $label }}</dt>
                        <dd class="break-all font-mono font-bold text-slate-900">{{ $value ?: '-' }}</dd>
                    </div>
                @endforeach

                <div class="grid gap-1 sm:grid-cols-[170px_1fr]">
                    <dt class="text-sm text-blue-700">Password PPPoE</dt>
                    <dd>
                        <span id="pppoe-password" class="break-all font-mono font-bold text-slate-900">
                            ••••••••••
                        </span>
                        <button type="button" onclick="togglePppoePassword()"
                                class="ml-2 text-xs font-bold text-blue-700 hover:underline">
                            Tampilkan/Sembunyikan
                        </button>
                    </dd>
                </div>
            </dl>
        </section>
    </div>

    <section class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
        <h2 class="mb-4 text-lg font-bold">Dokumentasi Pelanggan</h2>

        <div class="grid gap-5 md:grid-cols-2">
            <div>
                <p class="mb-2 text-sm font-bold text-slate-600">Foto KTP</p>
                @if($customer->foto_ktp)
                    <a href="{{ Storage::url($customer->foto_ktp) }}" target="_blank">
                        <img src="{{ Storage::url($customer->foto_ktp) }}"
                             class="h-72 w-full rounded-xl border border-slate-200 object-contain bg-slate-50">
                    </a>
                @else
                    <div class="flex h-48 items-center justify-center rounded-xl bg-slate-100 text-slate-500">
                        Foto KTP belum tersedia
                    </div>
                @endif
            </div>

            <div>
                <p class="mb-2 text-sm font-bold text-slate-600">Foto Rumah</p>
                @if($customer->foto_rumah)
                    <a href="{{ Storage::url($customer->foto_rumah) }}" target="_blank">
                        <img src="{{ Storage::url($customer->foto_rumah) }}"
                             class="h-72 w-full rounded-xl border border-slate-200 object-contain bg-slate-50">
                    </a>
                @else
                    <div class="flex h-48 items-center justify-center rounded-xl bg-slate-100 text-slate-500">
                        Foto rumah belum tersedia
                    </div>
                @endif
            </div>
        </div>

        <div class="mt-5 rounded-xl bg-slate-50 p-4">
            <p class="text-xs font-bold text-slate-500">CATATAN</p>
            <p class="mt-2 whitespace-pre-wrap text-slate-800">{{ $customer->catatan ?: '-' }}</p>
        </div>
    </section>
</div>

<script>
function togglePppoePassword() {
    const element = document.getElementById('pppoe-password');
    const realPassword = @json($customer->pppoe_password ?: '-');
    element.textContent = element.dataset.visible === '1' ? '••••••••••' : realPassword;
    element.dataset.visible = element.dataset.visible === '1' ? '0' : '1';
}
</script>
@endsection
