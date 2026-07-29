<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $permissions = [

            /*
            |--------------------------------------------------------------------------
            | Dashboard
            |--------------------------------------------------------------------------
            */
            'dashboard.view',

            /*
            |--------------------------------------------------------------------------
            | Registrations
            |--------------------------------------------------------------------------
            */
            'registrations.view',
            'registrations.show',
            'registrations.create',
            'registrations.edit',
            'registrations.delete',
            'registrations.change-status',
            'registrations.cancel',
            'registrations.export',

            'schedules.view',
            'troubles.view',

            /*
            |--------------------------------------------------------------------------
            | Packages
            |--------------------------------------------------------------------------
            */
            'packages.view',
            'packages.create',
            'packages.edit',
            'packages.delete',

            /*
            |--------------------------------------------------------------------------
            | ODPs
            |--------------------------------------------------------------------------
            */
            'odps.view',
            'odps.create',
            'odps.edit',
            'odps.delete',
            'odps.import',
            'odps.export',

            /*
            |--------------------------------------------------------------------------
            | Routers
            |--------------------------------------------------------------------------
            */
            'routers.view',
            'routers.create',
            'routers.edit',
            'routers.delete',

            /*
            |--------------------------------------------------------------------------
            | Marketings
            |--------------------------------------------------------------------------
            */
            'marketings.view',
            'marketings.create',
            'marketings.edit',
            'marketings.delete',

            /*
            |--------------------------------------------------------------------------
            | Technicians
            |--------------------------------------------------------------------------
            */
            'technicians.view',
            'technicians.create',
            'technicians.edit',
            'technicians.delete',

            /*
            |--------------------------------------------------------------------------
            | Teams
            |--------------------------------------------------------------------------
            */

            'teams.view',
            'teams.create',
            'teams.edit',
            'teams.delete',

            /*
            |--------------------------------------------------------------------------
            | Positions
            |--------------------------------------------------------------------------
            */

            'positions.view',
            'positions.create',
            'positions.edit',
            'positions.delete',

            /*
            |--------------------------------------------------------------------------
            | Surveys
            |--------------------------------------------------------------------------
            */
            'surveys.view',
            'surveys.schedule',
            'surveys.process',
            'surveys.finish',

            /*
            |--------------------------------------------------------------------------
            | Activations
            |--------------------------------------------------------------------------
            */
            'activations.view',
            'activations.process',

            /*
            |--------------------------------------------------------------------------
            | Customers
            |--------------------------------------------------------------------------
            */
            'customers.view',
            'customers.create',
            'customers.edit',
            'customers.delete',
            'customers.export',

            /*
            |--------------------------------------------------------------------------
            | Reports
            |--------------------------------------------------------------------------
            */
            'reports.view',
            'reports.export',

            /*
            |--------------------------------------------------------------------------
            | Users
            |--------------------------------------------------------------------------
            */
            'users.view',
            'users.create',
            'users.edit',
            'users.delete',

            /*
            |--------------------------------------------------------------------------
            | Roles
            |--------------------------------------------------------------------------
            */
            'roles.view',
            'roles.create',
            'roles.edit',
            'roles.delete',
            'roles.permission',

            /*
            |--------------------------------------------------------------------------
            | Permissions
            |--------------------------------------------------------------------------
            */
            'permissions.view',
            'permissions.create',
            'permissions.edit',
            'permissions.delete',
            'permissions.assign',

            /*
            |--------------------------------------------------------------------------
            | Permissions
            |--------------------------------------------------------------------------
            */
            'work-orders.view',
            'work-orders.create',
            'work-orders.edit',
            'work-orders.delete',

        ];

        foreach ($permissions as $permission) {
            Permission::findOrCreate($permission);
        }

        /*
        |--------------------------------------------------------------------------
        | Assign Permission
        |--------------------------------------------------------------------------
        */

        // Super Admin
        $superAdmin = Role::findByName('Super Admin');
        $superAdmin->syncPermissions(Permission::all());

        // Admin
        $admin = Role::findByName('Admin');
        $admin->syncPermissions([
            'dashboard.view',

            'technicians.view',
            'teams.view',
            'positions.view',
            'users.view',
            'roles.view',

            'registrations.view',
            'registrations.show',
            'registrations.create',
            'registrations.edit',
            'registrations.delete',
            'registrations.change-status',

            'work-orders.view',
            'work-orders.create',
            'work-orders.edit',
            'work-orders.delete',

            'packages.view',
            'packages.create',
            'packages.edit',

            'odps.view',
            'odps.create',
            'odps.edit',

            'routers.view',

            'marketings.view',
            'marketings.create',
            'marketings.edit',

            'customers.view',

            'reports.view',
            'reports.export',
        ]);

        // Marketing
        $marketing = Role::findByName('Marketing');
        $marketing->syncPermissions([
            'dashboard.view',
'marketings.view',
            'registrations.view',
            'registrations.show',
            'registrations.create',

            'packages.view',
            'odps.view',
        ]);

        // Teknisi
        $teknisi = Role::findByName('Teknisi');
        $teknisi->syncPermissions([
            'dashboard.view',
            'technicians.view',
            'registrations.view',
            'registrations.show',

            'work-orders.view',

            'surveys.view',
            'surveys.process',
        ]);

        // NOC
        $noc = Role::findByName('NOC');
        $noc->syncPermissions([
            'dashboard.view',

            'routers.view',
            'odps.view',
            'registrations.view',

            'activations.view',
            'activations.process',

            'customers.view',
        ]);
    }
}