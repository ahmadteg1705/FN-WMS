@extends('layouts.admin')

@section('page-title', 'Edit Marketing')

@section('content')

<div class="mx-auto max-w-5xl">

    <div class="mb-6 flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-slate-800">
                Edit Marketing
            </h2>

            <p class="mt-1 text-sm text-slate-500">
                Perbarui data marketing
                <span class="font-semibold text-indigo-600">
                    {{ $marketing->user?->name }}
                </span>
            </p>
        </div>

        <a href="{{ route('marketings.index') }}"
           class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50">

            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>

            Kembali
        </a>
    </div>


    <form action="{{ route('marketings.update',$marketing->id) }}"
          method="POST"
          enctype="multipart/form-data">

        @csrf
        @method('PUT')



        {{-- ===================================================== --}}
        {{-- BAGIAN 1 : INFORMASI AKUN --}}
        {{-- ===================================================== --}}

        <div class="mb-6 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">

            <div class="border-b border-slate-200 bg-slate-50 px-6 py-4">

                <h3 class="text-base font-bold text-slate-800">
                    Informasi Akun
                </h3>

                <p class="text-sm text-slate-500">
                    Pilih akun login yang digunakan oleh Marketing.
                </p>

            </div>

            <div class="p-6">

                <div>

                    <label class="mb-2 block text-sm font-semibold text-slate-700">
                        User Login <span class="text-red-500">*</span>
                    </label>

                    <select
                        name="user_id"
                        class="w-full rounded-xl border border-slate-300 px-4 py-3 focus:border-indigo-500 focus:ring-indigo-500">

                        @foreach($users as $user)

                            <option
                                value="{{ $user->id }}"
                                {{ old('user_id',$marketing->user_id)==$user->id ? 'selected' : '' }}>

                                {{ $user->name }}
                                ({{ $user->username }})

                            </option>

                        @endforeach

                    </select>

                    @error('user_id')
                        <p class="mt-2 text-sm text-red-600">
                            {{ $message }}
                        </p>
                    @enderror

                </div>

            </div>

        </div>



        {{-- ===================================================== --}}
        {{-- BAGIAN 2 : INFORMASI KONTAK --}}
        {{-- ===================================================== --}}

        <div class="mb-6 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">

            <div class="border-b border-slate-200 bg-slate-50 px-6 py-4">

                <h3 class="text-base font-bold text-slate-800">
                    Informasi Kontak
                </h3>

                <p class="text-sm text-slate-500">
                    Informasi kontak Marketing.
                </p>

            </div>

            <div class="grid grid-cols-1 gap-6 p-6 md:grid-cols-2">

                <div>

                    <label class="mb-2 block text-sm font-semibold text-slate-700">
                        Nomor Telepon
                    </label>

                    <input
                        type="text"
                        name="telepon"
                        value="{{ old('telepon',$marketing->telepon) }}"
                        class="w-full rounded-xl border border-slate-300 px-4 py-3 focus:border-indigo-500 focus:ring-indigo-500">

                </div>


                <div>

                    <label class="mb-2 block text-sm font-semibold text-slate-700">
                        Wilayah
                    </label>

                    <input
                        type="text"
                        name="wilayah"
                        value="{{ old('wilayah',$marketing->wilayah) }}"
                        class="w-full rounded-xl border border-slate-300 px-4 py-3 focus:border-indigo-500 focus:ring-indigo-500">

                </div>

            </div>

        </div>
         {{-- ===================================================== --}}
        {{-- BAGIAN 3 : INFORMASI PEKERJAAN --}}
        {{-- ===================================================== --}}

        <div class="mb-6 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">

            <div class="border-b border-slate-200 bg-slate-50 px-6 py-4">

                <h3 class="text-base font-bold text-slate-800">
                    Informasi Pekerjaan
                </h3>

                <p class="text-sm text-slate-500">
                    Data jabatan, status dan foto Marketing.
                </p>

            </div>

            <div class="grid grid-cols-1 gap-6 p-6 md:grid-cols-2">

                <div>

                    <label class="mb-2 block text-sm font-semibold text-slate-700">
                        Posisi <span class="text-red-500">*</span>
                    </label>

                    <select
                        name="position_id"
                        class="w-full rounded-xl border border-slate-300 px-4 py-3 focus:border-indigo-500 focus:ring-indigo-500">

                        @foreach($positions as $position)

                            <option
                                value="{{ $position->id }}"
                                {{ old('position_id',$marketing->position_id)==$position->id ? 'selected' : '' }}>

                                {{ $position->nama }}

                            </option>

                        @endforeach

                    </select>

                    @error('position_id')
                        <p class="mt-2 text-sm text-red-600">
                            {{ $message }}
                        </p>
                    @enderror

                </div>


                <div>

                    <label class="mb-2 block text-sm font-semibold text-slate-700">
                        Status
                    </label>

                    <select
                        name="status"
                        class="w-full rounded-xl border border-slate-300 px-4 py-3 focus:border-indigo-500 focus:ring-indigo-500">

                        <option value="1"
                            {{ old('status',$marketing->status)==1 ? 'selected' : '' }}>
                            Aktif
                        </option>

                        <option value="0"
                            {{ old('status',$marketing->status)==0 ? 'selected' : '' }}>
                            Nonaktif
                        </option>

                    </select>

                </div>


                <div>

                    <label class="mb-2 block text-sm font-semibold text-slate-700">
                        Tanggal Masuk
                    </label>

                    <input
                        type="date"
                        name="tanggal_masuk"
                        value="{{ old('tanggal_masuk',$marketing->tanggal_masuk) }}"
                        class="w-full rounded-xl border border-slate-300 px-4 py-3 focus:border-indigo-500 focus:ring-indigo-500">

                </div>


                <div>

                    <label class="mb-2 block text-sm font-semibold text-slate-700">
                        Foto Marketing
                    </label>

                    @if($marketing->foto)

                        <div class="mb-3">

                            <img
                                src="{{ asset('storage/'.$marketing->foto) }}"
                                alt="Foto Marketing"
                                class="h-24 w-24 rounded-xl border object-cover shadow">

                        </div>

                    @endif

                    <input
                        type="file"
                        name="foto"
                        class="block w-full rounded-xl border border-slate-300 file:mr-4 file:rounded-lg file:border-0 file:bg-indigo-600 file:px-4 file:py-2 file:text-white hover:file:bg-indigo-700">

                    <p class="mt-2 text-xs text-slate-500">
                        Kosongkan apabila foto tidak ingin diganti.
                    </p>

                </div>

            </div>

        </div>



        {{-- ===================================================== --}}
        {{-- BAGIAN 4 : KETERANGAN --}}
        {{-- ===================================================== --}}

        <div class="mb-6 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">

            <div class="border-b border-slate-200 bg-slate-50 px-6 py-4">

                <h3 class="text-base font-bold text-slate-800">
                    Keterangan
                </h3>

            </div>

            <div class="p-6">

                <textarea
                    name="keterangan"
                    rows="5"
                    class="w-full rounded-xl border border-slate-300 px-4 py-3 focus:border-indigo-500 focus:ring-indigo-500">{{ old('keterangan',$marketing->keterangan) }}</textarea>

            </div>

        </div>



        <div class="flex items-center justify-end gap-3">

            <a
                href="{{ route('marketings.index') }}"
                class="rounded-xl border border-slate-300 bg-white px-6 py-3 font-semibold text-slate-700 hover:bg-slate-50">

                Batal

            </a>

            <button
                type="submit"
                class="rounded-xl bg-indigo-600 px-6 py-3 font-semibold text-white hover:bg-indigo-700">

                Update Marketing

            </button>

        </div>

    </form>

</div>

@endsection       