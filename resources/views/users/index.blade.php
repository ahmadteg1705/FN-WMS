@extends('layouts.admin')

@section('content')

{{-- Header Halaman --}}
<div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 mb-6">

    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">

        <div>
            <h1 class="text-2xl font-bold text-slate-800">
                User Management
            </h1>

            <p class="text-sm text-slate-500 mt-1">
                Kelola akun pengguna, role, dan status akses sistem FN-WMS.
            </p>
        </div>

        <div class="flex flex-wrap gap-3">

            <a href="{{ route('dashboard') }}"
               class="inline-flex items-center px-5 py-3 rounded-xl border border-slate-300 bg-white hover:bg-slate-100 text-slate-700 font-semibold transition">
                ← Dashboard
            </a>

            <a href="{{ route('users.create') }}"
               class="inline-flex items-center gap-2 px-5 py-3 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-semibold shadow transition">

                <svg xmlns="http://www.w3.org/2000/svg"
                     class="w-5 h-5"
                     fill="none"
                     viewBox="0 0 24 24"
                     stroke="currentColor">
                    <path stroke-linecap="round"
                          stroke-linejoin="round"
                          stroke-width="2"
                          d="M12 4v16m8-8H4"/>
                </svg>

                Tambah User

            </a>

        </div>

    </div>

</div>

<div class="space-y-6">

        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- Flash Message --}}
            @if(session('success'))

                <div class="mb-6 rounded-xl bg-green-100 border border-green-300 text-green-700 px-5 py-4">

                    {{ session('success') }}

                </div>

            @endif

            @if(session('error'))

                <div class="mb-6 rounded-xl bg-red-100 border border-red-300 text-red-700 px-5 py-4">

                    {{ session('error') }}

                </div>

            @endif

            {{-- Statistik --}}
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6 mb-6">

                {{-- Total User --}}
                <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">

                    <div class="text-sm text-slate-500">
                        Total User
                    </div>

                    <div class="mt-2 text-3xl font-bold text-slate-800">
                        {{ $users->total() }}
                    </div>

                </div>

                {{-- User Aktif --}}
                <div class="bg-white rounded-2xl shadow-sm border border-green-200 p-6">

                    <div class="text-sm text-green-600">
                        User Aktif
                    </div>

                    <div class="mt-2 text-3xl font-bold text-green-700">
                        {{ \App\Models\User::where('status', true)->count() }}
                    </div>

                </div>

                {{-- User Nonaktif --}}
                <div class="bg-white rounded-2xl shadow-sm border border-red-200 p-6">

                    <div class="text-sm text-red-600">
                        User Nonaktif
                    </div>

                    <div class="mt-2 text-3xl font-bold text-red-700">
                        {{ \App\Models\User::where('status', false)->count() }}
                    </div>

                </div>

                {{-- Total Role --}}
                <div class="bg-white rounded-2xl shadow-sm border border-indigo-200 p-6">

                    <div class="text-sm text-indigo-600">
                        Total Role
                    </div>

                    <div class="mt-2 text-3xl font-bold text-indigo-700">
                        {{ $roles->count() }}
                    </div>

                </div>

            </div>

            {{-- Bagian 2 dimulai dari sini --}}
                        {{-- Filter --}}
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 mb-6">

                <form method="GET" action="{{ route('users.index') }}">

                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">

                        {{-- Search --}}
                        <div>

                            <label class="block text-sm font-medium text-slate-700 mb-2">
                                Pencarian
                            </label>

                            <input
                                type="text"
                                name="search"
                                value="{{ request('search') }}"
                                placeholder="Nama, Username, Email..."
                                class="w-full rounded-xl border-slate-300 focus:border-indigo-500 focus:ring-indigo-500">

                        </div>

                        {{-- Role --}}
                        <div>

                            <label class="block text-sm font-medium text-slate-700 mb-2">
                                Role
                            </label>

                            <select
                                name="role"
                                class="w-full rounded-xl border-slate-300 focus:border-indigo-500 focus:ring-indigo-500">

                                <option value="">Semua Role</option>

                                @foreach($roles as $role)

                                    <option
                                        value="{{ $role->name }}"
                                        {{ request('role') == $role->name ? 'selected' : '' }}>

                                        {{ $role->name }}

                                    </option>

                                @endforeach

                            </select>

                        </div>

                        {{-- Status --}}
                        <div>

                            <label class="block text-sm font-medium text-slate-700 mb-2">
                                Status
                            </label>

                            <select
                                name="status"
                                class="w-full rounded-xl border-slate-300 focus:border-indigo-500 focus:ring-indigo-500">

                                <option value="">Semua Status</option>

                                <option value="1"
                                    {{ request('status') === '1' ? 'selected' : '' }}>
                                    Aktif
                                </option>

                                <option value="0"
                                    {{ request('status') === '0' ? 'selected' : '' }}>
                                    Nonaktif
                                </option>

                            </select>

                        </div>

                        {{-- Tombol --}}
                        <div class="flex items-end gap-3">

                            <button
                                type="submit"
                                class="inline-flex items-center justify-center px-5 py-3 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-semibold transition">

                                Cari

                            </button>

                            <a
                                href="{{ route('users.index') }}"
                                class="inline-flex items-center justify-center px-5 py-3 rounded-xl bg-slate-200 hover:bg-slate-300 text-slate-700 font-semibold transition">

                                Reset

                            </a>

                        </div>

                    </div>

                </form>

            </div>

            {{-- Bagian 3 dimulai dari sini --}}
            {{-- Tabel User --}}
