<?php

namespace App\Livewire\Admin\RolesAndPermissions;

use App\Models\Permission;
use Livewire\Component;

class CreatePermission extends Component
{
    public $permissionName;
    public $permissionDisplayName;
    public $permissionDescription;

    public function createPermission() {
        $this->validate([
            'permissionName' => ['required', 'string', 'max:255', 'unique:permissions,name'],
            'permissionDisplayName' => ['nullable', 'string', 'max:255'],
            'permissionDescription' => ['nullable', 'string'],
        ]);

        Permission::create([
            'name' => $this->permissionName,
            'display_name' => $this->permissionDisplayName,
            'description' => $this->permissionDescription,
        ]);

        return redirect()->route('admin.roles-and-permissions.render')->success(__('admin.toasts.permission_created', ['permission' => $this->permissionName]));
    }

    public function render()
    {
        return view('livewire.admin.roles-and-permissions.create-permission');
    }
}
