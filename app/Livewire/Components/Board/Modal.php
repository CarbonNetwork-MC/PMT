<?php

namespace App\Livewire\Components\Board;

use App\Helpers\CheckProjectPermissions;
use App\Models\CardAssignee;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Modal extends Component
{
    public $card;
    public $sprint;
    public $users;

    public $cardDescription = '';

    public $search = '';
    public $filteredUsers = [];

    public $showApprovalStatusDropdown = false;
    public $showActions = false;
    public $showMoveOptions = false;

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

    public $sprintOrBacklog = 'sprint';
    public $column;
    public $position = 'top';

    public function mount($card, $sprint, $users) {
        $this->card = $card->load('assignees.user');
        $this->sprint = $sprint;
        $this->users = $users;
        $this->filteredUsers = $users;
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

    public function closeModal() {
        $this->dispatch('closeCardModal');
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

    // TODO: Move Card Method
    public function moveCard() {

    }

    public function render()
    {
        return view('livewire.components.board.modal');
    }
}
