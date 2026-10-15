<?php

namespace App\Livewire\Projects\Board;

use App\Helpers\CheckIfUserIsAdmin;
use App\Models\Card;
use App\Models\CardAssignee;
use App\Models\Log;
use App\Models\Project;
use App\Models\Sprint;
use App\Models\Task;
use App\Models\TaskAssignee;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;
use Masmerise\Toaster\Toaster;

class Board extends Component
{
    public $project;
    public $sprint;

    public $columns;
    public $users;

    public $daysLeft;

    public $selectedCard = null;

    public $cardToModify = null;
    public $showDeleteCardModal = false;

    public $taskToModify = null;
    public $showDeleteTaskModal = false;

    public $refreshKey = 0;

    public function mount($uuid, $sprintUuid) {
        $this->project = Project::where('uuid', $uuid)->firstOrFail();
        $this->sprint = Sprint::where('uuid', $sprintUuid)->firstOrFail();

        $this->columns = $this->project->columns()
            ->with('cards.assignees.user')
            ->orderBy('position')
            ->get();

        // Load all users assigned to the project + the owner
        $this->users = $this->project->members()->with('user')->get()->pluck('user');
        $this->users->push($this->project->owner);
        $this->users = $this->users->unique('uuid');

        $this->daysLeft = now()->startOfDay()->diffInDays($this->sprint->end_date->startOfDay(), false);
    }

    public function reloadBoard() {
        $this->project = $this->project->refresh();

        $this->columns = $this->project->columns()
            ->with('cards.assignees.user')
            ->orderBy('position')
            ->get();

        $this->refreshKey = now()->timestamp;
    }

    #[On('refreshBoard')]
    public function handleRefreshBoard() {
        $this->reloadBoard();
    }

    public function updateCardOrder($groups) {
        $oldOrder = $this->columns->mapWithKeys(function ($column) {
            return [$column->id => $column->cards->pluck('id')->toArray()];
        })->toArray();
        $newOrder = collect($groups)
            ->mapWithKeys(function ($group) {
                return [
                    (int) $group['value'] => collect($group['items'])
                        ->pluck('value')
                        ->map(fn ($id) => (int) $id)
                        ->toArray(),
                ];
            })
            ->toArray();

        // Determine which card was moved and from which column to which column
        $movedCard = null;

        $oldLocations = [];
        $newLocations = [];

        foreach ($oldOrder as $columnId => $cardIds) {
            foreach ($cardIds as $cardId) {
                $oldLocations[$cardId] = $columnId;
            }
        }

        foreach ($newOrder as $columnId => $cardIds) {
            foreach ($cardIds as $cardId) {
                $newLocations[$cardId] = $columnId;
            }
        }

        $columns = $this->columns->keyBy('id');

        foreach ($oldLocations as $cardId => $oldColumnId) {
            $newColumnId = $newLocations[$cardId] ?? null;

            if ($newColumnId && $newColumnId !== $oldColumnId) {
                $oldColumnName = $columns[$oldColumnId]->name;
                $newColumnName = $columns[$newColumnId]->name;

                $movedCard = [
                    'card_id' => $cardId,
                    'from' => $oldColumnName,
                    'to' => $newColumnName,
                ];
            }
        }

        foreach ($groups as $group) {
            $columnId = $group['value'];

            foreach ($group['items'] as $item) {
                Card::where('id', $item['value'])->update([
                    'column_id' => $columnId,
                    'card_index' => $item['order'],
                ]);
            }
        }

        if ($movedCard) {
            Log::create([
                'user_uuid' => auth()->user()->uuid,
                'project_uuid' => $this->project->uuid,
                'sprint_uuid' => $this->sprint->uuid,
                'action' => 'update',
                'table' => 'cards',
                'data' => json_encode(['old_order' => $oldOrder, 'new_order' => $newOrder]),
                'description' => __('logs.board.card_moved_same_board', [
                    'card' => $movedCard['card_id'], 
                    'fromColumn' => $movedCard['from'], 
                    'toColumn' => $movedCard['to'],
                    'sprint' => $this->sprint->name,
                ]),
                'environment' => app()->environment(),
                'by_admin' => CheckIfUserIsAdmin::check(Auth::user(), $this->project->uuid)
            ]);
        }

        $this->reloadBoard();
    }

    #[On('cardSelected')]
    public function handleCardSelected($cardId) {
        $this->selectedCard = Card::where('id', $cardId['cardId'])->first();
    }

    #[On('closeCardModal')]
    public function handleCloseCardModal() {
        $this->selectedCard = null;
    }

    #[On('cardDeleteInitiated')]
    public function handleCardDelete($cardId) {
        $this->cardToModify = Card::where('id', $cardId)->first();
        $this->showDeleteCardModal = true;
    }

    public function confirmDeleteCard() {
        if (!$this->cardToModify) {
            Toaster::error(__('board.toast.card_not_found'));
            $this->showDeleteCardModal = false;
            return;
        }

        $this->cardToModify->delete();
        $this->reset(['cardToModify', 'showDeleteCardModal']);

        Log::create([
            'user_uuid' => auth()->user()->uuid,
            'project_uuid' => $this->project->uuid,
            'sprint_uuid' => $this->sprint->uuid,
            'action' => 'delete',
            'table' => 'cards',
            'data' => json_encode($this->cardToModify->toArray()),
            'description' => __('logs.board.card_deleted', [
                'card' => $this->cardToModify->title,
                'sprint' => $this->sprint->name,
            ]),
            'environment' => app()->environment(),
            'by_admin' => CheckIfUserIsAdmin::check(Auth::user(), $this->project->uuid)
        ]);

        $this->reloadBoard();
    }

