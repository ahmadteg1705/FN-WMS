@extends('layouts.admin')

@section('page-title', 'Work Order')

@section('content')

<div class="max-w-7xl mx-auto py-6 space-y-6">

    {{-- ==========================================================
        HEADER
    =========================================================== --}}
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-5">

        <div>

            <nav class="flex items-center text-sm text-gray-500">

                <a href="{{ route('dashboard') }}"
                   class="hover:text-blue-600 transition">

                    Dashboard

                </a>

                <span class="mx-2">

                    /

                </span>

                <span class="font-medium text-gray-700">

                    Work Order

                </span>

            </nav>

            <h1 class="mt-2 text-3xl font-bold text-slate-800">

                Work Order

            </h1>

            <p class="mt-2 text-sm text-slate-500">

                Kelola seluruh penjadwalan pekerjaan teknisi Fahasa Net.

            </p>

        </div>

        <div class="flex gap-3">

            <a href="{{ route('registrations.index') }}"
               class="inline-flex items-center rounded-xl bg-blue-600 px-5 py-3 text-sm font-semibold text-white shadow hover:bg-blue-700 transition">

                <svg xmlns="http://www.w3.org/2000/svg"
                     class="w-5 h-5 mr-2"
                     fill="none"
                     viewBox="0 0 24 24"
                     stroke="currentColor">

                    <path stroke-linecap="round"
                          stroke-linejoin="round"
                          stroke-width="2"
                          d="M12 4v16m8-8H4"/>

                </svg>

                Buat Work Order

            </a>

        </div>

    </div>

    {{-- ==========================================================
        DASHBOARD
    =========================================================== --}}
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-5">

        {{-- Total --}}
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm">

            <div class="p-6">

                <div class="flex justify-between items-start">

                    <div>

                        <div class="text-sm text-slate-500">

                            Total Work Order

                        </div>

                        <div class="mt-3 text-3xl font-bold text-slate-800">

                            {{ $stats['total'] ?? 0 }}

                        </div>

                    </div>

                    <div class="w-14 h-14 rounded-xl bg-blue-100 flex items-center justify-center">

                        <svg xmlns="http://www.w3.org/2000/svg"
                             class="w-7 h-7 text-blue-600"
                             fill="none"
                             viewBox="0 0 24 24"
                             stroke="currentColor">

                            <path stroke-linecap="round"
                                  stroke-linejoin="round"
                                  stroke-width="2"
                                  d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2V9m-7-4h7m0 0v7m0-7L10 14"/>

                        </svg>

                    </div>

                </div>

            </div>

        </div>

        {{-- Pending --}}
        <div class="bg-white rounded-2xl border border-amber-200 shadow-sm">

            <div class="p-6">

                <div class="flex justify-between items-start">

                    <div>

                        <div class="text-sm text-amber-700">

                            Pending

                        </div>

                        <div class="mt-3 text-3xl font-bold text-amber-700">

                            {{ $stats['pending'] ?? 0 }}

                        </div>

                    </div>

                    <div class="w-14 h-14 rounded-xl bg-amber-100 flex items-center justify-center">

                        <svg xmlns="http://www.w3.org/2000/svg"
                             class="w-7 h-7 text-amber-600"
                             fill="none"
                             viewBox="0 0 24 24"
                             stroke="currentColor">

                            <path stroke-linecap="round"
                                  stroke-linejoin="round"
                                  stroke-width="2"
                                  d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>

                        </svg>

                    </div>

                </div>

            </div>

        </div>

        {{-- Progress --}}
        <div class="bg-white rounded-2xl border border-green-200 shadow-sm">

            <div class="p-6">

                <div class="flex justify-between items-start">

                    <div>

                        <div class="text-sm text-green-700">

                            On Progress

                        </div>

                        <div class="mt-3 text-3xl font-bold text-green-700">

                            {{ $stats['progress'] ?? 0 }}

                        </div>

                    </div>

                    <div class="w-14 h-14 rounded-xl bg-green-100 flex items-center justify-center">

                        <svg xmlns="http://www.w3.org/2000/svg"
                             class="w-7 h-7 text-green-600"
                             fill="none"
                             viewBox="0 0 24 24"
                             stroke="currentColor">

                            <path stroke-linecap="round"
                                  stroke-linejoin="round"
                                  stroke-width="2"
                                  d="M5 13l4 4L19 7"/>

                        </svg>

                    </div>

                </div>

            </div>

        </div>

        {{-- Completed --}}
        <div class="bg-white rounded-2xl border border-blue-200 shadow-sm">

            <div class="p-6">

                <div class="flex justify-between items-start">

                    <div>

                        <div class="text-sm text-blue-700">

                            Selesai

                        </div>

                        <div class="mt-3 text-3xl font-bold text-blue-700">

                            {{ $stats['completed'] ?? 0 }}

                        </div>

                    </div>

                    <div class="w-14 h-14 rounded-xl bg-blue-100 flex items-center justify-center">

                        <svg xmlns="http://www.w3.org/2000/svg"
                             class="w-7 h-7 text-blue-600"
                             fill="none"
                             viewBox="0 0 24 24"
                             stroke="currentColor">

                            <path stroke-linecap="round"
                                  stroke-linejoin="round"
                                  stroke-width="2"
                                  d="M9 12l2 2 4-4m5-2a9 9 0 11-18 0 9 9 0 0118 0z"/>

                        </svg>

                    </div>

                </div>

            </div>

        </div>

    </div>

    {{-- ==========================================================
        TOOLBAR
    =========================================================== --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm">

        <div class="p-5">

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-4">

                <div class="lg:col-span-4">

                    <label class="block text-sm font-medium text-slate-700 mb-2">

                        Pencarian

                    </label>

                    <input
                        type="text"
                        class="w-full rounded-xl border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                        placeholder="Cari No WO / Nama Pelanggan">

                </div>

                <div class="lg:col-span-2">

                    <label class="block text-sm font-medium text-slate-700 mb-2">

                        Status

                    </label>

                    <select class="w-full rounded-xl border-gray-300">

                        <option>Semua</option>
                        <option>Pending</option>
                        <option>On Progress</option>
                        <option>Selesai</option>

                    </select>

                </div>

                <div class="lg:col-span-2">

                    <label class="block text-sm font-medium text-slate-700 mb-2">

                        Prioritas

                    </label>

                    <select class="w-full rounded-xl border-gray-300">

                        <option>Semua</option>
                        <option>Urgent</option>
                        <option>Tinggi</option>
                        <option>Normal</option>

                    </select>

                </div>
                                <div class="lg:col-span-2">

                    <label class="block text-sm font-medium text-slate-700 mb-2">

                        Tanggal

                    </label>

                    <input
                        type="date"
                        class="w-full rounded-xl border-gray-300 focus:border-blue-500 focus:ring-blue-500">

                </div>

                <div class="lg:col-span-2 flex items-end gap-2">

                    <button
                        type="submit"
                        class="flex-1 rounded-xl bg-blue-600 px-4 py-3 text-white font-semibold hover:bg-blue-700 transition">

                        Cari

                    </button>

                    <button
                        type="reset"
                        class="rounded-xl border border-slate-300 px-4 py-3 hover:bg-slate-100 transition">

                        Reset

                    </button>

                </div>

            </div>

        </div>

    </div>

    {{-- ==========================================================
        DATA WORK ORDER
    =========================================================== --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">

        {{-- Header --}}
        <div class="px-6 py-5 border-b bg-slate-50 flex justify-between items-center">

            <div>

                <h2 class="text-lg font-semibold text-slate-800">

                    Daftar Work Order

                </h2>

                <p class="text-sm text-slate-500 mt-1">

                    Total :
                    <span class="font-semibold">

                        {{ $workOrders->total() }}

                    </span>
                    Data

                </p>

            </div>

        </div>

        <div class="overflow-x-auto">

            <table class="min-w-full">

                <thead class="bg-slate-100">

                    <tr>

                        <th class="px-5 py-4 text-left text-xs font-semibold uppercase">

                            No WO

                        </th>

                        <th class="px-5 py-4 text-left text-xs font-semibold uppercase">

                            Pelanggan

                        </th>

                        <th class="px-5 py-4 text-left text-xs font-semibold uppercase">

                            Tim Teknisi

                        </th>

                        <th class="px-5 py-4 text-center text-xs font-semibold uppercase">

                            Jadwal

                        </th>

                        <th class="px-5 py-4 text-center text-xs font-semibold uppercase">

                            Prioritas

                        </th>

                        <th class="px-5 py-4 text-center text-xs font-semibold uppercase">

                            Status

                        </th>

                        <th class="px-5 py-4 text-center text-xs font-semibold uppercase">

                            Aksi

                        </th>

                    </tr>

                </thead>

                <tbody class="divide-y divide-slate-200">

                @forelse($workOrders as $wo)

                    <tr class="hover:bg-sky-50 transition">

                        {{-- NO WO --}}
                        <td class="px-5 py-5">

                            <div class="font-bold text-slate-800">

                                {{ $wo->work_order_no }}

                            </div>

                            <div class="text-xs text-slate-500 mt-1">

                                {{ $wo->created_at->format('d M Y') }}

                            </div>

                        </td>

                        {{-- PELANGGAN --}}
                        <td class="px-5 py-5">

                            <div class="font-semibold text-slate-800">

                                {{ $wo->registration->nama }}

                            </div>

                            <div class="text-xs text-slate-500 mt-1">

                                {{ $wo->registration->telepon ?? '-' }}

                            </div>

                        </td>

                        {{-- TIM --}}
                        <td class="px-5 py-5">

                            <div class="font-semibold text-blue-700">

                                {{ $wo->team->nama }}

                            </div>

                            <div class="text-xs text-slate-500 mt-1">

                                Leader :
                                <span class="font-medium">

                                    {{ $wo->team->leader }}

                                </span>

                            </div>

                        </td>

                        {{-- JADWAL --}}
                        <td class="px-5 py-5 text-center">

                            <div class="font-semibold">

                                {{ $wo->tanggal->format('d M Y') }}

                            </div>

                            <div class="text-xs text-slate-500 mt-1">

                                {{ $wo->jam }}

                            </div>

                        </td>
                                                {{-- ==========================================================
                            PRIORITAS
                        =========================================================== --}}
                        <td class="px-5 py-5 text-center">

                            @php

                                $priorityClass = match($wo->prioritas) {

                                    'Urgent' => 'bg-red-100 text-red-700',

                                    'Tinggi' => 'bg-orange-100 text-orange-700',

                                    'Normal' => 'bg-blue-100 text-blue-700',

                                    'Rendah' => 'bg-slate-100 text-slate-700',

                                    default => 'bg-gray-100 text-gray-700',

                                };

                            @endphp

                            <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold {{ $priorityClass }}">

                                {{ $wo->prioritas }}

                            </span>

                        </td>

                        {{-- ==========================================================
                            STATUS
                        =========================================================== --}}
                        <td class="px-5 py-5 text-center">

                            @php

                                $statusClass = match($wo->status) {

                                    'Pending' => 'bg-amber-100 text-amber-700',

                                    'On Progress' => 'bg-green-100 text-green-700',

                                    'Selesai' => 'bg-blue-100 text-blue-700',

                                    'Dibatalkan' => 'bg-red-100 text-red-700',

                                    default => 'bg-gray-100 text-gray-700',

                                };

                            @endphp

                            <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold {{ $statusClass }}">

                                {{ $wo->status }}

                            </span>

                        </td>

                        {{-- ==========================================================
                            AKSI
                        =========================================================== --}}
                        <td class="px-5 py-5">

                            <div class="flex justify-center gap-2">

                                {{-- DETAIL --}}
                                <a href="{{ route('work-orders.show', $wo) }}"
                                class="inline-flex items-center gap-1.5 rounded border border-blue-600 px-3 py-1 text-xs font-medium text-blue-600 hover:bg-blue-50 transition">

                                    <svg xmlns="http://www.w3.org/2000/svg"
                                        class="w-4 h-4"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke="currentColor">

                                        <path stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>

                                        <path stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>

                                    </svg>

                                    Detail

                                </a>

                                @if(auth()->user()->hasAnyRole(['Super Admin','Admin']))

                                    <a href="{{ route('work-orders.edit',$wo) }}"
                                    class="w-8 h-8 flex items-center justify-center rounded-lg bg-amber-400 hover:bg-amber-500 text-white transition">

                                        <svg class="w-4 h-4"
                                            fill="none"
                                            stroke="currentColor"
                                            viewBox="0 0 24 24">

                                            <path stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>

                                        </svg>

                                    </a>

                                    <form action="{{ route('work-orders.destroy',$wo) }}"
                                        method="POST"
                                        onsubmit="return confirm('Yakin menghapus Work Order ini?')">

                                        @csrf
                                        @method('DELETE')

                                        <button
                                            type="submit"
                                            class="w-8 h-8 flex items-center justify-center rounded-lg bg-red-500 hover:bg-red-600 text-white transition">

                                            <svg class="w-4 h-4"
                                                fill="none"
                                                stroke="currentColor"
                                                viewBox="0 0 24 24">

                                                <path stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    stroke-width="2"
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>

                                            </svg>

                                        </button>

                                    </form>

                                @endif

                            </div>

                        </td>

                    </tr>

                @empty
                                    <tr>

                        <td colspan="7" class="px-6 py-16 text-center">

                            <div class="flex flex-col items-center">

                                <svg xmlns="http://www.w3.org/2000/svg"
                                     class="w-20 h-20 text-slate-300"
                                     fill="none"
                                     viewBox="0 0 24 24"
                                     stroke="currentColor">

                                    <path stroke-linecap="round"
                                          stroke-linejoin="round"
                                          stroke-width="1.5"
                                          d="M9 17v-2a4 4 0 014-4h6M3 7h18M5 3h14a2 2 0 012 2v14a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2z"/>

                                </svg>

                                <h3 class="mt-5 text-lg font-semibold text-slate-700">

                                    Belum Ada Work Order

                                </h3>

                                <p class="mt-2 text-sm text-slate-500">

                                    Data Work Order masih kosong.
                                    Silakan buat Work Order baru untuk mulai menjadwalkan pekerjaan.

                                </p>

                                <a href="{{ route('registrations.index') }}"
                                   class="mt-6 inline-flex items-center rounded-xl bg-blue-600 px-5 py-3 text-sm font-semibold text-white hover:bg-blue-700 transition">

                                    Buat Work Order

                                </a>

                            </div>

                        </td>

                    </tr>

                @endforelse

                </tbody>

            </table>

        </div>

        {{-- ==========================================================
            PAGINATION
        =========================================================== --}}

        @if($workOrders->hasPages())

            <div class="border-t bg-slate-50 px-6 py-4">

                {{ $workOrders->links() }}

            </div>

        @endif

    </div>

    {{-- ==========================================================
        FOOTER
    =========================================================== --}}

    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 text-sm text-slate-500">

        <div>

            Menampilkan

            <span class="font-semibold">

                {{ $workOrders->firstItem() ?? 0 }}

            </span>

            -

            <span class="font-semibold">

                {{ $workOrders->lastItem() ?? 0 }}

            </span>

            dari

            <span class="font-semibold">

                {{ $workOrders->total() }}

            </span>

            Work Order.

        </div>

        <div>

            FN-WMS &copy; {{ date('Y') }}

        </div>

    </div>

</div>

@endsection