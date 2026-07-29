<?php

namespace App\Http\Controllers;

use App\Models\Technician;
use App\Models\Team;
use Illuminate\Http\Request;

class TeamController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
{
    $teams = Team::latest()->get();

    return view('teams.index', compact('teams'));
}

public function create()
{
    $technicians = Technician::with('user')
        ->where('status', 1)
        ->get()
        ->sortBy(function ($item) {
            return $item->user->name ?? '';
        });

    return view('teams.create', compact('technicians'));
}
public function store(Request $request)
{
    $request->validate([

        'nama' => 'required|max:100|unique:teams,nama'

    ]);

    Team::create([

        'nama' => $request->nama,
        'leader' => $request->leader,
        'keterangan' => $request->keterangan,
        'status' => true

    ]);

    return redirect()
            ->route('teams.index')
            ->with('success','Tim berhasil ditambahkan');
}
    /**
     * Display the specified resource.
     */
    public function show(Team $team)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Team $team)
{
    $technicians = Technician::with('user')
        ->where('status', 1)
        ->get()
        ->sortBy(function ($item) {
            return $item->user->name ?? '';
        });

    return view('teams.edit', compact('team', 'technicians'));
}

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Team $team)
{
    $request->validate([
    'nama'        => 'required|max:100|unique:teams,nama,' . $team->id,
    'leader'      => 'nullable|max:100',
    'keterangan'  => 'nullable',
    'status'      => 'required|boolean',
]);

    $team->update([

        'nama' => $request->nama,

        'leader' => $request->leader,

        'keterangan' => $request->keterangan,

        'status' => $request->status,

    ]);

    return redirect()
        ->route('teams.index')
        ->with('success', 'Tim berhasil diperbarui.');
}

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Team $team)
{
    if ($team->technicians()->count() > 0) {

        return redirect()
            ->route('teams.index')
            ->with(
                'error',
                'Tim tidak dapat dihapus karena masih digunakan oleh Teknisi.'
            );

    }

    $team->delete();

    return redirect()
        ->route('teams.index')
        ->with(
            'success',
            'Tim berhasil dihapus.'
        );
}
}
