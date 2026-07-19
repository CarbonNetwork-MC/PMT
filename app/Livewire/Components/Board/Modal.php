<?php

namespace App\Livewire\Components\Board;

use App\Helpers\CheckProjectPermissions;
use App\Models\BacklogCard;
use App\Models\Card;
use App\Models\CardAssignee;
use App\Models\Log;
use App\Models\Task;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\On;
use Livewire\Component;
use Masmerise\Toaster\Toaster;

class Modal extends Component
{
    public $project;
    public $card;
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

        $this->entities = $this->selectedProject ? $this->selectedProject->sprints->where('is_archived', false) : collect();
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

    #[On('refreshModal')]
    public function handleRefreshModal() {
        $this->loadCard();
    }

    public function addTask($columnType) {
        $this->createNewTask = true;
        $this->taskName = '';
        $this->creatingTaskInColumn = $columnType;
    }

    public function addTaskToColumn() {
        if (empty($this->taskName)) {
            Toaster::error(__('board.toast.task_name_required'));
            return;
        }

        $maxIndex = Task::where('card_id', $this->card->id)
            ->where('status', $this->creatingTaskInColumn)
            ->max('task_index');

        Task::create([
            'card_id' => $this->card->id,
            'description' => $this->taskName,
            'status' => $this->creatingTaskInColumn,
            'task_index' => $maxIndex !== null ? $maxIndex + 1 : 0,
        ]);

        Log::create([
            'user_uuid' => auth()->user()->uuid,
            'project_uuid' => $this->project->uuid,
            'sprint_uuid' => $this->card->sprint_uuid,
            'card_id' => $this->card->id,
            'action' => 'create',
            'table' => 'tasks',
            'data' => json_encode([
                'description' => $this->taskName,
                'status' => $this->creatingTaskInColumn,
                'task_index' => $maxIndex !== null ? $maxIndex + 1 : 0,
            ]),
            'description' => __('logs.board.task_created', [
                'task' => $this->taskName,
                'card' => $this->card->title,
                'sprint' => $this->card->sprint ? $this->card->sprint->name : 'N/A',
            ]),
            'environment' => app()->environment(),
        ]);

        $this->createNewTask = false;
        $this->taskName = '';

        $this->loadTasks();
        $this->dispatch('$refresh');
        $this->dispatch('refreshBoard');
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
            'user_uuid' => auth()->user()->uuid,
            'project_uuid' => $this->project->uuid,
            'sprint_uuid' => $this->card->sprint_uuid,
            'card_id' => $this->card->id,
            'action' => 'update',
            'table' => 'cards',
            'data' => json_encode(['title' => $title]),
            'description' => __('logs.board.card_updated_title', [
                'title' => $originalTitle,
                'newTitle' => $title,
                'sprint' => $this->card->sprint ? $this->card->sprint->name : 'N/A',
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
            'user_uuid' => auth()->user()->uuid,
            'project_uuid' => $this->project->uuid,
            'sprint_uuid' => $this->card->sprint_uuid,
            'card_id' => $this->card->id,
            'action' => 'update',
            'table' => 'cards',
            'data' => json_encode(['description' => $description]),
            'description' => __('logs.board.card_updated_description', [
                'card' => $this->card->title,
                'sprint' => $this->card->sprint ? $this->card->sprint->name : 'N/A',
            ]),
            'environment' => app()->environment(),
        ]);

        $this->loadCard();
    }

    private function loadCard() {
        $this->card->refresh();
        $this->dispatch('cardRefreshed', ['cardId' => $this->card->id]);
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
        $this->dispatch('closeCardModal');
    }

