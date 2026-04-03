<?php

namespace App\Livewire\Projects\Settings;

use App\Models\Project;
use App\Models\ProjectMember;
use App\Models\ProjectRole;
use App\Models\User;
use Livewire\Component;

class Admin extends Component
{
    public $project;
    public $projectMembers;

    public $newOwnerId;
    public $newOwner;

    public $showChangeOwnerModal = false;
    public $showDeleteProjectModal = false;

    public function mount($uuid) {
        $this->project = Project::where('uuid', $uuid)->firstOrFail();
        $this->projectMembers = ProjectMember::where('project_uuid', $this->project->uuid)
            ->with(['user', 'role'])
            ->get();
    }

    public function updated($key, $value) {
        if ($key === 'newOwnerId') {
            $this->newOwner = User::where('uuid', $value)->first();
        }
    }

    public function confirmChangeOwner() {
        $oldOwner = $this->project->owner;

        // Update project owner
        $this->project->owner_uuid = $this->newOwner->uuid;
        $this->project->save();

        // Add old owner as project member with admin role
        $adminRole = ProjectRole::where('slug', 'admin')->first();

        ProjectMember::create([
            'project_uuid' => $this->project->uuid,
            'user_uuid' => $oldOwner->uuid,
            'project_role_id' => $adminRole->id,
        ]);

        // Remove new owner from project members
        ProjectMember::where('project_uuid', $this->project->uuid)
            ->where('user_uuid', $this->newOwner->uuid)
            ->delete();

        return redirect()->route('projects.settings.general.render', ['uuid' => $this->project->uuid])->success(__('settings.toast.owner_changed', ['newOwner' => $this->newOwner->name]));
    }

    public function confirmDeleteProject() {
        $this->project->delete();

        return redirect()->route('projects.render')->success(__('settings.toast.project_deleted'));
    }

    public function render()
    {
        return view('livewire.projects.settings.admin');
    }
}
