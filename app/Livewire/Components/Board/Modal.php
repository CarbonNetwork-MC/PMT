<?php

namespace App\Livewire\Components\Board;

use App\Helpers\CheckProjectPermissions;
use App\Models\BacklogCard;
use App\Models\Card;
use App\Models\CardAssignee;
use App\Models\Task;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Masmerise\Toaster\Toaster;

class Modal extends Component
{
    public $card;
    public $sprint;
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

    public function mount($card, $sprint, $users) {
        $this->card = $card->load('assignees.user');
        $this->sprint = $sprint;
        $this->users = $users;
        $this->filteredUsers = $users;

        $this->cardTitle = $card->title;
        $this->cardDescription = $card->description;

        $tasks = $card->tasks()->with('assignees.user')->get();
        foreach ($this->columns as &$column) {
            $column['cards'] = $tasks->where('status', $column['type'])->values();
        }

        $this->isProjectAdminOrOwner = CheckProjectPermissions::isProjectAdminOrOwner(Auth::user(), $card->column->project);

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

        $this->card->title = $title;
        $this->card->save();

        $this->loadCard();
    }

    public function saveDescription() {
        $description = $this->cardDescription;
        if (empty($description)) $description = null;

        $this->card->description = $description;
        $this->card->save();

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
        foreach ($groups as $group) {
            $status = $group['value'];

            foreach ($group['items'] as $item) {
                Task::where('id', $item['value'])->update([
                    'status' => $status,
                    'task_index' => $item['order'],
                ]);
            }
        }

        $this->loadTasks();
        $this->dispatch('$refresh');
        $this->dispatch('refreshBoard');
    }

    public function updateApprovalStatus($status) {
        $this->card->approval_status = $status;
        $this->card->save();

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

        $this->loadCard();
    }

    public function clearAssignees() {
        CardAssignee::where('card_id', $this->card->id)->delete();

        $this->loadCard();
    }

    public function assignToMe() {
        CardAssignee::firstOrCreate([
            'card_id' => $this->card->id,
            'user_uuid' => auth()->user()->uuid,
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

        $this->loadCard();
    }

    public function render()
    {
        return view('livewire.components.board.modal');
    }
}
