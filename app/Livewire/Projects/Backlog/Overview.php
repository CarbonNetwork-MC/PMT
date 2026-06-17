<?php

namespace App\Livewire\Projects\Backlog;

use App\Helpers\CheckProjectPermissions;
use App\Models\Backlog as BacklogModel;
use App\Models\Project;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Overview extends Component
{
    public $project;
    public $backlogs;

    public $entities;
    public $projects;
    public $users;

    public $selectedBacklog;
    public $selectedCard = null;

    public $approvalStatuses = ['Approved', 'Needs Work', 'Rejected', 'None'];
    public $isProjectAdminOrOwner;

    public $selectedProject;
    public $selectedProjectUuid;
    public $selectedEntityUuid;

    public $sprintOrBacklog = 'sprint';
    public $column;
    public $position = 'top';

    public function mount($uuid) {
        $this->project = Project::where('uuid', $uuid)->firstOrFail();
        $this->backlogs = $this->project->backlogs()->with(['cards.assignees', 'cards.tasks.assignees'])->orderBy('created_at', 'desc')->get();
        $this->selectedBacklog = $this->backlogs->first();
        $this->users = $this->project->members()->with('user')->get()->pluck('user');

        $user = Auth::user();
        $ownedProjects = $user->ownedProjects()->get();
        $projectsWhereAdmin = $user->projectsWhereAdmin()->get();

        $this->projects = $ownedProjects->merge($projectsWhereAdmin)->unique('uuid');
        $this->selectedProject = $this->projects->first();
        $this->selectedProjectUuid = $this->selectedProject ? $this->selectedProject->uuid : null;

        $this->entities = $this->selectedProject ? $this->selectedProject->sprints->where('is_archived', false) : collect();
        $this->selectedEntityUuid = $this->entities->first() ? $this->entities->first()->uuid : null;
        $this->column = $this->selectedProject ? $this->selectedProject->columns()->first()->id : null;

        $this->isProjectAdminOrOwner = CheckProjectPermissions::isProjectAdminOrOwner(Auth::user(), $this->project);
    }

    public function openBacklog($backlogUuid) {
        $this->selectedBacklog = BacklogModel::where('uuid', $backlogUuid)->with(['cards.assignees', 'cards.tasks.assignees'])->first();
        $this->selectedCard = null;
    }

    public function selectCard($cardId) {
        $this->selectedCard = $this->selectedBacklog->cards()->where('id', $cardId)->with(['assignees', 'tasks.assignees'])->first();
    }

    public function updateApprovalStatus($id, $status) {
        $card = $this->selectedBacklog->cards()->where('id', $id)->first();

        $card->approval_status = $status;
        $card->save();
    }

    public function render()
    {
        return view('livewire.projects.backlog.overview');
    }
}
