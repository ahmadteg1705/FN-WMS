<?php

namespace App\Http\Controllers;

use App\Models\Package;
use Illuminate\Http\Request;

class PackageController extends Controller
{
    public function index()
    {
        $packages = Package::latest()->get();

        return view('packages.index', compact('packages'));
    }

    public function create()
    {
        return view('packages.create');
    }

    public function store(Request $request)
    {
        $request->validate([
    'nama' => 'required|string|max:255|unique:packages,nama',
    'kecepatan' => 'required',
    'harga' => 'required|numeric',
    'status' => 'required',
    'keterangan' => 'nullable',
],[
    'nama.unique' => 'Nama paket sudah terdaftar.',
]);

        Package::create($request->all());

        return redirect()->route('packages.index')
            ->with('success','Paket berhasil ditambahkan');
    }

    public function edit(Package $package)
    {
        return view('packages.edit', compact('package'));
    }

    public function update(Request $request, Package $package)
{
    $request->validate([
        'nama' => 'required',
        'kecepatan' => 'required',
        'harga' => 'required',
    ]);

    $package->update($request->all());

    return redirect()
            ->route('packages.index')
            ->with('success','Paket berhasil diperbarui.');
}

    public function destroy(Package $package)
    {
        $package->delete();

        return redirect()->route('packages.index');
    }
    public function import(Request $request)
{
    $request->validate([
        'file' => 'required|mimes:xlsx,xls',
    ]);

    $import = new PackageImport();

    $import->import($request->file('file'));

    return redirect()
        ->route('packages.index')
        ->with('success', 'Import selesai.')
        ->with('import_result', [
            'success' => $import->success,
            'failed' => $import->failed,
            'errors' => $import->errors,
        ]);
}
}