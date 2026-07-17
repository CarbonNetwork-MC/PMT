<?php

namespace App\Livewire\Projects\Sprints;

use App\Helpers\CheckProjectPermissions;
use App\Models\BacklogCard;
use App\Models\Log;
use App\Models\Project;
use Livewire\Component;
use Masmerise\Toaster\Toaster;

class Overview extends Component
{
    public $project;
    public $sprints;

    public $sprintCount;
    public $activeSprints;
    public $completedSprints;
    public $archivedSprints;
    public $statuses = [
        ['value' => 'planned', 'disabled' => false],
        ['value' => 'active', 'disabled' => false],
        ['value' => 'completed', 'disabled' => true],
    ];

    public $name;
    public $start_date;
    public $end_date;
    public $status;

    public $isProjectAdminOrOwner = false;

    public $editingSprint;
    public $deletingSprint;

    public $sprintToComplete;
    public $incompleteTasks;
    public $completeSprintAction;

    public $entityUuid;
    public $entities;
    
    public $showCompleteSprintModal = false;

    public $showEditModal = false;
    public $showDeleteModal = false;

    public function mount($uuid) {
        $this->project = Project::where('uuid', $uuid)->firstOrFail();
        $this->sprints = $this->project->sprints()->where('is_archived', false)->orderBy('created_at')->get();

        $this->sprintCount = $this->sprints->count();
        $this->activeSprints = $this->sprints->where('status', 'active')->where('is_archived', false)->count();
        $this->completedSprints = $this->sprints->where('status', 'completed')->where('is_archived', false)->count();
        $this->archivedSprints = $this->project->sprints()->where('is_archived', true)->count();

        $this->isProjectAdminOrOwner = CheckProjectPermissions::isProjectAdminOrOwner(auth()->user(), $this->project);

        $this->entities = $this->project->backlogs()->where('is_archived', false)->get();
        $this->entityUuid = $this->entities->first()?->uuid;
        $this->completeSprintAction = 'backlog';
    }

    public function updated($key, $value) {
        if ($key === 'completeSprintAction') {
            if ($value === 'sprint') {
                $this->entities = $this->project->sprints()->where('is_archived', false)->where('status', '!=', 'completed')->where('uuid', '!=', $this->sprintToComplete?->uuid)->get();
            } else {
                $this->entities = $this->project->backlogs()->where('is_archived', false)->get();
            }

            $this->entityUuid = $this->entities->first()?->uuid;
        }
    }

    private function updateCounts() {
        $this->sprintCount = $this->sprints->count();
        $this->activeSprints = $this->sprints->where('status', 'active')->where('is_archived', false)->count();
        $this->completedSprints = $this->sprints->where('status', 'completed')->where('is_archived', false)->count();
        $this->archivedSprints = $this->project->sprints()->where('is_archived', true)->count();
    }

    public function editSprint($uuid) {
        $this->editingSprint = $this->sprints->where('uuid', $uuid)->firstOrFail();
        $this->name = $this->editingSprint->name;
        $this->start_date = $this->editingSprint->start_date?->format('Y-m-d');
        $this->end_date = $this->editingSprint->end_date?->format('Y-m-d');
        $this->status = $this->editingSprint->status;
        $this->showEditModal = true;
    }

    public function updateSprint() {
        $this->editingSprint->update([
            'name' => $this->name,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'status' => $this->status,
        ]);

        $this->sprints = $this->project->sprints()->where('is_archived', false)->orderBy('created_at')->get();
        $this->updateCounts();
        $this->showEditModal = false;

        Log::create([
            'user_uuid' => auth()->user()->uuid,
            'project_uuid' => $this->project->uuid,
            'sprint_uuid' => $this->editingSprint->uuid,
            'action' => 'update',
            'table' => 'sprints',
            'data' => json_encode([
                'name' => $this->editingSprint->name,
                'start_date' => $this->editingSprint->start_date,
                'end_date' => $this->editingSprint->end_date,
                'status' => $this->editingSprint->status,
            ]),
            'description' => __('logs.sprints.updated', ['sprint' => $this->editingSprint->name]),
        ]);

        Toaster::success(__('sprints.toast.sprint-updated'));
    }

    public function deleteSprint($uuid) {
        $this->deletingSprint = $this->sprints->where('uuid', $uuid)->firstOrFail();
        $this->showDeleteModal = true;
    }

    public function destroySprint() {
        $sprintName = $this->deletingSprint->name;

        $this->deletingSprint->delete();

        $this->sprints = $this->project->sprints()->where('is_archived', false)->orderBy('created_at')->get();
        $this->updateCounts();
        $this->showDeleteModal = false;

        Log::create([
            'user_uuid' => auth()->user()->uuid,
            'project_uuid' => $this->project->uuid,
            'sprint_uuid' => $this->deletingSprint->uuid,
            'action' => 'delete',
            'table' => 'sprints',
            'data' => json_encode([
                'name' => $this->deletingSprint->name,
                'start_date' => $this->deletingSprint->start_date,
                'end_date' => $this->deletingSprint->end_date,
                'status' => $this->deletingSprint->status,
            ]),
            'description' => __('logs.sprints.deleted', [
                'sprint' => $sprintName
            ]),
            'environment' => app()->environment(),
        ]);

        Toaster::success(__('sprints.toast.sprint-deleted'));
    }

