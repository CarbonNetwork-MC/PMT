<?php

namespace App\Livewire\Components\Backlog;

use App\Helpers\CheckProjectPermissions;
use App\Models\BacklogTaskAssignee;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class TaskCard extends Component
{
    public $task;

    public $users;
    public $filteredUsers = [];
    public $search = '';

    public $isProjectAdminOrOwner = false;

    public $estimatedTimeInput;
    public $actualTimeInput;
    public $deadlineInput;

    // Card Move Properties
    public $projects;
    public $entities;

    public $selectedProject;
    public $selectedProjectUuid;

    public $selectedEntityUuid;

    public $sprintOrBacklog = 'sprint';
    public $column;
    public $position = 'top';

    public function mount($task, $users) {
        $this->task = $task;
        $this->users = $users;
        $this->filteredUsers = $users;

        $this->isProjectAdminOrOwner = CheckProjectPermissions::isProjectAdminOrOwner(Auth::user(), $task->card->backlog->project);

        $this->deadlineInput = $task->deadline ? \Carbon\Carbon::parse($task->deadline)->format('Y-m-d\TH:i') : null;

        // Load projects for move options
        $user = Auth::user();
        $ownedProjects = $user->ownedProjects()->get();
        $projectsWhereAdmin = $user->projectsWhereAdmin()->get();

        $this->projects = $ownedProjects->merge($projectsWhereAdmin)->unique('uuid');

        $this->selectedProject = $this->projects->first();
        $this->selectedProjectUuid = $this->selectedProject ? $this->selectedProject->uuid : null;
        $this->column = $this->selectedProject ? $this->selectedProject->columns()->first()->id : null;

        $this->entities = $this->selectedProject ? $this->selectedProject->sprints->where('is_archived', false) : collect();
        $this->selectedEntityUuid = $this->entities->first() ? $this->entities->first()->uuid : null;
    }

    public function updated($key, $value) {
        switch ($key) {
            case 'selectedProjectUuid':
                $this->selectedProject = $this->projects->firstWhere('uuid', $value);

                if ($this->selectedProject) {
                    $this->entities = $this->sprintOrBacklog === 'sprint'
                        ? $this->selectedProject->sprints->where('is_archived', false)
                        : $this->selectedProject->backlogs;
                    $this->selectedEntityUuid = optional($this->entities->first())->uuid;
                    $this->column = optional($this->selectedProject->columns()->first())->id;
                }
                break;

            case 'sprintOrBacklog':
                if ($this->selectedProject) {
                    $this->entities = $value === 'sprint'
                        ? $this->selectedProject->sprints->where('is_archived', false)
                        : $this->selectedProject->backlogs;
                    $this->selectedEntityUuid = optional($this->entities->first())->uuid;
                }
                break;
        }
    }

    public function updatedSearch() {
        $searchTerm = strtolower($this->search);
        $this->filteredUsers = $this->users->filter(function ($user) use ($searchTerm) {
            return str_contains(strtolower($user->name), $searchTerm) || str_contains(strtolower($user->email), $searchTerm);
        });
    }

    private function refreshBacklogAndModal() {
        $this->task->refresh();
        $this->dispatch('refreshBacklog');
        $this->dispatch('refreshBacklogModal');
    }

    public function toggleAssignee($userUuid, $isChecked) {
        if ($isChecked) {
            BacklogTaskAssignee::firstOrCreate([
                'backlog_task_id' => $this->task->id,
                'user_uuid' => $userUuid,
            ]);
        } else {
            BacklogTaskAssignee::where('backlog_task_id', $this->task->id)
                ->where('user_uuid', $userUuid)
                ->delete();
        }

        $this->refreshBacklogAndModal();
    }

    public function clearAssignees() {
        BacklogTaskAssignee::where('backlog_task_id', $this->task->id)->delete();

        $this->refreshBacklogAndModal();
    }

    public function assignToMe() {
        BacklogTaskAssignee::firstOrCreate([
            'backlog_task_id' => $this->task->id,
            'user_uuid' => auth()->user()->uuid,
        ]);

        $this->refreshBacklogAndModal();
    }

    public function makeACopy() {
        $this->dispatch('closeBacklogCardModal');
        $this->dispatch('backlogTaskCopyInitiated', ['taskId' => $this->task->id]);
    }

    public function deleteTask() {
        $this->dispatch('closeBacklogCardModal');
        $this->dispatch('backlogTaskDeleteInitiated', ['taskId' => $this->task->id]);
    }

    public function convertToCard() {
        $this->dispatch('closeBacklogCardModal');
        $this->dispatch('taskConvertToBacklogCardInitiated', ['taskId' => $this->task->id]);
    }

    public function render()
    {
        return view('livewire.components.backlog.task-card');
    }
}
