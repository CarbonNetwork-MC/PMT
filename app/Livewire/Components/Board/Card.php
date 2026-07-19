<?php

namespace App\Livewire\Components\Board;

use App\Helpers\CheckProjectPermissions;
use App\Models\BacklogCard;
use App\Models\BacklogCardAssignee;
use App\Models\BacklogTask;
use App\Models\BacklogTaskAssignee;
use App\Models\Card as CardModel;
use App\Models\CardAssignee;
use App\Models\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\On;
use Livewire\Component;
use Masmerise\Toaster\Toaster;

class Card extends Component
{
    public $cardId;
    public $card;
    public $users;

    public $search = '';
    public $filteredUsers = [];
    public $deadlineInput;

    public $isProjectAdminOrOwner = false;

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
        $this->deadlineInput = $card->deadline ? \Carbon\Carbon::parse($card->deadline)->format('Y-m-d\TH:i') : null;

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
        $this->deadlineInput = $this->card->deadline ? \Carbon\Carbon::parse($this->card->deadline)->format('Y-m-d\TH:i') : null;
    }

    public function selectCard() {
        $this->dispatch('cardSelected', ['cardId' => $this->cardId]);
    }

    #[On('cardRefreshed')]
    public function handleCardRefreshed($data) {
        if ($data['cardId'] == $this->cardId) {
            $this->loadCard();
        }
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

        Log::create([
            'user_uuid' => auth()->user()->uuid,
            'project_uuid' => $this->selectedProject->uuid,
            'sprint_uuid' => $this->sprintOrBacklog === 'sprint' ? $this->selectedEntityUuid : null,
            'card_id' => $this->cardId,
            'action' => $isChecked ? 'create' : 'delete',
            'table' => 'card_assignees',
            'data' => json_encode(['user_uuid' => $userUuid]),
            'description' => $isChecked
                ? __('logs.board.card_assignee_added', [
                    'user' => optional($this->users->firstWhere('uuid', $userUuid))->name,
                    'card' => $this->card->title,
                    'sprint' => $this->card->sprint ? $this->card->sprint->name : 'N/A',
                ])
                : __('logs.board.card_assignee_removed', [
                    'user' => optional($this->users->firstWhere('uuid', $userUuid))->name,
                    'card' => $this->card->title,
                    'sprint' => $this->card->sprint ? $this->card->sprint->name : 'N/A',
                ]),
            'environment' => app()->environment(),
        ]);

        $this->loadCard();
    }

    public function clearAssignees() {
        CardAssignee::where('card_id', $this->cardId)->delete();
        $this->loadCard();

        Log::create([
            'user_uuid' => auth()->user()->uuid,
            'project_uuid' => $this->selectedProject->uuid,
            'sprint_uuid' => $this->sprintOrBacklog === 'sprint' ? $this->selectedEntityUuid : null,
            'card_id' => $this->cardId,
            'action' => 'delete',
            'table' => 'card_assignees',
            'data' => json_encode(['cleared_all_assignees' => true]),
            'description' => __('logs.board.card_assignee_removed_all', [
                'card' => $this->card->title,
                'sprint' => $this->card->sprint ? $this->card->sprint->name : 'N/A',
            ]),
            'environment' => app()->environment(),
        ]);
    }

    public function assignToMe() {
        CardAssignee::firstOrCreate([
            'card_id' => $this->cardId,
            'user_uuid' => auth()->user()->uuid,
        ]);

        Log::create([
            'user_uuid' => auth()->user()->uuid,
            'project_uuid' => $this->selectedProject->uuid,
            'sprint_uuid' => $this->sprintOrBacklog === 'sprint' ? $this->selectedEntityUuid : null,
            'card_id' => $this->cardId,
            'action' => 'create',
            'table' => 'card_assignees',
            'data' => json_encode(['user_uuid' => auth()->user()->uuid]),
            'description' => __('logs.board.card_assignee_added', [
                'user' => auth()->user()->name,
                'card' => $this->card->title,
                'sprint' => $this->card->sprint ? $this->card->sprint->name : 'N/A',
            ]),
            'environment' => app()->environment(),
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
            if ($index != 0) {
                CardModel::where('column_id', $this->column)
                    ->where('card_index', '>=', $index)
                    ->increment('card_index');
            }

            $updated = $card->update([
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
                'project_uuid' => $this->selectedProject->uuid,
                'sprint_uuid' => $this->selectedEntityUuid,
                'card_id' => $this->cardId,
                'action' => 'update',
                'table' => 'cards',
                'data' => json_encode([
                    'new_sprint_uuid' => $this->selectedEntityUuid,
                    'new_column_id' => $this->column,
                    'new_card_index' => $index,
                ]),
                'description' => __('logs.board.card_moved_sprints', [
                    'card' => $card->title,
                    'fromSprint' => optional($card->sprint)->name,
                    'toSprint' => optional($this->selectedProject->sprints()->find($this->selectedEntityUuid))->name,
                    'fromColumn' => optional($card->column)->name,
                    'toColumn' => optional($this->selectedProject->columns()->find($this->column))->name,
                ]),
                'environment' => app()->environment(),
            ]);
        } else {
            $index = $this->position === 'top' ? 0 : BacklogCard::where('backlog_uuid', $this->selectedEntityUuid)->max('card_index') + 1;
            BacklogCard::where('backlog_uuid', $this->selectedEntityUuid)
                ->where('card_index', '>=', $index)
                ->increment('card_index');

            DB::beginTransaction();

            try {
                $backlogCard = BacklogCard::create([
                    'backlog_uuid' => $this->selectedEntityUuid,
                    'title' => $card->title,
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

                Log::create([
                    'user_uuid' => auth()->user()->uuid,
                    'project_uuid' => $this->selectedProject->uuid,
                    'backlog_uuid' => $this->selectedEntityUuid,
                    'card_id' => $this->cardId,
                    'action' => 'create',
                    'table' => 'backlog_cards',
                    'data' => json_encode([
                        'new_backlog_card_id' => $backlogCard->id,
                        'new_backlog_uuid' => $this->selectedEntityUuid,
                        'new_card_index' => $index,
                    ]),
                    'description' => __('logs.board.card_moved_backlog', [
                        'card' => $card->title,
                        'fromSprint' => optional($card->sprint)->name,
                        'toBacklog' => optional($this->selectedProject->backlogs()->find($this->selectedEntityUuid))->name,
                        'fromColumn' => optional($card->column)->name,
                    ]),
                    'environment' => app()->environment(),
                ]);

                $card->delete();

                DB::commit();
            } catch (\Exception $e) {
                DB::rollBack();
                Toaster::error(__('board.toast.card_move_failed'));
                logger()->error('Failed to move card to backlog: ' . $e->getMessage());
                return;
            }
        }

        $this->dispatch('refreshBoard');
    }

    public function makeACopy() {
        $this->dispatch('cardCopyInitiated', ['cardId' => $this->cardId]);
    }

    public function updateCardDeadline() {
        $this->validate([
            'deadlineInput' => ['nullable', 'date'],
        ]);

        $card = CardModel::find($this->cardId);
        $card->update([
            'deadline' => $this->deadlineInput
                ? \Carbon\Carbon::parse($this->deadlineInput)
                : null,
        ]);

        Log::create([
            'user_uuid' => auth()->user()->uuid,
            'project_uuid' => $this->selectedProject->uuid,
            'sprint_uuid' => $this->sprintOrBacklog === 'sprint' ? $this->selectedEntityUuid : null,
            'card_id' => $this->cardId,
            'action' => 'update',
            'table' => 'cards',
            'data' => json_encode(['new_deadline' => $this->deadlineInput]),
            'description' => $this->deadlineInput
                ? __('logs.board.card_deadline_updated', [
                    'card' => $card->title, 
                    'deadline' => $this->deadlineInput,
                    'sprint' => $this->card->sprint ? $this->card->sprint->name : 'N/A',
                ])
                : __('logs.board.card_deadline_cleared', [
                    'card' => $card->title,
                    'sprint' => $this->card->sprint ? $this->card->sprint->name : 'N/A',
                ]),
            'environment' => app()->environment(),
        ]);

        $this->loadCard();
        $this->dispatch('refreshBoard');
    }

    public function render()
    {
        return view('livewire.components.board.card');
    }
}