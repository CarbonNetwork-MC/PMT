<?php

namespace App\Livewire\Projects\Boards;

use App\Models\Log;
use App\Models\Card;
use App\Models\Task;
use App\Models\Sprint;
use App\Models\Project;
use Livewire\Component;
use App\Models\Backlog;
use App\Models\BacklogCard;
use App\Models\BacklogTask;
use App\Models\CardAssignee;
use App\Models\TaskAssignee;
use App\Models\ProjectMember;
use App\Models\BacklogCardAssignee;
use App\Models\BacklogTaskAssignee;

class Board extends Component
{
    public $user;
    public $uuid;
    public $projectId;

    public $sprint;
    public $projects;
    public $backlogs;
    public $sprints;
    public $projectMembers;

    public $selectedId;
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

    public $approvalStatuses = [
        'None' => [
            'color_dark' => 'gray-500',
            'color_light' => 'gray-400',
        ],
        'Approved' => [
            'color_dark' => 'green-500',
            'color_light' => 'green-400'
        ],
        'Needs work' => [
            'color_dark' => 'amber-500',
            'color_light' => 'amber-400'
        ],
        'Rejected' => [
            'color_dark' => 'red-500',
            'color_light' => 'red-400'
        ]
    ];

    public $selectedCardModal = false;
    public $deleteCardModal = false;

    public $deleteTaskModal = false;

    public $isCreatingCard = false;
    public $isCreatingTask = false;
    public $isEditingCardName = false;
    public $isEditingCardDescription = false;
    public $isEditingTaskDescription = false;
    public $createdCardColumn;
    public $createdTaskColumn;
    public $editingCardId;
    public $editingTaskId;

    public $name, $description, $taskDescription;
    public $selectedProject, $backlogOrSprint = 'sprint', $backlogOrSprintName, $sprintColumn = 'todo', $position = 'top';

    /**
     * Mount the component
     * 
     * @param string $uuid
     * 
     * @return void
     */
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

