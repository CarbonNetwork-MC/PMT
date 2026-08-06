<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class PermissionsAndRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            [
                'name' => 'manage-users',
                'display_name' => 'Manage Users',
                'description' => 'This permission grants the user the ability to edit, invite and delete users.',
            ],
            [
                'name' => 'manage-projects',
                'display_name' => 'Manage Projects',
                'description' => 'This permissions grant the user the ability to view, manage and delete projects that they do not own or are a member of.',
            ],
            [
                'name' => 'manage-permissions',
                'display_name' => 'Manage Permissions',
                'description' => 'This permission grants the user the ability to view, create, edit and delete roles and permissions.',
            ]   
        ];

        foreach ($permissions as $permission) {
            \App\Models\Permission::create($permission);
        }

        \App\Models\Role::create([
            'name' => 'Superadmin'
        ]);
    }
}
