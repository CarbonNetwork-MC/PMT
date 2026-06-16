<?php

namespace App\Livewire\Components\Board;

use App\Helpers\CheckProjectPermissions;
use App\Helpers\TimeFormatter;
use App\Models\TaskAssignee;
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

        $this->isProjectAdminOrOwner = CheckProjectPermissions::isProjectAdminOrOwner(Auth::user(), $task->card->column->project);

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

    private function refreshBoardAndModal() {
        $this->task->refresh();
        $this->dispatch('refreshBoard');
        $this->dispatch('cardRefreshed', ['cardId' => $this->task->card_id]);
        $this->dispatch('refreshModal');
    }

    public function toggleAssignee($userUuid, $isChecked) {
        if ($isChecked) {
            TaskAssignee::firstOrCreate([
                'task_id' => $this->task->id,
                'user_uuid' => $userUuid,
            ]);
        } else {
            TaskAssignee::where('task_id', $this->task->id)
                ->where('user_uuid', $userUuid)
                ->delete();
        }

        $this->refreshBoardAndModal();
    }

    public function clearAssignees() {
        TaskAssignee::where('task_id', $this->task->id)->delete();

        $this->refreshBoardAndModal();
    }

    public function assignToMe() {
        TaskAssignee::firstOrCreate([
            'task_id' => $this->task->id,
            'user_uuid' => auth()->user()->uuid,
        ]);

        $this->refreshBoardAndModal();
    }

    public function makeACopy() {
        $this->dispatch('closeCardModal');
        $this->dispatch('taskCopyInitiated', ['taskId' => $this->task->id]);
    }

    public function deleteTask() {
        $this->dispatch('closeCardModal');
        $this->dispatch('taskDeleteInitiated', ['taskId' => $this->task->id]);
    }

    public function convertToCard() {
        $this->dispatch('closeCardModal');
        $this->dispatch('taskConvertToCardInitiated', ['taskId' => $this->task->id]);
    }

    public function updateEstimatedTime(){
        $data = $this->validate([
            'estimatedTimeInput' => [
                'nullable',
                'regex:/^\d+$|^\d+\s*h(\s*\d+\s*m)?$|^\d+\s*m$/i'
            ],
        ]);

        $this->task->update([
            'estimated_time' => TimeFormatter::humanToMinutes($data['estimatedTimeInput']),
        ]);

        $this->estimatedTimeInput = null;

        $this->refreshBoardAndModal();
    }

    public function updateActualTime() {
        $this->validate([
            'actualTimeInput' => [
                'nullable',
                'regex:/^\d+$|^\d+\s*h(\s*\d+\s*m)?$|^\d+\s*m$/i'
            ],
        ]);

        $this->task->update([
            'actual_time' => TimeFormatter::humanToMinutes($this->actualTimeInput),
        ]);

        $this->actualTimeInput = null;

        $this->refreshBoardAndModal();
    }

    public function updateDeadline() {
        $this->validate([
            'deadlineInput' => ['nullable', 'date'],
        ]);

        $this->task->update([
            'deadline' => $this->deadlineInput
                ? \Carbon\Carbon::parse($this->deadlineInput)
                : null,
        ]);

        $this->refreshBoardAndModal();
    }

    public function render()
    {
        return view('livewire.components.board.task-card');
    }
}