    public function updateCardOrder($groups) {
        $oldOrder = $this->card->tasks()
            ->get(['id', 'status', 'task_index'])
            ->mapWithKeys(fn ($task) => [
                $task->id => [
                    'group' => $task->status,
                    'index' => $task->task_index,
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
            ->mapWithKeys(function ($taskIds, $group) {
                return collect($taskIds)
                    ->mapWithKeys(fn ($taskId, $index) => [
                        $taskId => [
                            'group' => $group,
                            'index' => $index,
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

            $columnChanged =
                $oldPosition['group'] !== $newPosition['group'];

            $indexChanged =
                (int) $oldPosition['index'] !== (int) $newPosition['index'];

            if ($columnChanged || $indexChanged) {
                $movedTask = [
                    'id' => (int) $taskId,
                    'from_group' => $oldPosition['group'],
                    'to_group' => $newPosition['group'],
                    'from_index' => (int) $oldPosition['index'],
                    'to_index' => (int) $newPosition['index'],
                    'column_changed' => $columnChanged,
                ];

                break;
            }
        }

        foreach ($groups as $group) {
            $status = $group['value'];

            foreach ($group['items'] as $item) {
                Task::where('id', $item['value'])->update([
                    'status' => $status,
                    'task_index' => $item['order'],
                ]);
            }
        }

        Log::create([
            'user_uuid' => auth()->user()->uuid,
            'project_uuid' => $this->project->uuid,
            'sprint_uuid' => $this->card->sprint_uuid,
            'card_id' => $this->card->id,
            'action' => 'update',
            'table' => 'tasks',
            'data' => json_encode([
                'old_order' => $oldOrder,
                'new_order' => $newOrder,
            ]),
            'description' => __('logs.board.task_order_updated', [
                'task' => $movedTask ? Task::find($movedTask['id'])->id : null,
                'from' => $movedTask ? $movedTask['from_group'] : null,
                'to' => $movedTask ? $movedTask['to_group'] : null,
                'card' => $this->card->title,
                'sprint' => $this->card->sprint ? $this->card->sprint->name : 'N/A',
            ]),
            'environment' => app()->environment(),
        ]);

        $this->loadTasks();
        $this->dispatch('$refresh');
        $this->dispatch('refreshBoard');
    }

    public function updateApprovalStatus($status) {
        $originalStatus = $this->card->approval_status;

        $this->card->approval_status = $status;
        $this->card->save();

        Log::create([
            'user_uuid' => auth()->user()->uuid,
            'project_uuid' => $this->project->uuid,
            'sprint_uuid' => $this->card->sprint_uuid,
            'card_id' => $this->card->id,
            'action' => 'update',
            'table' => 'cards',
            'data' => json_encode(['approval_status' => $status]),
            'description' => __('logs.board.card_approval_status_updated', [
                'card' => $this->card->title,
                'originalStatus' => $originalStatus,
                'status' => $status,
                'sprint' => $this->card->sprint ? $this->card->sprint->name : 'N/A',
            ]),
            'environment' => app()->environment(),
        ]);

        $this->loadCard();
    }

    public function toggleAssignee($userUuid, $isChecked) {
        if ($isChecked) {
            CardAssignee::firstOrCreate([
                'card_id' => $this->card->id,
                'user_uuid' => $userUuid,
            ]);
        } else {
            CardAssignee::where('card_id', $this->card->id)
                ->where('user_uuid', $userUuid)
                ->delete();
        }

        Log::create([
            'user_uuid' => auth()->user()->uuid,
            'project_uuid' => $this->project->uuid,
            'sprint_uuid' => $this->card->sprint_uuid,
            'card_id' => $this->card->id,
            'action' => $isChecked ? 'create' : 'delete',
            'table' => 'card_assignees',
            'data' => json_encode(['user_uuid' => $userUuid]),
            'description' => $isChecked
                ? __('logs.board.card_assignee_added', [
                    'user' => $this->users->firstWhere('uuid', $userUuid)->name,
                    'sprint' => $this->card->sprint ? $this->card->sprint->name : 'N/A',
                ])
                : __('logs.board.card_assignee_removed', [
                    'user' => $this->users->firstWhere('uuid', $userUuid)->name,
                    'sprint' => $this->card->sprint ? $this->card->sprint->name : 'N/A',
                ]),
            'environment' => app()->environment(),
        ]);

        $this->loadCard();
    }

    public function clearAssignees() {
        CardAssignee::where('card_id', $this->card->id)->delete();

        Log::create([
            'user_uuid' => auth()->user()->uuid,
            'project_uuid' => $this->project->uuid,
            'sprint_uuid' => $this->card->sprint_uuid,
            'card_id' => $this->card->id,
            'action' => 'delete',
            'table' => 'card_assignees',
            'data' => json_encode([]),
            'description' => __('logs.board.card_assignee_removed_all', [
                'sprint' => $this->card->sprint ? $this->card->sprint->name : 'N/A',
            ]),
            'environment' => app()->environment(),
        ]);

        $this->loadCard();
    }

    public function assignToMe() {
        CardAssignee::firstOrCreate([
            'card_id' => $this->card->id,
            'user_uuid' => auth()->user()->uuid,
        ]);

        Log::create([
            'user_uuid' => auth()->user()->uuid,
            'project_uuid' => $this->project->uuid,
            'sprint_uuid' => $this->card->sprint_uuid,
            'card_id' => $this->card->id,
            'action' => 'create',
            'table' => 'card_assignees',
            'data' => json_encode(['user_uuid' => auth()->user()->uuid]),
            'description' => __('logs.board.card_assignee_added', [
                'user' => auth()->user()->name,
                'sprint' => $this->card->sprint ? $this->card->sprint->name : 'N/A',
            ]),
            'environment' => app()->environment(),
        ]);

        $this->loadCard();
    }

    public function makeACopy() {
        $this->dispatch('closeCardModal');
        $this->dispatch('cardCopyInitiated', ['cardId' => $this->card->id]);
    }

    public function deleteCard() {
        $this->dispatch('closeCardModal');
        $this->dispatch('cardDeleteInitiated', ['cardId' => $this->card->id]);
    }

    public function moveCard() {
        if ($this->sprintOrBacklog === 'sprint') {
            $index = $this->position  === 'top' ? 0 : Card::where('column_id', $this->column)->max('card_index') + 1;
            if ($index != 0) {
                Card::where('column_id', $this->column)
                    ->where('card_index', '>=', $index)
                    ->increment('card_index');
            }

            $updated = $this->card->update([
                'sprint_uuid' => $this->selectedEntityUuid,
                'column_id' => $this->column,
                'card_index' => $index,
            ]);

            if (!$updated) {
                Toaster::error(__('board.toast.card_move_failed'));
                return;
            }

            Log::create([
                'user_uuid' => auth()->user()->uuid,
                'project_uuid' => $this->project->uuid,
                'sprint_uuid' => $this->selectedEntityUuid,
                'card_id' => $this->card->id,
                'action' => 'update',
                'table' => 'cards',
                'data' => json_encode([
                    'sprint_uuid' => $this->selectedEntityUuid,
                    'column_id' => $this->column,
                    'card_index' => $index,
                ]),
                'description' => __('logs.board.card_moved_sprints', [
                    'card' => $this->card->title,
                    'fromSprint' => $this->card->sprint ? $this->card->sprint->name : __('board.backlog'),
                    'toSprint' => $this->selectedProject->sprints->firstWhere('uuid', $this->selectedEntityUuid)->name ?? __('board.backlog'),
                    'fromColumn' => $this->card->column ? $this->card->column->name : __('board.no_column'),
                    'toColumn' => $this->selectedProject->columns->firstWhere('id', $this->column)->name ?? __('board.no_column'),
                ]),
                'environment' => app()->environment(),
            ]);
        } else {
            $index = $this->position === 'top' ? 0 : BacklogCard::where('backlog_uuid', $this->selectedEntityUuid)->max('card_index') + 1;
            if ($index != 0) {
                BacklogCard::where('backlog_uuid', $this->selectedEntityUuid)
                    ->where('card_index', '>=', $index)
                    ->increment('card_index');
            }

            DB::beginTransaction();

            try {
                $backlogCard = BacklogCard::create([
                    'backlog_uuid' => $this->selectedEntityUuid,
                    'title' => $this->card->title,
                    'description' => $this->card->description,
                    'approval_status' => $this->card->approval_status,
                    'card_index' => $index,
                ]);

                foreach ($this->card->tasks as $task) {
                    $backlogTask = $backlogCard->tasks()->create([
                        'backlog_card_id' => $backlogCard->id,
                        'description' => $task->description,
                        'status' => $task->status,
                        'task_index' => $task->task_index,
                    ]);

                    foreach ($task->assignees as $assignee) {
                        $backlogTask->assignees()->create([
                            'backlog_task_id' => $backlogTask->id,
                            'user_uuid' => $assignee->user_uuid,
                        ]);
                    }
                }

                foreach ($this->card->assignees as $assignee) {
                    $backlogCard->assignees()->create([
                        'backlog_card_id' => $backlogCard->id,
                        'user_uuid' => $assignee->user_uuid,
                    ]);
                }

                Log::create([
                    'user_uuid' => auth()->user()->uuid,
                    'project_uuid' => $this->project->uuid,
                    'backlog_uuid' => $this->selectedEntityUuid,
                    'card_id' => $backlogCard->id,
                    'action' => 'create',
                    'table' => 'backlog_cards',
                    'data' => json_encode([
                        'backlog_uuid' => $this->selectedEntityUuid,
                        'title' => $backlogCard->title,
                        'description' => $backlogCard->description,
                        'approval_status' => $backlogCard->approval_status,
                        'card_index' => $index,
                    ]),
                    'description' => __('logs.board.card_moved_backlog', [
                        'card' => $this->card->title,
                        'fromSprint' => $this->card->sprint ? $this->card->sprint->name : __('board.backlog'),
                        'toBacklog' => $this->selectedProject->backlogs->firstWhere('uuid', $this->selectedEntityUuid)->name ?? __('board.backlog'),
                        'fromColumn' => $this->card->column ? $this->card->column->name : __('board.no_column'),
                    ]),
                    'environment' => app()->environment(),
                ]);

                $this->card->delete();

                DB::commit();
            } catch (Exception $ex) {
                DB::rollBack();
                Toaster::error(__('board.toast.card_move_failed'));
                logger()->error('Failed to move card to backlog: ' . $ex->getMessage());
                return;
            }
        }

        $this->dispatch('closeCardModal');
        $this->dispatch('refreshBoard');
    }

    public function updateCardDeadline() {
        $this->validate([
            'deadlineInput' => ['nullable', 'date'],
        ]);

        $this->card->update([
            'deadline' => $this->deadlineInput
                ? \Carbon\Carbon::parse($this->deadlineInput)
                : null,
        ]);

        Log::create([
            'user_uuid' => auth()->user()->uuid,
            'project_uuid' => $this->project->uuid,
            'sprint_uuid' => $this->card->sprint_uuid,
            'card_id' => $this->card->id,
            'action' => 'update',
            'table' => 'cards',
            'data' => json_encode(['deadline' => $this->deadlineInput]),
            'description' => $this->deadlineInput
                ? __('logs.board.card_deadline_updated', [
                    'card' => $this->card->title,
                    'deadline' => \Carbon\Carbon::parse($this->deadlineInput)->format('Y-m-d'),
                    'sprint' => $this->card->sprint ? $this->card->sprint->name : 'N/A',
                ])
                : __('logs.board.card_deadline_cleared', [
                    'card' => $this->card->title,
                    'sprint' => $this->card->sprint ? $this->card->sprint->name : 'N/A',
                ]),
            'environment' => app()->environment(),
        ]);

        $this->loadCard();
    }

    public function render()
    {
        return view('livewire.components.board.modal');
    }
}
