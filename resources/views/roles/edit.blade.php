@extends('layouts.admin')

@section('content')

@php

$systemRoles = [
    'Super Admin',
    'Admin',
    'Marketing',
    'Teknisi',
    'NOC'
];

@endphp

<div class="max-w-3xl mx-auto">

    <div class="flex items-center justify-between mb-6">

        <div>

            <h1 class="text-2xl font-bold text-gray-800">

                Edit Role

            </h1>

            <p class="text-gray-500 mt-1">

                Perbarui informasi role.

            </p>

        </div>

        <a href="{{ route('roles.index') }}"
           class="px-4 py-2 rounded-xl bg-gray-200 hover:bg-gray-300">

            Kembali

        </a>

    </div>

    <div class="bg-white rounded-2xl shadow border border-gray-100 p-8">

        @if(in_array($role->name,$systemRoles))

        <div class="mb-6 rounded-xl border border-blue-200 bg-blue-50 px-5 py-4">

            <div class="font-semibold text-blue-700">

                Role Sistem

            </div>

            <div class="text-blue-600 text-sm mt-1">

                Role bawaan sistem tidak dapat diubah namanya.

            </div>

        </div>

        @endif

        <form action="{{ route('roles.update',$role) }}"
              method="POST">

            @csrf
            @method('PUT')

            <div>

                <label class="block mb-2 font-semibold">

                    Nama Role

                </label>

                <input
                    type="text"
                    name="name"
                    value="{{ old('name',$role->name) }}"
                    {{ in_array($role->name,$systemRoles) ? 'readonly' : '' }}
                    class="w-full rounded-xl border-gray-300 focus:ring-blue-500 focus:border-blue-500 {{ in_array($role->name,$systemRoles) ? 'bg-gray-100 cursor-not-allowed' : '' }}">

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

                @unless(in_array($role->name,$systemRoles))

                <button
                    type="submit"
                    class="px-6 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-semibold">

                    Update

                </button>

                @endunless

            </div>

        </form>

    </div>

</div>

@endsection