<?php

namespace App\Livewire\Components\Backlog;

use App\Helpers\CheckIfUserIsAdmin;
use App\Helpers\CheckProjectPermissions;
use App\Models\BacklogTaskAssignee;
use App\Models\Log;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class TaskCard extends Component
{
    public $task;
    public $taskTitle;

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
        $this->taskTitle = $task->description;
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

    public function updateTitle() {
        $this->task->description = $this->taskTitle;
        $this->task->save();

        Log::create([
            'user_uuid' => auth()->user()->uuid,
            'project_uuid' => $this->task->card->backlog->project->uuid,
            'backlog_uuid' => $this->task->card->backlog->uuid,
            'backlog_card_id' => $this->task->card->id,
            'backlog_task_id' => $this->task->id,
            'action' => 'update',
            'table' => 'backlog_tasks',
            'data' => json_encode(['description' => $this->taskTitle]),
            'description' => __('logs.backlog.task_title_updated', [
                'task' => $this->taskTitle,
                'card' => $this->task->card->title,
                'backlog' => $this->task->card->backlog->name
            ]),
            'environment' => app()->environment(),
            'by_admin' => CheckIfUserIsAdmin::check(Auth::user(), $this->task->card->backlog->project->uuid)
        ]);

        $this->refreshBacklogAndModal();
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

        Log::create([
            'user_uuid' => auth()->user()->uuid,
            'project_uuid' => $this->task->card->backlog->project->uuid,
            'backlog_uuid' => $this->task->card->backlog->uuid,
            'backlog_card_id' => $this->task->card->id,
            'backlog_task_id' => $this->task->id,
            'action' => $isChecked ? 'create' : 'delete',
            'table' => 'backlog_task_assignees',
            'data' => json_encode(['assignee_user_uuid' => $userUuid]),
            'description' => $isChecked
                ? __('logs.backlog.task_assignee_added', [
                    'user' => optional($this->users->firstWhere('uuid', $userUuid))->name, 
                    'task' => $this->task->name,
                    'card' => $this->task->card->title,
                    'backlog' => $this->task->card->backlog->name
                ])
                : __('logs.backlog.task_assignee_removed', [
                    'user' => optional($this->users->firstWhere('uuid', $userUuid))->name, 
                    'task' => $this->task->name,
                    'card' => $this->task->card->title,
                    'backlog' => $this->task->card->backlog->name
                ]),
            'environment' => app()->environment(),
            'by_admin' => CheckIfUserIsAdmin::check(Auth::user(), $this->task->card->backlog->project->uuid)
        ]);

        $this->refreshBacklogAndModal();
    }

    public function clearAssignees() {
        BacklogTaskAssignee::where('backlog_task_id', $this->task->id)->delete();

        Log::create([
            'user_uuid' => auth()->user()->uuid,
            'project_uuid' => $this->task->card->backlog->project->uuid,
            'backlog_uuid' => $this->task->card->backlog->uuid,
            'backlog_card_id' => $this->task->card->id,
            'backlog_task_id' => $this->task->id,
            'action' => 'delete',
            'table' => 'backlog_task_assignees',
            'data' => json_encode([]),
            'description' => __('logs.backlog.task_assignee_removed_all', [
                'task' => $this->task->name,
                'card' => $this->task->card->title,
                'backlog' => $this->task->card->backlog->name
            ]),
            'environment' => app()->environment(),
            'by_admin' => CheckIfUserIsAdmin::check(Auth::user(), $this->task->card->backlog->project->uuid)
        ]);

        $this->refreshBacklogAndModal();
    }

    public function assignToMe() {
        BacklogTaskAssignee::firstOrCreate([
            'backlog_task_id' => $this->task->id,
            'user_uuid' => auth()->user()->uuid,
        ]);

        Log::create([
            'user_uuid' => auth()->user()->uuid,
            'project_uuid' => $this->task->card->backlog->project->uuid,
            'backlog_uuid' => $this->task->card->backlog->uuid,
            'backlog_card_id' => $this->task->card->id,
            'backlog_task_id' => $this->task->id,
            'action' => 'create',
            'table' => 'backlog_task_assignees',
            'data' => json_encode(['assignee_user_uuid' => auth()->user()->uuid]),
            'description' => __('logs.backlog.task_assignee_added', [
                'user' => auth()->user()->name, 
                'task' => $this->task->name,
                'card' => $this->task->card->title,
                'backlog' => $this->task->card->backlog->name
            ]),
            'environment' => app()->environment(),
            'by_admin' => CheckIfUserIsAdmin::check(Auth::user(), $this->task->card->backlog->project->uuid)
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
