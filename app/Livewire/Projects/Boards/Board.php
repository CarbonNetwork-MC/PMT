<?php

namespace App\Livewire\Projects\Boards;

use App\Models\Log;
use App\Models\Card;
use App\Models\Task;
use App\Models\Sprint;
use App\Models\Project;
use Livewire\Component;
use App\Models\Backlog;
use App\Models\CardAssignee;
use App\Models\TaskAssignee;
use App\Models\ProjectMember;

class Board extends Component
{
    public $user;
    public $uuid;
    public $projectId;

    public $sprint;
    public $projects;
    public $backlogs;
    public $sprints;

    public $selectedCard;

    public $columns = [
        'todo' => [
            'internal_name' => 'todo',
            'name' => 'To Do',
            'text_color' => 'rose-500',
            'bg_color' => 'rose-500'
        ], 
        'doing' => [
            'internal_name' => 'doing',
            'name' => 'Doing',
            'text_color' => 'amber-500',
            'bg_color' => 'amber-500'
        ], 
        'testing' => [
            'internal_name' => 'testing',
            'name' => 'Testing',
            'text_color' => 'yellow-300',
            'bg_color' => 'yellow-400'
        ], 
        'done' => [
            'internal_name' => 'done',
            'name' => 'Done',
            'text_color' => 'green-500',
            'bg_color' => 'green-500'
        ], 
        'released' => [
            'internal_name' => 'released',
            'name' => 'Released',
            'text_color' => 'sky-500',
            'bg_color' => 'sky-500'
        ]
    ];

    public $selectedCardModal = false;

    public $isCreatingCard = false;
    public $isEditingCardName = false;
    public $createdCardColumn;
    public $editingCardId;

    public $name, $description;
    public $selectedProject, $backlogOrSprint = 'sprint', $backlogOrSprintName, $sprintColumn = 'todo', $position = 'top';

    public function mount($uuid)
    {
        $this->user = auth()->user();
        $this->uuid = $uuid;

        // Check if the uuid is a sprint or a project
        $sprint = Sprint::where('uuid', $this->uuid)->with(['cards.assignees.user', 'cards.tasks.assignees.user'])->first();

        if ($sprint) {
            $this->sprint = $sprint;
            
            $this->projectId = $this->sprint->project_id;
        } else {
            $project = Project::find($this->uuid);
            if ($project) {
                // Set the selected project
                session()->put('selected_project', $this->uuid);

                $this->projectId = $this->uuid;
            }
        }

        // Set the columns as objects
        $this->columns = collect($this->columns)->map(function ($column) {
            return (object) $column;
        });

        // Get all projects where the user is an admin or project owner
        $projectMembersOfAllProjects = ProjectMember::where('user_id', auth()->user()->uuid)->get();
        foreach ($projectMembersOfAllProjects as $projectMember) {
            if ($projectMember->role_id === 2 || $projectMember->role_id === 3) {
                $this->projects[] = Project::where('uuid', $projectMember->project_id)->first();
            }
        }

        // Get all backlogs
        $this->backlogs = Backlog::where('project_id', $this->projectId)->with(['cards.assignees.user', 'cards.tasks.assignees.user'])->get();

        // Get all sprints
        $this->sprints = Sprint::where('project_id', $this->projectId)->with(['cards.assignees.user', 'cards.tasks.assignees.user'])->orderBy('start_date')->get();

        // Set backlogOrSprintName to the selected sprint and set the selectedProject
        $this->backlogOrSprintName = $this->sprint->uuid;
        $this->selectedProject = $this->projectId;
    }

    // Functions
    // Edit Card, Delete Card, Change Card name, Change Card Description, Change Card Assignees, Change Card Approval Status, AssignCardToMe,
    // Move Card, Copy Card, Create Task + cancellation, Change Task Assignees, Update Task Order, AssignTaskToMe, Move Task, Copy Task, 
    // Convert Task to Card, Delete Task, Change Task Name, 

    public function updated($key, $value) {
        if ($key === 'backlogOrSprint') {
            if ($value === 'backlog') {
                // Get all backlogs for the selected project
                $this->backlogs = Backlog::where('project_id', $this->selectedProject)->with(['cards.assignees.user', 'cards.tasks.assignees.user'])->get();
            } elseif ($value === 'sprint') {
                // Get all sprints for the selected project
                $this->sprints = Sprint::where('project_id', $this->selectedProject)->with(['cards.assignees.user', 'cards.tasks.assignees.user'])->orderBy('start_date')->get();
            }
        } elseif ($key === 'selectedProject') {
            // Get all backlogs for the selected project
            $this->backlogs = Backlog::where('project_id', $this->selectedProject)->with(['cards.assignees.user', 'cards.tasks.assignees.user'])->get();

            // Get all sprints for the selected project
            $this->sprints = Sprint::where('project_id', $this->selectedProject)->with(['cards.assignees.user', 'cards.tasks.assignees.user'])->orderBy('start_date')->get();
        }
    }

    /**
     * Set the isCreatingCard variable to true
     * 
     * @param string $column
     * 
     * @return void
     */
    public function createCard($column) {
        $this->isCreatingCard = true;
        $this->createdCardColumn = $column;
    }

