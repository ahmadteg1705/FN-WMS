@extends('layouts.admin')

@section('page-title', 'Detail Work Order')

@section('content')
@if(session('success'))

<div class="mb-4 rounded-lg bg-green-100 border border-green-300 p-4 text-green-800">

    {{ session('success') }}

</div>

@endif
<div class="max-w-full mx-auto py-6">

    {{-- ==========================================================
        TOP BAR (Kembali & Status)
    ========================================================== --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
        
        <a href="{{ route('work-orders.index') }}" class="inline-flex items-center text-sm text-blue-600 bg-white border border-slate-200 rounded-md px-4 py-2 shadow-sm hover:bg-slate-50 transition">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Kembali ke Daftar
        </a>

        <div class="flex items-center gap-3 bg-amber-50 border border-amber-100 rounded-md px-4 py-2 shadow-sm">
            <span class="text-sm font-semibold text-slate-700">Status Work Order</span>
            @php
                $statusColor = match(strtolower($workOrder->status)) {
                    'pending' => 'bg-amber-100 text-amber-700 border-amber-200',
                    'on progress', 'accepted' => 'bg-blue-100 text-blue-700 border-blue-200',
                    'completed', 'selesai' => 'bg-green-100 text-green-700 border-green-200',
                    'cancelled', 'dibatalkan' => 'bg-red-100 text-red-700 border-red-200',
                    default => 'bg-slate-100 text-slate-700 border-slate-200'
                };
            @endphp
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-md text-xs font-bold border {{ $statusColor }}">
                @if(strtolower($workOrder->status) == 'pending') 🕒 @endif
                {{ ucfirst($workOrder->status) }}
            </span>
        </div>

    </div>

    {{-- ==========================================================
        HEADER TITLE
    ========================================================== --}}
    <div class="flex items-center gap-3 mb-6">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-7 h-7 text-slate-800" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
        </svg>
        <h1 class="text-2xl font-bold text-slate-800">Detail Work Order</h1>
        <span class="bg-emerald-50 text-emerald-600 px-3 py-1 rounded text-sm font-semibold tracking-wide border border-emerald-100">
            #{{ $workOrder->work_order_no }}
        </span>
    </div>

    {{-- ==========================================================
        TOP ROW CARDS (3 Columns)
    ========================================================== --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-6">

        {{-- CARD 1: Informasi Penjadwalan --}}
        <div class="bg-white rounded-lg border border-slate-200 shadow-sm p-5">
            <div class="flex items-center gap-2 mb-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                <h2 class="font-semibold text-slate-800">Informasi Penjadwalan</h2>
            </div>
            
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <div class="text-xs text-slate-500 mb-1">Tim Teknisi</div>
                    <div class="text-xl font-bold text-slate-800 tracking-tight">{{ $workOrder->team->nama ?? '-' }}</div>
                    
                    <div class="text-xs text-slate-500 mt-4 mb-1">Leader</div>
                    <div class="flex items-center gap-2">
                        <div class="w-6 h-6 rounded-full bg-slate-100 flex items-center justify-center text-slate-500 text-xs">👤</div>
                        <span class="text-sm font-semibold text-slate-800">{{ $workOrder->team->leader ?? '-' }}</span>
                    </div>
                </div>
                <div>
                <div class="text-xs text-slate-500 mb-1">Anggota Tim</div>

                <ul class="text-sm text-slate-800 space-y-1 list-disc list-inside">

                    @forelse($workOrder->team->technicians as $technician)
                        <li>{{ $technician->user->name }}</li>
                    @empty
                        <li>Belum ada anggota tim</li>
                    @endforelse

                </ul>
            </div>
            </div>
        </div>

        {{-- CARD 2: Jadwal Pekerjaan --}}
        <div class="bg-white rounded-lg border border-slate-200 shadow-sm p-5">
            <div class="flex items-center gap-2 mb-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <h2 class="font-semibold text-slate-800">Jadwal Pekerjaan</h2>
            </div>

            <div class="space-y-3 text-sm">
                <div class="flex justify-between items-center border-b border-slate-100 pb-2">
                    <span class="text-slate-500">Tanggal</span>
                    <div class="flex items-center gap-2 font-medium text-slate-800">
                        {{ $workOrder->tanggal->format('d F Y') }}
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                    </div>
                </div>
                <div class="flex justify-between items-center border-b border-slate-100 pb-2">
                    <span class="text-slate-500">Jam</span>
                    <div class="flex items-center gap-2 font-medium text-slate-800">
                        {{ \Carbon\Carbon::parse($workOrder->jam)->format('H:i') }}
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    </div>
                </div>
                <div class="flex justify-between items-center pb-1">
                    <span class="text-slate-500">Prioritas</span>
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded bg-blue-50 text-blue-700 text-xs font-semibold border border-blue-100">
                        <span class="w-1.5 h-1.5 rounded-full bg-blue-600"></span>
                        {{ $workOrder->prioritas }}
                    </span>
                </div>
            </div>
        </div>

        {{-- CARD 3: Informasi Work Order --}}
        <div class="bg-white rounded-lg border border-slate-200 shadow-sm p-5">
            <div class="flex items-center gap-2 mb-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <h2 class="font-semibold text-slate-800">Informasi Work Order</h2>
            </div>

            <div class="space-y-2.5 text-sm">
                <div class="flex justify-between">
                    <span class="text-slate-500">No Work Order</span>
                    <span class="font-medium text-slate-800">{{ $workOrder->work_order_no }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-500">Dibuat Oleh</span>
                    <span class="font-medium text-slate-800">{{ $workOrder->assignedBy->name ?? 'Admin Fahasa' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-500">Dibuat Pada</span>
                    <span class="font-medium text-slate-800">{{ $workOrder->created_at->format('d M Y H:i') }}</span>
                </div>
                <hr class="my-4">

@role('Super Admin|Admin|NOC')
<div>
    <h3 class="font-semibold text-slate-800 mb-3">
        Akun PPPoE
    </h3>
    @if($workOrder->account)
        <div class="flex justify-between mb-2">
            <span class="text-slate-500">
                Username
            </span>
            <span class="font-medium">
                {{ $workOrder->account->username }}
            </span>
        </div>
        <div class="flex justify-between items-center">
            <span class="text-slate-500">
                Password
            </span>
            <div class="text-right">
                <span
                    id="passwordText"
                    class="font-medium">
                    ••••••••••
                </span>
                <br>
                <button
                    type="button"
                    onclick="togglePassword()"
                    class="text-blue-600 text-xs hover:underline">
                    👁 Tampilkan Password
                </button>
            </div>
        </div>
    @else
        <div class="text-slate-500 italic">
            Belum dibuat.
        </div>
    @endif
</div>
@endrole

<hr class="my-4">
                <div class="pt-2">
                    <span class="text-slate-500 block mb-1">Catatan Admin</span>
                    <p class="text-slate-800 leading-relaxed text-sm">
                        {{ $workOrder->catatan ?: 'Tidak ada catatan.' }}
                    </p>
                </div>
            </div>
        </div>

    </div>

    {{-- ==========================================================
        BOTTOM ROW CARDS (2 Columns: Data Pelanggan & Aksi)
    ========================================================== --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- CARD 4: Data Pelanggan (Takes 2 Columns) --}}
        <div class="lg:col-span-2 bg-white rounded-lg border border-slate-200 shadow-sm p-5">
            <div class="flex items-center gap-2 mb-5">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
                <h2 class="font-semibold text-slate-800">Data Pelanggan</h2>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-y-4 gap-x-8 text-sm">
                <div class="flex justify-between">
                    <span class="text-slate-500">No Registrasi</span>
                    <span class="font-medium text-slate-800">{{ $workOrder->registration->registration_number ?? '-' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-500">ODP</span>
                    <span class="font-medium text-slate-800">{{ $workOrder->registration->odp->nama ?? '-' }}</span>
                </div>

                <div class="flex justify-between">
                    <span class="text-slate-500">Nama</span>
                    <span class="font-medium text-slate-800">{{ $workOrder->registration->nama }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-500">Paket</span>
                    <span class="font-medium text-slate-800">{{ $workOrder->registration->package->nama ?? '-' }}</span>
                </div>

                <div class="flex justify-between">
                    <span class="text-slate-500">Telepon</span>
                    <span class="font-medium text-slate-800">{{ $workOrder->registration->telepon }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-500">Tanggal Registrasi</span>
                    <span class="font-medium text-slate-800">{{ optional($workOrder->registration->created_at)->format('d M Y H:i') }}</span>
                </div>

                <div class="flex justify-between md:col-span-1">
                    <span class="text-slate-500">Alamat</span>
                    <span class="font-medium text-slate-800 text-right w-2/3">{{ $workOrder->registration->alamat }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-500">Status Registrasi</span>
                    <span class="inline-block bg-emerald-50 text-emerald-600 px-2.5 py-1 rounded text-xs font-semibold border border-emerald-100">
                        {{ $workOrder->registration->status ?? 'Dijadwalkan' }}
                    </span>
                </div>
            </div>
        </div>
</div>
        {{-- CARD 5: Aksi --}}
        <div class="bg-white rounded-lg border border-slate-200 shadow-sm p-5">
            <h2 class="font-semibold text-slate-800 mb-5">
                Aksi Work Order
            </h2>
            <div class="flex flex-col gap-3">

        @if(auth()->user()->hasRole('Teknisi'))

            @php
                $status = trim($workOrder->status);
            @endphp

            @if(in_array($status, ['Pending','Draft','Menunggu Diterima Teknisi']))

            <form method="POST"
                    action="{{ route('work-orders.accept', $workOrder) }}">
                    @csrf
                    <button
                        type="submit"
                        class="w-full rounded-lg bg-green-600 hover:bg-green-700 text-white py-3 font-semibold">
                        ✅ Terima Work Order
                    </button>
                </form>
            @elseif($status=='Diterima Teknisi')
                <form method="POST"
                    action="{{ route('work-orders.preparation', $workOrder) }}">
                    @csrf
                    <button
                        type="submit"
                        class="w-full rounded-lg bg-blue-600 hover:bg-blue-700 text-white py-3 font-semibold">
                        🔧 Persiapan
                    </button>
                </form>
            @elseif($status=='Persiapan')
                <form method="POST"
                    action="{{ route('work-orders.depart', $workOrder) }}">
                    @csrf
                    <button
                        type="submit"
                        class="w-full rounded-lg bg-orange-600 hover:bg-orange-700 text-white py-3 font-semibold">
                        🚗 Menuju Lokasi
                    </button>
                </form>
        @elseif($status=='Menuju Lokasi')
                <form method="POST"
                    action="{{ route('work-orders.arrive', $workOrder) }}">
                    @csrf
                    <button
                        type="submit"
                        class="w-full rounded-lg bg-cyan-600 hover:bg-cyan-700 text-white py-3 font-semibold">
                        📍 Sampai Lokasi
                    </button>
                </form>
            @elseif($status=='Di Lokasi')
            <div class="space-y-3">
                {{-- Mulai Instalasi --}}
                <form method="POST"
                    action="{{ route('work-orders.installation', $workOrder) }}">
                    @csrf

                    <button
                        type="submit"
                        class="w-full rounded-lg bg-indigo-600 hover:bg-indigo-700 text-white py-3 font-semibold">
                        🔨 Mulai Instalasi
                    </button>
                </form>
                {{-- Pelanggan Tidak Ditemui --}}
                <form method="POST"
                    action="{{ route('work-orders.customer-not-found', $workOrder) }}">
                    @csrf
                    <button
                        type="submit"
                        class="w-full rounded-lg bg-red-600 hover:bg-red-700 text-white py-3 font-semibold">
                        🚫 Pelanggan Tidak Ditemui
                    </button>
                </form>
                {{-- Pelanggan Minta Dijadwalkan Ulang --}}
                <form method="POST"
                    action="{{ route('work-orders.reschedule-request', $workOrder) }}">
                    @csrf
                    <button
                        type="submit"
                        class="w-full rounded-lg bg-amber-500 hover:bg-amber-600 text-white py-3 font-semibold">
                        📅 Pelanggan Minta Dijadwalkan Ulang
                    </button>
                </form>
            </div>
            
            @elseif($status=='Pelanggan Tidak Ditemui')
                <div class="w-full rounded-lg bg-red-100 text-red-800 py-3 px-4 text-center font-semibold">
                    🚫 Pelanggan tidak ditemui. Menunggu tindakan Admin.
                </div>
            @elseif($status=='Dijadwalkan Ulang')
                <div class="w-full rounded-lg bg-amber-100 text-amber-800 py-3 px-4 text-center font-semibold">
                    📅 Menunggu Admin menjadwalkan ulang.
                </div>

            @elseif($status=='Instalasi')
                <div class="space-y-3">

                    <div class="rounded-lg border border-blue-200 bg-blue-50 p-4">
                        <p class="font-semibold text-blue-800">
                            Instalasi sedang berlangsung
                        </p>

                        <p class="mt-1 text-sm text-blue-700">
                            Lengkapi foto dokumentasi, checklist pekerjaan, lokasi,
                            dan catatan teknisi pada halaman instalasi.
                        </p>
                    </div>

                    <a
                        href="{{ route('work-order-installation.edit', $workOrder) }}"
                        class="block w-full rounded-lg bg-indigo-600 px-4 py-3 text-center font-semibold text-white hover:bg-indigo-700"
                    >
                        🔨 Buka Form Instalasi
                    </a>

                </div>
            @elseif($status=='Menunggu Verifikasi')
                @if(auth()->user()->hasAnyRole(['Super Admin', 'Admin']))
                        <form method="POST"
                            action="{{ route('work-orders.complete', $workOrder) }}">
                            @csrf
                            <button
                                type="submit"
                                class="w-full rounded-lg bg-green-600 hover:bg-green-700 text-white py-3 font-semibold">
                                ✅ Verifikasi & Selesaikan
                            </button>
                        </form>
                        @else
                    <div class="w-full rounded-lg bg-yellow-100 text-yellow-800 py-3 px-4 text-center font-semibold">
                        ⏳ Menunggu verifikasi Admin
                    </div>
                @endif
            @endif
        @endif
        @if(
    auth()->user()->hasAnyRole(['Super Admin', 'Admin']) &&
    in_array(trim($workOrder->status), [
        'Pelanggan Tidak Ditemui',
        'Dijadwalkan Ulang'
    ])
)

    <a href="{{ route('work-orders.reschedule', $workOrder) }}"
       class="w-full block text-center rounded-lg bg-amber-500 hover:bg-amber-600 text-white py-3 font-semibold">

        📅 Jadwalkan Ulang

    </a>

@endif
</div>
            </div>
        </div>

    </div>

</div>
<div id="pppoeModal"
     class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50">

    <div class="bg-white rounded-lg shadow-lg w-full max-w-lg">

        <div class="border-b px-6 py-4">
            <h2 class="text-lg font-bold">
                {{ $workOrder->account ? 'Edit Akun PPPoE' : 'Tambah Akun PPPoE' }}
            </h2>
        </div>

        @if($workOrder->account)

            <form
                action="{{ route('work-order-accounts.update',$workOrder->account) }}"
                method="POST">

                @csrf
                @method('PUT')

        @else

            <form
                action="{{ route('work-order-accounts.store') }}"
                method="POST">

                @csrf

                <input
                    type="hidden"
                    name="work_order_id"
                    value="{{ $workOrder->id }}">

        @endif
        @if ($errors->any())

        <div class="mb-4 rounded-lg bg-red-50 border border-red-200 p-3">

            <ul class="text-sm text-red-700 list-disc list-inside">

                @foreach ($errors->all() as $error)

                    <li>{{ $error }}</li>

                @endforeach

            </ul>

        </div>

        @endif
        <div class="p-6 space-y-4">

            <div>
                <label class="block text-sm font-semibold mb-2">
                    Username
                </label>

                <input
                    type="text"
                    name="username"
                    value="{{ old('username',$workOrder->account->username ?? '') }}"
                    class="w-full border rounded-md px-3 py-2"
                    required>
            </div>

            <div>
                <label class="block text-sm font-semibold mb-2">
                    Password
                </label>

                <input
                    id="pppoe_password"
                    type="text"
                    name="password"
                    value="{{ old('password',$workOrder->account->password ?? '') }}"
                    class="w-full border rounded-md px-3 py-2"
                    required>
            </div>

            <button
                type="button"
                onclick="generatePassword()"
                class="bg-slate-200 hover:bg-slate-300 rounded px-3 py-2 text-sm">

                Generate Password
            </button>

        </div>

        <div class="border-t px-6 py-4 flex justify-end gap-2">

            <button
                type="button"
                onclick="closePPPoEModal()"
                class="px-4 py-2 rounded border">

                Batal
            </button>

            <button
                type="submit"
                class="bg-blue-600 text-white px-4 py-2 rounded">

                Simpan
            </button>

        </div>

        </form>

    </div>

</div>
<script>

function openPPPoEModal(){
    document.getElementById('pppoeModal').classList.remove('hidden');
    document.getElementById('pppoeModal').classList.add('flex');
}

function closePPPoEModal(){
    document.getElementById('pppoeModal').classList.remove('flex');
    document.getElementById('pppoeModal').classList.add('hidden');
}

function generatePassword(){

    const chars =
        'ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnopqrstuvwxyz23456789';

    let password='';

    for(let i=0;i<10;i++){

        password += chars.charAt(
            Math.floor(Math.random()*chars.length)
        );

    }

    document
        .getElementById('pppoe_password')
        .value=password;

}
function togglePassword(){

    let el = document.getElementById('passwordText');

    if(el.innerHTML === '••••••••••'){

        el.innerHTML = "{{ $workOrder->account->password ?? '' }}";

    }else{

        el.innerHTML = "••••••••••";

    }

}
</script>
@endsection