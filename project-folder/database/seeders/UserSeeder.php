<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Seeds the database with a test user.
     */
    public function run(): void
    {
        User::create([
            'email'    => 'testuser@example.com',
            'password' => Hash::make('password123'),
        ]);
    }
}
