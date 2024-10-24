<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            ['name' => 'User', 'short_name' => 'user', 'permissions' => json_encode(['create_project' => true, 'edit_profile' => true, 'view_other_projects' => false, 'delete_other_projects' => false, 'manage_users' => false, 'manage_staff' => false, 'manage_app_settings' => false])],
            ['name' => 'Moderator', 'short_name' => 'mod', 'permissions' => json_encode(['create_project' => true, 'edit_profile' => true, 'view_other_projects' => true, 'delete_other_projects' => false, 'manage_users' => false, 'manage_staff' => false, 'manage_app_settings' => false])],
            ['name' => 'Admin', 'short_name' => 'admin', 'permissions' => json_encode(['create_project' => true, 'edit_profile' => true, 'view_other_projects' => true, 'delete_other_projects' => true, 'manage_users' => true, 'manage_staff' => false, 'manage_app_settings' => false])],
            ['name' => 'Superadmin', 'short_name' => 'superadmin', 'permissions' => json_encode(['create_project' => true, 'edit_profile' => true, 'view_other_projects' => true, 'delete_other_projects' => true, 'manage_users' => false, 'manage_staff' => false, 'manage_app_settings' => true])],
        ];

        foreach ($roles as $role) {
            \App\Models\Role::create($role);
        }
    }
}
