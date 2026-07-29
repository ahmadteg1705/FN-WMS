<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class ActivationPermissionSeeder extends Seeder
{
    /**
     * Permission Modul Aktivasi FN-WMS.
     */
    private array $permissions = [
        'noc-activations.view',
        'noc-activations.accept',
        'noc-activations.process',
        'noc-activations.verify',
    ];

    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $guardName = config('auth.defaults.guard', 'web');

        foreach ($this->permissions as $permissionName) {
            Permission::firstOrCreate([
                'name' => $permissionName,
                'guard_name' => $guardName,
            ]);
        }

        /*
         * Pencarian role dibuat toleran terhadap beberapa nama yang lazim
         * dipakai di proyek FN-WMS.
         */
        $superUserRole = $this->findRole([
            'Super User',
            'Superuser',
            'Super Admin',
            'super_user',
            'superuser',
            'super_admin',
        ], $guardName);

        $adminRole = $this->findRole([
            'Admin',
            'Administrator',
            'admin',
            'administrator',
        ], $guardName);

        $nocRole = $this->findRole([
            'NOC',
            'Noc',
            'noc',
        ], $guardName);

        /*
         * Super User: seluruh permission Aktivasi.
         */
        if ($superUserRole) {
            $superUserRole->givePermissionTo($this->permissions);
        }

        /*
         * Admin: melihat antrean, menerima pekerjaan, dan verifikasi.
         * Admin tidak menjalankan proses provisioning teknis.
         */
        if ($adminRole) {
            $adminRole->givePermissionTo([
                'noc-activations.view',
                'noc-activations.accept',
                'noc-activations.verify',
            ]);
        }

        /*
         * NOC: melihat, menerima, dan memproses aktivasi.
         */
        if ($nocRole) {
            $nocRole->givePermissionTo([
                'noc-activations.view',
                'noc-activations.accept',
                'noc-activations.process',
            ]);
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $this->command?->info('Permission Modul Aktivasi berhasil dibuat.');
        $this->reportRole($superUserRole, 'Super User');
        $this->reportRole($adminRole, 'Admin');
        $this->reportRole($nocRole, 'NOC');
    }

    private function findRole(array $names, string $guardName): ?Role
    {
        return Role::query()
            ->where('guard_name', $guardName)
            ->whereIn('name', $names)
            ->first();
    }

    private function reportRole(?Role $role, string $label): void
    {
        if ($role) {
            $this->command?->info(
                "Permission Aktivasi diberikan ke role {$role->name}."
            );

            return;
        }

        $this->command?->warn(
            "Role {$label} belum ditemukan. Permission sudah dibuat, tetapi belum di-assign."
        );
    }
}
