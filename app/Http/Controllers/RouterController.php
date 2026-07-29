<?php

namespace App\Http\Controllers;

use App\Models\Router;
use Illuminate\Http\Request;

class RouterController extends Controller
{
    public function index()
    {
        $routers = Router::latest()->paginate(10);

        return view('routers.index', compact('routers'));
    }

    public function create()
    {
        return view('routers.create');
    }

    public function store(Request $request)
{
    $request->validate([

        'nama'            => 'required',
        'kota'            => 'required',
        'hostname'        => 'required',
        'ip'              => 'required',
        'vlan'            => 'required',
        'vlan_profile'    => 'required',
        'tcont_profile'   => 'required',
        'onu_type'        => 'required',
        'security_mgmt'   => 'required',

    ]);

    Router::create($request->all());

    return redirect()
            ->route('routers.index')
            ->with('success', 'Router NAS berhasil ditambahkan.');
}

    public function show(Router $router)
    {

    }

    public function edit(Router $router)
{
    return view('routers.edit', compact('router'));
}

    public function update(Request $request, Router $router)
{
    $request->validate([
        'nama' => 'required',
        'kota' => 'required',
        'hostname' => 'required',
        'ip' => 'required',
        'vlan' => 'required',
        'vlan_profile' => 'required',
        'tcont_profile' => 'required',
        'onu_type' => 'required',
        'security_mgmt' => 'required',
    ]);

    $router->update($request->all());

    return redirect()
        ->route('routers.index')
        ->with('success', 'Router berhasil diperbarui.');
}

    public function destroy(Router $router)
{
    $router->delete();

    return redirect()
        ->route('routers.index')
        ->with('success', 'Router berhasil dihapus.');
}
}