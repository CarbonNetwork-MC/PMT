<?php

namespace App\Livewire\Admin\RolesAndPermissions;

use App\Models\Permission;
use App\Models\Role;
use Livewire\Component;

class EditRole extends Component
{
    public $role;

    public $roleName;
    public $permissions = [];

    public $allPermissions;

    public function mount($uuid) {
        $this->role = Role::where('uuid', $uuid)->firstOrFail();
        $this->roleName = $this->role->name;
        $this->permissions = $this->role->permissions()->pluck('uuid')->toArray();

        $this->allPermissions = Permission::all();
    }

    public function updateRole() {
        $this->validate([
            'roleName' => ['required', 'string', 'max:255'],
            'permissions' => ['array'],
            'permissions.*' => ['exists:permissions,uuid'],
        ]);

        $this->role->update([
            'name' => $this->roleName,
        ]);

        if (!empty($this->permissions)) {
            $this->role->syncPermissions($this->permissions);
        } else {
            $this->role->syncPermissions([]);
        }

        return redirect()->route('admin.roles-and-permissions.render')->success(__('admin.toasts.role_updated', ['role' => $this->roleName]));
    }

    public function render()
    {
        return view('livewire.admin.roles-and-permissions.edit-role');
    }
}
