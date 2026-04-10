<?php

namespace App\Livewire\Components\Board;

use App\Helpers\CheckProjectPermissions;
use App\Models\BacklogCard;
use App\Models\BacklogCardAssignee;
use App\Models\BacklogTask;
use App\Models\BacklogTaskAssignee;
use App\Models\Card as CardModel;
use App\Models\CardAssignee;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Masmerise\Toaster\Toaster;

class Card extends Component
{
    public $cardId;
    public $card;
    public $users;

    public $search = '';
    public $filteredUsers = [];

    public $isProjectAdminOrOwner = false;

    public $showActions = false;
    public $showMoveOptions = false;

    // Card Move Properties
    public $projects;
    public $entities;

    public $selectedProject;
    public $selectedProjectUuid;

    public $selectedEntityUuid;

    public $sprintOrBacklog = 'sprint';
    public $column;
    public $position = 'top';

    public function mount($card, $users) {
        $this->cardId = $card->id;
        $this->users = $users;
        $this->filteredUsers = $users;

        $this->isProjectAdminOrOwner = CheckProjectPermissions::isProjectAdminOrOwner(Auth::user(), $card->column->project);

        $this->loadCard();

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

    public function loadCard() {
        $this->card = CardModel::with('assignees.user')->find($this->cardId);
    }

    public function selectCard() {
        $this->dispatch('cardSelected', ['cardId' => $this->cardId]);
    }

    public function toggleAssignee($userUuid, $isChecked) {
        if ($isChecked) {
            CardAssignee::firstOrCreate([
                'card_id' => $this->cardId,
                'user_uuid' => $userUuid,
            ]);
        } else {
            CardAssignee::where('card_id', $this->cardId)
                ->where('user_uuid', $userUuid)
                ->delete();
        }

        $this->loadCard();
    }

    public function clearAssignees() {
        CardAssignee::where('card_id', $this->cardId)->delete();
        $this->loadCard();
    }

    public function assignToMe() {
        CardAssignee::firstOrCreate([
            'card_id' => $this->cardId,
            'user_uuid' => auth()->user()->uuid,
        ]);

        $this->loadCard();
    }

    public function deleteCard() {
        $this->dispatch('cardDeleteInitiated', ['cardId' => $this->cardId]);
    }
    
    public function moveCard() {
        $card = CardModel::where('id', $this->cardId)->first();

        if ($this->sprintOrBacklog === 'sprint') {
            $index = $this->position === 'top' ? 0 : CardModel::where('column_id', $this->column)->max('card_index') + 1;
            CardModel::where('column_id', $this->column)
                ->where('card_index', '>=', $index)
                ->increment('card_index');

            $updated = $card->update([
                'sprint_uuid' => $this->selectedEntityUuid,
                'column_id' => $this->column,
                'card_index' => $index,
            ]);

            if (!$updated) {
                Toaster::error(__('board.toast.card_move_failed'));
                return;
            }
        } else {
            $index = $this->position === 'top' ? 0 : BacklogCard::where('backlog_uuid', $this->selectedEntityUuid)->max('card_index') + 1;
            BacklogCard::where('backlog_uuid', $this->selectedEntityUuid)
                ->where('card_index', '>=', $index)
                ->increment('card_index');

            DB::beginTransaction();

            try {
                $backlogCard = BacklogCard::create([
                    'backlog_uuid' => $this->selectedEntityUuid,
                    'name' => $card->name,
                    'description' => $card->description,
                    'approval_status' => $card->approval_status,
                    'card_index' => $index,
                ]);

                foreach ($card->tasks as $task) {
                    $backlogTask = BacklogTask::create([
                        'backlog_card_id' => $backlogCard->id,
                        'description' => $task->description,
                        'status' => $task->status,
                        'task_index' => $task->task_index,
                    ]);

                    foreach ($task->assignees as $assignee) {
                        BacklogTaskAssignee::create([
                            'backlog_task_id' => $backlogTask->id,
                            'user_uuid' => $assignee->user_uuid,
                        ]);
                    }
                }

                foreach ($card->assignees as $assignee) {
                    BacklogCardAssignee::create([
                        'backlog_card_id' => $backlogCard->id,
                        'user_uuid' => $assignee->user_uuid,
                    ]);
                }

                $card->delete();

                DB::commit();
            } catch (\Exception $e) {
                DB::rollBack();
                Toaster::error(__('board.toast.card_move_failed'));
                logger()->error('Failed to move card to backlog: ' . $e->getMessage());
                return;
            }
        }

        $this->dispatch('cardMoveInitiated');
    }

    public function makeACopy() {
        $this->dispatch('cardCopyInitiated', ['cardId' => $this->cardId]);
    }

    public function render()
    {
        return view('livewire.components.board.card');
    }
}