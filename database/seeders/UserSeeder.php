<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\User::create([
            'uuid' => \Illuminate\Support\Str::uuid(),
            'name' => 'Admin',
            'email' => 'admin@cn.net',
            'password' => \Illuminate\Support\Facades\Hash::make('password'),
        ]);
    }
}
