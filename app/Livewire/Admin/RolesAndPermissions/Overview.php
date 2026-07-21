<?php

namespace App\Livewire\Admin\RolesAndPermissions;

use Livewire\Component;
use App\Models\Role;
use App\Models\Permission;

class Overview extends Component
{
    public $rolesPerPage = 10;
    public $permissionsPerPage = 10;

    public function render()
    {
        $roles = Role::paginate($this->rolesPerPage);
        $permissions = Permission::paginate($this->permissionsPerPage);

        return view('livewire.admin.roles-and-permissions.overview', [
            'roles' => $roles,
            'permissions' => $permissions,
        ]);
    }
}
