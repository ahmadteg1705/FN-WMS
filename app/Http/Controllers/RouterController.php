<?php

namespace App\Http\Controllers;

use App\Models\Router;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RouterController extends Controller
{
    public function index(): View
    {
        $routers = Router::latest()->paginate(10);

        return view('routers.index', compact('routers'));
    }

    public function create(): View
    {
        return view('routers.create');
    }

    public function store(Request $request): RedirectResponse
    {
        Router::create($this->validated($request));

        return redirect()->route('routers.index')
            ->with('success', 'Router NAS berhasil ditambahkan.');
    }

    public function edit(Router $router): View
    {
        return view('routers.edit', compact('router'));
    }

    public function update(Request $request, Router $router): RedirectResponse
    {
        $router->update($this->validated($request));

        return redirect()->route('routers.index')
            ->with('success', 'Router NAS berhasil diperbarui.');
    }

    public function destroy(Router $router): RedirectResponse
    {
        $router->delete();

        return redirect()->route('routers.index')
            ->with('success', 'Router NAS berhasil dihapus.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'nama' => ['required', 'string', 'max:100'],
            'kota' => ['required', 'string', 'max:100'],
            'hostname' => ['required', 'string', 'max:150'],
            'ip' => ['required', 'string', 'max:45'],
            'vlan' => ['required', 'string', 'max:30'],
            'vlan_profile' => ['required', 'string', 'max:100'],
            'tcont_profile' => ['required', 'string', 'max:100'],
            'onu_type' => ['required', 'string', 'max:100'],
            'security_mgmt' => ['required', 'string', 'max:30'],
            'service_command' => ['required', 'string', 'max:150'],
            'wan_ethuni' => ['required', 'string', 'max:50'],
            'wan_ssid' => ['required', 'string', 'max:30'],
            'wan_service' => ['required', 'string', 'max:50'],
            'status' => ['required', 'boolean'],
        ]);
    }
}
