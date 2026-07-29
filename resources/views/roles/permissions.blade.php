@extends('layouts.admin')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-2 border-b border-slate-200/80">
        <div>
            <div class="flex items-center gap-3">
                <h1 class="text-2xl sm:text-3xl font-bold text-slate-800 tracking-tight">
                    Permission Role
                </h1>
                <span class="px-3 py-1 text-xs font-semibold rounded-full bg-indigo-50 text-indigo-700 border border-indigo-100">
                    {{ $role->name }}
                </span>
            </div>
            <p class="text-sm text-slate-500 mt-1">
                Kelola hak akses dan izin modul untuk role ini.
            </p>
        </div>

        <a href="{{ route('roles.index') }}"
           class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-white border border-slate-200 text-slate-700 text-sm font-medium shadow-sm hover:bg-slate-50 hover:text-slate-900 transition-colors">
            <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Kembali
        </a>
    </div>

    <form action="{{ route('roles.permissions.update', $role) }}" method="POST">
        @csrf
        @method('PUT')

        {{-- Toolbar / Actions --}}
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 p-4 sm:p-5 mb-6">
            <div class="flex flex-col xl:flex-row gap-4 xl:items-center xl:justify-between">

                {{-- Search Box --}}
                <div class="relative flex-1">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                    <input id="permissionSearch"
                           type="text"
                           placeholder="Cari modul atau permission..."
                           class="w-full pl-10 pr-4 py-2.5 rounded-xl text-sm border border-slate-200 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 text-slate-800 placeholder-slate-400 transition-all">
                </div>

                {{-- Toolbar Buttons --}}
                <div class="flex flex-wrap items-center gap-2.5">
                    <button type="button"
                            id="checkAll"
                            class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-indigo-50 hover:bg-indigo-100 text-indigo-700 text-sm font-semibold transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Pilih Semua
                    </button>

                    <button type="button"
                            id="uncheckAll"
                            class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-700 text-sm font-semibold transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                        Hapus Semua
                    </button>

                    <button type="submit"
                            class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold shadow-sm transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/>
                        </svg>
                        Simpan Perubahan
                    </button>
                </div>

            </div>
        </div>

        {{-- Grid Permission --}}
<div id="permissionContainer" class="grid grid-cols-1 xl:grid-cols-2 gap-6">

    @foreach($groupedPermissions as $module => $permissions)

        <div
            class="permission-card bg-white rounded-2xl shadow-sm border border-slate-200/80 overflow-hidden transition-all duration-200 hover:border-indigo-200 hover:shadow-md">

            {{-- Header Card --}}
            <div class="px-6 py-4 bg-slate-50/70 border-b border-slate-100 flex items-center justify-between">

                <div class="flex items-center gap-3">

                    <div class="w-9 h-9 rounded-lg bg-indigo-100/70 text-indigo-600 flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round"
                                  stroke-linejoin="round"
                                  stroke-width="2"
                                  d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                    </div>

                    <div>
                        <h2 class="text-base font-bold text-slate-800 tracking-wide">
                            {{ strtoupper(str_replace('_',' ',$module)) }}
                        </h2>

                        <p class="text-xs text-slate-500 mt-0.5">
                            {{ count($permissions) }} Permission
                        </p>
                    </div>

                </div>

                <div class="flex items-center gap-4">

                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-slate-200/60 text-slate-600">
                        Modul
                    </span>

                    <label class="inline-flex items-center gap-2 cursor-pointer">

                        <input
                            type="checkbox"
                            class="module-checkbox rounded border-slate-300 text-indigo-600"
                            data-module="{{ $module }}">

                        <span class="text-xs font-semibold text-slate-600">
                            Pilih Semua
                        </span>

                    </label>

                </div>

            </div>

            {{-- Body --}}
            <div class="p-5 sm:p-6">

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">

                    @foreach($permissions as $permission)

                        @php
                            $action = ucfirst(
                                str_replace(
                                    '-',
                                    ' ',
                                    \Illuminate\Support\Str::after($permission->name,'.')
                                )
                            );
                        @endphp

                        <label
                            class="flex items-center gap-3 p-3 rounded-xl border border-slate-200/80 bg-slate-50/30 hover:bg-indigo-50/40 hover:border-indigo-300 transition-all cursor-pointer group">

                            <input
                                type="checkbox"
                                class="permission-checkbox w-4 h-4 rounded text-indigo-600 border-slate-300 focus:ring-indigo-500 focus:ring-offset-0"
                                data-module="{{ $module }}"
                                name="permissions[]"
                                value="{{ $permission->name }}"
                                {{ in_array($permission->name,$rolePermissions) ? 'checked' : '' }}>

                            <span class="text-sm font-medium text-slate-700 group-hover:text-indigo-900 transition-colors">
                                {{ $action }}
                            </span>

                        </label>

                    @endforeach

                </div>

            </div>

        </div>

    @endforeach

