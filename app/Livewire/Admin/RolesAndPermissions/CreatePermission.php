<?php

namespace App\Livewire\Admin\RolesAndPermissions;

use App\Models\Permission;
use Livewire\Component;

class CreatePermission extends Component
{
    public $permissionName;
    public $permissionDescription;

    public function createPermission() {
        $this->validate([
            'permissionName' => ['required', 'string', 'max:255', 'unique:permissions,name'],
            'permissionDescription' => ['nullable', 'string', 'max:255'],
        ]);

        Permission::create([
            'name' => $this->permissionName,
            'description' => $this->permissionDescription,
        ]);

        return redirect()->route('admin.roles-and-permissions.render')->success(__('admin.toasts.permission_created', ['permission' => $this->permissionName]));
    }

    public function render()
    {
        return view('livewire.admin.roles-and-permissions.create-permission');
    }
}
