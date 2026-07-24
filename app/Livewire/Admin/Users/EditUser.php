<?php

namespace App\Livewire\Admin\Users;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Validation\Rule;
use Livewire\Component;

class EditUser extends Component
{
    public $user;
    public $username;
    public $email;
    public $permissions = [];
    public $roles = [];

    public $allPermissions = [];
    public $allRoles = [];

    public function mount($uuid) {
        $this->user = User::where('uuid', $uuid)->firstOrFail();
        $this->username = $this->user->name;
        $this->email = $this->user->email;

        $this->permissions = $this->user->permissions()->pluck('uuid')->toArray();
        $this->roles = $this->user->roles()->pluck('uuid')->toArray();

        $this->loadAllPermissions();
        $this->loadAllRoles();
    }

    private function loadAllPermissions() {
        $allPermissions = Permission::all();

        $this->user->loadMissing('permissions');

        $selectedRoles = Role::query()
            ->whereIn('uuid', $this->roles)
            ->with('permissions')
            ->get();

        $this->allPermissions = $allPermissions->map(function ($permission) use ($selectedRoles) {
            $roles = $selectedRoles
                ->filter(
                    fn (Role $role) => $role->permissions->contains(
                        'uuid',
                        $permission->uuid
                    )
                )
                ->pluck('name')
                ->filter()
                ->values();

            return [
                'uuid' => $permission->uuid,
                'name' => $permission->name,
                'display_name' => $permission->display_name,
                'description' => $permission->description,

                // Directly assigned permission
                'has_permission' => $this->user->permissions->contains(
                    'uuid',
                    $permission->uuid
                ),

                // Permission granted by one of the currently selected roles
                'has_permission_via_role' => $roles->isNotEmpty(),
                'roles' => $roles->toArray(),
            ];
        })->toArray();
    }

    private function loadAllRoles() {
        $allRoles = Role::all();

        $this->user->loadMissing([
            'roles',
        ]);

        // Get the user's current roles and create an array with the role uuid and if the user has that role
        $this->allRoles = $allRoles->map(function ($role) {
            return [
                'uuid' => $role->uuid,
                'name' => $role->name,
                'display_name' => $role->display_name,
                'description' => $role->description,
                'has_role' => $this->user->roles->contains('uuid', $role->uuid),
            ];
        })->toArray();
    }

    public function updatedRoles() {
        $this->loadAllPermissions();
    }

    public function saveUser() {
        $this->validate([
            'username' => ['required', 'string', 'max:255'],
            'email' => [
                'required', 
                'string', 
                'email', 
                'max:255', 
                Rule::unique('users', 'email')->ignore($this->user->uuid, 'uuid')
            ],
        ]);

        $this->user->name = $this->username;
        $this->user->email = $this->email;
        $this->user->save();

        // Sync the user's permissions and roles
        $this->user->permissions()->sync($this->permissions);
        $this->user->roles()->sync($this->roles);

        return redirect()->route('admin.users.render')->success(__('admin.toasts.users.user_updated', [
            'user' => $this->user->name,
        ]));
    }

    public function render()
    {
        return view('livewire.admin.users.edit-user');
    }
}
