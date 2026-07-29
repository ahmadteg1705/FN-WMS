@extends('layouts.admin')

@section('content')

<div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6">

    <div class="bg-white rounded-xl shadow-lg p-6">
        <h3 class="text-gray-500">Pelanggan</h3>
        <p class="text-4xl font-bold text-blue-600 mt-3">0</p>
    </div>

    <div class="bg-white rounded-xl shadow-lg p-6">
        <h3 class="text-gray-500">Teknisi</h3>
        <p class="text-4xl font-bold text-green-600 mt-3">0</p>
    </div>

    <div class="bg-white rounded-xl shadow-lg p-6">
        <h3 class="text-gray-500">Jadwal Hari Ini</h3>
        <p class="text-4xl font-bold text-orange-500 mt-3">0</p>
    </div>

    <div class="bg-white rounded-xl shadow-lg p-6">
        <h3 class="text-gray-500">Pekerjaan Selesai</h3>
        <p class="text-4xl font-bold text-purple-600 mt-3">0</p>
    </div>

</div>

@endsection