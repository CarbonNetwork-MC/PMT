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
            'table' => 'projects',
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
        $project = Project::where('uuid', $this->projectId)->with(['members.user', 'logs', 'sprints.cards.assignees.user', 'sprints.cards.tasks.assignees.user', 'backlogs.cards.assignees.user', 'backlogs.cards.tasks.assignees.user'])->first();

        // Delete the project members
        $project->members()->delete();

        // Delete the logs
        $project->logs()->delete();

        // Delete the sprint cards with their assignees
        $sprints = $project->sprints;
        foreach ($sprints as $sprint) {
            $cards = $sprint->cards;
            foreach ($cards as $card) {
                $tasks = $card->tasks;
                foreach ($tasks as $task) {
                    $task->assignees()->delete();
                }
                $card->tasks()->delete();
                $card->assignees()->delete();
            }
            $sprint->cards()->delete();
        }

        $project->sprints()->delete();

        // Delete the backlog cards with their assignees
        $backlogs = $project->backlogs;
        foreach ($backlogs as $backlog) {
            $cards = $backlog->cards;
            foreach ($cards as $card) {
                $tasks = $card->tasks;
                foreach ($tasks as $task) {
                    $task->assignees()->delete();
                }
                $card->tasks()->delete();
                $card->assignees()->delete();
            }
            $backlog->cards()->delete();
        }

        $project->backlogs()->delete();

        // Delete the project
        $project->delete();

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
