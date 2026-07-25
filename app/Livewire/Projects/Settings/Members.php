<?php

namespace App\Livewire\Projects\Settings;

use App\Helpers\CheckIfUserIsAdmin;
use App\Models\Log;
use App\Models\Project;
use App\Models\ProjectMember;
use App\Models\ProjectRole;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Masmerise\Toaster\Toaster;

class Members extends Component
{
    public $project;
    public $members;
    public $allMembers;
    public $roles;
    public $users;

    public $search = '';

    public $isProjectOwner;
    public $isProjectAdmin;
    public $isAppAdmin;

    public $userToModify;
    public $newRole;

    public $newMemberUuid;
    public $newMemberRole;

    public $showChangeRoleModal = false;
    public $showAddMemberModal = false;
    public $showRemoveMemberModal = false;

    public function mount($uuid) {
        $this->project = Project::where('uuid', $uuid)->firstOrFail();
        $members = ProjectMember::where('project_uuid', $this->project->uuid)
            ->with(['user', 'role'])
            ->orderBy('project_role_id')
            ->get()
            ->map(function ($m) {
                return [
                    'uuid' => $m->user_uuid,
                    'user' => $m->user->name,
                    'role' => $m->role->name,
                    'is_owner' => false,
                ];
            });

        $owner = [
            'uuid' => $this->project->owner->uuid,
            'user' => $this->project->owner->name,
            'role' => 'Owner',
            'is_owner' => true,
        ];

        $this->allMembers = collect([$owner])->merge($members);
        $this->members = $this->allMembers->map(function ($m) {
            $m['visible'] = true;
            return $m;
        });

        $this->roles = ProjectRole::where('slug', '!=', 'owner')->get();
        $this->newMemberRole = $this->roles->first()->id;
        $this->users = $this->getUsers();

        $this->isProjectOwner = auth()->user()->uuid === $this->project->owner_uuid;
        $this->isProjectAdmin = $this->project->members()
            ->where('user_uuid', auth()->user()->uuid)
            ->whereHas('role', function ($query) {
                $query->where('name', 'Admin');
            })
            ->exists();
        $this->isAppAdmin = auth()->user()->can('manage-projects');
    }

    public function updated($key, $value) {
        if ($key === 'search') {
            $search = strtolower($value);

            $this->members = empty($search)
                ? $this->allMembers
                : $this->allMembers->filter(
                    fn ($m) => str_contains(strtolower($m['user']), $search)
                );
        }
    }

    private function getUsers() {
        return User::select(['uuid', 'name'])
            ->whereNotIn('uuid', function ($query) {
                $query->select('user_uuid')
                    ->from('project_members')
                    ->where('project_uuid', $this->project->uuid);
            })
            ->where('uuid', '!=', $this->project->owner_uuid)
            ->limit(20)
            ->get();
    }

    public function changeRole($uuid) {
        if (!$this->isProjectOwner && !$this->isProjectAdmin && !$this->isAppAdmin) {
            Toaster::error(__('general.toasts.unauthorized'));
            return;
        }

        $this->showChangeRoleModal = true;
        $this->userToModify = $this->members->where('uuid', $uuid)->first();
        $this->newRole = ProjectRole::where('name', $this->userToModify['role'])->first();
    }

    public function confirmChangeRole() {
        if (!$this->isProjectOwner && !$this->isProjectAdmin && !$this->isAppAdmin) {
            Toaster::error(__('general.toasts.unauthorized'));
            return;
        }

        $member = ProjectMember::where('project_uuid', $this->project->uuid)
            ->where('user_uuid', $this->userToModify['uuid'])
            ->first();

        $member->project_role_id = $this->newRole->id;
        $member->save();

        Log::create([
            'user_uuid' => auth()->user()->uuid,
            'project_uuid' => $this->project->uuid,
            'action' => 'update',
            'table' => 'project_members',
            'data' => json_encode([
                'user_uuid' => $member->user_uuid,
                'project_role_id' => $member->project_role_id,
            ]),
            'description' => __('logs.project_members.role_changed', ['user' => $this->userToModify['user'], 'role' => $this->newRole->name]),
            'environment' => config('app.env'),
            'by_admin' => CheckIfUserIsAdmin::check(Auth::user(), $this->project->uuid)
        ]);

        $this->showChangeRoleModal = false;

        return redirect()->route('projects.settings.members.render', ['uuid' => $this->project->uuid])->success(__('settings.toast.role_changed', [
            'name' => $this->userToModify['user'], 
            'role' => $this->newRole->name
        ]));
    }

    public function addMember() {
        if (!$this->isProjectOwner && !$this->isProjectAdmin && !$this->isAppAdmin) {
            Toaster::error(__('general.toasts.unauthorized'));
            return;
        }

        $member = ProjectMember::create([
            'project_uuid' => $this->project->uuid,
            'user_uuid' => $this->newMemberUuid,
            'project_role_id' => $this->newMemberRole,
        ]);

        Log::create([
            'user_uuid' => auth()->user()->uuid,
            'project_uuid' => $this->project->uuid,
            'action' => 'create',
            'table' => 'project_members',
            'data' => json_encode([
                'user_uuid' => $member->user_uuid,
                'project_role_id' => $member->project_role_id,
            ]),
            'description' => __('logs.project_members.added', ['user' => $member->user->name, 'role' => $member->role->name]),
            'environment' => config('app.env'),
            'by_admin' => CheckIfUserIsAdmin::check(Auth::user(), $this->project->uuid)
        ]);

        return redirect()->route('projects.settings.members.render', ['uuid' => $this->project->uuid])->success(__('settings.toast.member_added', ['name' => $member->user->name]));
    }

    public function removeMember($uuid) {
        if (!$this->isProjectOwner && !$this->isProjectAdmin && !$this->isAppAdmin) {
            Toaster::error(__('general.toasts.unauthorized'));
            return;
        }

        $this->showRemoveMemberModal = true;
        $this->userToModify = $this->members->where('uuid', $uuid)->first();
    }

    public function confirmRemoveMember() {
        if (!$this->isProjectOwner && !$this->isProjectAdmin && !$this->isAppAdmin) {
            Toaster::error(__('general.toasts.unauthorized'));
            return;
        }

        $member = ProjectMember::where('project_uuid', $this->project->uuid)
            ->where('user_uuid', $this->userToModify['uuid'])
            ->first();

        $member->delete();

        Log::create([
            'user_uuid' => auth()->user()->uuid,
            'project_uuid' => $this->project->uuid,
            'action' => 'delete',
            'table' => 'project_members',
            'data' => json_encode([
                'user_uuid' => $member->user_uuid,
                'project_role_id' => $member->project_role_id,
            ]),
            'description' => __('logs.project_members.removed', ['user' => $this->userToModify['user']]),
            'environment' => config('app.env'),
            'by_admin' => CheckIfUserIsAdmin::check(Auth::user(), $this->project->uuid)
        ]);

        return redirect()->route('projects.settings.members.render', ['uuid' => $this->project->uuid])->success(__('settings.toast.member_removed', ['name' => $this->userToModify['user']]));
    }

    public function render()
    {
        return view('livewire.projects.settings.members');
    }
}