    public function startSprint($uuid) {
        $sprint = $this->sprints->where('uuid', $uuid)->firstOrFail();
        $sprint->update(['status' => 'active']);

        $this->sprints = $this->project->sprints()->where('is_archived', false)->orderBy('created_at')->get();
        $this->updateCounts();

        Log::create([
            'user_uuid' => auth()->user()->uuid,
            'project_uuid' => $this->project->uuid,
            'sprint_uuid' => $sprint->uuid,
            'action' => 'update',
            'table' => 'sprints',
            'data' => json_encode([
                'name' => $sprint->name,
                'start_date' => $sprint->start_date,
                'end_date' => $sprint->end_date,
                'status' => $sprint->status,
            ]),
            'description' => __('logs.sprints.status_changed', ['sprint' => $sprint->name, 'status' => $sprint->status]),
        ]);

        Toaster::success(__('sprints.toast.start_sprint', ['name' => $sprint->name]));
    }

    public function completeSprint($uuid) {
        $sprint = $this->sprints->where('uuid', $uuid)->firstOrFail();

        // Get cards which are not in 'done' column
        $incompleteTasks = $sprint->cards()
            ->whereHas('column', function($query) {
                $query->where('column_type', '!=', 'done');
            })
            ->get();

        if ($incompleteTasks->isNotEmpty()) {
            $this->incompleteTasks = $incompleteTasks;
        }
            
        $this->sprintToComplete = $sprint;
        $this->showCompleteSprintModal = true;
    }

    public function confirmCompleteSprint() {
        $sprint = $this->sprintToComplete;

        if ($this->incompleteTasks) {
            if ($this->completeSprintAction === 'backlog') {
                // Move cards to backlog
                $this->incompleteTasks->each(function ($card) {
                    $index = BacklogCard::where('backlog_uuid', $this->entityUuid)->max('card_index') + 1;

                    // 1. Create a new BacklogCard with the same data as the sprint card
                    $backlogCard = BacklogCard::create([
                        'backlog_uuid' => $this->entityUuid,
                        'title' => $card->title,
                        'description' => $card->description,
                        'approval_status' => $card->approval_status,
                        'card_index' => $index,
                    ]);

                    // 2. Create BacklogCardAssignees for the new BacklogCard
                    $card->assignees->each(function ($assignee) use ($backlogCard) {
                        $backlogCard->assignees()->create([
                            'user_uuid' => $assignee->user_uuid,
                        ]);
                    });

                    // 3. Create BacklogTasks for the new BacklogCard
                    $card->tasks->each(function ($task) use ($backlogCard) {
                        $newTask = $backlogCard->tasks()->create([
                            'description' => $task->description,
                            'status' => $task->status,
                            'task_index' => $task->task_index,
                        ]);

                        // 4. Create BacklogTaskAssignees for the new BacklogTask
                        $task->assignees->each(function ($assignee) use ($newTask) {
                            $newTask->assignees()->create([
                                'user_uuid' => $assignee->user_uuid,
                            ]);
                        });
                    });

                    // 5. Delete the original sprint card
                    $card->delete();
                });
            }

            if ($this->completeSprintAction === 'sprint') {
                $targetSprint = $this->project->sprints()->where('uuid', $this->entityUuid)->firstOrFail();

                if ($targetSprint->status === 'completed') {
                    Toaster::error(__('sprints.toast.sprint-completed-error', ['name' => $targetSprint->name]));
                    return;
                }

                $this->incompleteTasks->each(function ($card) use ($targetSprint) {
                    $index = $targetSprint->cards()->where('column_id', $card->column_id)->max('card_index') + 1 ?? 0;

                    $card->update([
                        'sprint_uuid' => $targetSprint->uuid,
                        'card_index' => $index,
                    ]);
                });
            }
        }

        $this->finishSprintCompletion($sprint);
    }

    public function finishSprintCompletion($sprint) {
        $sprint->update(['status' => 'completed']);

        $this->sprints = $this->project->sprints()
            ->where('is_archived', false)
            ->orderBy('created_at')
            ->get();

        $this->updateCounts();

        Log::create([
            'user_uuid' => auth()->user()->uuid,
            'project_uuid' => $this->project->uuid,
            'sprint_uuid' => $sprint->uuid,
            'action' => 'update',
            'table' => 'sprints',
            'data' => json_encode([
                'name' => $sprint->name,
                'start_date' => $sprint->start_date,
                'end_date' => $sprint->end_date,
                'status' => $sprint->status,
            ]),
            'description' => __('logs.sprints.status_changed', [
                'sprint' => $sprint->name, 
                'status' => $sprint->status
            ]),
        ]);

        $this->showCompleteSprintModal = false;

        Toaster::success(__('sprints.toast.complete_sprint', [
            'name' => $sprint->name
        ]));
    }

    public function archiveSprint($uuid) {
        $sprint = $this->sprints->where('uuid', $uuid)->firstOrFail();
        $sprint->update([
            'is_archived' => true,
            'archived_at' => now(),
            'archived_by' => auth()->user()->uuid,
        ]);

        $this->sprints = $this->project->sprints()->where('is_archived', false)->orderBy('created_at')->get();
        $this->updateCounts();

        Log::create([
            'user_uuid' => auth()->user()->uuid,
            'project_uuid' => $this->project->uuid,
            'sprint_uuid' => $sprint->uuid,
            'action' => 'update',
            'table' => 'sprints',
            'data' => json_encode([
                'name' => $sprint->name,
                'start_date' => $sprint->start_date,
                'end_date' => $sprint->end_date,
                'status' => $sprint->status,
            ]),
            'description' => __('logs.sprints.archived', [
                'sprint' => $sprint->name
            ]),
        ]);

        Toaster::success(__('sprints.toast.archive_sprint', ['name' => $sprint->name]));
    }
    
    public function render()
    {
        return view('livewire.projects.sprints.overview');
    }
}
