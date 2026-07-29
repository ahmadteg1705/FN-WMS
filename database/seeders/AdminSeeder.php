<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::updateOrCreate(
            [
                'email' => 'ahmadteguhsusilo@gmail.com',
            ],
            [
                'name' => 'Administrator',
                'password' => Hash::make('!Rahasia1234'),
            ]
        );

        $user->assignRole('Super Admin');
    }
}