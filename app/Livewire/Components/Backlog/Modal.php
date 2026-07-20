<?php

namespace App\Livewire\Components\Backlog;

use App\Helpers\CheckProjectPermissions;
use App\Models\BacklogCard;
use App\Models\BacklogCardAssignee;
use App\Models\BacklogTask;
use App\Models\Log;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;
use Masmerise\Toaster\Toaster;

class Modal extends Component
{
    public $project;
    public ?BacklogCard $card = null;
    public $users;

    public $cardTitle = '';
    public $cardDescription = '';

    public $search = '';
    public $filteredUsers = [];
    public $deadlineInput;

    public $approvalStatuses = ['Approved', 'Needs Work', 'Rejected', 'None'];
    public $columns = [
        ['type' => 'todo', 'name' => 'To Do', 'color' => 'purple-600', 'cards' => []],
        ['type' => 'doing', 'name' => 'In Progress', 'color' => 'sky-500', 'cards' => []],
        ['type' => 'done', 'name' => 'Done', 'color' => 'green-500', 'cards' => []],
    ];
    public $isProjectAdminOrOwner = false;

    // Card Move Properties
    public $projects;
    public $entities;

    public $selectedProject;
    public $selectedProjectUuid;

    public $selectedEntityUuid;

    public $createNewTask = false;
    public $creatingTaskInColumn = null;
    public $taskName = '';

    public $sprintOrBacklog = 'sprint';
    public $column;
    public $position = 'top';

    public function mount($project, $card, $users) {
        $this->project = $project;
        $this->card = $card->load('assignees.user');
        $this->users = $users;
        $this->filteredUsers = $users;

        $this->cardTitle = $card->title;
        $this->cardDescription = $card->description;

        $tasks = $card->tasks()->with('assignees.user')->get();
        foreach ($this->columns as &$column) {
            $column['cards'] = $tasks->where('status', $column['type'])->values();
        }

        $this->isProjectAdminOrOwner = CheckProjectPermissions::isProjectAdminOrOwner(Auth::user(), $this->project);

        // Load projects for move options
        $user = Auth::user();
        $ownedProjects = $user->ownedProjects()->get();
        $projectsWhereAdmin = $user->projectsWhereAdmin()->get();

        $this->projects = $ownedProjects->merge($projectsWhereAdmin)->unique('uuid');

        $this->selectedProject = $this->projects->first();
        $this->selectedProjectUuid = $this->selectedProject ? $this->selectedProject->uuid : null;
        $this->column = $this->selectedProject ? $this->selectedProject->columns()->first()->id : null;

        $this->entities = $this->selectedProject ? $this->selectedProject->sprints->where('is_archived', false)->whereIn('status', ['planned', 'active']) : collect();
        $this->selectedEntityUuid = $this->entities->first() ? $this->entities->first()->uuid : null;
    }

    public function updated($key, $value) {
        if ($key === 'selectedProjectUuid') {
            $this->selectedProject = $this->projects->firstWhere('uuid', $value);

            if ($this->selectedProject) {
                $this->entities = $this->sprintOrBacklog === 'sprint'
                    ? $this->selectedProject->sprints->where('is_archived', false)
                    : $this->selectedProject->backlogs;
                $this->selectedEntityUuid = optional($this->entities->first())->uuid;
                $this->column = optional($this->selectedProject->columns()->first())->id;
            }
        } elseif ($key === 'sprintOrBacklog') {
            if ($this->selectedProject) {
                $this->entities = $value === 'sprint'
                    ? $this->selectedProject->sprints->where('is_archived', false)
                    : $this->selectedProject->backlogs;
                $this->selectedEntityUuid = optional($this->entities->first())->uuid;
            }
        }
    }

    public function updatedSearch() {
        $searchTerm = strtolower($this->search);
        $this->filteredUsers = $this->users->filter(function ($user) use ($searchTerm) {
            return str_contains(strtolower($user->name), $searchTerm) || str_contains(strtolower($user->email), $searchTerm);
        });
    }