<div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">

    <div class="overflow-x-auto">

        <table class="min-w-full divide-y divide-slate-200">

            <thead class="bg-slate-50">

                <tr>

                    <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider text-slate-600">
                        User
                    </th>

                    <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider text-slate-600">
                        Username
                    </th>

                    <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider text-slate-600">
                        Role
                    </th>

                    <th class="px-6 py-4 text-center text-xs font-bold uppercase tracking-wider text-slate-600">
                        Status
                    </th>

                    <th class="px-6 py-4 text-center text-xs font-bold uppercase tracking-wider text-slate-600">
                        Login Terakhir
                    </th>

                    <th class="px-6 py-4 text-center text-xs font-bold uppercase tracking-wider text-slate-600">
                        Aksi
                    </th>

                </tr>

            </thead>

            <tbody class="divide-y divide-slate-100 bg-white">

                @forelse($users as $user)

                    <tr class="hover:bg-slate-50 transition">

                        {{-- User --}}
                        <td class="px-6 py-4">

                            <div class="flex items-center gap-4">

                                <img
                                    src="{{ $user->photoUrl() }}"
                                    class="w-12 h-12 rounded-full object-cover border">

                                <div>

                                    <div class="font-semibold text-slate-800">
                                        {{ $user->name }}
                                    </div>

                                    <div class="text-sm text-slate-500">
                                        {{ $user->email }}
                                    </div>

                                    @if($user->employee_code)

                                        <div class="text-xs text-slate-400 mt-1">
                                            {{ $user->employee_code }}
                                        </div>

                                    @endif

                                </div>

                            </div>

                        </td>

                        {{-- Username --}}
                        <td class="px-6 py-4">

                            <span class="font-medium text-slate-700">

                                {{ $user->username }}

                            </span>

                        </td>

                        {{-- Role --}}
                        <td class="px-6 py-4">

                            @forelse($user->roles as $role)

                                <span
                                    class="inline-flex px-3 py-1 rounded-full bg-indigo-100 text-indigo-700 text-xs font-semibold mr-1">

                                    {{ $role->name }}

                                </span>

                            @empty

                                <span
                                    class="inline-flex px-3 py-1 rounded-full bg-slate-100 text-slate-500 text-xs">

                                    Tidak Ada Role

                                </span>

                            @endforelse

                        </td>

                        {{-- Status --}}
                        <td class="px-6 py-4 text-center">

                            @if($user->status)

                                <span
                                    class="inline-flex px-3 py-1 rounded-full bg-green-100 text-green-700 text-xs font-bold">

                                    Aktif

                                </span>

                            @else

                                <span
                                    class="inline-flex px-3 py-1 rounded-full bg-red-100 text-red-700 text-xs font-bold">

                                    Nonaktif

                                </span>

                            @endif

                        </td>

                        {{-- Login Terakhir --}}
                        <td class="px-6 py-4 text-center text-sm text-slate-500">

                            @if($user->last_login_at)

                                {{ $user->last_login_at->format('d M Y') }}

                                <br>

                                <span class="text-xs">

                                    {{ $user->last_login_at->format('H:i') }}

                                </span>

                            @else

                                -

                            @endif

                        </td>

                        {{-- Aksi --}}
                        <td class="px-6 py-4">

                            <div class="flex justify-center gap-2">

                                <a
                                    href="{{ route('users.edit',$user) }}"
                                    class="px-4 py-2 rounded-lg bg-amber-500 hover:bg-amber-600 text-white text-sm font-semibold transition">

                                    Edit

                                </a>

                                <form
                                    action="{{ route('users.destroy',$user) }}"
                                    method="POST"
                                    onsubmit="return confirm('Yakin ingin menghapus user ini?')">

                                    @csrf
                                    @method('DELETE')

                                    <button
                                        class="px-4 py-2 rounded-lg bg-red-600 hover:bg-red-700 text-white text-sm font-semibold transition">

                                        Hapus

                                    </button>

                                </form>

                            </div>

                        </td>

                    </tr>

                @empty

                    <tr>

                        <td colspan="6"
                            class="px-6 py-12 text-center text-slate-500">

                            Belum ada data user.

                        </td>

                    </tr>

                @endforelse

            </tbody>

        </table>

    </div>

</div>

{{-- Bagian 4 dimulai dari sini --}}
{{-- Pagination --}}
<div class="mt-6">

    {{ $users->links() }}

</div>

</div>
</div>

@endsection