<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Admin user
        User::create([
            'name' => 'Test Admin',
            'email' => 'admin@test.com',
            'password' => Hash::make('password'), // simple password
            'is_admin' => true,
        ]);

        // Normal user
        User::create([
            'name' => 'Test gebruiker',
            'email' => 'test@test.com',
            'password' => Hash::make('password'),
            'is_admin' => false,
        ]);
    }
}