    #[On('refreshBacklogModal')]
    public function handleRefreshBacklogModal() {
        $this->loadCard();
    }

    public function addTask($columnType) {
        $this->createNewTask = true;
        $this->taskName = '';
        $this->creatingTaskInColumn = $columnType;
    }

    public function addTaskToColumn($columnType) {
        if (empty($this->taskName)) {
            Toaster::error(__('board.toast.task_name_required'));
            return;
        }

        $maxIndex = BacklogTask::where('backlog_card_id', $this->card->id)
            ->max('task_index');

        $newTask = BacklogTask::create([
            'backlog_card_id' => $this->card->id,
            'description' => $this->taskName,
            'status' => $columnType,
            'task_index' => $maxIndex !== null ? $maxIndex + 1 : 0,
        ]);

        Log::create([
            'user_uuid' => Auth::user()->uuid,
            'project_uuid' => $this->project->uuid,
            'backlog_uuid' => $this->card->backlog->uuid,
            'backlog_card_id' => $this->card->id,
            'backlog_task_id' => $newTask->id,
            'action' => 'create',
            'table' => 'backlog_tasks',
            'data' => json_encode([
                'task_name' => $this->taskName,
                'card_title' => $this->card->title,
            ]),
            'description' => __('logs.backlog.task_created', [
                'task' => $this->taskName,
                'card' => $this->card->title,
                'backlog' => $this->card->backlog->name,
            ]),
            'environment' => app()->environment(),
        ]);

        $this->createNewTask = false;
        $this->taskName = '';

        $this->loadTasks();
        $this->dispatch('$refresh');
        $this->dispatch('refreshBacklog');
    }

    public function cancelTaskCreation() {
        $this->createNewTask = false;
        $this->taskName = '';
    }

    public function saveTitle() {
        $title = $this->cardTitle;
        if (empty($title)) {
            Toaster::error(__('board.toast.title_required'));
            return;
        }

        $originalTitle = $this->card->title;

        $this->card->title = $title;
        $this->card->save();

        Log::create([
            'user_uuid' => Auth::user()->uuid,
            'project_uuid' => $this->project->uuid,
            'backlog_uuid' => $this->card->backlog->uuid,
            'backlog_card_id' => $this->card->id,
            'action' => 'update',
            'table' => 'backlog_cards',
            'data' => json_encode([
                'old_title' => $originalTitle,
                'new_title' => $title,
            ]),
            'description' => __('logs.backlog.card_updated_title', [
                'oldTitle' => $originalTitle,
                'newTitle' => $title,
                'backlog' => $this->card->backlog->name,
            ]),
            'environment' => app()->environment(),
        ]);

        $this->loadCard();
    }

    public function saveDescription() {
        $description = $this->cardDescription;
        if (empty($description)) $description = null;

        $this->card->description = $description;
        $this->card->save();

        Log::create([
            'user_uuid' => Auth::user()->uuid,
            'project_uuid' => $this->project->uuid,
            'backlog_uuid' => $this->card->backlog->uuid,
            'backlog_card_id' => $this->card->id,
            'action' => 'update',
            'table' => 'backlog_cards',
            'data' => json_encode([
                'description' => $description,
            ]),
            'description' => __('logs.backlog.card_updated_description', [
                'card' => $this->card->title,
                'backlog' => $this->card->backlog->name,
            ]),
            'environment' => app()->environment(),
        ]);

        $this->loadCard();
    }

    private function loadCard() {
        $this->card->refresh();
    }

    private function loadTasks() {
        $tasks = $this->card->tasks()->with('assignees.user')->orderBy('task_index')->get();

        foreach ($this->columns as &$column) {
            $column['cards'] = $tasks
                ->where('status', $column['type'])
                ->sortBy('task_index')
                ->values();
        }
    }

