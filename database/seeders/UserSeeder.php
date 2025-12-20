<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {


        // Create Admin user (user_type + Spatie role for permissions)
        $admin = User::create([
            'first_name' => 'Test',
            'last_name' => 'Admin',
            'name' => 'Test Admin',
            'email' => 'admin@example.com',
            'user_type' => 'admin',
            'password' => Hash::make('Password123'),
            'email_verified_at' => now(),
        ]);

        $this->command->info('Created 3 test users: student@example.com, instructor@example.com, admin@example.com');
        $this->command->info('All test users have password: Password123');
    }
}
