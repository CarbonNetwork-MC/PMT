<?php

namespace App\Livewire\Admin\RolesAndPermissions;

use App\Models\Permission;
use App\Models\Role;
use Livewire\Component;
use Masmerise\Toaster\Toaster;

class Overview extends Component
{
    public $rolesPerPage = 10;
    public $permissionsPerPage = 10;

    public $entityToDelete = null;

    public $showDeleteRoleModal = false;
    public $showDeletePermissionModal = false;

    public function deletePermission($uuid) {
        $this->entityToDelete = Permission::where('uuid', $uuid)->firstOrFail();
        $this->showDeletePermissionModal = true;
    }

    public function deleteRole($uuid) {
        $this->entityToDelete = Role::where('uuid', $uuid)->firstOrFail();
        $this->showDeleteRoleModal = true;
    }

    public function confirmDeleteRole() {
        if (!$this->entityToDelete) return;

        $this->entityToDelete->delete();
        $this->reset(['entityToDelete', 'showDeleteRoleModal']);

        Toaster::success(__('admin.toasts.role_deleted_successfully', ['role' => $this->entityToDelete->name ?? '']));
    }

    public function confirmDeletePermission() {
        if (!$this->entityToDelete) return;

        $this->entityToDelete->delete();
        $this->reset(['entityToDelete', 'showDeletePermissionModal']);

        Toaster::success(__('admin.toasts.permission_deleted_successfully', ['permission' => $this->entityToDelete->name ?? '']));
    }

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