    public function closeModal() {
        $this->dispatch('closeBacklogCardModal');
    }

    public function updateCardOrder($groups) {
        $oldOrder = $this->card->tasks()
            ->get(['id', 'status', 'task_index'])
            ->mapWithKeys(fn ($task) => [
                $task->id => [
                    'status' => $task->status,
                    'index' => (int) $task->task_index,
                ],
            ])
            ->toArray();

        $newOrder = collect($groups)
            ->mapWithKeys(function ($group) {
                return [
                    $group['value'] => collect($group['items'])
                        ->pluck('value')
                        ->map(fn ($id) => (int) $id)
                        ->values()
                        ->toArray(),
                ];
            })
            ->toArray();

        $newTaskPositions = collect($newOrder)
            ->mapWithKeys(function ($taskIds, $status) {
                return collect($taskIds)
                    ->mapWithKeys(fn ($taskId, $index) => [
                        $taskId => [
                            'status' => $status,
                            'index' => $index + 1,
                        ],
                    ])
                    ->all();
            });

        $movedTask = null;

        foreach ($newTaskPositions as $taskId => $newPosition) {
            $oldPosition = $oldOrder[$taskId] ?? null;

            if ($oldPosition === null) {
                continue;
            }

            $statusChanged =
                $oldPosition['status'] !== $newPosition['status'];

            $indexChanged =
                $oldPosition['index'] !== $newPosition['index'];

            if ($statusChanged || $indexChanged) {
                $movedTask = [
                    'id' => (int) $taskId,
                    'from_status' => $oldPosition['status'],
                    'to_status' => $newPosition['status'],
                    'from_index' => $oldPosition['index'],
                    'to_index' => $newPosition['index'],
                    'status_changed' => $statusChanged,
                ];

                break;
            }
        }

        foreach ($groups as $group) {
            $status = $group['value'];

            foreach ($group['items'] as $item) {
                BacklogTask::where('id', $item['value'])->update([
                    'status' => $status,
                    'task_index' => $item['order'],
                ]);
            }
        }

        if ($movedTask !== null) {
            Log::create([
                'user_uuid' => Auth::user()->uuid,
                'project_uuid' => $this->project->uuid,
                'backlog_uuid' => $this->card->backlog->uuid,
                'backlog_card_id' => $this->card->id,
                'backlog_task_id' => $movedTask['id'],
                'action' => 'update',
                'table' => 'backlog_tasks',
                'data' => json_encode([
                    'moved_task' => $movedTask,
                    'new_order' => $newOrder,
                ]),
                'description' => __('logs.backlog.task_order_updated', [
                    'task' => optional($this->card->tasks()->find($movedTask['id']))->id,
                    'from' => $movedTask['from_status'],
                    'to' => $movedTask['to_status'],
                    'card' => $this->card->title,
                    'backlog' => $this->card->backlog->name,
                ]),
                'environment' => app()->environment(),
            ]);
        }

        $this->loadTasks();
        $this->dispatch('$refresh');
        $this->dispatch('refreshBacklog');
    }

    public function updateApprovalStatus($status) {
        $originalStatus = $this->card->approval_status;

        $this->card->approval_status = $status;
        $this->card->save();

        Log::create([
            'user_uuid' => Auth::user()->uuid,
            'project_uuid' => $this->project->uuid,
            'backlog_uuid' => $this->card->backlog->uuid,
            'backlog_card_id' => $this->card->id,
            'action' => 'update',
            'table' => 'backlog_cards',
            'data' => json_encode(['approval_status' => $status]),
            'description' => __('logs.backlog.card_approval_status_updated', [
                'card' => $this->card->title,
                'originalStatus' => $originalStatus,
                'status' => $status,
            ]),
            'environment' => app()->environment(),
        ]);

        $this->loadCard();
    }

