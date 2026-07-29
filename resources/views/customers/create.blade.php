@extends('layouts.admin')

@section('content')

<h1 class="text-3xl font-bold mb-6">
Tambah Pelanggan
</h1>

<form action="{{ route('customers.store') }}" method="POST">

@csrf

<div class="bg-white rounded-xl shadow p-6">

<div class="grid grid-cols-2 gap-4">

<div>
<label>Nama</label>

<input
type="text"
name="nama"
class="w-full border rounded p-2">
</div>

<div>
<label>Telepon</label>

<input
type="text"
name="telepon"
class="w-full border rounded p-2">
</div>

<div>
<label>Paket</label>

<input
type="text"
name="paket"
class="w-full border rounded p-2">
</div>

<div>
<label>ODP</label>

<input
type="text"
name="odp"
class="w-full border rounded p-2">
</div>

<div class="col-span-2">

<label>Alamat</label>

<textarea
name="alamat"
class="w-full border rounded p-2"></textarea>

</div>

</div>

<button
class="mt-6 bg-blue-600 text-white px-6 py-2 rounded">

Simpan

</button>

</div>

</form>

@endsection