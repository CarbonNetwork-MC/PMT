<?php

namespace App\Livewire\Projects\Settings;

use App\Models\Log;
use App\Models\Task;
use App\Models\Card;
use App\Models\Sprint;
use App\Models\Project;
use Livewire\Component;
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

        // Create a new Log
        Log::create([
            'user_id' => auth()->user()->uuid,
            'project_id' => $this->uuid,
            'action' => 'update',
            'data' => json_encode(['newOwner' => $this->newOwner, 'oldOwner' => auth()->user()->uuid]),
            'description' => 'Changed the owner of the project',
        ]);

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

        // Delete all project members
        ProjectMember::where('project_id', $this->projectId)->delete();

        // Delete all sprints, cards and tasks
        $sprints = Sprint::where('project_id', $this->projectId)->get();
        foreach ($sprints as $sprint) {
            $cards = Card::where('sprint_id', $sprint->id)->get();
            foreach ($cards as $card) {
                $card->delete();
            }

            $tasks = Task::where('sprint_id', $sprint->id)->get();
            foreach ($tasks as $task) {
                $task->delete();
            }

            $sprint->delete();
        }

        // Create a new Log - Delete Project
        Log::create([
            'user_id' => auth()->user()->uuid,
            'project_id' => $this->projectId,
            'action' => 'delete',
            'table' => 'projects',
            'data' => json_encode(['project_id' => $this->projectId, 'project' => $project]),
            'description' => 'Deleted project <b>' . $project->name . '</b>',
        ]);

        // Create a new Log - Delete Project Members
        Log::create([
            'user_id' => auth()->user()->uuid,
            'project_id' => $this->projectId,
            'action' => 'delete',
            'table' => 'project_members',
            'data' => json_encode(['project_id' => $this->projectId]),
            'description' => 'Deleted all project members',
        ]);

        // Create a new Log - Delete Sprints
        Log::create([
            'user_id' => auth()->user()->uuid,
            'project_id' => $this->projectId,
            'action' => 'delete',
            'table' => 'sprints',
            'data' => json_encode(['project_id' => $this->projectId]),
            'description' => 'Deleted all sprints',
        ]);

        // Create a new Log - Delete Cards
        Log::create([
            'user_id' => auth()->user()->uuid,
            'project_id' => $this->projectId,
            'action' => 'delete',
            'table' => 'cards',
            'data' => json_encode(['project_id' => $this->projectId]),
            'description' => 'Deleted all cards',
        ]);

        // Create a new Log - Delete Tasks
        Log::create([
            'user_id' => auth()->user()->uuid,
            'project_id' => $this->projectId,
            'action' => 'delete',
            'table' => 'tasks',
            'data' => json_encode(['project_id' => $this->projectId]),
            'description' => 'Deleted all tasks',
        ]);

        // Toast
        $this->dispatch('projectDeleted', ['message' => 'The project has been deleted.']);

        // Redirect to the projects overview
        return redirect()->route('projects.projects.render');
    }

    /**
     * Render the livewire component
     * 
     * @return \Illuminate\View\View
     */
    public function render()
    {
        return view('livewire.projects.settings.admin');
    }
}
