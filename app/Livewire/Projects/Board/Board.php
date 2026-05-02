<?php

namespace App\Livewire\Projects\Board;

use App\Models\Card;
use App\Models\CardAssignee;
use App\Models\Project;
use App\Models\Sprint;
use App\Models\Task;
use App\Models\TaskAssignee;
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

    public function updateCardOrder($groups) {
        foreach ($groups as $group) {
            $columnId = $group['value'];

            foreach ($group['items'] as $item) {
                Card::where('id', $item['value'])->update([
                    'column_id' => $columnId,
                    'card_index' => $item['order'],
                ]);
            }
        }

        return redirect()->route('projects.board.render', ['uuid' => $this->project->uuid, 'sprintUuid' => $this->sprint->uuid])->success(__('board.toast.card_moved'));
    }

    #[On('cardSelected')]
    public function handleCardSelected($cardId) {
        // dd("Card selected: " . $cardId['cardId']);
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

        return redirect()->route('projects.board.render', ['uuid' => $this->project->uuid, 'sprintUuid' => $this->sprint->uuid])->success(__('board.toast.card_deleted'));
    }

    #[On('cardMoveInitiated')]
    public function handleCardMove() {
        return redirect()->route('projects.board.render', ['uuid' => $this->project->uuid, 'sprintUuid' => $this->sprint->uuid])->success(__('board.toast.card_moved'));
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

        return redirect()->route('projects.board.render', ['uuid' => $this->project->uuid, 'sprintUuid' => $this->sprint->uuid])->success(__('board.toast.card_copied'));
    }

    public function render()
    {
        return view('livewire.projects.board.board');
    }
}
