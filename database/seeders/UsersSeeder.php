<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // admin
        $admin = User::create([
            'name' => 'Admin',
            'email' => 'admin@admin.com',
            'password' => Hash::make('admin@admin.com'),
            'email_verified_at' => now(),
        ]);
        $admin->assignRole('admin');

        // regular user
        $user = User::create([
            'name' => 'Sam',
            'email' => 'user@user.com',
            'password' => Hash::make('user@user.com'),
            'email_verified_at' => now(),
        ]);
        $user->assignRole('user');
    }
}
