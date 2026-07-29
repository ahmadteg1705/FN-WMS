<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class ActivationPermissionSeeder extends Seeder
{
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $guard = config('auth.defaults.guard', 'web');

        $permissions = [
            'noc-activations.view',
            'noc-activations.accept',
            'noc-activations.process',
            'noc-activations.verify',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => $guard,
            ]);
        }

        $rolePermissions = [
            'Super User' => $permissions,
            'Super Admin' => $permissions,
            'Admin' => [
                'noc-activations.view',
                'noc-activations.verify',
            ],
            'NOC' => [
                'noc-activations.view',
                'noc-activations.accept',
                'noc-activations.process',
            ],
        ];

        foreach ($rolePermissions as $roleName => $assignedPermissions) {
            $role = Role::query()
                ->where('guard_name', $guard)
                ->whereRaw('LOWER(name) = ?', [strtolower($roleName)])
                ->first();

            if ($role) {
                $role->givePermissionTo($assignedPermissions);
            }
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