        // Set the approval status as objects
        $this->approvalStatuses = collect($this->approvalStatuses)->map(function ($status) {
            return (object) $status;
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

        // Get all project members
        $this->projectMembers = ProjectMember::where('project_id', $this->projectId)->with('user')->get();

        // Set backlogOrSprintName to the selected sprint and set the selectedProject
        if ($sprint) {
            $this->backlogOrSprintName = $this->sprint->uuid;
            $this->selectedProject = $this->projectId;
        }
    }

    public function updated($key, $value) {
        if ($key === 'backlogOrSprint') {
            if ($value === 'backlog') {
                // Get all backlogs for the selected project
                $this->backlogs = Backlog::where('project_id', $this->selectedProject)->with(['cards.assignees.user', 'cards.tasks.assignees.user'])->get();

                // Set the backlogOrSprintName to the first backlog
                $this->backlogOrSprintName = $this->backlogs->first()->uuid;
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
     * Open the delete card modal and set the ID
     * 
     * @param int $id
     * 
     * @return void
     */
    public function deleteCard($id) {
        // Set the selected card ID
        $this->selectedId = $id;

        // Open the modal
        $this->deleteCardModal = true;
    }

    /**
     * Delete the card
     * 
     * @return void
     */
    public function destroyCard() {
        // Get all assignees of the card
        $assignees = CardAssignee::where('card_id', $this->selectedId)->get();
        foreach ($assignees as $assignee) {
            $assignee->delete();
        }

        // Get all tasks for the card and delete them
        $tasks = Task::where('card_id', $this->selectedId)->get();
        foreach ($tasks as $task) {
            // Get all assignees of the task
            $taskAssignees = TaskAssignee::where('task_id', $task->id)->get();
            foreach ($taskAssignees as $taskAssignee) {
                $taskAssignee->delete();
            }

            $task->delete();
        }

        // Get the card and delete it
        $card = Card::where('id', $this->selectedId)->first();
        $card->delete();

        // Update the sprint
        $this->sprint = Sprint::where('uuid', $this->uuid)->with(['cards.assignees.user', 'cards.tasks.assignees.user'])->first();

        // Create a new log
        Log::create([
            'user_id' => auth()->user()->uuid,
            'project_id' => $this->sprint->project_id,
            'sprint_id' => $this->sprint->uuid,
            'card_id' => $this->selectedId,
            'action' => 'delete',
            'data' => json_encode($card),
            'table' => 'cards',
            'description' => 'Card <b>' . $card->name . '</b> deleted',
            'environment' => config('app.env')
        ]);

        // Close the modal
        $this->deleteCardModal = false;

        // Reset the variables
        $this->selectedId = null;
        $this->selectedCard = null;
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
     * Change the status of a card
     * 
     * @param int $id
     * @param string $status
     * 
     * @return void
     */
    public function changeApprovalStatus($id, $status) {
        // Get the card
        $card = Card::where('id', $id)->first();

        // Update the card
        $card->update([
            'approval_status' => $status
        ]);

        // Update the sprint
        $this->sprint = Sprint::where('uuid', $this->uuid)->with(['cards.assignees.user', 'cards.tasks.assignees.user'])->first();

        // Update the selected Card
        $this->selectedCard = Card::where('id', $id)->with(['assignees.user', 'tasks.assignees.user'])->first();

        // Create a new log
        Log::create([
            'user_id' => auth()->user()->uuid,
            'project_id' => $this->sprint->project_id,
            'sprint_id' => $this->sprint->uuid,
            'card_id' => $card->id,
            'action' => 'update',
            'data' => json_encode($card),
            'table' => 'cards',
            'description' => 'Card <b>' . $card->name . '</b> approval status changed to ' . $status,
            'environment' => config('app.env')
        ]);
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
     * Set the isEditingCardDescription variable to true
     * 
     * @return void
     */
    public function startEditingCardDescription($id) {
        $this->isEditingCardDescription = true;
        $this->editingCardId = $id;
        $this->description = Card::where('id', $id)->first()->description;
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
            'name' => $this->name
        ]);

        // Update the sprint
        $this->sprint = Sprint::where('uuid', $this->uuid)->with(['cards.assignees.user', 'cards.tasks.assignees.user'])->first();

        // Update the selected Card
        $this->selectedCard = Card::where('id', $this->editingCardId)->with(['assignees.user', 'tasks.assignees.user'])->first();

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
        $this->name = null;
    }

    /**
     * Save the card description
     * 
     * @return void
     */
    public function saveCardDescription() {
        // Get the card
        $card = Card::where('id', $this->editingCardId)->first();

        // Update the card
        $card->update([
            'description' => $this->description
        ]);

        // Update the sprint
        $this->sprint = Sprint::where('uuid', $this->uuid)->with(['cards.assignees.user', 'cards.tasks.assignees.user'])->first();

        // Update the selected Card
        $this->selectedCard = Card::where('id', $this->editingCardId)->with(['assignees.user', 'tasks.assignees.user'])->first();

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
        $this->isEditingCardDescription = false;
        $this->description = null;
    }

    /**
     * Add an assignee to a card
     * 
     * @param int $id
     * 
     * @return void
     */
    public function addCardAssignee($id) {
        // Get the clicked project member
        $projectMember = ProjectMember::where('id', $id)->first();

        // Create the assignee
        CardAssignee::create([
            'card_id' => $this->selectedCard->id,
            'user_id' => $projectMember->user_id
        ]);

        // Update the selected Card
        $this->selectedCard = Card::where('id', $this->selectedCard->id)->with(['assignees.user', 'tasks.assignees.user'])->first();

        // Update the sprint
        $this->sprint = Sprint::where('uuid', $this->uuid)->with(['cards.assignees.user', 'cards.tasks.assignees.user'])->first();

        // Create a new log
        Log::create([
            'user_id' => auth()->user()->uuid,
            'project_id' => $this->sprint->project_id,
            'sprint_id' => $this->sprint->uuid,
            'card_id' => $this->selectedCard->id,
            'action' => 'create',
            'data' => json_encode($projectMember),
            'table' => 'card_assignees',
            'description' => 'User <b>' . $projectMember->user->name . '</b> was added to card <b>' . $this->selectedCard->name . '</b>',
            'environment' => config('app.env')
        ]);
    }

    /**
     * Remove an assignee from a card
     * 
     * @param int $id
     * 
     * @return void
     */
    public function removeCardAssignee($id) {
        // Get the clicked project member
        $projectMember = ProjectMember::where('id', $id)->first();
        
        // Get the assignee
        $assignee = CardAssignee::where('card_id', $this->selectedCard->id)->where('user_id', $projectMember->user_id)->first();

        // Delete the assignee
        $assignee->delete();

        // Update the selected Card
        $this->selectedCard = Card::where('id', $this->selectedCard->id)->with(['assignees.user', 'tasks.assignees.user'])->first();

        // Update the sprint
        $this->sprint = Sprint::where('uuid', $this->uuid)->with(['cards.assignees.user', 'cards.tasks.assignees.user'])->first();

        // Create a new log
        Log::create([
            'user_id' => auth()->user()->uuid,
            'project_id' => $this->sprint->project_id,
            'sprint_id' => $this->sprint->uuid,
            'card_id' => $this->selectedCard->id,
            'action' => 'delete',
            'data' => json_encode($projectMember),
            'table' => 'card_assignees',
            'description' => 'User <b>' . $projectMember->user->name . '</b> was removed from card <b>' . $this->selectedCard->name . '</b>',
            'environment' => config('app.env')
        ]);
    }

    /**
     * Assign the card to the current user\
     * 
     * @param int $id
     * 
     * @return void
     */
    public function assignCardToMe($id) {
        // Get the card
        $card = Card::where('id', $id)->first();

        // Check if the user is already assigned to the card
        $assignee = CardAssignee::where('card_id', $card->id)->where('user_id', auth()->user()->uuid)->first();

        if ($assignee) return;

        // Create the assignee
        CardAssignee::create([
            'card_id' => $card->id,
            'user_id' => auth()->user()->uuid
        ]);

        // Update the selected Card
        $this->selectedCard = Card::where('id', $id)->with(['assignees.user', 'tasks.assignees.user'])->first();

        // Update the sprint
        $this->sprint = Sprint::where('uuid', $this->uuid)->with(['cards.assignees.user', 'cards.tasks.assignees.user'])->first();

        // Create a new log
        Log::create([
            'user_id' => auth()->user()->uuid,
            'project_id' => $this->sprint->project_id,
            'sprint_id' => $this->sprint->uuid,
            'card_id' => $card->id,
            'action' => 'create',
            'data' => json_encode(auth()->user()),
            'table' => 'card_assignees',
            'description' => 'User <b>' . auth()->user()->name . '</b> was added to card <b>' . $card->name . '</b>',
            'environment' => config('app.env')
        ]);
    }

    /**
     * Move a card
     * 
     * @param int $id
     * 
     * @return void
     */
    public function moveCard($id) {
        // Get the card
        $card = Card::where('id', $id)->first();

        // dd($this->selectedProject, $this->backlogOrSprint, $this->backlogOrSprintName, $this->sprintColumn, $this->position);

        if ($this->backlogOrSprint === 'backlog') {
            // Get the backlog
            $backlog = Backlog::where('uuid', $this->backlogOrSprintName)->first();

            // Position
            $position = 0;
            if ($this->position === 'bottom') $position = $backlog->cards->count() + 1;

            // Create a backlog card
            $backlogCard = BacklogCard::create([
                'backlog_id' => $backlog->uuid,
                'name' => $card->name,
                'description' => $card->description,
                'approval_status' => $card->approval_status,
                'card_index' => $position
            ]);

            // Get all assignees of the card, and create assignees for the backlog card
            $assignees = CardAssignee::where('card_id', $card->id)->get();
            foreach ($assignees as $assignee) {
                BacklogCardAssignee::create([
                    'backlog_card_id' => $backlogCard->id,
                    'user_id' => $assignee->user_id
                ]);

                // Delete the card assignee
                $assignee->delete();
            }

            // Get all tasks of the card, and create tasks for the backlog card
            $tasks = Task::where('card_id', $card->id)->get();
            foreach ($tasks as $task) {
                $backlogCardTask = BacklogTask::create([
                    'backlog_card_id' => $backlogCard->id,
                    'name' => $task->name,
                    'status' => $task->status,
                    'description' => $task->description,
                    'task_index' => $task->task_index
                ]);

                // Get all assignees of the task, and create assignees for the backlog card task
                $taskAssignees = TaskAssignee::where('task_id', $task->id)->get();
                foreach ($taskAssignees as $taskAssignee) {
                    BacklogTaskAssignee::create([
                        'backlog_task_id' => $backlogCardTask->id,
                        'user_id' => $taskAssignee->user_id
                    ]);

                    // Delete the task assignee
                    $taskAssignee->delete();
                }

                // Delete the task
                $task->delete();
            }

            // Delete the card
            $card->delete();

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
                'description' => 'Card <b>' . $card->name . '</b> moved to backlog <b>' . $backlog->name . '</b>',
                'environment' => config('app.env')
            ]);

            // Close the selected card modal
            $this->selectedCardModal = false;
        } elseif ($this->backlogOrSprint === 'sprint') {
            // Get the sprint
            $sprint = Sprint::where('uuid', $this->backlogOrSprintName)->first();

            // Position
            $position = 0;
            if ($this->position === 'top') {
                // Check if there is already a card with index 0, if so, increment the index of all cards in the column
                $cardsInColumn = Card::where('sprint_id', $sprint->uuid)->where('status', $this->sprintColumn)->orderBy('card_index')->get();
                $cardWithIndexZero = $cardsInColumn->where('card_index', 0)->first();
                if ($cardWithIndexZero) {
                    foreach ($cardsInColumn as $cardInColumn) {
                        $cardInColumn->increment('card_index');
                    }
                }
            } elseif ($this->position === 'bottom') {
                $position = $sprint->cards->count() + 1;
            } 

            // Update the card
            $card->update([
                'sprint_id' => $sprint->uuid,
                'status' => $this->sprintColumn,
                'card_index' => $position
            ]);

            // Update the sprint
            $this->sprint = Sprint::where('uuid', $this->uuid)->with(['cards.assignees.user', 'cards.tasks.assignees.user'])->first();

            // Create a new log
            Log::create([
                'user_id' => auth()->user()->uuid,
                'project_id' => $this->sprint->project_id,
                'sprint_id' => $this->sprint->uuid,
                'card_id' => $card->id,
                'action' => 'move',
                'data' => json_encode($card),
                'table' => 'cards',
                'description' => 'Card <b>' . $card->name . '</b> moved to sprint <b>' . $sprint->name . '</b>',
                'environment' => config('app.env')
            ]);

            // Close the selected card modal
            $this->selectedCardModal = false;
        }
    }

    /**
     * Copy a card
     * 
     * @param int $id
     * 
     * @return void
     */
    public function copyCard($id) {
        // Get the card
        $card = Card::where('id', $id)->first();

        // Check if there is a task with the same index, if so, increment the index of all tasks
        $cardWithSameIndex = Card::where('sprint_id', $this->sprint->uuid)->where('status', $card->status)->where('card_index', $card->card_index)->first();

        if ($cardWithSameIndex) {
            $cardsInColumn = Card::where('sprint_id', $this->sprint->uuid)->where('status', $card->status)->orderBy('card_index')->get();
            foreach ($cardsInColumn as $cardInColumn) {
                if ($cardInColumn->card_index >= $card->card_index) {
                    $cardInColumn->increment('card_index');
                }
            }
        }

        // Create the card
        $newCard = Card::create([
            'sprint_id' => $this->sprint->uuid,
            'name' => $card->name,
            'description' => $card->description,
            'status' => $card->status,
            'card_index' => $card->card_index
        ]);

        // Get all assignees of the card, and create assignees for the new card
        $assignees = CardAssignee::where('card_id', $card->id)->get();
        foreach ($assignees as $assignee) {
            CardAssignee::create([
                'card_id' => $newCard->id,
                'user_id' => $assignee->user_id
            ]);
        }

        // Get all tasks of the card, and create tasks for the new card
        $tasks = Task::where('card_id', $card->id)->get();
        foreach ($tasks as $task) {
            $newTask = Task::create([
                'card_id' => $newCard->id,
                'description' => $task->description,
                'status' => $task->status,
                'task_index' => $task->task_index,
                'sprint_id' => $task->sprint_id
            ]);

            // Get all assignees of the task, and create assignees for the new task
            $taskAssignees = TaskAssignee::where('task_id', $task->id)->get();
            foreach ($taskAssignees as $taskAssignee) {
                TaskAssignee::create([
                    'task_id' => $newTask->id,
                    'user_id' => $taskAssignee->user_id
                ]);
            }
        }

        // Update the sprint
        $this->sprint = Sprint::where('uuid', $this->uuid)->with(['cards.assignees.user', 'cards.tasks.assignees.user'])->first();

        // Create a new log
        Log::create([
            'user_id' => auth()->user()->uuid,
            'project_id' => $this->sprint->project_id,
            'sprint_id' => $this->sprint->uuid,
            'card_id' => $newCard->id,
            'action' => 'create',
            'data' => json_encode($newCard),
            'table' => 'cards',
            'description' => 'Card <b>' . $newCard->name . '</b> copied from card <b>' . $card->name . '</b>',
            'environment' => config('app.env')
        ]);
    }

    /**
     * Set the isCreatingTask variable to true
     * 
     * @param string $column
     * 
     * @return void
     */
    public function createTask($column) {
        $this->isCreatingTask = true;
        $this->createdTaskColumn = $column;
    }

    /**
     * Cancel the task creation
     * 
     * @return void
     */
    public function cancelTaskCreation() {
        $this->isCreatingTask = false;
        $this->createdTaskColumn = null;
    }

    /**
     * Store the task
     * 
     * @param string $column
     * 
     * @return void
     */
    public function storeTask($column) {
        // Validate the data
        $data = $this->validate([
            'taskDescription' => 'required|string'
        ]);

        $data['description'] = $this->taskDescription;
        $data['card_id'] = $this->selectedCard->id;
        $data['status'] = $column;
        $data['task_index'] = 0;
        $data['sprint_id'] = $this->sprint->uuid;

        // Create the task
        $task = Task::create($data);

        // Check if there is already a task with index 0, if so, increment the index of all tasks in the column
        $tasksInColumn = Task::where('card_id', $this->selectedCard->id)->where('status', $column)->orderBy('task_index')->get();
        $taskWithIndexZero = $tasksInColumn->where('task_index', 0)->first();
        if ($taskWithIndexZero) {
            foreach ($tasksInColumn as $taskInColumn) {
                $taskInColumn->task_index++;
                $taskInColumn->save();
            }
        }

        // Update the selected Card
        $this->selectedCard = Card::where('id', $this->selectedCard->id)->with(['assignees.user', 'tasks.assignees.user'])->first();

        // Update the sprint
        $this->sprint = Sprint::where('uuid', $this->uuid)->with(['cards.assignees.user', 'cards.tasks.assignees.user'])->first();

        // Create a new log
        Log::create([
            'user_id' => auth()->user()->uuid,
            'project_id' => $this->sprint->project_id,
            'sprint_id' => $this->sprint->uuid,
            'card_id' => $this->selectedCard->id,
            'task_id' => $task->id,
            'action' => 'create',
            'data' => json_encode($task),
            'table' => 'tasks',
            'description' => 'Task <b>' . $task->name . '</b> created in ' . $column,
            'environment' => config('app.env')
        ]);

        // Reset the variables
        $this->isCreatingTask = false;
        $this->createdTaskColumn = null;
        $this->description = null;
    }

    /**
     * Open the delete task modal and set the ID
     * 
     * @param int $id
     * 
     * @return void
     */
    public function deleteTask($id) {
        // Set the selected task ID
        $this->selectedId = $id;

        // Open the modal
        $this->deleteTaskModal = true;
    }

    /**
     * Delete the task
     * 
     * @return void
     */
    public function destroyTask() {
        // Get the task
        $task = Task::where('id', $this->selectedId)->first();

        // Get all assignees of the task
        $assignees = TaskAssignee::where('task_id', $task->id)->get();
        foreach ($assignees as $assignee) {
            $assignee->delete();
        }

        // Delete the task
        $task->delete();

        // Update the selected Card
        $this->selectedCard = Card::where('id', $task->card_id)->with(['assignees.user', 'tasks.assignees.user'])->first();

        // Update the sprint
        $this->sprint = Sprint::where('uuid', $this->uuid)->with(['cards.assignees.user', 'cards.tasks.assignees.user'])->first();

        // Create a new log
        Log::create([
            'user_id' => auth()->user()->uuid,
            'project_id' => $this->sprint->project_id,
            'sprint_id' => $this->sprint->uuid,
            'card_id' => $task->card_id,
            'task_id' => $task->id,
            'action' => 'delete',
            'data' => json_encode($task),
            'table' => 'tasks',
            'description' => 'Task <b>' . $task->name . '</b> deleted',
            'environment' => config('app.env')
        ]);

        // Close the modal
        $this->deleteTaskModal = false;

        // Reset the variables
        $this->selectedId = null;
    }

    /**
     * Uppdate the task order
     * 
     * @param int $taskId
     * @param string $newColumn
     * @param int $newIndex
     * 
     * @return void
     */
    public function updateTaskOrder($taskId, $newColumn, $newIndex) {
        // Get the task
        $task = Task::where('id', $taskId)->first();

        // Get all tasks in the new column and sort them by their index
        $tasksInColumn = Task::where('card_id', $task->card_id)->where('status', $newColumn)->orderBy('task_index')->get();

        // Check if there is a task with the same index, if so, increment the index of all tasks in the new column
        $taskWithSameIndex = $tasksInColumn->where('task_index', $newIndex)->first();

        if ($taskWithSameIndex) {
            foreach ($tasksInColumn as $taskInColumn) {
                if ($taskInColumn->task_index >= $newIndex) {
                    $taskInColumn->increment('task_index');
                }
            }
        }

        // Update the task with the new column and index
        $task->update([
            'status' => $newColumn,
            'task_index' => $newIndex
        ]);

        // Update the selected Card
        $this->selectedCard = Card::where('id', $task->card_id)->with(['assignees.user', 'tasks.assignees.user'])->first();

        // Update the sprint
        $this->sprint = Sprint::where('uuid', $this->uuid)->with(['cards.assignees.user', 'cards.tasks.assignees.user'])->first();

        // Create a new log
        Log::create([
            'user_id' => auth()->user()->uuid,
            'project_id' => $this->sprint->project_id,
            'sprint_id' => $this->sprint->uuid,
            'card_id' => $task->card_id,
            'task_id' => $task->id,
            'action' => 'update',
            'data' => json_encode($task),
            'table' => 'tasks',
            'description' => 'Task <b>' . $task->name . '</b> moved to ' . $newColumn,
            'environment' => config('app.env')
        ]);
    }

    /**
     * Assign the task to the current user
     * 
     * @param int $id
     * 
     * @return void
     */
    public function assignTaskToMe($id) {
        // Get the current user
        $user = auth()->user();

        // Check if the user is already assigned to the task
        $assignee = TaskAssignee::where('task_id', $id)->where('user_id', $user->uuid)->first();

        if ($assignee) return;

        // Create the assignee
        TaskAssignee::create([
            'task_id' => $id,
            'user_id' => $user->uuid
        ]);

        // Update the selected Card
        $this->selectedCard = Card::where('id', $this->selectedCard->id)->with(['assignees.user', 'tasks.assignees.user'])->first();

        // Update the sprint
        $this->sprint = Sprint::where('uuid', $this->uuid)->with(['cards.assignees.user', 'cards.tasks.assignees.user'])->first();

        // Create a new log
        Log::create([
            'user_id' => auth()->user()->uuid,
            'project_id' => $this->sprint->project_id,
            'sprint_id' => $this->sprint->uuid,
            'card_id' => $this->selectedCard->id,
            'task_id' => $id,
            'action' => 'create',
            'data' => json_encode($user),
            'table' => 'task_assignees',
            'description' => 'User <b>' . $user->name . '</b> was added to task <b>' . Task::where('id', $id)->first()->name . '</b>',
            'environment' => config('app.env')
        ]);
    }

    /**
     * Add an assignee to a task
     * 
     * @param int $id
     * @param int $assigneeId
     * 
     * @return void
     */
    public function addTaskAssignee($id, $assigneeId) {
        // Get the clicked project member
        $projectMember = ProjectMember::where('id', $assigneeId)->first();
        
        // Create the assignee
        TaskAssignee::create([
            'task_id' => $id,
            'user_id' => $projectMember->user_id
        ]);

        // Update the selected Card
        $this->selectedCard = Card::where('id', $this->selectedCard->id)->with(['assignees.user', 'tasks.assignees.user'])->first();

        // Update the sprint
        $this->sprint = Sprint::where('uuid', $this->uuid)->with(['cards.assignees.user', 'cards.tasks.assignees.user'])->first();

        // Create a new log
        Log::create([
            'user_id' => auth()->user()->uuid,
            'project_id' => $this->sprint->project_id,
            'sprint_id' => $this->sprint->uuid,
            'card_id' => $this->selectedCard->id,
            'task_id' => $id,
            'action' => 'create',
            'data' => json_encode($projectMember),
            'table' => 'task_assignees',
            'description' => 'User <b>' . $projectMember->user->name . '</b> was added to task <b>' . Task::where('id', $id)->first()->name . '</b>',
            'environment' => config('app.env')
        ]);
    }

    /**
     * Remove an assignee from a task
     * 
     * @param int $id
     * @param int $assigneeId
     * 
     * @return void
     */
    public function removeTaskAssignee($id, $assigneeId) {
        // Get the clicked project member
        $projectMember = ProjectMember::where('id', $assigneeId)->first();

        // Get the assignee
        $assignee = TaskAssignee::where('task_id', $id)->where('user_id', $projectMember->user_id)->first();

        // Delete the assignee
        $assignee->delete();

        // Update the selected Card
        $this->selectedCard = Card::where('id', $this->selectedCard->id)->with(['assignees.user', 'tasks.assignees.user'])->first();

        // Update the sprint
        $this->sprint = Sprint::where('uuid', $this->uuid)->with(['cards.assignees.user', 'cards.tasks.assignees.user'])->first();

        // Create a new log
        Log::create([
            'user_id' => auth()->user()->uuid,
            'project_id' => $this->sprint->project_id,
            'sprint_id' => $this->sprint->uuid,
            'card_id' => $this->selectedCard->id,
            'task_id' => $id,
            'action' => 'delete',
            'data' => json_encode($projectMember),
            'table' => 'task_assignees',
            'description' => 'User <b>' . $projectMember->user->name . '</b> was removed from task <b>' . Task::where('id', $id)->first()->name . '</b>',
            'environment' => config('app.env')
        ]);
    }

    /**
     * Set the isEditingTaskName variable to true
     * 
     * @param int $id
     * 
     * @return void
     */
    public function startEditingTaskDescription($id) {
        $this->isEditingTaskDescription = true;
        $this->editingTaskId = $id;
        $this->taskDescription = Task::where('id', $id)->first()->description;
    }

    /**
     * Save the task description
     * 
     * @param int $id
     * 
     * @return void
     */
    public function saveTaskDescription($id) {
        // Get the task
        $task = Task::where('id', $id)->first();

        // Update the task
        $task->update([
            'description' => $this->taskDescription
        ]);

        // Update the selected Card
        $this->selectedCard = Card::where('id', $task->card_id)->with(['assignees.user', 'tasks.assignees.user'])->first();

        // Update the sprint
        $this->sprint = Sprint::where('uuid', $this->uuid)->with(['cards.assignees.user', 'cards.tasks.assignees.user'])->first();

        // Create a new log
        Log::create([
            'user_id' => auth()->user()->uuid,
            'project_id' => $this->sprint->project_id,
            'sprint_id' => $this->sprint->uuid,
            'card_id' => $task->card_id,
            'task_id' => $task->id,
            'action' => 'update',
            'data' => json_encode($task),
            'table' => 'tasks',
            'description' => 'Task <b>' . $task->name . '</b> updated',
            'environment' => config('app.env')
        ]);

        // Reset the variables
        $this->isEditingTaskDescription = false;
        $this->taskDescription = null;
    }

    /**
     * Copy a task
     * 
     * @param int $id
     * 
     * @return void
     */
    public function copyTask($id) {
        // Get the task
        $task = Task::where('id', $id)->first();

        // Check if there is a task with the same index, if so, increment the index of all tasks
        $taskWithSameIndex = Task::where('card_id', $task->card_id)->where('status', $task->status)->where('task_index', $task->task_index)->first();

        if ($taskWithSameIndex) {
            $tasksInColumn = Task::where('card_id', $task->card_id)->where('status', $task->status)->orderBy('task_index')->get();
            foreach ($tasksInColumn as $taskInColumn) {
                if ($taskInColumn->task_index >= $task->task_index) {
                    $taskInColumn->increment('task_index');
                }
            }
        }

        // Create the task
        $newTask = Task::create([
            'card_id' => $task->card_id,
            'description' => $task->description,
            'status' => $task->status,
            'task_index' => $task->task_index,
            'sprint_id' => $task->sprint_id
        ]);

        // Get all assignees of the task, and create assignees for the new task
        $assignees = TaskAssignee::where('task_id', $task->id)->get();
        foreach ($assignees as $assignee) {
            TaskAssignee::create([
                'task_id' => $newTask->id,
                'user_id' => $assignee->user_id
            ]);
        }

        // Update the selected Card
        $this->selectedCard = Card::where('id', $task->card_id)->with(['assignees.user', 'tasks.assignees.user'])->first();

        // Update the sprint
        $this->sprint = Sprint::where('uuid', $this->uuid)->with(['cards.assignees.user', 'cards.tasks.assignees.user'])->first();

        // Create a new log
        Log::create([
            'user_id' => auth()->user()->uuid,
            'project_id' => $this->sprint->project_id,
            'sprint_id' => $this->sprint->uuid,
            'card_id' => $task->card_id,
            'task_id' => $newTask->id,
            'action' => 'create',
            'data' => json_encode($newTask),
            'table' => 'tasks',
            'description' => 'Task <b>' . $newTask->name . '</b> copied from task <b>' . $task->name . '</b>',
            'environment' => config('app.env')
        ]);
    }

    /**
     * Convert a task to a card
     * 
     * @param int $id
     * 
     * @return void
     */
    public function convertToCard($id) {
        // Get the task
        $task = Task::where('id', $id)->first();

        // Check if there is a card with the same index, if so, increment the index of all cards in the column
        $cardWithSameIndex = Card::where('sprint_id', $task->sprint_id)->where('status', $task->status)->where('card_index', $task->task_index)->first();

        if ($cardWithSameIndex) {
            $cardsInColumn = Card::where('sprint_id', $task->sprint_id)->where('status', $task->status)->orderBy('card_index')->get();
            foreach ($cardsInColumn as $cardInColumn) {
                if ($cardInColumn->card_index >= $task->task_index) {
                    $cardInColumn->increment('card_index');
                }
            }
        }

        // Create the card
        $card = Card::create([
            'sprint_id' => $task->sprint_id,
            'name' => $task->description,
            'description' => null,
            'status' => $task->status,
            'card_index' => $task->task_index
        ]);

        // Get all assignees of the task, and create assignees for the new card
        $assignees = TaskAssignee::where('task_id', $task->id)->get();
        foreach ($assignees as $assignee) {
            CardAssignee::create([
                'card_id' => $card->id,
                'user_id' => $assignee->user_id
            ]);
        }

        // Delete the task
        $task->delete();

        // Update the selected Card
        $this->selectedCard = Card::where('id', $card->id)->with(['assignees.user', 'tasks.assignees.user'])->first();

        // Update the sprint
        $this->sprint = Sprint::where('uuid', $this->uuid)->with(['cards.assignees.user', 'cards.tasks.assignees.user'])->first();

        // Create a new log
        Log::create([
            'user_id' => auth()->user()->uuid,
            'project_id' => $this->sprint->project_id,
            'sprint_id' => $this->sprint->uuid,
            'card_id' => $card->id,
            'task_id' => $task->id,
            'action' => 'create',
            'data' => json_encode($card),
            'table' => 'cards',
            'description' => 'Card <b>' . $card->name . '</b> created from task <b>' . $task->name . '</b>',
            'environment' => config('app.env')
        ]);
    }

    /**
     * Move a task
     * 
     * @param int $id
     * @param string $column
     * 
     * @return void
     */
    public function moveTask($id, $column) {
        // Get the task
        $task = Task::where('id', $id)->first();

        // Get all tasks in the new column and sort them by their index
        $tasksInColumn = Task::where('card_id', $task->card_id)->where('status', $column)->orderBy('task_index')->get();

        // Check if there is a task with the same index, if so, increment the index of all tasks in the new column
        $taskWithSameIndex = $tasksInColumn->where('task_index', $task->task_index)->first();

        if ($taskWithSameIndex) {
            foreach ($tasksInColumn as $taskInColumn) {
                if ($taskInColumn->task_index >= $task->task_index) {
                    $taskInColumn->increment('task_index');
                }
            }
        }

        // Update the task with the new column and index
        $task->update([
            'status' => $column,
            'task_index' => 0
        ]);

        // Update the selected Card
        $this->selectedCard = Card::where('id', $task->card_id)->with(['assignees.user', 'tasks.assignees.user'])->first();

        // Update the sprint
        $this->sprint = Sprint::where('uuid', $this->uuid)->with(['cards.assignees.user', 'cards.tasks.assignees.user'])->first();

        // Create a new log
        Log::create([
            'user_id' => auth()->user()->uuid,
            'project_id' => $this->sprint->project_id,
            'sprint_id' => $this->sprint->uuid,
            'card_id' => $task->card_id,
            'task_id' => $task->id,
            'action' => 'update',
            'data' => json_encode($task),
            'table' => 'tasks',
            'description' => 'Task <b>' . $task->name . '</b> moved to ' . $column,
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
