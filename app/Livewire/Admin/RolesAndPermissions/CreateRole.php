<?php

namespace App\Livewire\Admin\RolesAndPermissions;

use App\Models\Permission;
use App\Models\Role;
use Livewire\Component;

class CreateRole extends Component
{
    public $roleName;
    public $permissions = [];

    public $allPermissions;

    public function mount() {
        $this->allPermissions = Permission::all();
    }

    public function createRole() {
        $this->validate([
            'roleName' => ['required', 'string', 'max:255', 'unique:roles,name'],
            'permissions' => ['array'],
            'permissions.*' => ['exists:permissions,uuid'],
        ]);

        $role = Role::create(['name' => $this->roleName]);

        if (!empty($this->permissions)) {
            $role->syncPermissions($this->permissions);
        }

        return redirect()->route('admin.roles-and-permissions.render')->success(__('admin.toasts.role_created', ['role' => $this->roleName]));
    }

    public function render()
    {
        return view('livewire.admin.roles-and-permissions.create-role');
    }
}
