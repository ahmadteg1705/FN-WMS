<?php

namespace App\Http\Controllers;

use App\Models\Position;
use Illuminate\Http\Request;

class PositionController extends Controller
{
    public function index(Request $request)
    {
        $keyword = $request->keyword;

        $positions = Position::when($keyword, function ($query) use ($keyword) {

            $query->where('nama', 'like', "%{$keyword}%")
                  ->orWhere('keterangan', 'like', "%{$keyword}%");

        })
        ->latest()
        ->paginate(10)
        ->withQueryString();

        return view('positions.index', compact('positions', 'keyword'));
    }

    public function create()
    {
        return view('positions.create');
    }

    public function store(Request $request)
    {
        $request->validate([

            'nama' => 'required|unique:positions,nama',

            'status' => 'required|boolean',

            'keterangan' => 'nullable'

        ]);

        Position::create([

            'nama' => $request->nama,

            'status' => $request->status,

            'keterangan' => $request->keterangan

        ]);

        return redirect()
            ->route('positions.index')
            ->with('success', 'Jabatan berhasil ditambahkan.');
    }

    public function show(Position $position)
    {
        //
    }

    public function edit(Position $position)
    {
        return view('positions.edit', compact('position'));
    }

    public function update(Request $request, Position $position)
    {
        $request->validate([

            'nama' => 'required|unique:positions,nama,' . $position->id,

            'status' => 'required|boolean',

            'keterangan' => 'nullable'

        ]);

        $position->update([

            'nama' => $request->nama,

            'status' => $request->status,

            'keterangan' => $request->keterangan

        ]);

        return redirect()
            ->route('positions.index')
            ->with('success', 'Jabatan berhasil diperbarui.');
    }

    public function destroy(Position $position)
{
    if ($position->technicians()->count() > 0) {

        return redirect()
            ->route('positions.index')
            ->with(
                'error',
                'Jabatan tidak dapat dihapus karena masih digunakan oleh Teknisi.'
            );

    }

    $position->delete();

    return redirect()
        ->route('positions.index')
        ->with(
            'success',
            'Jabatan berhasil dihapus.'
        );
}
}