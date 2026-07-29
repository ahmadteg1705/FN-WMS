@extends('layouts.admin')

@section('content')

<div class="max-w-3xl mx-auto">

<div class="bg-white rounded-2xl shadow p-6">

<h1 class="text-2xl font-bold mb-6">

Ubah Status Registrasi

</h1>

<form method="POST"
      action="{{ route('registrations.status.update',$registration) }}">

@csrf
@method('PUT')

<div class="mb-5">

<label class="font-semibold">

Status Baru

</label>

<select
name="status"
class="w-full mt-2 rounded-lg border">

@foreach($statuses as $status)

<option
value="{{ $status }}"
@if($registration->status==$status) selected @endif>

{{ $status }}

</option>

@endforeach

</select>

</div>

<div class="mb-5">

<label class="font-semibold">

Catatan

</label>

<textarea
name="catatan"
rows="4"
class="w-full mt-2 rounded-lg border"></textarea>

</div>

<div class="flex gap-3">

<button
class="bg-blue-600 text-white px-5 py-2 rounded-lg">

Simpan

</button>

<a
href="{{ route('registrations.show',$registration) }}"
class="bg-gray-300 px-5 py-2 rounded-lg">

Batal

</a>

</div>

</form>

</div>

</div>

@endsection