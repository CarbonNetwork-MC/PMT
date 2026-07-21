<?php

namespace App\Livewire\Admin\RolesAndPermissions;

use App\Models\Permission;
use Livewire\Component;

class EditPermission extends Component
{
    public $permission;

    public $permissionName;
    public $permissionDescription;

    public function mount($uuid) {
        $this->permission = Permission::where('uuid', $uuid)->firstOrFail();
        $this->permissionName = $this->permission->name;
        $this->permissionDescription = $this->permission->description;
    }

    public function updatePermission() {
        $this->validate([
            'permissionName' => ['required', 'string', 'max:255', 'unique:permissions,name,' . $this->permission->uuid . ',uuid'],
            'permissionDescription' => ['nullable', 'string', 'max:255'],
        ]);

        $this->permission->update([
            'name' => $this->permissionName,
            'description' => $this->permissionDescription,
        ]);

        return redirect()->route('admin.roles-and-permissions.render')->success(__('admin.toasts.permission_updated', ['permission' => $this->permissionName]));
    }

    public function render()
    {
        return view('livewire.admin.roles-and-permissions.edit-permission');
    }
}