    /**
     * Cancel the card creation
     * 
     * @return void
     */
    public function cancelCardCreation() {
        $this->isCreatingCard = false;
        $this->createdCardColumn = null;
    }

    /**
     * Store the card
     * 
     * @param string $column
     * 
     * @return void
     */
    public function storeCard($column) {
        // Validate the data
        $data = $this->validate([
            'name' => 'required|string'
        ]);

        $data['sprint_id'] = $this->sprint->uuid;
        $data['status'] = $column;
        $data['card_index'] = 0;
        $data['approval_status'] = 'None';

        // Create the card
        $card = Card::create($data);

        // Check if there is already a card with index 0, if so, increment the index of all cards in the column
        $cardsInColumn = Card::where('sprint_id', $this->sprint->id)->where('status', $column)->orderBy('card_index')->get();
        $cardWithIndexZero = $cardsInColumn->where('card_index', 0)->first();
        if ($cardWithIndexZero) {
            foreach ($cardsInColumn as $cardInColumn) {
                $cardInColumn->card_index++;
                $cardInColumn->save();
            }
        }

        // Update the sprint
        $this->sprint = Sprint::where('uuid', $this->uuid)->with(['cards.assignees.user', 'cards.tasks.assignees.user'])->first();

        // Create a new log
        Log::create([
            'user_id' => auth()->user()->uuid,
            'project_id' => $this->sprint->project_id,
            'sprint_id' => $this->sprint->uuid,
            'card_id' => $card->id,
            'action' => 'create',
            'data' => json_encode($card),
            'table' => 'cards',
            'description' => 'Card <b>' . $card->name . '</b> created in ' . $column,
            'environment' => config('app.env')
        ]);

        // Reset the variables
        $this->isCreatingCard = false;
        $this->createdCardColumn = null;
        $this->name = null;
    }

    /**
     * Select a Card
     * 
     * @param int $id
     * 
     * @return void
     */
    public function selectCard($id) {
        // Set the selected card
        $this->selectedCard = Card::where('id', $id)->with(['assignees.user', 'tasks.assignees.user'])->first();

        // Set the card variables
        $this->name = $this->selectedCard->name;
        $this->description = $this->selectedCard->description;

        // Open the modal
        $this->selectedCardModal = true;
    }

    /**
     * Set the isEditingCardName variable to true
     * 
     * @return void
     */
    public function startEditingCardName($id) {
        $this->isEditingCardName = true;
        $this->editingCardId = $id;
        $this->name = Card::where('id', $id)->first()->name;
    }

    /**
     * Save the card name
     * 
     * @return void
     */
    public function saveCardName() {
        // Get the card
        $card = Card::where('id', $this->editingCardId)->first();

        // Update the card
        $card->update([
            'name' => $this->cardName
        ]);

        // Update the sprint
        $this->sprint = Sprint::where('uuid', $this->uuid)->with(['cards.assignees.user', 'cards.tasks.assignees.user'])->first();

        // Create a new log
        Log::create([
            'user_id' => auth()->user()->uuid,
            'project_id' => $this->sprint->project_id,
            'sprint_id' => $this->sprint->uuid,
            'card_id' => $card->id,
            'action' => 'update',
            'data' => json_encode($card),
            'table' => 'cards',
            'description' => 'Card <b>' . $card->name . '</b> updated',
            'environment' => config('app.env')
        ]);

        // Reset the variables
        $this->isEditingCardName = false;
        $this->editingCardId = null;
    }

    /** 
     * Update the card order
     * 
     * @param int $cardId
     * @param string $newColumn
     * @param int $newIndex
     * 
     * @return void 
     */
    public function updateCardOrder($cardId, $newColumn, $newIndex) {
        // Get the card
        $card = Card::where('id', $cardId)->first();

        // Get all cards in the new column and sort them by their index
        $cardsInColumn = Card::where('sprint_id', $this->sprint->id)->where('status', $newColumn)->orderBy('card_index')->get();

        // Check if there is a card with the same index, if so, increment the index of all cards in the new column
        $cardWithSameIndex = $cardsInColumn->where('card_index', $newIndex)->first();

        if ($cardWithSameIndex) {
            foreach ($cardsInColumn as $cardInColumn) {
                if ($cardInColumn->card_index >= $newIndex) {
                    $cardInColumn->card_index++;
                    $cardInColumn->save();
                }
            }
        }

        // Update the card with the new column and index
        $card->update([
            'status' => $newColumn,
            'card_index' => $newIndex
        ]);

        // Update the sprint
        $this->sprint = Sprint::where('uuid', $this->uuid)->with(['cards.assignees.user', 'cards.tasks.assignees.user'])->first();

        // Create a new log
        Log::create([
            'user_id' => auth()->user()->uuid,
            'project_id' => $this->sprint->project_id,
            'sprint_id' => $this->sprint->uuid,
            'card_id' => $card->id,
            'action' => 'update',
            'data' => json_encode($card),
            'table' => 'cards',
            'description' => 'Card <b>' . $card->name . '</b> moved to ' . $newColumn,
            'environment' => config('app.env')
        ]);
    }

    /**
     * Render the component
     * 
     * @return \Illuminate\View\View
     */
    public function render()
    {
        return view('livewire.projects.boards.board');
    }
}
