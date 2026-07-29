@extends('layouts.admin')

@section('content')
        <div class="flex items-center justify-between">

            <div>

                <h2 class="text-2xl font-bold text-slate-800">
                    Tambah User
                </h2>

                <p class="text-sm text-slate-500 mt-1">
                    Tambahkan pengguna baru ke sistem FN-WMS.
                </p>

            </div>

            <a
                href="{{ route('users.index') }}"
                class="inline-flex items-center px-4 py-2 rounded-lg bg-slate-200 hover:bg-slate-300 text-slate-700 font-medium transition">

                ← Kembali

            </a>

        </div>

    <div class="py-6">

        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">

            @if ($errors->any())

                <div class="mb-6 rounded-xl border border-red-300 bg-red-50 p-4">

                    <div class="font-semibold text-red-700 mb-2">
                        Terjadi kesalahan:
                    </div>

                    <ul class="list-disc ml-5 text-red-600 space-y-1">

                        @foreach ($errors->all() as $error)

                            <li>{{ $error }}</li>

                        @endforeach

                    </ul>

                </div>

            @endif

            <div class="bg-white rounded-2xl shadow-sm border border-slate-200">

                <div class="p-8">

                    <form
                        action="{{ route('users.store') }}"
                        method="POST"
                        enctype="multipart/form-data">

                        @include('users._form')

                    </form>

                </div>

            </div>

        </div>

    </div>

@endsection