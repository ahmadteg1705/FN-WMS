@extends('layouts.admin')

@section('content')

<div class="flex items-center justify-between mb-6">

    <div>
        <h1 class="text-2xl font-bold text-gray-800">
            Manajemen Role
        </h1>

        <p class="text-gray-500 mt-1">
            Kelola role pengguna pada sistem FN-WMS.
        </p>
    </div>

    <a href="{{ route('roles.create') }}"
       class="px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-semibold shadow">

        + Tambah Role

    </a>

</div>

@if(session('success'))

<div class="mb-5 rounded-xl bg-green-100 border border-green-300 text-green-700 px-5 py-3">

    {{ session('success') }}

</div>

@endif

<div class="bg-white rounded-2xl shadow border border-gray-100 overflow-hidden">

    <table class="min-w-full">

        <thead class="bg-gray-50">

            <tr>

                <th class="px-6 py-4 text-left text-sm font-bold text-gray-700">
                    No
                </th>

                <th class="px-6 py-4 text-left text-sm font-bold text-gray-700">
                    Nama Role
                </th>

                <th class="px-6 py-4 text-center text-sm font-bold text-gray-700">
                    Permission
                </th>

                <th class="px-6 py-4 text-center text-sm font-bold text-gray-700">
                    User
                </th>

                <th class="px-6 py-4 text-center text-sm font-bold text-gray-700">
                    Status
                </th>

                <th class="px-6 py-4 text-center text-sm font-bold text-gray-700">
                    Aksi
                </th>

            </tr>

        </thead>

        <tbody>

        @forelse($roles as $role)

            @php

                $systemRoles = [
                    'Super Admin',
                    'Admin',
                    'Marketing',
                    'Teknisi',
                    'NOC'
                ];

            @endphp

            <tr class="border-t">

                <td class="px-6 py-4">

                    {{ $loop->iteration }}

                </td>

                <td class="px-6 py-4 font-semibold">

                    {{ $role->name }}

                </td>

                <td class="px-6 py-4 text-center">

                    {{ $role->permissions->count() }}

                </td>

                <td class="px-6 py-4 text-center">

                    {{ $role->users->count() }}

                </td>

                <td class="px-6 py-4 text-center">

                    @if(in_array($role->name,$systemRoles))

                        <span class="px-3 py-1 rounded-full bg-blue-100 text-blue-700 text-xs font-semibold">

                            SYSTEM

                        </span>

                    @else

                        <span class="px-3 py-1 rounded-full bg-gray-100 text-gray-700 text-xs font-semibold">

                            CUSTOM

                        </span>

                    @endif

                </td>

                <td class="px-6 py-4">

                    <div class="flex justify-center gap-2">
                        <a
    href="{{ route('roles.permissions',$role) }}"
    class="px-3 py-1 rounded-lg bg-indigo-600 hover:bg-indigo-700 text-white">

    Permission

</a>

                        <a href="{{ route('roles.edit',$role) }}"
                           class="px-3 py-1 rounded-lg bg-amber-500 text-white hover:bg-amber-600">

                            Edit

                        </a>

                        @if(!in_array($role->name,$systemRoles))

                        <form
                            method="POST"
                            action="{{ route('roles.destroy',$role) }}"
                            onsubmit="return confirm('Hapus role ini?')">

                            @csrf
                            @method('DELETE')

                            <button
                                class="px-3 py-1 rounded-lg bg-red-600 text-white hover:bg-red-700">

                                Hapus

                            </button>

                        </form>

                        @endif

                    </div>

                </td>

            </tr>

        @empty

            <tr>

                <td colspan="6"
                    class="text-center py-8 text-gray-500">

                    Belum ada data Role.

                </td>

            </tr>

        @endforelse

        </tbody>

    </table>

</div>

@endsection