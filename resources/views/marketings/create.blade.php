@extends('layouts.admin')

@section('page-title', 'Tambah Marketing')

@section('content')

<div class="mx-auto max-w-4xl">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h2 class="text-xl font-bold text-slate-800">Tambah Marketing Baru</h2>
            <p class="text-xs text-slate-500">Lengkapi formulir di bawah ini untuk menambahkan data marketing.</p>
        </div>
        <a href="{{ route('marketings.index') }}" 
           class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-600 shadow-sm transition hover:bg-slate-50">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Kembali
        </a>
    </div>

    <div class="rounded-2xl border border-slate-200/80 bg-white p-6 shadow-sm">
        <form action="{{ route('marketings.store') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
            @csrf

            <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                <div>
                    <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wider text-slate-600">
                        User Login *
                    </label>

                    <select
                        name="user_id"
                        class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm">

                        <option value="">-- Pilih User --</option>

                        @foreach($users as $user)

                            <option
                                value="{{ $user->id }}"
                                {{ old('user_id') == $user->id ? 'selected' : '' }}>

                                {{ $user->name }}
                                ({{ $user->username }})
                                - {{ $user->roleName() }}

                            </option>

                        @endforeach

                    </select>

                    @error('user_id')
                        <p class="mt-1 text-sm text-red-500">
                            {{ $message }}
                        </p>
                    @enderror
                </div>
                <div>
                    <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wider text-slate-600">Telepon</label>
                    <input type="text"
                           name="telepon"
                           value="{{ old('telepon') }}"
                           placeholder="Contoh: 08123456789"
                           class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm text-slate-800 placeholder-slate-400 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 transition">
                </div>
                <div>
                    <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wider text-slate-600">Wilayah</label>
                    <input type="text"
                           name="wilayah"
                           value="{{ old('wilayah') }}"
                           placeholder="Masukkan wilayah tugas"
                           class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm text-slate-800 placeholder-slate-400 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 transition">
                </div>
                <div>
                    <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wider text-slate-600">
                        Posisi *
                    </label>

                    <select
                        name="position_id"
                        class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm">

                        <option value="">-- Pilih Posisi --</option>

                        @foreach($positions as $position)

                            <option
                                value="{{ $position->id }}"
                                {{ old('position_id') == $position->id ? 'selected' : '' }}>

                                {{ $position->nama }}

                            </option>

                        @endforeach

                    </select>

                    @error('position_id')
                        <p class="mt-1 text-sm text-red-500">
                            {{ $message }}
                        </p>
                    @enderror
                </div>
            </div>

            <div>
                <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wider text-slate-600">Status</label>
                <select name="status"
                        class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm text-slate-800 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 transition">
                    <option value="1">Aktif</option>
                    <option value="0">Nonaktif</option>
                </select>
            </div>
            <div>
                <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wider text-slate-600">
                    Tanggal Masuk
                </label>

                <input
                    type="date"
                    name="tanggal_masuk"
                    value="{{ old('tanggal_masuk') }}"
                    class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm">
            </div>
            <div>
                <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wider text-slate-600">
                    Foto
                </label>

                <input
                    type="file"
                    name="foto"
                    class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm">
            </div>
            <div>
                <label class="mb-1.5 block text-xs font-semibold uppercase tracking-wider text-slate-600">Keterangan</label>
                <textarea name="keterangan"
                          rows="4"
                          placeholder="Tambahkan catatan jika ada..."
                          class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm text-slate-800 placeholder-slate-400 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 transition">{{ old('keterangan') }}</textarea>
            </div>

            <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-100">
                <a href="{{ route('marketings.index') }}"
                   class="rounded-xl border border-slate-200 px-5 py-2.5 text-sm font-semibold text-slate-600 transition hover:bg-slate-50">
                    Batal
                </a>
                <button type="submit"
                        class="inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-6 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-700 active:scale-95">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>

@endsection