    public function toggleAssignee($userUuid, $isChecked) {
        if ($isChecked) {
            BacklogCardAssignee::firstOrCreate([
                'backlog_card_id' => $this->card->id,
                'user_uuid' => $userUuid,
            ]);
        } else {
            BacklogCardAssignee::where('backlog_card_id', $this->card->id)
                ->where('user_uuid', $userUuid)
                ->delete();
        }

        Log::create([
            'user_uuid' => Auth::user()->uuid,
            'project_uuid' => $this->project->uuid,
            'backlog_uuid' => $this->card->backlog->uuid,
            'backlog_card_id' => $this->card->id,
            'action' => $isChecked ? 'create' : 'delete',
            'table' => 'backlog_card_assignees',
            'data' => json_encode(['assignee_user_uuid' => $userUuid]),
            'description' => $isChecked
                ? __('logs.backlog.card_assignee_added', [
                    'user' => optional($this->users->firstWhere('uuid', $userUuid))->name, 
                    'card' => $this->card->id, 
                    'backlog' => $this->card->backlog->name
                    ])
                : __('logs.backlog.card_assignee_removed', [
                    'user' => optional($this->users->firstWhere('uuid', $userUuid))->name, 
                    'card' => $this->card->id, 
                    'backlog' => $this->card->backlog->name
                ]),
            'environment' => app()->environment(),
        ]);

        $this->loadCard();
    }

    public function clearAssignees() {
        BacklogCardAssignee::where('backlog_card_id', $this->card->id)->delete();

        Log::create([
            'user_uuid' => Auth::user()->uuid,
            'project_uuid' => $this->project->uuid,
            'backlog_uuid' => $this->card->backlog->uuid,
            'backlog_card_id' => $this->card->id,
            'action' => 'delete',
            'table' => 'backlog_card_assignees',
            'data' => json_encode(['assignee_user_uuid' => 'all']),
            'description' => __('logs.backlog.card_assignee_removed_all', [
                'card' => $this->card->title, 
                'backlog' => $this->card->backlog->name
            ]),
            'environment' => app()->environment(),
        ]);

        $this->loadCard();
    }

    public function assignToMe() {
        BacklogCardAssignee::firstOrCreate([
            'backlog_card_id' => $this->card->id,
            'user_uuid' => auth()->user()->uuid,
        ]);

        Log::create([
            'user_uuid' => Auth::user()->uuid,
            'project_uuid' => $this->project->uuid,
            'backlog_uuid' => $this->card->backlog->uuid,
            'backlog_card_id' => $this->card->id,
            'action' => 'create',
            'table' => 'backlog_card_assignees',
            'data' => json_encode(['assignee_user_uuid' => auth()->user()->uuid]),
            'description' => __('logs.backlog.card_assignee_added', [
                'user' => auth()->user()->name, 
                'card' => $this->card->id, 
                'backlog' => $this->card->backlog->name
            ]),
            'environment' => app()->environment(),
        ]);

        $this->loadCard();
    }

    public function makeACopy() {
        $this->dispatch('closeBacklogCardModal');
        $this->dispatch('backlogCardCopyInitiated', ['cardId' => $this->card->id]);
    }

    public function deleteCard() {
        $this->dispatch('closeBacklogCardModal');
        $this->dispatch('backlogCardDeleteInitiated', ['cardId' => $this->card->id]);
    }

    public function moveCard() {
        if (!$this->card) {
            Toaster::error(__('board.toast.card_move_failed'));
            return;
        }

        $this->dispatch(
            'moveBacklogCardRequested',
            cardId: $this->card->id,
            sprintOrBacklog: $this->sprintOrBacklog,
            selectedEntityUuid: $this->selectedEntityUuid,
            column: $this->column,
            position: $this->position,
        )->to(\App\Livewire\Projects\Backlog\Overview::class);
    }

    public function render()
    {
        return view('livewire.components.backlog.modal');
    }
}