    #[On('cardMoveInitiated')]
    public function handleCardMove() {
        $this->reloadBoard();
    }

    #[On('cardCopyInitiated')]
    public function handleCardCopy($cardId) {
        $card = Card::where('id', $cardId)->firstOrFail();
        if (!$card) {
            Toaster::error(__('board.toast.card_not_found'));
            return;
        }

        $newCardIndex = Card::where('column_id', $card->column_id)
            ->where('sprint_uuid', $card->sprint_uuid)
            ->max('card_index') + 1;

        $newCard = Card::create([
            'sprint_uuid' => $card->sprint_uuid,
            'title' => $card->title . ' (Copy)',
            'description' => $card->description,
            'column_id' => $card->column_id,
            'approval_status' => $card->approval_status,
            'deadline' => $card->deadline,
            'card_index' => $newCardIndex,
        ]);

        // Copy assignees
        foreach ($card->assignees as $assignee) {
            CardAssignee::create([
                'card_id' => $newCard->id,
                'user_uuid' => $assignee->user_uuid,
            ]);
        }

        // Copy tasks
        $newTaskIndex = $card->tasks()->max('task_index') + 1;

        foreach ($card->tasks as $task) {
            $newTask = Task::create([
                'card_id' => $newCard->id,
                'description' => $task->description,
                'status' => $task->status,
                'task_index' => $newTaskIndex,
                'deadline' => $task->deadline,
                'estimated_time' => $task->estimated_time,
                'actual_time' => $task->actual_time,
            ]);

            // Copy task assignees
            foreach ($task->assignees as $assignee) {
                TaskAssignee::create([
                    'task_id' => $newTask->id,
                    'user_uuid' => $assignee->user_uuid,
                ]);
            }
        }

        Log::create([
            'user_uuid' => auth()->user()->uuid,
            'project_uuid' => $this->project->uuid,
            'sprint_uuid' => $this->sprint->uuid,
            'card_id' => $newCard->id,
            'action' => 'create',
            'table' => 'cards',
            'data' => json_encode($newCard->toArray()),
            'description' => __('logs.board.card_copied', [
                'card' => $card->title,
                'sprint' => $this->sprint->name,
            ]),
            'environment' => app()->environment(),
            'by_admin' => CheckIfUserIsAdmin::check(Auth::user(), $this->project->uuid)
        ]);

        $this->reloadBoard();
    }

    #[On('taskConvertToCardInitiated')]
    public function handleTaskConvertToCard($taskId) {
        $task = Task::where('id', $taskId)->firstOrFail();
        if (!$task) {
            Toaster::error(__('board.toast.card_not_found'));
            return;
        }

        $card = Card::create([
            'sprint_uuid' => $task->card->sprint_uuid,
            'title' => $task->description,
            'column_id' => $task->card->column_id,
            'approval_status' => 'None',
            'deadline' => $task->deadline,
            'card_index' => Card::where('column_id', $task->card->column_id)
                ->where('sprint_uuid', $task->card->sprint_uuid)
                ->max('card_index') + 1,
        ]);

        foreach ($task->assignees as $assignee) {
            CardAssignee::create([
                'card_id' => $card->id,
                'user_uuid' => $assignee->user_uuid,
            ]);
        }

        Log::create([
            'user_uuid' => auth()->user()->uuid,
            'project_uuid' => $this->project->uuid,
            'sprint_uuid' => $this->sprint->uuid,
            'card_id' => $card->id,
            'task_id' => $task->id,
            'action' => 'create',
            'table' => 'cards',
            'data' => json_encode($card->toArray()),
            'description' => __('logs.board.card_created_from_task', [
                'task' => $task->description,
                'card' => $card->title,
                'sprint' => $this->sprint->name,
            ]),
            'environment' => app()->environment(),
            'by_admin' => CheckIfUserIsAdmin::check(Auth::user(), $this->project->uuid)
        ]);

        $task->delete();

        $this->reloadBoard();
    }

    #[On('taskDeleteInitiated')]
    public function handleTaskDelete($taskId) {
        $this->taskToModify = Task::where('id', $taskId)->firstOrFail();
        $this->showDeleteTaskModal = true;
    }

    public function confirmDeleteTask() {
        if (!$this->taskToModify) {
            Toaster::error(__('board.toast.task_not_found'));
            $this->showDeleteTaskModal = false;
            return;
        }

        Log::create([
            'user_uuid' => auth()->user()->uuid,
            'project_uuid' => $this->project->uuid,
            'sprint_uuid' => $this->sprint->uuid,
            'task_id' => $this->taskToModify->id,
            'action' => 'delete',
            'table' => 'tasks',
            'data' => json_encode($this->taskToModify->toArray()),
            'description' => __('logs.board.task_deleted', [
                'task' => $this->taskToModify->description,
                'sprint' => $this->sprint->name,
            ]),
            'environment' => app()->environment(),
            'by_admin' => CheckIfUserIsAdmin::check(Auth::user(), $this->project->uuid)
        ]);

        $this->taskToModify->delete();
        $this->reset(['taskToModify', 'showDeleteTaskModal']);

        $this->reloadBoard();
        $this->dispatch('refreshModal');
    }

    public function render()
    {
        return view('livewire.projects.board.board');
    }
}
