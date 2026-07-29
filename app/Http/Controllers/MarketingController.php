<?php

namespace App\Http\Controllers;

use App\Models\Marketing;
use App\Models\User;
use App\Models\Position;
use Illuminate\Http\Request;

class MarketingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
{
    $marketings = Marketing::latest()->get();

    return view(
        'marketings.index',
        compact('marketings')
    );
}

    /**
     * Show the form for creating a new resource.
     */
    public function create()
{
    $users = User::where('status',1)
        ->whereHas('roles', function ($q) {
            $q->where('name','Marketing');
        })
        ->whereDoesntHave('marketing')
        ->orderBy('name')
        ->get();

    $positions = Position::where('status',1)
        ->orderBy('nama')
        ->get();

    return view('marketings.create', compact(
        'users',
        'positions'
    ));
}

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
{
    $request->validate([

    'user_id' => 'required|exists:users,id',

    'position_id' => 'required|exists:positions,id',

    'telepon' => 'nullable|max:20',

    'wilayah' => 'nullable|max:255',

    'status' => 'required',

    'tanggal_masuk' => 'nullable|date',

    'foto' => 'nullable|image',

    'keterangan' => 'nullable',

]);
$foto = null;

if ($request->hasFile('foto')) {

    $foto = $request->file('foto')->store(
        'marketings',
        'public'
    );

}

Marketing::create([

    'user_id' => $request->user_id,

    'position_id' => $request->position_id,

    'telepon' => $request->telepon,

    'wilayah' => $request->wilayah,

    'status' => $request->status,

    'foto' => $foto ?? null,

    'tanggal_masuk' => $request->tanggal_masuk,

    'keterangan' => $request->keterangan,

]);

    return redirect()
        ->route('marketings.index')
        ->with('success','Data Marketing berhasil ditambahkan.');
}
    /**
     * Display the specified resource.
     */
    public function show(Marketing $marketing)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Marketing $marketing)
{
    $users = User::where('status',1)
        ->whereHas('roles', function ($q) {
            $q->where('name','Marketing');
        })
        ->where(function($q) use ($marketing){

            $q->whereDoesntHave('marketing')
              ->orWhere('id',$marketing->user_id);

        })
        ->orderBy('name')
        ->get();

    $positions = Position::where('status',1)
        ->orderBy('nama')
        ->get();

    return view(
        'marketings.edit',
        compact(
            'marketing',
            'users',
            'positions'
        )
    );
}

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Marketing $marketing)
{
    $request->validate([

        'user_id' => 'required|exists:users,id',

    'position_id' => 'required|exists:positions,id',

    'telepon' => 'nullable|max:20',

    'wilayah' => 'nullable|max:255',

    'status' => 'required',

    'tanggal_masuk' => 'nullable|date',

    'foto' => 'nullable|image',

    'keterangan' => 'nullable',

    ]);
    if ($request->hasFile('foto')) {

    $foto = $request->file('foto')->store(
        'marketings',
        'public'
    );

} else {

    $foto = $marketing->foto;

}

    $marketing->update([

    'user_id' => $request->user_id,

    'position_id' => $request->position_id,

    'telepon' => $request->telepon,

    'wilayah' => $request->wilayah,

    'status' => $request->status,
    'foto' => $foto,

    'tanggal_masuk' => $request->tanggal_masuk,

    'keterangan' => $request->keterangan,

]);

    return redirect()
        ->route('marketings.index')
        ->with('success','Data Marketing berhasil diperbarui.');
}

    /**
     * Remove the specified resource from storage.
     */
   public function destroy(Marketing $marketing)
{
    $marketing->delete();

    return redirect()
        ->route('marketings.index')
        ->with('success','Data Marketing berhasil dihapus.');
}
}
