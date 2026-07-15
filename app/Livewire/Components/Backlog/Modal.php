<?php

namespace App\Livewire\Components\Backlog;

use App\Helpers\CheckProjectPermissions;
use App\Models\BacklogCard;
use App\Models\BacklogCardAssignee;
use App\Models\BacklogTask;
use App\Models\Card;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\On;
use Livewire\Component;
use Masmerise\Toaster\Toaster;
use Throwable;

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

    public function addTaskToColumn() {
        if (empty($this->taskName)) {
            Toaster::error(__('board.toast.task_name_required'));
            return;
        }

        $maxIndex = BacklogTask::where('backlog_card_id', $this->card->id)
            ->max('task_index');

        BacklogTask::create([
            'backlog_card_id' => $this->card->id,
            'description' => $this->taskName,
            'task_index' => $maxIndex !== null ? $maxIndex + 1 : 0,
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
        // $this->dispatch('cardRefreshed', ['cardId' => $this->card->id]);
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
        foreach ($groups as $group) {
            $status = $group['value'];

            foreach ($group['items'] as $item) {
                BacklogTask::where('id', $item['value'])->update([
                    'status' => $status,
                    'task_index' => $item['order'],
                ]);
            }
        }

        $this->loadTasks();
        $this->dispatch('$refresh');
        $this->dispatch('refreshBacklog');
    }

    public function updateApprovalStatus($status) {
        $this->card->approval_status = $status;
        $this->card->save();

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

        $this->loadCard();
    }

    public function clearAssignees() {
        BacklogCardAssignee::where('backlog_card_id', $this->card->id)->delete();

        $this->loadCard();
    }

    public function assignToMe() {
        BacklogCardAssignee::firstOrCreate([
            'backlog_card_id' => $this->card->id,
            'user_uuid' => auth()->user()->uuid,
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

    private function clearTaskState(): void {
        foreach ($this->columns as &$column) {
            $column['cards'] = [];
        }

        unset($column);
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
