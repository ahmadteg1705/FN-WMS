<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        Role::findOrCreate('Super Admin');
        Role::findOrCreate('Admin');
        Role::findOrCreate('Marketing');
        Role::findOrCreate('Teknisi');
        Role::findOrCreate('NOC');
    }
}