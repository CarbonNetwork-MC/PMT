<?php

namespace App\Livewire\Projects\Settings;

use App\Models\Role;
use App\Models\User;
use App\Models\Project;
use App\Models\ProjectMember;
use Livewire\Component;

class Members extends Component
{
    public $uuid;
    public $project;

    public $userRole;

    public $users;
    public $roles;

    public $projectMembers;

    public $memberId;
    public $emails, $role_id;
    public $search;

    public $addMemberModal = false;
    public $deleteMemberModal = false;

    public function mount($uuid)
    {
        $this->uuid = $uuid;
        $this->project = Project::where('uuid', $uuid)->first();

        $this->userRole = ProjectMember::where('project_id', $this->project->uuid)->with('role')
            ->where('user_id', auth()->user()->uuid)->first()->role->id;

        $this->users = User::all();
        $this->roles = Role::all();

        $this->projectMembers = ProjectMember::where('project_id', $this->project->uuid)->with('user')->with('role')->get();
    }

    /**
     * Update the search query
     * 
     * @param string $key
     * @param string $value
     * 
     * @return void
     */
    public function updated($key, $value) {
        if ($key === 'search') {
            $this->projectMembers = ProjectMember::where('project_id', $this->project->uuid)
                ->whereHas('user', function($query) {
                    $query->where('name', 'like', '%' . $this->search . '%');
                })->get();
        }  
    }

    /**
     * Add members to the project
     * 
     * @return void
     */
    public function addMember() {
        $emails = explode(',', $this->emails);

        foreach ($emails as $email) {
            $user = User::where('email', $email)->first();
            if ($user) {
                // Check if the user is already a member
                $member = ProjectMember::where('project_id', $this->project->uuid)
                    ->where('user_id', $user->id)->first();
                if (!$member) {
                    // Add the user as a member
                    ProjectMember::create([
                        'project_id' => $this->project->uuid,
                        'user_id' => $user->uuid,
                        'role_id' => $this->role_id
                    ]);
                }
            }
        }

        // Toast
        $this->dispatch('memberAdded', ['message' => 'Members added successfully!']);

        // Refresh project members
        $this->projectMembers = ProjectMember::where('project_id', $this->project->uuid)->with('user')->get();

        // Close the modal
        $this->addMemberModal = false;
    }

    /**
     * Update the role of a project member
     * 
     * @param string $memberId
     * @param int $roleId
     * 
     * @return void
     */
    public function updateRole($memberId, $roleId) {
        $member = ProjectMember::find($memberId);
        if ($member) {
            // Update the role
            $member->role_id = $roleId;
            $member->save();

            // Toast
            $this->dispatch('roleUpdated', ['message' => 'Role updated successfully!']);
        }
    }

    /**
     * Initialize the removal of a member
     * 
     * @param string $memberId
     * 
     * @return void
     */
    public function initializeRemoveMember($memberId) {
        $this->memberId = $memberId;
        $this->deleteMemberModal = true;
    }

    /**
     * Remove a member from a project
     * 
     * @return void
     */
    public function removeMember() {
        $member = ProjectMember::find($this->memberId);
        if ($member) {
            // Delete the member
            $member->delete();

            // Toast
            $this->dispatch('memberRemoved', ['message' => 'Member removed successfully!']);

            // Refresh project members
            $this->projectMembers = ProjectMember::where('project_id', $this->project->uuid)->with('user')->get();

            // Close the modal
            $this->deleteMemberModal = false;
        }
    }

    /**
     * Render the component
     * 
     * @return \Illuminate\View\View
     */
    public function render()
    {
        return view('livewire.projects.settings.members');
    }
}
