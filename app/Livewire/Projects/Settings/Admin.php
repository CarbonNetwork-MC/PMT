<?php

namespace App\Livewire\Projects\Settings;

use Livewire\Component;
use App\Models\Project;
use App\Models\ProjectMember;

class Admin extends Component
{
    public $uuid;

    public $projectMembers;
    public $projectId;

    public $newOwner;

    public $changeOwnerModal = false;
    public $deleteProjectModal = false;

    public function mount($uuid)
    {
        $this->uuid = $uuid;

        $this->projectMembers = ProjectMember::where('project_id', $this->uuid)->with('user')->with('role')->get();
    }

    /**
     * Change the owner of the project
     * 
     * @return \Illuminate\Http\RedirectResponse
     */
    public function changeOwner() {
        // Find the project
        $project = Project::where('uuid', $this->uuid)->first();

        // Update the owner
        $project->owner_id = $this->newOwner;
        $project->save();

        // Update the project members
        ProjectMember::where('project_id', $this->uuid)->where('user_id', $this->newOwner)->update(['role_id' => 3]);
        ProjectMember::where('project_id', $this->uuid)->where('user_id', auth()->user()->uuid)->update(['role_id' => 2]);

        // Toast
        $this->dispatch('changedOwner', ['message' => 'The owner of the project has been changed.']);

        // Close the modal
        $this->changeOwnerModal = false;

        // Redirect to the general settings
        return redirect()->route('projects.settings.overall.render', ['uuid' => $this->uuid]);
    }

    /**
     * Initialize the deletion of the project
     * 
     * @param string $projectId
     * 
     * @return void
     */
    public function initializeProjectDeletion($projectId) {
        $this->deleteProjectModal = true;
        $this->projectId = $projectId;
    }

    /**
     * Delete the project
     * 
     * @return \Illuminate\Http\RedirectResponse
     */
    public function deleteProject() {
        // Find the project
        $project = Project::where('uuid', $this->projectId)->first();

        // Delete the project
        $project->delete();

        // Toast
        $this->dispatch('projectDeleted', ['message' => 'The project has been deleted.']);

        // Redirect to the projects overview
        return redirect()->route('projects.overview.render');
    }

    public function render()
    {
        return view('livewire.projects.settings.admin');
    }
}
