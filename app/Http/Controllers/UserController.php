<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    /**
     * Daftar User
     */
    public function index(Request $request)
    {
        $search = $request->search;
        $role = $request->role;
        $status = $request->status;

        $users = User::with('roles')

            ->when($search, function ($query) use ($search) {

                $query->where(function ($q) use ($search) {

                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('username', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('employee_code', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%");

                });

            })

            ->when($role, function ($query) use ($role) {

                $query->role($role);

            })

            ->when($status !== null && $status !== '', function ($query) use ($status) {

                $query->where('status', $status);

            })

            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        $roles = Role::orderBy('name')->get();

        return view('users.index', compact(
            'users',
            'roles',
            'search',
            'role',
            'status'
        ));
    }
        /**
     * Form Tambah User
     */
    public function create()
    {
        $roles = Role::orderBy('name')->get();

        return view('users.create', compact('roles'));
    }

    /**
     * Simpan User Baru
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'employee_code' => 'nullable|string|max:30|unique:users,employee_code',
            'name'          => 'required|string|max:255',
            'username'      => 'required|string|max:50|unique:users,username',
            'email'         => 'required|email|max:255|unique:users,email',
            'phone'         => 'nullable|string|max:20',
            'password'      => 'required|string|min:8|confirmed',
            'role'          => 'required|exists:roles,name',
            'status' => 'boolean',
            'photo'         => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);
        $validated['status'] = $request->boolean('status');

        // Upload Foto
        if ($request->hasFile('photo')) {

            $validated['photo'] = $request
                ->file('photo')
                ->store('users', 'public');

        }

        // Simpan User
        $user = User::create([
            'employee_code' => $validated['employee_code'] ?? null,
            'name'          => $validated['name'],
            'username'      => $validated['username'],
            'email'         => $validated['email'],
            'phone'         => $validated['phone'] ?? null,
            'password'      => $validated['password'], // otomatis di-hash oleh casts()
            'photo'         => $validated['photo'] ?? null,
            'status'        => $validated['status'],
        ]);

        // Assign Role
        $user->assignRole($validated['role']);

        return redirect()
            ->route('users.index')
            ->with('success', 'User berhasil ditambahkan.');
    }
        /**
     * Form Edit User
     */
    public function edit(User $user)
    {
        $roles = Role::orderBy('name')->get();

        return view('users.edit', compact(
            'user',
            'roles'
        ));
    }

    /**
     * Update User
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'employee_code' => 'nullable|string|max:30|unique:users,employee_code,' . $user->id,

            'name' => 'required|string|max:255',

            'username' => 'required|string|max:50|unique:users,username,' . $user->id,

            'email' => 'required|email|max:255|unique:users,email,' . $user->id,

            'phone' => 'nullable|string|max:20',

            'password' => 'nullable|string|min:8|confirmed',

            'role' => 'required|exists:roles,name',

            'status' => 'boolean',

            'photo' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:3072',
        ]);
        $validated['status'] = $request->boolean('status');

        /*
        |--------------------------------------------------------------------------
        | Upload Foto Baru
        |--------------------------------------------------------------------------
        */

        if ($request->hasFile('photo')) {

            if ($user->photo && Storage::disk('public')->exists($user->photo)) {

                Storage::disk('public')->delete($user->photo);

            }

            $validated['photo'] = $request
                ->file('photo')
                ->store('users', 'public');

        }

        /*
        |--------------------------------------------------------------------------
        | Password
        |--------------------------------------------------------------------------
        */

        if (empty($validated['password'])) {

            unset($validated['password']);

        }

        /*
        |--------------------------------------------------------------------------
        | Update Data
        |--------------------------------------------------------------------------
        */

        $user->update([
            'employee_code' => $validated['employee_code'] ?? null,
            'name'          => $validated['name'],
            'username'      => $validated['username'],
            'email'         => $validated['email'],
            'phone'         => $validated['phone'] ?? null,
            'password'      => $validated['password'] ?? $user->password,
            'photo'         => $validated['photo'] ?? $user->photo,
            'status'        => $validated['status'],
        ]);

        /*
        |--------------------------------------------------------------------------
        | Sync Role
        |--------------------------------------------------------------------------
        */

        $user->syncRoles([
            $validated['role']
        ]);

        return redirect()
            ->route('users.index')
            ->with('success', 'User berhasil diperbarui.');
    }
        /**
     * Hapus User
     */
    public function destroy(User $user)
    {
        /*
        |--------------------------------------------------------------------------
        | Tidak boleh menghapus akun sendiri
        |--------------------------------------------------------------------------
        */

        if (auth()->id() === $user->id) {

            return back()->with(
                'error',
                'Anda tidak dapat menghapus akun yang sedang digunakan.'
            );

        }

        /*
        |--------------------------------------------------------------------------
        | Cegah menghapus Super Admin terakhir
        |--------------------------------------------------------------------------
        */

        if ($user->hasRole('Super Admin')) {

            $superAdminCount = User::role('Super Admin')->count();

            if ($superAdminCount <= 1) {

                return back()->with(
                    'error',
                    'Minimal harus ada satu Super Admin.'
                );

            }

        }

        /*
        |--------------------------------------------------------------------------
        | Hapus Foto
        |--------------------------------------------------------------------------
        */

        if ($user->photo && Storage::disk('public')->exists($user->photo)) {

            Storage::disk('public')->delete($user->photo);

        }

        /*
        |--------------------------------------------------------------------------
        | Hapus Role
        |--------------------------------------------------------------------------
        */

        $user->syncRoles([]);

        /*
        |--------------------------------------------------------------------------
        | Hapus User
        |--------------------------------------------------------------------------
        */

        $user->delete();

        return redirect()
            ->route('users.index')
            ->with(
                'success',
                'User berhasil dihapus.'
            );
    }
}