</div>

        {{-- Footer Sticky Bar / Stats --}}
        <div class="mt-8 pt-4 border-t border-slate-200/80 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-2 text-sm text-slate-500">
                <span class="inline-block w-2 h-2 rounded-full bg-emerald-500"></span>
                Total dipilih:
                <span
                    id="selectedCount"
                    class="font-bold text-indigo-600">
                    {{ count($rolePermissions) }}
                </span>
                Permission
            </div>

            <div class="flex items-center gap-3">
                <a href="{{ route('roles.index') }}"
                   class="px-4 py-2.5 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 text-sm font-medium transition-colors">
                    Batal
                </a>

                <button type="submit"
                        class="px-6 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold shadow-sm transition-colors">
                    Simpan Perubahan
                </button>
            </div>
        </div>

    </form>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    const permissionCheckboxes = document.querySelectorAll('.permission-checkbox');
    const moduleCheckboxes = document.querySelectorAll('.module-checkbox');

    const checkAll = document.getElementById('checkAll');
    const uncheckAll = document.getElementById('uncheckAll');
    const search = document.getElementById('permissionSearch');

    /*
    |--------------------------------------------------------------------------
    | Update Counter
    |--------------------------------------------------------------------------
    */

    function updateCounter() {

        const total = document.querySelectorAll('.permission-checkbox:checked').length;

        document.getElementById('selectedCount').textContent = total;

    }

    /*
    |--------------------------------------------------------------------------
    | Sinkronisasi Checkbox Modul
    |--------------------------------------------------------------------------
    */

    function syncModuleCheckbox(module) {

        const permissions = document.querySelectorAll(
            '.permission-checkbox[data-module="' + module + '"]'
        );

        const moduleCheckbox = document.querySelector(
            '.module-checkbox[data-module="' + module + '"]'
        );

        if (!moduleCheckbox) return;

        const checked = [...permissions].filter(p => p.checked).length;

        moduleCheckbox.checked = permissions.length > 0 && checked === permissions.length;

    }

    /*
    |--------------------------------------------------------------------------
    | Saat Permission dicentang
    |--------------------------------------------------------------------------
    */

    permissionCheckboxes.forEach(function (checkbox) {

        checkbox.addEventListener('change', function () {

            syncModuleCheckbox(this.dataset.module);

            updateCounter();

        });

    });

    /*
    |--------------------------------------------------------------------------
    | Saat Pilih Semua Modul
    |--------------------------------------------------------------------------
    */

    moduleCheckboxes.forEach(function (checkbox) {

        checkbox.addEventListener('change', function () {

            const module = this.dataset.module;

            document.querySelectorAll(
                '.permission-checkbox[data-module="' + module + '"]'
            ).forEach(function (permission) {

                permission.checked = checkbox.checked;

            });

            updateCounter();

        });

    });

    /*
    |--------------------------------------------------------------------------
    | Tombol Pilih Semua
    |--------------------------------------------------------------------------
    */

    if (checkAll) {

        checkAll.addEventListener('click', function () {

            permissionCheckboxes.forEach(function (checkbox) {

                checkbox.checked = true;

            });

            moduleCheckboxes.forEach(function (checkbox) {

                checkbox.checked = true;

            });

            updateCounter();

        });

    }

    /*
    |--------------------------------------------------------------------------
    | Tombol Hapus Semua
    |--------------------------------------------------------------------------
    */

    if (uncheckAll) {

        uncheckAll.addEventListener('click', function () {

            permissionCheckboxes.forEach(function (checkbox) {

                checkbox.checked = false;

            });

            moduleCheckboxes.forEach(function (checkbox) {

                checkbox.checked = false;

            });

            updateCounter();

        });

    }

    /*
    |--------------------------------------------------------------------------
    | Search
    |--------------------------------------------------------------------------
    */

    if (search) {

        search.addEventListener('keyup', function () {

            const keyword = this.value.toLowerCase();

            document.querySelectorAll('.permission-card').forEach(function (card) {

                const text = card.innerText.toLowerCase();

                card.style.display = text.includes(keyword)
                    ? ''
                    : 'none';

            });

        });

    }

    /*
    |--------------------------------------------------------------------------
    | Inisialisasi
    |--------------------------------------------------------------------------
    */

    moduleCheckboxes.forEach(function (checkbox) {

        syncModuleCheckbox(checkbox.dataset.module);

    });

    updateCounter();

});
</script>
@endpush