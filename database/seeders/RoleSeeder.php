<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            ['name' => 'User', 'short_name' => 'user', 'permissions' => json_encode(['create_project' => false, 'delete_project' => false, 'update_project' => false, 'delete_card' => false, 'delete_task' => false])],
            ['name' => 'Admin', 'short_name' => 'admin', 'permissions' => json_encode(['create_project' => false, 'delete_project' => false, 'update_project' => false, 'delete_card' => true, 'delete_task' => true])],
            ['name' => 'Project Manager', 'short_name' => 'project-manager', 'permissions' => json_encode(['create_project' => true, 'delete_project' => true, 'update_project' => true, 'delete_card' => true, 'delete_task' => true])]
        ];

        foreach ($roles as $role) {
            \App\Models\Role::create($role);
        }
    }
}
