<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1, viewport-fit=cover"
    >

    <meta name="theme-color" content="#1e3a8a">

    <title>
        Instalasi Work Order #{{ $workOrder->id }}
    </title>

    @vite([
        'resources/css/app.css',
        'resources/js/app.js'
    ])
</head>

<body class="min-h-screen bg-slate-100 text-slate-800">

    {{-- ============================================================= --}}
    {{-- HEADER KHUSUS TEKNISI                                         --}}
    {{-- ============================================================= --}}

    <header class="sticky top-0 z-40 border-b border-slate-200 bg-white shadow-sm">

        <div class="mx-auto flex max-w-lg items-center gap-3 px-4 py-3">

            <a
                href="{{ route('work-orders.show', $workOrder) }}"
                class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-slate-100 text-xl text-slate-700 transition hover:bg-slate-200"
                aria-label="Kembali ke detail Work Order"
            >
                ←
            </a>

            <div class="min-w-0 flex-1">

                <h1 class="truncate text-base font-bold text-slate-900">
                    Instalasi Pelanggan
                </h1>

                <p class="truncate text-xs text-slate-500">
                    Work Order #{{ $workOrder->id }}
                </p>

            </div>

            <div class="shrink-0 rounded-full bg-indigo-100 px-3 py-1 text-xs font-semibold text-indigo-700">
                Instalasi
            </div>

        </div>

    </header>


    {{-- ============================================================= --}}
    {{-- KONTEN UTAMA                                                   --}}
    {{-- ============================================================= --}}

    <main class="mx-auto w-full max-w-lg px-4 pb-32 pt-4">

        {{-- Pesan berhasil --}}

        @if(session('success'))

            <div class="mb-4 rounded-2xl border border-emerald-200 bg-emerald-50 p-4">

                <div class="flex items-start gap-3">

                    <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-emerald-100">
                        ✅
                    </div>

                    <div>

                        <p class="font-semibold text-emerald-800">
                            Berhasil
                        </p>

                        <p class="mt-1 text-sm leading-5 text-emerald-700">
                            {{ session('success') }}
                        </p>

                    </div>

                </div>

            </div>

        @endif


        {{-- Pesan validasi --}}

        @if($errors->any())

            <div class="mb-4 rounded-2xl border border-red-200 bg-red-50 p-4">

                <p class="font-semibold text-red-800">
                    Data belum dapat disimpan
                </p>

                <ul class="mt-2 space-y-1 text-sm text-red-700">

                    @foreach($errors->all() as $error)

                        <li>
                            • {{ $error }}
                        </li>

                    @endforeach

                </ul>

            </div>

        @endif


        {{-- ========================================================= --}}
        {{-- STATUS PEKERJAAN                                          --}}
        {{-- ========================================================= --}}

        <section class="mb-4 overflow-hidden rounded-2xl border border-indigo-100 bg-white shadow-sm">

            <div class="bg-gradient-to-r from-indigo-700 to-blue-600 px-4 py-4 text-white">

                <div class="flex items-center justify-between gap-3">

                    <div>

                        <p class="text-xs font-medium text-indigo-100">
                            Status pekerjaan
                        </p>

                        <p class="mt-1 text-lg font-bold">
                            Instalasi Berlangsung
                        </p>

                    </div>

                    <div class="flex h-12 w-12 items-center justify-center rounded-full bg-white/20 text-2xl">
                        🔨
                    </div>

                </div>

            </div>

            <div class="px-4 py-3">

                <p class="text-sm leading-6 text-slate-600">
                    Upload foto SN modem terlebih dahulu lalu tekan
                    <strong>Simpan Sementara</strong>.
                    Data tersebut nantinya dapat diproses NOC secara paralel.
                </p>

            </div>

        </section>


        <form
            id="installationForm"
            action="{{ route('work-order-installation.update', $workOrder) }}"
            method="POST"
            enctype="multipart/form-data"
            class="space-y-4"
        >

            @csrf

            {{-- ===================================================== --}}
            {{-- PROGRESS DOKUMENTASI                                  --}}
            {{-- ===================================================== --}}

            <section class="overflow-hidden rounded-2xl border border-blue-100 bg-white shadow-sm">
                <div class="border-b border-slate-100 px-4 py-4">
                    <div class="flex items-center justify-between gap-3">
                        <div>
                            <h2 class="font-bold text-slate-900">Progress Dokumentasi</h2>
                            <p class="mt-1 text-xs text-slate-500">Terbarui otomatis saat data dilengkapi</p>
                        </div>
                        <div id="progressPercentage" class="text-2xl font-bold text-blue-700">0%</div>
                    </div>
                </div>

                <div class="p-4">
                    <div class="h-3 overflow-hidden rounded-full bg-slate-200">
                        <div
                            id="documentationProgressBar"
                            class="h-full rounded-full bg-blue-600 transition-all duration-300"
                            style="width: 0%"
                        ></div>
                    </div>

                    <div class="mt-4 grid grid-cols-3 gap-2 text-center">
                        <div class="rounded-xl bg-slate-50 px-2 py-3">
                            <p id="progressPhotoCount" class="text-lg font-bold text-slate-900">0/5</p>
                            <p class="text-xs text-slate-500">Foto</p>
                        </div>
                        <div class="rounded-xl bg-slate-50 px-2 py-3">
                            <p id="progressChecklistCount" class="text-lg font-bold text-slate-900">0/5</p>
                            <p class="text-xs text-slate-500">Checklist</p>
                        </div>
                        <div class="rounded-xl bg-slate-50 px-2 py-3">
                            <p id="progressGpsStatus" class="text-lg font-bold text-slate-400">—</p>
                            <p class="text-xs text-slate-500">GPS</p>
                        </div>
                    </div>
                </div>
            </section>


            {{-- ===================================================== --}}
            {{-- INFORMASI MODEM                                       --}}
            {{-- ===================================================== --}}

            <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">

                <div class="border-b border-slate-100 px-4 py-4">

                    <div class="flex items-center gap-3">

                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-blue-100 text-xl">
                            📡
                        </div>

                        <div>

                            <h2 class="font-bold text-slate-900">
                                Informasi Modem
                            </h2>

                            <p class="text-xs text-slate-500">
                                Data awal untuk proses aktivasi NOC
                            </p>

                        </div>

                    </div>

                </div>

                <div class="space-y-5 p-4">

                    {{-- SN Modem --}}

                    <div>

                        <label
                            for="sn_modem"
                            class="mb-2 block text-sm font-semibold text-slate-700"
                        >
                            Serial Number Modem
                        </label>

                        <input
                            id="sn_modem"
                            type="text"
                            name="sn_modem"
                            value="{{ old('sn_modem', $installation->sn_modem) }}"
                            placeholder="Contoh: ZTEGC1234567"
                            autocomplete="off"
                            autocapitalize="characters"
                            class="block h-14 w-full rounded-xl border-slate-300 bg-white px-4 text-base font-semibold uppercase tracking-wide text-slate-900 shadow-sm transition focus:border-blue-500 focus:ring-blue-500"
                        >

                        <p class="mt-2 text-xs leading-5 text-slate-500">
                            Pastikan SN yang diketik sama dengan label pada modem.
                        </p>

                    </div>

                    {{-- Panjang Kabel --}}

                    <div>
                        <label
                            for="panjang_kabel"
                            class="mb-2 block text-sm font-semibold text-slate-700"
                        >
                            Panjang Kabel yang Digunakan
                        </label>

                        <div class="relative">
                            <input
                                id="panjang_kabel"
                                type="number"
                                name="panjang_kabel"
                                value="{{ old('panjang_kabel', $installation->panjang_kabel) }}"
                                min="0"
                                max="100000"
                                step="1"
                                inputmode="numeric"
                                placeholder="Contoh: 75"
                                class="block h-14 w-full rounded-xl border-slate-300 bg-white px-4 pr-20 text-base font-semibold text-slate-900 shadow-sm transition focus:border-blue-500 focus:ring-blue-500"
                            >

                            <span class="pointer-events-none absolute inset-y-0 right-4 flex items-center text-sm font-semibold text-slate-500">
                                meter
                            </span>
                        </div>

                        <p class="mt-2 text-xs leading-5 text-slate-500">
                            Isi total panjang kabel dropcore yang digunakan pada instalasi ini.
                        </p>
                    </div>
                    {{-- Foto SN Modem --}}

                    <x-photo-upload-card
                        field="foto_sn_modem"
                        title="Foto Label SN Modem"
                        description="Pastikan label dan serial number modem terlihat jelas"
                        button-text="Ambil Foto SN Modem"
                        :existing-path="$installation->foto_sn_modem"
                        :hero="true"
                    />


                    {{-- Status SN tersimpan --}}

                    @if($installation->sn_disimpan_at)

                        <div class="rounded-xl border border-emerald-200 bg-emerald-50 p-3">

                            <div class="flex items-start gap-3">

                                <div class="text-lg">
                                    ✅
                                </div>

                                <div>

                                    <p class="text-sm font-semibold text-emerald-800">
                                        Data modem sudah disimpan
                                    </p>

                                    <p class="mt-1 text-xs leading-5 text-emerald-700">
                                        Tersimpan pada
                                        {{ $installation->sn_disimpan_at->format('d-m-Y H:i') }}.
                                        Data siap digunakan untuk proses NOC.
                                    </p>

                                </div>

                            </div>

                        </div>

                    @endif

                </div>

            </section>


            {{-- ===================================================== --}}
            {{-- DOKUMENTASI INSTALASI                                 --}}
            {{-- ===================================================== --}}

            <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">

                <div class="border-b border-slate-100 px-4 py-4">

                    <div class="flex items-center gap-3">

                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-violet-100 text-xl">
                            📸
                        </div>

                        <div>

                            <h2 class="font-bold text-slate-900">
                                Dokumentasi Instalasi
                            </h2>

                            <p class="text-xs text-slate-500">
                                Lengkapi seluruh foto sebelum menyelesaikan pekerjaan
                            </p>

                        </div>

                    </div>

                </div>

                <div class="divide-y divide-slate-100">
                    {{-- Foto Speedtest --}}

                    <x-photo-upload-card
                        field="foto_speedtest"
                        title="Foto Speedtest"
                        description="Pastikan hasil download, upload, dan ping terlihat"
                        button-text="Ambil Foto Speedtest"
                        :existing-path="$installation->foto_speedtest"
                    />


                    {{-- Foto Rumah Tampak Depan --}}

                    <div class="p-4">

                        <div class="mb-3 flex items-center justify-between gap-3">

                            <div>

                                <p class="text-sm font-semibold text-slate-800">
                                    Foto Rumah Tampak Depan
                                </p>

                                <p class="mt-1 text-xs text-slate-500">
                                    Ambil gambar bangunan pelanggan dengan jelas
                                </p>

                            </div>

                            @if($installation->foto_rumah_depan)

                                <span class="shrink-0 rounded-full bg-emerald-100 px-2.5 py-1 text-xs font-semibold text-emerald-700">
                                    ✓ Ada
                                </span>

                            @else

                                <span class="shrink-0 rounded-full bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-500">
                                    Belum
                                </span>

                            @endif

                        </div>

                        <label
                            id="capture-foto_rumah_depan"
                            for="foto_rumah_depan"
                            class="{{ $installation->foto_rumah_depan ? 'hidden' : 'flex' }} h-14 cursor-pointer items-center justify-center gap-2 rounded-xl border border-blue-200 bg-blue-50 px-4 font-semibold text-blue-700 transition active:scale-[0.99] active:bg-blue-100"
                        >
                            <span class="text-xl">📷</span>
                            Ambil Foto Rumah
                        </label>

                        <input
                            id="foto_rumah_depan"
                            type="file"
                            name="foto_rumah_depan"
                            accept="image/*"
                            capture="environment"
                            class="sr-only"
                            data-existing="{{ $installation->foto_rumah_depan ? '1' : '0' }}"
                            onchange="previewSelectedPhoto(this, 'preview-foto-rumah', 'name-foto-rumah')"
                        >

                        <div
                            id="preview-foto-rumah"
                            class="mt-3 {{ $installation->foto_rumah_depan ? '' : 'hidden' }} overflow-hidden rounded-xl border border-slate-200 bg-slate-50"
                        >

                            <img
                                src="{{ $installation->foto_rumah_depan ? asset('storage/' . $installation->foto_rumah_depan) : '' }}"
                                alt="Preview Foto Rumah Tampak Depan"
                                class="max-h-72 w-full object-contain"
                            >

                            <div class="border-t border-slate-200 p-3">
                                <p
                                    id="name-foto-rumah"
                                    class="truncate text-xs text-slate-600"
                                >{{ $installation->foto_rumah_depan ? 'Foto tersimpan' : '' }}</p>

                                <button
                                    type="button"
                                    onclick="document.getElementById('foto_rumah_depan').click()"
                                    class="mt-3 flex h-11 w-full items-center justify-center gap-2 rounded-xl border border-blue-200 bg-white text-sm font-bold text-blue-700 transition active:bg-blue-50"
                                >
                                    📷 Ambil Ulang
                                </button>
                            </div>

                        </div>

                    </div>
                    {{-- Foto Form Registrasi --}}

                    <x-photo-upload-card
                        field="foto_form_registrasi"
                        title="Foto Form Registrasi"
                        description="Pastikan seluruh isi form dapat terbaca"
                        button-text="Ambil Foto Form"
                        :existing-path="$installation->foto_form_registrasi"
                    />


                    {{-- Foto Redaman Modem --}}

                    <div class="p-4">

                        <div class="mb-3 flex items-center justify-between gap-3">

                            <div>

                                <p class="text-sm font-semibold text-slate-800">
                                    Foto Redaman Modem
                                </p>

                                <p class="mt-1 text-xs text-slate-500">
                                    Pastikan nilai redaman terlihat jelas
                                </p>

                            </div>

                            @if($installation->foto_redaman_modem)

                                <span class="shrink-0 rounded-full bg-emerald-100 px-2.5 py-1 text-xs font-semibold text-emerald-700">
                                    ✓ Ada
                                </span>

                            @else

                                <span class="shrink-0 rounded-full bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-500">
                                    Belum
                                </span>

                            @endif

                        </div>

                        <label
                            id="capture-foto_redaman_modem"
                            for="foto_redaman_modem"
                            class="{{ $installation->foto_redaman_modem ? 'hidden' : 'flex' }} h-14 cursor-pointer items-center justify-center gap-2 rounded-xl border border-blue-200 bg-blue-50 px-4 font-semibold text-blue-700 transition active:scale-[0.99] active:bg-blue-100"
                        >
                            <span class="text-xl">📷</span>
                            Ambil Foto Redaman
                        </label>

                        <input
                            id="foto_redaman_modem"
                            type="file"
                            name="foto_redaman_modem"
                            accept="image/*"
                            capture="environment"
                            class="sr-only"
                            data-existing="{{ $installation->foto_redaman_modem ? '1' : '0' }}"
                            onchange="previewSelectedPhoto(this, 'preview-foto-redaman', 'name-foto-redaman')"
                        >

                        <div
                            id="preview-foto-redaman"
                            class="mt-3 {{ $installation->foto_redaman_modem ? '' : 'hidden' }} overflow-hidden rounded-xl border border-slate-200 bg-slate-50"
                        >

                            <img
                                src="{{ $installation->foto_redaman_modem ? asset('storage/' . $installation->foto_redaman_modem) : '' }}"
                                alt="Preview Foto Redaman Modem"
                                class="max-h-72 w-full object-contain"
                            >

                            <div class="border-t border-slate-200 p-3">
                                <p
                                    id="name-foto-redaman"
                                    class="truncate text-xs text-slate-600"
                                >{{ $installation->foto_redaman_modem ? 'Foto tersimpan' : '' }}</p>

                                <button
                                    type="button"
                                    onclick="document.getElementById('foto_redaman_modem').click()"
                                    class="mt-3 flex h-11 w-full items-center justify-center gap-2 rounded-xl border border-blue-200 bg-white text-sm font-bold text-blue-700 transition active:bg-blue-50"
                                >
                                    📷 Ambil Ulang
                                </button>
                            </div>

                        </div>

                    </div>

                </div>

            </section>


            {{-- ===================================================== --}}
            {{-- CHECKLIST PEKERJAAN                                   --}}
            {{-- ===================================================== --}}

            <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">

                <div class="border-b border-slate-100 px-4 py-4">

                    <div class="flex items-center gap-3">

                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-100 text-xl">
                            ✅
                        </div>

                        <div>

                            <h2 class="font-bold text-slate-900">
                                Checklist Pekerjaan
                            </h2>

                            <p class="text-xs text-slate-500">
                                Centang setelah setiap tahapan selesai
                            </p>

                        </div>

                    </div>

                </div>

                <div class="divide-y divide-slate-100">

                    @php
                        $checklists = [
                            [
                                'name' => 'modem_terpasang',
                                'label' => 'Modem Terpasang',
                                'description' => 'Modem sudah dipasang dengan rapi',
                                'checked' => $installation->modem_terpasang,
                            ],
                            [
                                'name' => 'onu_online',
                                'label' => 'ONU Online',
                                'description' => 'ONU sudah terdeteksi dan online',
                                'checked' => $installation->onu_online,
                            ],
                            [
                                'name' => 'internet_normal',
                                'label' => 'Internet Normal',
                                'description' => 'Koneksi internet berjalan dengan baik',
                                'checked' => $installation->internet_normal,
                            ],
                            [
                                'name' => 'speedtest_berhasil',
                                'label' => 'Speedtest Berhasil',
                                'description' => 'Hasil speedtest sesuai paket pelanggan',
                                'checked' => $installation->speedtest_berhasil,
                            ],
                            [
                                'name' => 'pelanggan_menerima',
                                'label' => 'Pelanggan Menerima Hasil',
                                'description' => 'Pelanggan telah menerima hasil instalasi',
                                'checked' => $installation->pelanggan_menerima,
                            ],
                        ];
                    @endphp

                    @foreach($checklists as $item)

                        <label class="flex cursor-pointer items-start gap-3 px-4 py-4 active:bg-slate-50">

                            <input
                                type="checkbox"
                                name="{{ $item['name'] }}"
                                value="1"
                                class="mt-1 h-6 w-6 rounded border-slate-300 text-blue-600 focus:ring-blue-500"
                                {{ old($item['name'], $item['checked']) ? 'checked' : '' }}
                            >

                            <span class="min-w-0 flex-1">

                                <span class="block text-sm font-semibold text-slate-800">
                                    {{ $item['label'] }}
                                </span>

                                <span class="mt-1 block text-xs leading-5 text-slate-500">
                                    {{ $item['description'] }}
                                </span>

                            </span>

                        </label>

                    @endforeach

                </div>

            </section>


            {{-- ===================================================== --}}
            {{-- LOKASI GPS                                            --}}
            {{-- ===================================================== --}}

            <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">

                <div class="border-b border-slate-100 px-4 py-4">

                    <div class="flex items-center gap-3">

                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-amber-100 text-xl">
                            📍
                        </div>

                        <div>

                            <h2 class="font-bold text-slate-900">
                                Lokasi Pekerjaan
                            </h2>

                            <p class="text-xs text-slate-500">
                                Simpan koordinat lokasi pelanggan
                            </p>

                        </div>

                    </div>

                </div>

                <div class="space-y-4 p-4">

                    <button
                        type="button"
                        id="locationButton"
                        onclick="getCurrentLocation()"
                        class="flex h-14 w-full items-center justify-center gap-2 rounded-xl bg-amber-500 px-4 font-bold text-white shadow-sm transition active:scale-[0.99] active:bg-amber-600"
                    >
                        <span class="text-xl">
                            📍
                        </span>

                        <span id="locationButtonText">
                            Ambil Lokasi Saya
                        </span>
                    </button>

                    <div
                        id="locationStatus"
                        class="hidden rounded-xl border p-3 text-sm"
                    ></div>

                    <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">

                        <div>

                            <label
                                for="latitude"
                                class="mb-2 block text-xs font-semibold uppercase tracking-wide text-slate-500"
                            >
                                Latitude
                            </label>

                            <input
                                id="latitude"
                                type="text"
                                name="latitude"
                                value="{{ old('latitude', $installation->latitude) }}"
                                readonly
                                placeholder="Belum tersedia"
                                class="block h-12 w-full rounded-xl border-slate-300 bg-slate-50 px-3 text-sm text-slate-700 shadow-sm"
                            >

                        </div>

                        <div>

                            <label
                                for="longitude"
                                class="mb-2 block text-xs font-semibold uppercase tracking-wide text-slate-500"
                            >
                                Longitude
                            </label>

                            <input
                                id="longitude"
                                type="text"
                                name="longitude"
                                value="{{ old('longitude', $installation->longitude) }}"
                                readonly
                                placeholder="Belum tersedia"
                                class="block h-12 w-full rounded-xl border-slate-300 bg-slate-50 px-3 text-sm text-slate-700 shadow-sm"
                            >

                        </div>

                    </div>

                    @if($installation->latitude && $installation->longitude)

                        <div class="rounded-xl border border-emerald-200 bg-emerald-50 p-3">

                            <p class="text-sm font-semibold text-emerald-800">
                                ✓ Lokasi sudah tersimpan
                            </p>

                            <p class="mt-1 text-xs text-emerald-700">
                                {{ $installation->latitude }},
                                {{ $installation->longitude }}
                            </p>

                        </div>

                    @endif

                    <p class="text-xs leading-5 text-slate-500">
                        Aktifkan GPS dan izinkan browser mengakses lokasi perangkat.
                        Pengambilan lokasi akan lebih akurat ketika berada di luar ruangan.
                    </p>

                </div>

            </section>


            {{-- ===================================================== --}}
            {{-- CATATAN TEKNISI                                       --}}
            {{-- ===================================================== --}}

            <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">

                <div class="border-b border-slate-100 px-4 py-4">

                    <div class="flex items-center gap-3">

                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-slate-100 text-xl">
                            📝
                        </div>

                        <div>

                            <h2 class="font-bold text-slate-900">
                                Catatan Teknisi
                            </h2>

                            <p class="text-xs text-slate-500">
                                Tambahkan informasi penting dari lapangan
                            </p>

                        </div>

                    </div>

                </div>

                <div class="p-4">

                    <textarea
                        id="catatan_teknisi"
                        name="catatan_teknisi"
                        rows="5"
                        maxlength="2000"
                        placeholder="Contoh: Instalasi berjalan normal, kabel ditarik melalui sisi kanan rumah..."
                        class="block w-full resize-none rounded-xl border-slate-300 px-4 py-3 text-base text-slate-800 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                    >{{ old('catatan_teknisi', $installation->catatan_teknisi) }}</textarea>

                    <p class="mt-2 text-right text-xs text-slate-500">
                        Maksimal 2.000 karakter
                    </p>

                </div>

            </section>


            {{-- ===================================================== --}}
            {{-- RINGKASAN KELENGKAPAN                                 --}}
            {{-- ===================================================== --}}

            @php
                $jumlahFoto = collect([
                    $installation->foto_sn_modem,
                    $installation->foto_speedtest,
                    $installation->foto_rumah_depan,
                    $installation->foto_form_registrasi,
                    $installation->foto_redaman_modem,
                ])->filter()->count();

                $jumlahChecklist = collect([
                    $installation->modem_terpasang,
                    $installation->onu_online,
                    $installation->internet_normal,
                    $installation->speedtest_berhasil,
                    $installation->pelanggan_menerima,
                ])->filter()->count();

                $gpsTersimpan =
                    filled($installation->latitude) &&
                    filled($installation->longitude);
            @endphp

            <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">

                <div class="border-b border-slate-100 px-4 py-4">

                    <div class="flex items-center gap-3">

                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-cyan-100 text-xl">
                            📋
                        </div>

                        <div>

                            <h2 class="font-bold text-slate-900">
                                Ringkasan Kelengkapan
                            </h2>

                            <p class="text-xs text-slate-500">
                                Data yang telah tersimpan
                            </p>

                        </div>

                    </div>

                </div>

                <div class="grid grid-cols-3 divide-x divide-slate-100">

                    <div class="px-2 py-4 text-center">

                        <p id="summaryPhotoCount" class="text-2xl font-bold text-slate-900">
                            {{ $jumlahFoto }}/5
                        </p>

                        <p class="mt-1 text-xs text-slate-500">
                            Foto
                        </p>

                    </div>

                    <div class="px-2 py-4 text-center">

                        <p id="summaryChecklistCount" class="text-2xl font-bold text-slate-900">
                            {{ $jumlahChecklist }}/5
                        </p>

                        <p class="mt-1 text-xs text-slate-500">
                            Checklist
                        </p>

                    </div>

                    <div class="px-2 py-4 text-center">

                        <p id="summaryGpsStatus" class="text-2xl font-bold {{ $gpsTersimpan ? 'text-emerald-600' : 'text-slate-400' }}">
                            {{ $gpsTersimpan ? '✓' : '—' }}
                        </p>

                        <p class="mt-1 text-xs text-slate-500">
                            GPS
                        </p>

                    </div>

                </div>

            </section>


            {{-- ===================================================== --}}
            {{-- TOMBOL SIMPAN                                         --}}
            {{-- ===================================================== --}}

            <section class="rounded-2xl border border-blue-100 bg-blue-50 p-4">

                <p class="mb-3 text-sm leading-6 text-blue-800">
                    Simpan sementara untuk melanjutkan nanti. Tombol selesai hanya
                    aktif setelah 5 foto, 5 checklist, dan GPS lengkap.
                </p>

                <div class="space-y-3">
                    <button
                        type="submit"
                        id="saveButton"
                        class="flex h-14 w-full items-center justify-center gap-2 rounded-xl bg-blue-700 px-4 text-base font-bold text-white shadow-sm transition active:scale-[0.99] active:bg-blue-800 disabled:cursor-not-allowed disabled:opacity-60"
                    >
                        <span>💾</span>
                        <span id="saveButtonText">Simpan Sementara</span>
                    </button>

                    <button
                        type="submit"
                        id="completeButton"
                        formaction="{{ route('work-order-installation.complete', $workOrder) }}"
                        disabled
                        class="flex h-14 w-full items-center justify-center gap-2 rounded-xl bg-emerald-600 px-4 text-base font-bold text-white shadow-sm transition active:scale-[0.99] active:bg-emerald-700 disabled:cursor-not-allowed disabled:bg-slate-300 disabled:text-slate-500 disabled:shadow-none"
                    >
                        <span>✅</span>
                        <span id="completeButtonText">Selesai Instalasi</span>
                    </button>
                </div>

                <div
                    id="completionHint"
                    class="mt-3 rounded-xl border border-amber-200 bg-amber-50 px-3 py-2 text-xs leading-5 text-amber-700"
                >
                    Lengkapi 5 foto, 5 checklist, dan lokasi GPS.
                </div>

            </section>

        </form>

    </main>


    {{-- ============================================================= --}}
    {{-- JAVASCRIPT                                                     --}}
    {{-- ============================================================= --}}

    <script>
        const photoInputIds = [
            'foto_sn_modem',
            'foto_speedtest',
            'foto_rumah_depan',
            'foto_form_registrasi',
            'foto_redaman_modem'
        ];

        const checklistNames = [
            'modem_terpasang',
            'onu_online',
            'internet_normal',
            'speedtest_berhasil',
            'pelanggan_menerima'
        ];

        function previewSelectedPhoto(input) {
            const file = input.files && input.files[0];

            if (!file) {
                return;
            }

            const suffix = input.id.replaceAll('_', '-');
            const previewContainer = document.getElementById('preview-' + suffix);
            const previewImage = previewContainer.querySelector('img');
            const fileName = document.getElementById('name-' + suffix);
            const captureButton = document.getElementById('capture-' + input.id);
            const statusBadge = document.getElementById('status-' + input.id);
            const reader = new FileReader();

            reader.onload = function (event) {
                previewImage.src = event.target.result;
                fileName.textContent = file.name + ' • siap diunggah';
                previewContainer.classList.remove('hidden');

                if (captureButton) {
                    captureButton.classList.add('hidden');
                    captureButton.classList.remove('flex');
                }

                if (statusBadge) {
                    statusBadge.textContent = '✓ Siap';
                    statusBadge.className =
                        'shrink-0 rounded-full bg-emerald-100 px-2.5 py-1 text-xs font-semibold text-emerald-700';
                }

                input.dataset.existing = '1';
                updateDocumentationProgress();
            };

            reader.readAsDataURL(file);
        }

        function getPhotoCount() {
            return photoInputIds.filter(function (id) {
                const input = document.getElementById(id);
                return input && (input.files.length > 0 || input.dataset.existing === '1');
            }).length;
        }

        function getChecklistCount() {
            return checklistNames.filter(function (name) {
                const checkbox = document.querySelector('input[name="' + name + '"]');
                return checkbox && checkbox.checked;
            }).length;
        }

        function hasGps() {
            return Boolean(
                document.getElementById('latitude').value.trim() &&
                document.getElementById('longitude').value.trim()
            );
        }

        function updateDocumentationProgress() {
            const photoCount = getPhotoCount();
            const checklistCount = getChecklistCount();
            const gpsReady = hasGps();
            const completedItems = photoCount + checklistCount + (gpsReady ? 1 : 0);
            const totalItems = 11;
            const percentage = Math.round((completedItems / totalItems) * 100);

            document.getElementById('progressPercentage').textContent = percentage + '%';
            document.getElementById('documentationProgressBar').style.width = percentage + '%';
            document.getElementById('progressPhotoCount').textContent = photoCount + '/5';
            document.getElementById('progressChecklistCount').textContent = checklistCount + '/5';
            document.getElementById('progressGpsStatus').textContent = gpsReady ? '✓' : '—';
            document.getElementById('progressGpsStatus').className =
                'text-lg font-bold ' + (gpsReady ? 'text-emerald-600' : 'text-slate-400');

            document.getElementById('summaryPhotoCount').textContent = photoCount + '/5';
            document.getElementById('summaryChecklistCount').textContent = checklistCount + '/5';
            document.getElementById('summaryGpsStatus').textContent = gpsReady ? '✓' : '—';
            document.getElementById('summaryGpsStatus').className =
                'text-2xl font-bold ' + (gpsReady ? 'text-emerald-600' : 'text-slate-400');

            updateCompletionButton(photoCount, checklistCount, gpsReady);
        }

        function updateCompletionButton(photoCount, checklistCount, gpsReady) {
            const completeButton = document.getElementById('completeButton');
            const completionHint = document.getElementById('completionHint');
            const isComplete = photoCount === 5 && checklistCount === 5 && gpsReady;

            completeButton.disabled = !isComplete;

            if (isComplete) {
                completionHint.textContent =
                    'Dokumentasi lengkap. Instalasi siap dikirim untuk verifikasi.';
                completionHint.className =
                    'mt-3 rounded-xl border border-emerald-200 bg-emerald-50 px-3 py-2 text-xs leading-5 text-emerald-700';
                return;
            }

            const missing = [];

            if (photoCount < 5) {
                missing.push((5 - photoCount) + ' foto');
            }

            if (checklistCount < 5) {
                missing.push((5 - checklistCount) + ' checklist');
            }

            if (!gpsReady) {
                missing.push('GPS');
            }

            completionHint.textContent = 'Belum lengkap: ' + missing.join(', ') + '.';
            completionHint.className =
                'mt-3 rounded-xl border border-amber-200 bg-amber-50 px-3 py-2 text-xs leading-5 text-amber-700';
        }


        function showLocationStatus(type, message) {
            const status = document.getElementById('locationStatus');

            status.classList.remove(
                'hidden',
                'border-emerald-200',
                'bg-emerald-50',
                'text-emerald-700',
                'border-red-200',
                'bg-red-50',
                'text-red-700',
                'border-amber-200',
                'bg-amber-50',
                'text-amber-700'
            );

            if (type === 'success') {
                status.classList.add(
                    'border-emerald-200',
                    'bg-emerald-50',
                    'text-emerald-700'
                );
            } else if (type === 'error') {
                status.classList.add(
                    'border-red-200',
                    'bg-red-50',
                    'text-red-700'
                );
            } else {
                status.classList.add(
                    'border-amber-200',
                    'bg-amber-50',
                    'text-amber-700'
                );
            }

            status.textContent = message;
        }


        function getCurrentLocation() {
            const button = document.getElementById('locationButton');
            const buttonText = document.getElementById('locationButtonText');

            if (!navigator.geolocation) {
                showLocationStatus(
                    'error',
                    'Perangkat atau browser ini tidak mendukung GPS.'
                );

                return;
            }

            button.disabled = true;
            buttonText.textContent = 'Mengambil Lokasi...';

            showLocationStatus(
                'loading',
                'Sedang mencari koordinat perangkat. Mohon tunggu.'
            );

            navigator.geolocation.getCurrentPosition(
                function (position) {
                    const latitude = position.coords.latitude.toFixed(7);
                    const longitude = position.coords.longitude.toFixed(7);
                    const accuracy = Math.round(position.coords.accuracy);

                    document.getElementById('latitude').value = latitude;
                    document.getElementById('longitude').value = longitude;
                    updateDocumentationProgress();

                    showLocationStatus(
                        'success',
                        'Lokasi berhasil diambil. Akurasi sekitar ' +
                        accuracy +
                        ' meter.'
                    );

                    button.disabled = false;
                    buttonText.textContent = 'Perbarui Lokasi Saya';
                },

                function (error) {
                    let message = 'Lokasi gagal diambil.';

                    if (error.code === error.PERMISSION_DENIED) {
                        message =
                            'Akses lokasi ditolak. Izinkan lokasi pada pengaturan browser.';
                    } else if (error.code === error.POSITION_UNAVAILABLE) {
                        message =
                            'Koordinat belum tersedia. Pastikan GPS perangkat aktif.';
                    } else if (error.code === error.TIMEOUT) {
                        message =
                            'Pengambilan lokasi terlalu lama. Silakan coba kembali.';
                    }

                    showLocationStatus('error', message);

                    button.disabled = false;
                    buttonText.textContent = 'Coba Ambil Lokasi Lagi';
                },

                {
                    enableHighAccuracy: true,
                    timeout: 15000,
                    maximumAge: 0
                }
            );
        }


        document.addEventListener('DOMContentLoaded', function () {
            checklistNames.forEach(function (name) {
                const checkbox = document.querySelector('input[name="' + name + '"]');

                if (checkbox) {
                    checkbox.addEventListener('change', updateDocumentationProgress);
                }
            });

            updateDocumentationProgress();
        });


        document
            .getElementById('installationForm')
            .addEventListener('submit', function (event) {
                const submitter = event.submitter;
                const saveButton = document.getElementById('saveButton');
                const saveButtonText = document.getElementById('saveButtonText');
                const completeButton = document.getElementById('completeButton');
                const completeButtonText = document.getElementById('completeButtonText');

                if (submitter && submitter.id === 'completeButton') {
                    const confirmed = window.confirm(
                        'Anda yakin instalasi sudah selesai?\n\n' +
                        'Dokumentasi akan dikirim ke Admin untuk verifikasi.'
                    );

                    if (!confirmed) {
                        event.preventDefault();
                        return;
                    }

                    completeButton.disabled = true;
                    completeButtonText.textContent = 'Mengirim Verifikasi...';
                    saveButton.disabled = true;
                    return;
                }

                saveButton.disabled = true;
                saveButtonText.textContent = 'Menyimpan...';
                completeButton.disabled = true;
            });
    </script>

</body>
</html>