<?php
// database/seeders/UserSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Admin User (Full Access + Create User)
        User::create([
            'name' => 'Administrator',
            'email' => 'admin@linknet.com',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
            'is_active' => true
        ]);

        // Regular User (No Create User Permission)
        User::create([
            'name' => 'User Demo',
            'email' => 'user@linknet.com',
            'password' => Hash::make('user123'),
            'role' => 'user',
            'is_active' => true
        ]);

        $this->command->info('✓ Default users created!');
        $this->command->info('  Admin: admin@linknet.com / admin123');
        $this->command->info('  User: user@linknet.com / user123');
    }
}