<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Auth;

class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::with(['permissions', 'users'])
            ->orderBy('name')
            ->get();

        return view('roles.index', compact('roles'));
    }

    public function create()
    {
        return view('roles.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|max:100|unique:roles,name',
        ]);

        Role::create([
            'name' => $request->name,
            'guard_name' => 'web',
        ]);

        return redirect()
            ->route('roles.index')
            ->with('success', 'Role berhasil ditambahkan.');
    }

    public function show(Role $role)
    {
        return redirect()->route('roles.index');
    }

    public function edit(Role $role)
    {
        return view('roles.edit', compact('role'));
    }

    public function update(Request $request, Role $role)
    {
        $systemRoles = [
            'Super Admin',
            'Admin',
            'Marketing',
            'Teknisi',
            'NOC',
        ];

        if (in_array($role->name, $systemRoles)) {
            return back()->with(
                'error',
                'Role bawaan sistem tidak boleh diubah.'
            );
        }

        $request->validate([
            'name' => 'required|max:100|unique:roles,name,' . $role->id,
        ]);

        $role->update([
            'name' => $request->name,
        ]);

        return redirect()
            ->route('roles.index')
            ->with('success', 'Role berhasil diperbarui.');
    }
    public function permissions(Role $role)
{
    $permissions = \Spatie\Permission\Models\Permission::orderBy('name')->get();

    /*
    |--------------------------------------------------------------------------
    | Kelompokkan permission berdasarkan prefix
    |--------------------------------------------------------------------------
    */

    $groupedPermissions = [];

    foreach ($permissions as $permission) {

        $parts = explode('.', $permission->name);

        $module = ucfirst($parts[0]);

        $groupedPermissions[$module][] = $permission;
    }

    ksort($groupedPermissions);

    $rolePermissions = $role
        ->permissions
        ->pluck('name')
        ->toArray();

    return view(
        'roles.permissions',
        compact(
            'role',
            'groupedPermissions',
            'rolePermissions'
        )
    );
}
public function updatePermissions(Request $request, Role $role)
{
    $permissions = $request->permissions ?? [];

    $role->syncPermissions($permissions);

    return redirect()
        ->route('roles.index')
        ->with(
            'success',
            'Permission role berhasil diperbarui.'
        );
}
    public function destroy(Role $role)
    {
        $systemRoles = [
            'Super Admin',
            'Admin',
            'Marketing',
            'Teknisi',
            'NOC',
        ];

        if (in_array($role->name, $systemRoles)) {
            return back()->with(
                'error',
                'Role bawaan sistem tidak boleh dihapus.'
            );
        }

        if ($role->users()->count() > 0) {
            return back()->with(
                'error',
                'Role masih digunakan oleh user.'
            );
        }

        $role->delete();

        return redirect()
            ->route('roles.index')
            ->with('success', 'Role berhasil dihapus.');
    }
}