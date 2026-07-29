@extends('layouts.admin')

@section('page-title', 'Paket Internet')

@section('content')

{{-- Header --}}
<div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
    <div>
        <h1 class="text-4xl font-bold text-gray-800">
            Paket Internet
        </h1>
        <p class="text-gray-500">
            Kelola daftar paket internet Fahasa Net
        </p>
    </div>

    <a href="{{ route('packages.create') }}"
       class="bg-blue-600 hover:bg-blue-700 text-white font-medium px-6 py-3 rounded-lg shadow transition flex items-center gap-2">
        <span>+</span> Tambah Paket
    </a>
</div>

{{-- Alert Success --}}
@if(session('success'))
<div class="mb-6 bg-green-50 border-l-4 border-green-500 text-green-700 p-4 rounded-r-lg shadow-sm flex items-center justify-between">
    <div class="flex items-center gap-2">
        <svg class="w-5 h-5 text-green-500" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
        </svg>
        <span>{{ session('success') }}</span>
    </div>
</div>
@endif

{{-- Table Data --}}
<div class="bg-white rounded-xl shadow overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-100 border-b border-gray-200 text-gray-700 text-xs uppercase tracking-wider font-semibold">
                    <th class="py-4 px-6 text-center w-16">No</th>
                    <th class="py-4 px-6">Nama Paket</th>
                    <th class="py-4 px-6">Kecepatan</th>
                    <th class="py-4 px-6">Harga</th>
                    <th class="py-4 px-6 text-center">Status</th>
                    <th class="py-4 px-6 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 text-gray-700 text-sm">
                @forelse($packages as $package)
                <tr class="hover:bg-gray-50 transition">
                    <td class="py-4 px-6 text-center font-medium text-gray-500">
                        {{ $loop->iteration }}
                    </td>

                    <td class="py-4 px-6 font-semibold text-gray-800">
                        {{ $package->nama }}
                    </td>

                    <td class="py-4 px-6">
                        <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-medium bg-blue-50 text-blue-700 border border-blue-200">
                            {{ $package->kecepatan }}
                        </span>
                    </td>

                    <td class="py-4 px-6 font-medium text-gray-800">
                        Rp {{ number_format($package->harga, 0, ',', '.') }}
                    </td>

                    <td class="py-4 px-6 text-center">
                        @if($package->status)
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700">
                                Aktif
                            </span>
                        @else
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-700">
                                Nonaktif
                            </span>
                        @endif
                    </td>

                    <td class="py-4 px-6 text-center">
                        <div class="flex items-center justify-center gap-2">
                            <a href="{{ route('packages.edit', $package->id) }}"
                               class="inline-flex items-center gap-1.5 rounded-xl border border-blue-200 bg-blue-50 px-3 py-1.5 text-xs font-semibold text-blue-600 transition hover:bg-blue-100 hover:text-blue-700">
            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
            </svg>
            Edit
                            </a>

                            <form action="{{ route('packages.destroy', $package->id) }}"
                                  method="POST"
                                  class="inline">
                                @csrf
                                @method('DELETE')
                                <button
                                    type="submit"
                                    onclick="return confirm('Yakin ingin menghapus paket ini?')"
                                    class="inline-flex items-center gap-1.5 rounded-xl border border-rose-200 bg-rose-50 px-3 py-1.5 text-xs font-semibold text-rose-600 transition hover:bg-rose-100 hover:text-rose-700">
                <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
                Hapus
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center py-12 text-gray-500">
                        <div class="flex flex-col items-center justify-center gap-2">
                            <p class="text-base font-medium text-gray-600">Belum ada data paket.</p>
                            <p class="text-xs text-gray-400">Klik tombol "+ Tambah Paket" di atas untuk menambahkan data baru.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection