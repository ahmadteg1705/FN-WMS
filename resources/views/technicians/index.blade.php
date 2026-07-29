@extends('layouts.admin')

@section('page-title', 'Master Teknisi')

@section('content')

{{-- Header --}}
<div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
    <div>
        <h1 class="text-4xl font-bold text-gray-800">
            Master Teknisi
        </h1>
        <p class="text-gray-500">
            Kelola data dan profil Teknisi Fahasa Net
        </p>
    </div>

    {{-- Action Toolbar --}}
    <div class="flex flex-wrap items-center gap-3">
        <form action="{{ route('technicians.index') }}" method="GET">
            <input
                type="text"
                name="keyword"
                value="{{ request('keyword') }}"
                placeholder="🔍 Cari Teknisi..."
                class="w-64 border border-gray-300 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
        </form>

        <a href="{{ route('technicians.create') }}"
           class="bg-blue-600 hover:bg-blue-700 text-white font-medium px-4 py-2.5 rounded-lg shadow text-sm transition flex items-center gap-1">
            <span>+</span> Tambah Teknisi
        </a>

        <button
            type="button"
            onclick="openImportModal()"
            class="bg-emerald-600 hover:bg-emerald-700 text-white font-medium px-4 py-2.5 rounded-lg shadow text-sm transition flex items-center gap-1">
            📥 Import
        </button>

        <a href="{{ route('technicians.export') }}"
           class="bg-amber-500 hover:bg-amber-600 text-white font-medium px-4 py-2.5 rounded-lg shadow text-sm transition flex items-center gap-1">
            📤 Export
        </a>

        <a href="#"
           class="bg-gray-700 hover:bg-gray-800 text-white font-medium px-4 py-2.5 rounded-lg shadow text-sm transition flex items-center gap-1">
            🖨 Print
        </a>
    </div>
</div>

{{-- Alert Success --}}
@if(session('success'))
    @if(is_array(session('success')))
        @php $data = session('success'); @endphp
        <div class="mb-6 rounded-xl border border-green-300 bg-green-50 p-5 shadow-sm">
            <h2 class="text-lg font-bold text-green-700 flex items-center gap-2">
                ✅ Import Selesai
            </h2>
            <div class="mt-2 text-sm text-green-800 space-y-1">
                <p><span class="font-semibold">Berhasil:</span> {{ $data['success'] }}</p>
                <p><span class="font-semibold">Gagal:</span> {{ $data['failed'] }}</p>
            </div>
            @if(count($data['errors']) > 0)
                <hr class="my-3 border-green-200">
                <div class="space-y-2">
                    @foreach($data['errors'] as $error)
                        <div class="rounded-lg border border-red-200 bg-red-50 p-3 text-xs">
                            <div class="font-semibold text-red-800">{{ $error['nama'] }}</div>
                            <div class="text-red-600">{{ $error['pesan'] }}</div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    @else
        <div class="mb-6 bg-green-50 border-l-4 border-green-500 text-green-700 p-4 rounded-r-lg shadow-sm flex items-center gap-2">
            <svg class="w-5 h-5 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
            </svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif
@endif

{{-- Table Data --}}
<div class="bg-white rounded-xl shadow overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-100 border-b border-gray-200 text-gray-700 text-xs uppercase tracking-wider font-semibold">
                    <th class="py-4 px-4 text-center w-12">No</th>
                    <th class="py-4 px-4 text-center w-16">Foto</th>
                    <th class="py-4 px-4">Nama Teknisi</th>
                    <th class="py-4 px-4 text-center">Username</th>
                    <th class="py-4 px-4 text-center">Team</th>
                    <th class="py-4 px-4 text-center">Jabatan</th>
                    <th class="py-4 px-4 text-center">No HP</th>
                    <th class="py-4 px-4 text-center">Status</th>
                    <th class="py-4 px-4 text-center w-36">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 text-gray-700 text-sm">
                @forelse($technicians as $technician)
                <tr class="hover:bg-gray-50 transition">
                    <td class="py-4 px-4 text-center font-medium text-gray-500">
                        {{ ($technicians->currentPage()-1) * $technicians->perPage() + $loop->iteration }}
                    </td>

                    <td class="py-4 px-4 text-center">
                        @if($technician->foto)
                            <img
                                src="{{ asset('storage/'.$technician->foto) }}"
                                class="mx-auto h-10 w-10 rounded-full object-cover border border-gray-200 shadow-sm">
                        @else
                            <div class="mx-auto flex h-10 w-10 items-center justify-center rounded-full bg-gray-100 text-gray-500 border border-gray-200">
                                👤
                            </div>
                        @endif
                    </td>

                    <td class="py-4 px-4">
                        <div class="font-semibold text-gray-800">{{ $technician->user?->name ?? '-' }}</div>
                        <div class="text-xs text-gray-400">{{ $technician->email ?? '-' }}</div>
                    </td>

                    <td class="py-4 px-4 text-center font-mono text-xs text-gray-600">
                        {{ $technician->user?->username ?? '-' }}
                    </td>

                    <td class="py-4 px-4 text-center">
                        <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-medium bg-blue-50 text-blue-700 border border-blue-200">
                            {{ $technician->team?->nama ?? '-' }}
                        </span>
                    </td>

                    <td class="py-4 px-4 text-center font-medium text-gray-700">
                        {{ $technician->position?->nama ?? '-' }}
                    </td>

                    <td class="py-4 px-4 text-center font-medium text-gray-700">
                        {{ $technician->telepon }}
                    </td>

                    <td class="py-4 px-4 text-center">
                        @if($technician->status)
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700">
                                Aktif
                            </span>
                        @else
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-700">
                                Nonaktif
                            </span>
                        @endif
                    </td>

                    <td class="px-6 py-4 text-center whitespace-nowrap">
                        <div class="flex items-center justify-center gap-2">
                            <a href="{{ route('technicians.edit', $technician->id) }}"
                               class="inline-flex items-center gap-1.5 rounded-xl border border-blue-200 bg-blue-50 px-3 py-1.5 text-xs font-semibold text-blue-600 transition hover:bg-blue-100 hover:text-blue-700">
                                <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                                Edit
                            </a>

                            <form action="{{ route('technicians.destroy', $technician->id) }}"
                                  method="POST"
                                  class="inline">
                                @csrf
                                @method('DELETE')
                                <button
                                    type="submit"
                                    onclick="return confirm('Yakin ingin menghapus teknisi ini?')"
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
                    <td colspan="9" class="text-center py-12 text-gray-500">
                        <div class="flex flex-col items-center justify-center gap-2">
                            <p class="text-base font-medium text-gray-600">Belum ada data Teknisi.</p>
                            <p class="text-xs text-gray-400">Klik tombol "+ Tambah Teknisi" di atas untuk menambahkan data baru.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination Footer --}}
    @if($technicians->hasPages())
    <div class="border-t border-gray-200 p-4">
        {{ $technicians->withQueryString()->links() }}
    </div>
    @endif
</div>

{{-- MODAL IMPORT --}}
<x-fn.import-technician-modal />

{{-- SCRIPT --}}
<script>
function openImportModal(){
    const modal = document.getElementById('importModal');
    if(modal) {
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }
}

function closeImportModal(){
    const modal = document.getElementById('importModal');
    if(modal) {
        modal.classList.remove('flex');
        modal.classList.add('hidden');
    }
}
</script>

@endsection