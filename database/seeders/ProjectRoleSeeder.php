<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ProjectRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            [
                'name' => 'Admin',
                'slug' => 'admin',
            ],
            [
                'name' => 'Member',
                'slug' => 'member',
            ]
        ];

        foreach ($roles as $role) {
            \App\Models\ProjectRole::create($role);
        }
    }
}
