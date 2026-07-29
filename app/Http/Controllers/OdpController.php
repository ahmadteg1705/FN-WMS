<?php

namespace App\Http\Controllers;

use App\Exports\OdpExport;
use App\Imports\OdpImport;
use Illuminate\Http\Request;
use App\Models\Router;
use App\Models\Odp;

class OdpController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
{
    $keyword = $request->keyword;

    $odps = Odp::when($keyword, function ($query) use ($keyword) {

        $query->where('nama', 'like', "%{$keyword}%")
              ->orWhere('router', 'like', "%{$keyword}%")
              ->orWhere('card', 'like', "%{$keyword}%");

    })
    ->latest()
    ->paginate(10);

    // Semua ODP untuk Leaflet
    $mapOdps = Odp::whereNotNull('latitude')
        ->whereNotNull('longitude')
        ->get();

    return view('odps.index', compact(
        'odps',
        'keyword',
        'mapOdps'
    ));
}
    /**
     * Show the form for creating a new resource.
     */
    public function create()
{
    $routers = Router::where('status',1)
                ->orderBy('nama')
                ->get();

    return view('odps.create', compact('routers'));
}

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
{
    $request->validate([

    'nama'       => 'required|unique:odps,nama',
    'router'     => 'required|exists:routers,nama',
    'card'       => 'required',
    'onu_awal'   => 'required|integer|min:1',
    'onu_akhir'  => 'required|integer|gte:onu_awal',
    'latitude' => 'nullable|string|max:50',
    'longitude' => 'nullable|string|max:50',
],[
    'nama.required' => 'Nama ODP wajib diisi.',
    'nama.unique' => 'Nama ODP sudah terdaftar. Gunakan menu Edit jika ingin mengubah data.',
    'router.required' => 'Router NAS wajib dipilih.',
    'router.exists' => 'Router NAS tidak ditemukan.',
    'card.required' => 'Card / PON wajib diisi.',
    'onu_awal.required' => 'ONU Awal wajib diisi.',
    'onu_awal.integer' => 'ONU Awal harus berupa angka.',
    'onu_akhir.required' => 'ONU Akhir wajib diisi.',
    'onu_akhir.integer' => 'ONU Akhir harus berupa angka.',
    'onu_akhir.gte' => 'ONU Akhir harus lebih besar atau sama dengan ONU Awal.',
]);

    Odp::create([

        'nama'        => $request->nama,
        'router'      => $request->router,
        'card'        => $request->card,
        'onu_awal'    => $request->onu_awal,
        'onu_akhir'   => $request->onu_akhir,
        'kapasitas' => ($request->onu_akhir - $request->onu_awal) + 1,
        'latitude' => $request->latitude,
        'longitude' => $request->longitude,
        'status'      => true,
        'keterangan'  => $request->keterangan,

    ]);

    return redirect()
            ->route('odps.index')
            ->with('success','ODP berhasil ditambahkan.');
}

    /**
     * Display the specified resource.
     */
    public function show(Odp $odp)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Odp $odp)
{
    $routers = Router::orderBy('nama')->get();

    return view('odps.edit', compact('odp','routers'));
}

    /**
     * Update the specified resource.
     */
    public function update(Request $request, Odp $odp)
{
    $request->validate([

        'nama'      => 'required|unique:odps,nama,' . $odp->id,
        'router'    => 'required|exists:routers,nama',
        'card'      => 'required',
        'onu_awal'  => 'required|integer|min:1',
        'onu_akhir' => 'required|integer|gte:onu_awal',
        'latitude' => 'nullable|string|max:50',
        'longitude' => 'nullable|string|max:50',

    ]);

    $odp->update([

        'nama'       => $request->nama,
        'router'     => $request->router,
        'card'       => $request->card,
        'onu_awal'   => $request->onu_awal,
        'onu_akhir'  => $request->onu_akhir,
        'kapasitas'  => ($request->onu_akhir - $request->onu_awal) + 1,
        'latitude' => $request->latitude,
        'longitude' => $request->longitude,
        'status'     => $request->status,
        'keterangan' => $request->keterangan,

    ]);

    return redirect()
        ->route('odps.index')
        ->with('success', 'ODP berhasil diperbarui.');
}

    /**
     * Remove the specified resource.
     */
    public function destroy(Odp $odp)
{
    $odp->delete();

    return redirect()
            ->route('odps.index')
            ->with('success','ODP berhasil dihapus.');
}

public function import(Request $request)
{
    $request->validate([

        'file' => 'required|file'

    ]);

$import = new OdpImport();

$import->import($request->file('file'));

return redirect()
    ->route('odps.index')
    ->with('success', [

        'success' => $import->success,

        'failed' => $import->failed,

        'errors' => $import->errors

    ]);}

public function downloadTemplate()
{
    return response()->download(
        storage_path('app/templates/odp_template.xlsx')
    );
}

public function export()
{
    return (new OdpExport())->download();
}
}