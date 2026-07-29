@extends('layouts.admin')

@section('content')

<div class="max-w-3xl mx-auto">

    <div class="flex items-center justify-between mb-6">

        <div>

            <h1 class="text-2xl font-bold text-gray-800">
                Tambah Role
            </h1>

            <p class="text-gray-500 mt-1">
                Tambahkan role baru pada sistem.
            </p>

        </div>

        <a href="{{ route('roles.index') }}"
           class="px-4 py-2 rounded-xl bg-gray-200 hover:bg-gray-300 text-gray-700 font-semibold">

            Kembali

        </a>

    </div>

    <div class="bg-white rounded-2xl shadow border border-gray-100 p-8">

        <form action="{{ route('roles.store') }}" method="POST">

            @csrf

            <div>

                <label class="block mb-2 font-semibold text-gray-700">

                    Nama Role

                </label>

                <input
                    type="text"
                    name="name"
                    value="{{ old('name') }}"
                    class="w-full rounded-xl border-gray-300 focus:ring-blue-500 focus:border-blue-500"
                    placeholder="Contoh : Supervisor">

                @error('name')

                    <p class="text-red-500 text-sm mt-2">

                        {{ $message }}

                    </p>

                @enderror

            </div>

            <div class="mt-8 flex justify-end gap-3">

                <a href="{{ route('roles.index') }}"
                   class="px-5 py-2 rounded-xl bg-gray-200 hover:bg-gray-300">

                    Batal

                </a>

                <button
                    type="submit"
                    class="px-6 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-semibold">

                    Simpan

                </button>

            </div>

        </form>

    </div>

</div>

@endsection