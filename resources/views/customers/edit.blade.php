@extends('layouts.admin')

@section('content')
<div class="space-y-5">
    <div>
        <h1 class="text-2xl font-bold text-slate-800">Edit Pelanggan</h1>
        <p class="mt-1 text-sm text-slate-500">{{ $customer->nomor_pelanggan }} — {{ $customer->nama }}</p>
    </div>

    <form method="POST" action="{{ route('customers.update', $customer) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        @include('customers._form')
    </form>
</div>
@endsection
