@extends('layouts.admin')

@section('content')
<div class="space-y-5">
    <div>
        <h1 class="text-2xl font-bold text-slate-800">Tambah Pelanggan</h1>
        <p class="mt-1 text-sm text-slate-500">Tambahkan data identitas, jaringan, PPPoE, dan foto pelanggan.</p>
    </div>

    <form method="POST" action="{{ route('customers.store') }}" enctype="multipart/form-data">
        @csrf
        @include('customers._form')
    </form>
</div>
@endsection
