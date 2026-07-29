<?php

namespace App\Http\Controllers;

use App\Models\Team;
use App\Models\Position;
use App\Models\Technician;
use App\Models\User;
use App\Imports\TechnicianImport;
use App\Exports\TechnicianExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TechnicianController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
{
    $keyword = $request->keyword;

    $technicians = Technician::with([
    'team',
    'user',
    'position'
])
        ->when($keyword, function ($query) use ($keyword) {

            $query->whereHas('user', function ($q) use ($keyword) {

    $q->where('name', 'like', "%{$keyword}%")
      ->orWhere('username', 'like', "%{$keyword}%")
      ->orWhere('email', 'like', "%{$keyword}%");

})
                  ->orWhere('telepon', 'like', "%{$keyword}%")
                  ->orWhereHas('team', function ($q) use ($keyword) {

                    $q->where('nama', 'like', "%{$keyword}%");

                });

        })
        ->latest()
        ->paginate(10)
        ->withQueryString();

    return view(
        'technicians.index',
        compact('technicians', 'keyword')
    );
}

public function create()
{
    $teams = Team::orderBy('nama')->get();

    $positions = Position::where('status', 1)
        ->orderBy('nama')
        ->get();

    $users = User::role('Teknisi')
        ->whereDoesntHave('technician')
        ->active()
        ->orderBy('name')
        ->get();

    return view(
        'technicians.create',
        compact(
            'teams',
            'positions',
            'users'
        )
    );
}
public function store(Request $request)
{
    $request->validate([

        'user_id' => 'required|exists:users,id|unique:technicians,user_id',
        'nik'             => 'required|unique:technicians,nik',
        'telepon'         => 'required',
        'alamat'            => 'nullable',
        'position_id' => 'required|exists:positions,id',
        'team_id'         => 'required|exists:teams,id',
        'status'          => 'required|boolean',
        'tanggal_masuk'   => 'nullable|date',
        'foto'            => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        'keterangan'      => 'nullable',

    ]);

    $foto = null;

    if ($request->hasFile('foto')) {

        $foto = $request->file('foto')
                        ->store('technicians', 'public');

    }

    Technician::create([

        'user_id' => $request->user_id,
        'nik'             => $request->nik,
        'telepon'         => $request->telepon,
        'alamat' => $request->alamat,
        'position_id' => $request->position_id,
        'team_id'         => $request->team_id,
        'status'          => $request->status,
        'foto'            => $foto,
        'tanggal_masuk'   => $request->tanggal_masuk,
        'keterangan'      => $request->keterangan,

    ]);

    return redirect()
            ->route('technicians.index')
            ->with('success', 'Teknisi berhasil ditambahkan.');
}
    /**
     * Display the specified resource.
     */
    public function show(Technician $technician)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Technician $technician)
{
    $teams = Team::orderBy('nama')->get();

    $positions = Position::where('status', 1)
        ->orderBy('nama')
        ->get();

    $users = User::role('Teknisi')
        ->where(function ($q) use ($technician) {
            $q->whereDoesntHave('technician')
              ->orWhere('id', $technician->user_id);
        })
        ->active()
        ->orderBy('name')
        ->get();

    return view(
        'technicians.edit',
        compact(
            'technician',
            'teams',
            'positions',
            'users'
        )
    );
}

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Technician $technician)
{
    $request->validate([

'user_id' => 'required|exists:users,id|unique:technicians,user_id,' . $technician->id,
        'nik'             => 'required|unique:technicians,nik,' . $technician->id,

        'telepon'         => 'required',

        'alamat'          => 'nullable',

        'position_id' => 'required|exists:positions,id',

        'team_id'         => 'required|exists:teams,id',

        'status'          => 'required|boolean',

        'tanggal_masuk'   => 'nullable|date',

        'foto'            => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',

        'keterangan'      => 'nullable',

    ]);

    $data = [

        'user_id' => $request->user_id,

        'nik'             => $request->nik,

        'telepon'         => $request->telepon,

        'alamat'          => $request->alamat,

        'position_id' => $request->position_id,

        'team_id'         => $request->team_id,

        'status'          => $request->status,

        'tanggal_masuk'   => $request->tanggal_masuk,

        'keterangan'      => $request->keterangan,

    ];

    if ($request->hasFile('foto')) {

        if ($technician->foto) {

            Storage::disk('public')->delete($technician->foto);

        }

        $data['foto'] = $request->file('foto')
            ->store('technicians', 'public');

    }

    $technician->update($data);

    return redirect()
        ->route('technicians.index')
        ->with('success', 'Teknisi berhasil diperbarui.');
}

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Technician $technician)
{
    if ($technician->foto) {

        Storage::disk('public')->delete($technician->foto);

    }

    $technician->delete();

    return redirect()
            ->route('technicians.index')
            ->with(
                'success',
                'Teknisi berhasil dihapus.'
            );
}
public function import(Request $request)
{
    $request->validate([

        'file' => 'required|file'

    ]);

    $import = new TechnicianImport();

    $import->import($request->file('file'));

    return redirect()
        ->route('technicians.index')
        ->with('success', [

            'success' => $import->success,

            'failed' => $import->failed,

            'errors' => $import->errors

        ]);
}
public function downloadTemplate()
{
    return response()->download(
        storage_path('app/templates/technician_template.xlsx')
    );
}
public function export()
{
    return (new TechnicianExport())->download();
}
}
