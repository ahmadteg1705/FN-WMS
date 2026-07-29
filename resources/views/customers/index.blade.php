@extends('layouts.admin')
@section('page-title','Data Pelanggan')
@section('content')

<div class="flex justify-end mb-6">

    <a href="{{ route('customers.create') }}"
       class="bg-blue-600 text-white px-5 py-2 rounded-lg">

        + Tambah Pelanggan

    </a>

</div>

<div class="bg-white rounded-xl shadow">

<table class="w-full">

<thead class="bg-gray-100">

<tr>

<th class="p-3 text-left">No</th>
<th class="p-3 text-left">Nama</th>
<th class="p-3 text-left">Telepon</th>
<th class="p-3 text-left">Paket</th>
<th class="p-3 text-left">ODP</th>
<th class="p-3 text-left">Status</th>

</tr>

</thead>

<tbody>

@forelse($customers as $customer)

<tr class="border-b">

<td class="p-3">{{ $loop->iteration }}</td>

<td class="p-3">{{ $customer->nama }}</td>

<td class="p-3">{{ $customer->telepon }}</td>

<td class="p-3">{{ $customer->paket }}</td>

<td class="p-3">{{ $customer->odp }}</td>

<td class="p-3">{{ $customer->status }}</td>

</tr>

@empty

<tr>

<td colspan="6"
class="text-center p-8 text-gray-500">

Belum ada data pelanggan

</td>

</tr>

@endforelse

</tbody>

</table>

</div>

@endsection