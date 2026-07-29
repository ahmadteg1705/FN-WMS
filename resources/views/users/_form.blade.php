@csrf

<div class="grid grid-cols-1 md:grid-cols-2 gap-6">

    {{-- Kode Pegawai --}}
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">
            Kode Pegawai
        </label>

        <input
            type="text"
            name="employee_code"
            value="{{ old('employee_code', $user->employee_code ?? '') }}"
            class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">

        @error('employee_code')
            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
        @enderror
    </div>

    {{-- Nama --}}
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">
            Nama Lengkap
        </label>

        <input
            type="text"
            name="name"
            value="{{ old('name', $user->name ?? '') }}"
            required
            class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">

        @error('name')
            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
        @enderror
    </div>

    {{-- Username --}}
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">
            Username
        </label>

        <input
            type="text"
            name="username"
            value="{{ old('username', $user->username ?? '') }}"
            required
            class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">

        @error('username')
            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
        @enderror
    </div>

    {{-- Email --}}
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">
            Email
        </label>

        <input
            type="email"
            name="email"
            value="{{ old('email', $user->email ?? '') }}"
            required
            class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">

        @error('email')
            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
        @enderror
    </div>

    {{-- No HP --}}
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">
            Nomor HP
        </label>

        <input
            type="tel"
            name="phone"
            value="{{ old('phone', $user->phone ?? '') }}"
            class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">

        @error('phone')
            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
        @enderror
    </div>

    {{-- Role --}}
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">
            Role
        </label>

        <select
            name="role"
            required
            class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">

            <option value="">Pilih Role</option>

            @foreach($roles as $role)

                <option
                    value="{{ $role->name }}"
                    {{ old('role', isset($user) ? optional($user->roles->first())->name : '') == $role->name ? 'selected' : '' }}>

                    {{ $role->name }}

                </option>

            @endforeach

        </select>

        @error('role')
            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
        @enderror
    </div>

    {{-- Password --}}
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">
            Password
        </label>

        <input
            type="password"
            name="password"
            class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">

        @isset($user)
            <small class="text-gray-500">
                Kosongkan jika tidak ingin mengganti password.
            </small>
        @endisset

        @error('password')
            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
        @enderror
    </div>

    {{-- Konfirmasi Password --}}
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">
            Konfirmasi Password
        </label>

        <input
            type="password"
            name="password_confirmation"
            class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
    </div>

    {{-- Foto --}}
    <div class="md:col-span-2">
        <label class="block text-sm font-medium text-gray-700 mb-2">
            Foto Profil
        </label>

        <input
            type="file"
            name="photo"
            accept="image/*"
            class="w-full rounded-lg border-gray-300">

        @error('photo')
            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
        @enderror

        @isset($user)
            @if($user->photo)
                <img
                    src="{{ $user->photoUrl() }}"
                    class="mt-4 w-24 h-24 rounded-full border object-cover">
            @endif
        @endisset
    </div>

    {{-- Status --}}
    <div class="md:col-span-2">

        <label class="inline-flex items-center">

            <input type="hidden" name="status" value="0">

            <input
                type="checkbox"
                name="status"
                value="1"
                class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                {{ old('status', $user->status ?? true) ? 'checked' : '' }}>

            <span class="ml-2">
                User Aktif
            </span>

        </label>

    </div>

</div>

<div class="mt-8 flex justify-end gap-3">

    <a
        href="{{ route('users.index') }}"
        class="px-5 py-2 rounded-lg bg-gray-200 hover:bg-gray-300">

        Batal

    </a>

    <button
        type="submit"
        class="px-5 py-2 rounded-lg bg-indigo-600 hover:bg-indigo-700 text-white">

        Simpan

    </button>

</div>