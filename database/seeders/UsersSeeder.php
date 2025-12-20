<?php

namespace Database\Seeders;

use App\Models\User;
use Faker\Generator;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(Generator $faker)
    {
        User::query()->delete();

        User::create([
            'name' => 'owner',
            'email_verified_at' => now(),
            'email' => 'owner@intrazero.com',
            'user_type' => 'admin',
            'password' => Hash::make('Owner123'),
        ]);

        $this->command->info('Admin user created.');
    }
}
