<?php

namespace App\Livewire\Projects\Backlog;

use App\Models\Log;
use App\Models\Card;
use App\Models\Task;
use App\Models\Sprint;
use Livewire\Component;
use App\Models\Project;
use Illuminate\Support\Str;
use App\Models\BacklogCard;
use App\Models\BacklogTask;
use App\Models\CardAssignee;
use App\Models\TaskAssignee;
use App\Models\ProjectMember;
use App\Models\BacklogCardAssignee;
use App\Models\BacklogTaskAssignee;
use App\Models\Backlog as BacklogModel;

class Backlog extends Component
{
    public $uuid;
    public $backlogId;

    public $projects;
    public $sprints;
    public $buckets;
    public $numOfCards = 0;
    public $projectMembers;
    public $projectMember; // (For permissions)
    
    public $selectedBucket;
    public $selectedCard;
    public $selectedTask;
    public $selectedCardApprovalStatus;
    public $selectedCardColor = 'gray';

    public $approvalStatusOptions = [
        'None' => 'gray', 
        'Approved' => 'green', 
        'Rejected' => 'red', 
        'Needs work' => 'yellow'
    ];

    public $selectedId;

    public $createBucketModal = false;
    public $editBucketModal = false;
    public $deleteBucketModal = false;

    public $selectedCardModal = false;
    public $createCardModal = false;
    public $editCardModal = false;
    public $deleteCardModal = false;

    public $deleteTaskModal = false;

    public $isEditingCardName = false;
    public $isEditingCardDescription = false;
    public $isCreatingTask = false;
    public $createdTaskColumn;
    public $openTaskboard = false;

    public $name, $description, $taskDescription;
    public $selectedProject, $backlogOrSprint = 'backlog', $backlogOrSprintName, $sprintColumn = 'todo', $position = 'top';

    public function mount($uuid, $backlogId = null) {
        $this->uuid = $uuid;
        $this->backlogId = $backlogId;

        $cards = [];

        // Get all projects where the user is an admin or project owner
        $projectMembersOfAllProjects = ProjectMember::where('user_id', auth()->user()->uuid)->get();
        foreach ($projectMembersOfAllProjects as $projectMember) {
            if ($projectMember->role_id === 2 || $projectMember->role_id === 3) {
                $this->projects[] = Project::where('uuid', $projectMember->project_id)->first();
            }
        }

        // Set the selected project
        $this->selectedProject = $this->uuid;

        // Get all buckets and cards for the selected project.
        $this->buckets = BacklogModel::where('project_id', $uuid)->with(['cards.assignees.user', 'cards.tasks.assignees.user'])->get();

        // Get all project users
        $this->projectMembers = ProjectMember::where('project_id', $uuid)->with('user')->get();

        // Store the current logged in user as a project member - (For permissions)
        $this->projectMember = ProjectMember::where('project_id', $uuid)->where('user_id', auth()->user()->uuid)->first();

        // Check the session if the user has already selected a backlog and select it, if not, select the first one.
        if (session()->has('selected_backlog')) {
            $this->selectedBucket = BacklogModel::where('uuid', session('selected_backlog'))->with('cards.tasks')->first();
        } else {
            $this->selectedBucket = $this->buckets->first();
        }

        // Set backlogOrSprintName to the selected bucket
        $this->backlogOrSprintName = $this->selectedBucket->uuid;
        
        // Get the number of cards in the selected bucket
        foreach ($this->buckets as $bucket) {
            foreach ($bucket->cards as $card) {
                $cards[] = $card;
            }
        }
        
        $this->numOfCards = count($cards);
    }

    public function updated($key, $value) {
        if ($key === 'selectedCardApprovalStatus') {
            $card = BacklogCard::where('id', $this->selectedCard->id)->first();
            
            $card->update([
                'approval_status' => $this->selectedCardApprovalStatus
            ]);

            // Create a new Log
            Log::create([
                'user_id' => auth()->user()->uuid,
                'project_id' => $this->uuid,
                'backlog_id' => $this->selectedBucket->uuid,
                'card_id' => $card->id,
                'action' => 'update',
                'data' => json_encode($card),
                'table' => 'backlog_cards',
                'description' => 'Updated card <b>' . $card->name . '</b>',
                'environment' => config('app.env')
            ]);
        } else if ($key === 'backlogOrSprint' && $value === 'sprint') {
            // Get all sprints for the selected project
            $this->sprints = Sprint::where('project_id', $this->selectedProject)->orderBy('start_date')->get();
        }
    }

    /**
     * Select a Bucket
     * 
     * @param int $id
     * 
     * @return void
     */
    public function selectBucket($id) {
        $this->selectedBucket = $this->buckets->where('uuid', $id)->first();

        // Set the selected backlog in the session
        session()->put('selected_backlog', $this->selectedBucket->uuid);
    }

    /**
     * Create a new bucket
     * 
     * @return void
     */
    public function createBucket() {
        // Validate the data
        $data = $this->validate([
            'name' => ['required', 'string'],
            'description' => ['required', 'string'],
        ]);
        
        $data['uuid'] = Str::uuid()->toString();
        $data['project_id'] = $this->uuid;
        $data['status'] = 'active';

        // Create a new bucket
        $bucket = BacklogModel::create($data);

        // Create a new Log
        Log::create([
            'user_id' => auth()->user()->uuid,
            'project_id' => $this->uuid,
            'backlog_id' => $bucket->uuid,
            'action' => 'create',
            'data' => json_encode($bucket),
            'table' => 'backlogs',
            'description' => 'Created bucket <b>' . $data['name'] . '</b>',
            'environment' => config('app.env')
        ]);

        // Close the modal
        $this->createBucketModal = false;

        // Update the buckets
        $this->buckets = BacklogModel::where('project_id', $this->uuid)->with('cards')->get();
    }

    /**
     * Open the Edit Bucket Modal and set the ID
     * 
     * @param int $id
     * 
     * @return void
     */
    public function editBucket($id) {
        // Set the selected bucket ID
        $this->selectedId = $id;

        // Open the edit bucket modal
        $this->editBucketModal = true;

        // Get the bucket
        $bucket = $this->buckets->where('uuid', $id)->first();

        // Set the name and description
        $this->name = $bucket->name;
        $this->description = $bucket->description;
    }

    /**
     * Update the Bucket
     * 
     * @return void
     */
    public function updateBucket() {
        // Validate the data
        $data = $this->validate([
            'name' => ['required', 'string'],
            'description' => ['required', 'string'],
        ]);

        // Get the bucket and update it
        $bucket = BacklogModel::where('uuid', $this->selectedId)->first();
        $bucket->update($data);

        // Create a new Log
        Log::create([
            'user_id' => auth()->user()->uuid,
            'project_id' => $this->uuid,
            'backlog_id' => $bucket->uuid,
            'action' => 'update',
            'data' => json_encode($data),
            'table' => 'backlogs',
            'description' => 'Updated bucket <b>' . $data['name'] . '</b>',
            'environment' => config('app.env')
        ]);

        // Close the modal
        $this->editBucketModal = false;

        // Update the buckets
        $this->buckets = BacklogModel::where('project_id', $this->uuid)->with('cards')->get();
    }

    /**
     * Open the Delete Bucket Modal and set the ID
     * 
     * @param int $id
     * 
     * @return void
     */
    public function deleteBucket($id) {
        // Set the selected bucket ID
        $this->selectedId = $id;

        // Open the delete bucket modal
        $this->deleteBucketModal = true;
    }

    /**
     * Delete the Bucket
     * 
     * @return void
     */
    public function destroyBucket() {
        // Get the bucket and delete it
        $bucket = BacklogModel::where('uuid', $this->selectedId)->first();
        $bucket->delete();

        // Create a new Log
        Log::create([
            'user_id' => auth()->user()->uuid,
            'project_id' => $this->uuid,
            'backlog_id' => $bucket->uuid,
            'action' => 'delete',
            'data' => json_encode($bucket),
            'table' => 'backlogs',
            'description' => 'Deleted bucket <b>' . $bucket->name . '</b>',
            'environment' => config('app.env')
        ]);

        // Close the modal
        $this->deleteBucketModal = false;

        // Update the buckets
        $this->buckets = BacklogModel::where('project_id', $this->uuid)->with('cards')->get();
    }

    /**
     * Create a new card
     * 
     * @return void
     */
    public function createCard() {
        // Validate the data
        $data = $this->validate([
            'name' => ['required', 'string'],
            'description' => ['required', 'string'],
        ]);

        $data['backlog_id'] = $this->selectedBucket->uuid;

        // Create a new card
        $card = BacklogCard::create($data);

        // Create a new Log
        Log::create([
            'user_id' => auth()->user()->uuid,
            'project_id' => $this->uuid,
            'backlog_id' => $this->selectedBucket->uuid,
            'card_id' => $card->id,
            'action' => 'create',
            'data' => json_encode($card),
            'table' => 'backlog_cards',
            'description' => 'Created card <b>' . $data['name'] . '</b>',
            'environment' => config('app.env')
        ]);

        // Close the modal
        $this->createCardModal = false;

        // Update the selected bucket
        $this->selectedBucket = BacklogModel::where('uuid', $this->selectedBucket->uuid)->with('cards')->first();
    }

    /**
     * Select a Card
     * 
     * @param int $id
     * 
     * @return void
     */
    public function selectCard($id, $showModal = true) {
        // Get the selected card
        $this->selectedCard = $this->selectedBucket->cards->where('id', $id)->first();

        // Set the card variables
        $this->name = $this->selectedCard->name;
        $this->description = $this->selectedCard->description;

        // Set selectedCardApprovalStatus
        $this->selectedCardApprovalStatus = $this->selectedCard->approval_status;

        // Set the selected card color based on the approval status
        $this->selectedCardColor = $this->approvalStatusOptions[$this->selectedCard->approval_status];

        if ($showModal) {
            // Open the selected card modal
            $this->selectedCardModal = true;
        }
    }

    /**
     * Change the status of a card
     * 
     * @param string $status
     * 
     * @return void
     */
    public function changeStatus($status) {
        BacklogCard::where('id', $this->selectedCard->id)->update([
            'approval_status' => $status
        ]);

        // Update the selected card
        $this->selectedCard = BacklogCard::where('id', $this->selectedCard->id)->first();

        // Update the selected card color
        $this->selectedCardColor = $this->approvalStatusOptions[$status];

        // Create a new Log
        Log::create([
            'user_id' => auth()->user()->uuid,
            'project_id' => $this->uuid,
            'backlog_id' => $this->selectedBucket->uuid,
            'card_id' => $this->selectedCard->id,
            'action' => 'update',
            'data' => json_encode($this->selectedCard),
            'table' => 'backlog_cards',
            'description' => 'Updated status for card <b>' . $this->selectedCard->name . '</b>',
            'environment' => config('app.env')
        ]);

    }

    /**
     * Add an assignee to a card
     * 
     * @param int $id
     * 
     * @return void
     */
    public function addAssignee($id) {
        $projectMember = ProjectMember::where('id', $id)->first();
        $assignee = BacklogCardAssignee::create([
            'backlog_card_id' => $this->selectedCard->id,
            'user_id' => $projectMember->user_id
        ]);

        // Update the selected card
        $card = BacklogCard::where('id', $this->selectedCard->id)->with(['assignees.user', 'tasks.assignees.user'])->first();

        // Create a new Log
        Log::create([
            'user_id' => auth()->user()->uuid,
            'project_id' => $this->uuid,
            'backlog_id' => $this->selectedBucket->uuid,
            'card_id' => $this->selectedCard->id,
            'action' => 'update',
            'data' => json_encode($card),
            'table' => 'backlog_cards',
            'description' => 'Added assignee to card <b>' . $card->name . '</b>',
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
    public function removeAssignee($id) {
        $projectMember = ProjectMember::where('id', $id)->first();
        $assignee = BacklogCardAssignee::where('user_id', $projectMember->user_id)->where('backlog_card_id', $this->selectedCard->id)->first();
        $assignee->delete();

        // Update the selected card
        $card = BacklogCard::where('id', $this->selectedCard->id)->with(['assignees.user', 'tasks.assignees.user'])->first();

        // Create a new Log
        Log::create([
            'user_id' => auth()->user()->uuid,
            'project_id' => $this->uuid,
            'backlog_id' => $this->selectedBucket->uuid,
            'card_id' => $this->selectedCard->id,
            'action' => 'update',
            'data' => json_encode($card),
            'table' => 'backlog_cards',
            'description' => 'Removed assignee from card <b>' . $card->name . '</b>',
            'environment' => config('app.env')
        ]);
    }

    /**
     * Open the Delete Card Modal and set the ID
     * 
     * @param int $id
     * 
     * @return void
     */
    public function deleteCard($id) {
        // Set the selected card ID
        $this->selectedId = $id;

        // Open the delete card modal
        $this->deleteCardModal = true;
    }

    /**
     * Delete the Card
     * 
     * @return void
     */
    public function destroyCard() {
        // Get all assignees for the card and delete them
        $assignees = BacklogCardAssignee::where('backlog_card_id', $this->selectedId)->get();
        foreach ($assignees as $assignee) {
            $assignee->delete();
        }

        // Get all tasks for the card and delete them
        $tasks = BacklogTask::where('backlog_card_id', $this->selectedId)->get();
        foreach ($tasks as $task) {
            // Get all assignees for the task and delete them
            $taskAssignees = BacklogTaskAssignee::where('backlog_task_id', $task->id)->get();
            foreach ($taskAssignees as $taskAssignee) {
                $taskAssignee->delete();
            }

            $task->delete();
        }

        // Get the card and delete it
        $card = BacklogCard::where('id', $this->selectedId)->first();
        $card->delete();

        // Create a new Log
        Log::create([
            'user_id' => auth()->user()->uuid,
            'project_id' => $this->uuid,
            'backlog_id' => $this->selectedBucket->uuid,
            'card_id' => $card->id,
            'action' => 'delete',
            'data' => json_encode($card),
            'table' => 'backlog_cards',
            'description' => 'Deleted card <b>' . $card->name . '</b>',
            'environment' => config('app.env')
        ]);

        // Close the modal
        $this->deleteCardModal = false;

        // Close the selected card modal (if open)
        $this->selectedCardModal = false;

        // Update the selected bucket
        $this->selectedBucket = BacklogModel::where('uuid', $this->selectedBucket->uuid)->with('cards')->first();

        // Update the number of cards
        $this->numOfCards = $this->numOfCards - 1;
    }

    /**
     * Set the isEditingCardName variable to true
     * 
     * @return void
     */
    public function startEditingCardName() {
        $this->isEditingCardName = true;
    }

    /**
     * Set the isEditingCardDescription variable to true
     * 
     * @return void
     */
    public function startEditingCardDescription() {
        $this->isEditingCardDescription = true;
    }

    /**
     * Update the card name
     * 
     * @return void
     */
    public function saveCardName() {
        // Get the card and update it
        $card = BacklogCard::where('id', $this->selectedCard->id)->first();
        $card->update([
            'name' => $this->name
        ]);

        // Create a new Log
        Log::create([
            'user_id' => auth()->user()->uuid,
            'project_id' => $this->uuid,
            'backlog_id' => $this->selectedBucket->uuid,
            'card_id' => $card->id,
            'action' => 'update',
            'data' => json_encode($card),
            'table' => 'backlog_cards',
            'description' => 'Updated card name to <b>' . $this->name . '</b>',
            'environment' => config('app.env')
        ]);

        // Set the isEditingCardName variable to false
        $this->isEditingCardName = false;

        // Update the selected card
        $this->selectedCard = BacklogCard::where('id', $this->selectedCard->id)->first();
    }

    /**
     * Update the card description
     * 
     * @return void
     */
    public function saveCardDescription() {
        // Get the card and update it
        $card = BacklogCard::where('id', $this->selectedCard->id)->first();
        $card->update([
            'description' => $this->description
        ]);

        // Create a new Log
        Log::create([
            'user_id' => auth()->user()->uuid,
            'project_id' => $this->uuid,
            'backlog_id' => $this->selectedBucket->uuid,
            'card_id' => $card->id,
            'action' => 'update',
            'data' => json_encode($card),
            'table' => 'backlog_cards',
            'description' => 'Updated card description to <b>' . $this->description . '</b>',
            'environment' => config('app.env')
        ]);

        // Set the isEditingCardDescription variable to false
        $this->isEditingCardDescription = false;

        // Update the selected card
        $this->selectedCard = BacklogCard::where('id', $this->selectedCard->id)->first();
    }

    /**
     * Assign the card to the current user
     * 
     * @return void
     */
    public function assignCardToMe() {
        // Check if the user is already assigned to the card
        $assignee = BacklogCardAssignee::where('user_id', auth()->user()->uuid)->where('backlog_card_id', $this->selectedCard->id)->first();

        if ($assignee) return;

        // Create the assignee
        $assignee = BacklogCardAssignee::create([
            'backlog_card_id' => $this->selectedCard->id,
            'user_id' => auth()->user()->uuid
        ]);

        // Update the selected card
        $card = BacklogCard::where('id', $this->selectedCard->id)->with(['assignees.user', 'tasks.assignees.user'])->first();

        // Create a new Log
        Log::create([
            'user_id' => auth()->user()->uuid,
            'project_id' => $this->uuid,
            'backlog_id' => $this->selectedBucket->uuid,
            'card_id' => $this->selectedCard->id,
            'action' => 'update',
            'data' => json_encode($card),
            'table' => 'backlog_cards',
            'description' => 'Added assignee to card <b>' . $card->name . '</b>',
            'environment' => config('app.env')
        ]);

        // Update the selected card
        $this->selectedCard = BacklogCard::where('id', $this->selectedCard->id)->with(['assignees.user', 'tasks.assignees.user'])->first();
    }

    /**
     * Move a card
     * 
     * @return void
     */
    public function moveCard() {
        // Get the selected project
        Project::where('uuid', $this->selectedProject)->first();

        if ($this->backlogOrSprint === 'backlog') {
            // Get the selected backlog
            $backlog = BacklogModel::where('uuid', $this->backlogOrSprintName)->first();

            // Get the selected card
            $card = BacklogCard::where('id', $this->selectedCard->id)->first();

            $position = 0;
            if ($this->position === 'bottom') {
                $position = $backlog->cards->count() + 1;
            }

            // Update the card
            $card->update([
                'backlog_id' => $backlog->uuid,
                'card_index' => $position
            ]);

            // Update the backlog_id in the tasks
            $tasks = BacklogTask::where('backlog_card_id', $card->id)->get();
            foreach ($tasks as $task) {
                $task->update([
                    'backlog_id' => $backlog->uuid
                ]);
            }

            // Create a new Log
            Log::create([
                'user_id' => auth()->user()->uuid,
                'project_id' => $this->uuid,
                'backlog_id' => $backlog->uuid,
                'card_id' => $card->id,
                'action' => 'update',
                'data' => json_encode($card),
                'table' => 'backlog_cards',
                'description' => 'Moved card <b>' . $card->name . '</b> to backlog <b>' . $backlog->name . '</b>',
                'environment' => config('app.env')
            ]);

            // Update the selectedbucket
            $this->selectedBucket = BacklogModel::where('uuid', $this->selectedBucket->uuid)->with('cards')->first();

            // Update the buckets
            $this->buckets = BacklogModel::where('project_id', $this->uuid)->with('cards')->get();

            // Close the modal
            $this->selectedCardModal = false;
        } elseif ($this->backlogOrSprint === 'sprint') {
            // Get the selected sprint
            $sprint = Sprint::where('uuid', $this->backlogOrSprintName)->first();

            // Get the selected card
            $card = BacklogCard::where('id', $this->selectedCard->id)->first();

            $position = 0;
            if ($this->position === 'top') {
                // Check if there is already a card with index 0, if so increment the card index
                $cardsWithIndexZero = Card::where('sprint_id', $sprint->uuid)->where('card_index', 0)->get();
                foreach ($cardsWithIndexZero as $cardWithIndexZero) {
                    $cardWithIndexZero->increment('card_index');
                }
            } elseif ($this->position === 'bottom') {
                $position = $sprint->cards->count() + 1;
            }

            // Create a sprint card from the backlog card
            $sprintCard = Card::create([
                'sprint_id' => $sprint->uuid,
                'name' => $card->name,
                'description' => $card->description,
                'status' => $this->sprintColumn,
                'approval_status' => $card->approval_status,
                'card_index' => $position
            ]);

            // Get all assignees for the card and add them to the sprint card
            $assignees = BacklogCardAssignee::where('backlog_card_id', $card->id)->get();
            foreach ($assignees as $assignee) {
                CardAssignee::create([
                    'card_id' => $sprintCard->id,
                    'user_id' => $assignee->user_id
                ]);

                // Delete the backlog card assignee
                $assignee->delete();
            }

            // Get all tasks for the card and add them to the sprint card
            $tasks = BacklogTask::where('backlog_card_id', $card->id)->get();
            foreach ($tasks as $task) {
                $sprintTask = Task::create([
                    'card_id' => $sprintCard->id,
                    'description' => $task->description,
                    'status' => $task->status,
                    'task_index' => 0,
                    'sprint_id' => $sprint->uuid
                ]);

                // Check if there are any tasks with task_index 0, if so increment the task_index
                $tasksWithIndexZero = Task::where('card_id', $sprintCard->id)->where('task_index', 0)->get();
                foreach ($tasksWithIndexZero as $taskWithIndexZero) {
                    $taskWithIndexZero->increment('task_index');
                }

                // Get all assignees for the task and add them to the sprint task
                $taskAssignees = BacklogTaskAssignee::where('backlog_task_id', $task->id)->get();
                foreach ($taskAssignees as $taskAssignee) {
                    TaskAssignee::create([
                        'task_id' => $sprintTask->id,
                        'user_id' => $taskAssignee->user_id
                    ]);

                    // Delete the backlog task assignee
                    $taskAssignee->delete();
                }

                // Delete the backlog task
                $task->delete();
            }

            // Delete the backlog card
            $card->delete();

            // Create a new Log
            Log::create([
                'user_id' => auth()->user()->uuid,
                'project_id' => $this->uuid,
                'backlog_id' => $sprint->uuid,
                'card_id' => $sprintCard->id,
                'action' => 'create',
                'data' => json_encode($sprintCard),
                'table' => 'cards',
                'description' => 'Moved card <b>' . $card->name . '</b> to sprint <b>' . $sprint->name . '</b>',
                'environment' => config('app.env')
            ]);

            // Update the selectedbucket
            $this->selectedBucket = BacklogModel::where('uuid', $this->selectedBucket->uuid)->with('cards')->first();

            // Update the buckets
            $this->buckets = BacklogModel::where('project_id', $this->uuid)->with('cards')->get();

            // Close the modal
            $this->selectedCardModal = false;
        }
    }

    /**
     * Set the isCreatingTask variable to true
     * 
     * @param string $taskType
     * 
     * @return void
     */
    public function createTask($taskType) {
        $this->isCreatingTask = true;
        $this->createdTaskColumn = $taskType;
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
     * Store the new task
     * 
     * @param string $taskType
     * 
     * @return void
     */
    public function storeTask($taskType) {
        // Validate the data
        $data = $this->validate([
            'taskDescription' => ['required', 'string'],
        ]);

        $data['description'] = $this->taskDescription;
        $data['backlog_card_id'] = $this->selectedCard->id;
        $data['status'] = $taskType;
        $data['backlog_id'] = $this->selectedBucket->uuid;

        // Create a new task
        $task = BacklogTask::create($data);

        // Update the selected card
        $this->selectedCard = BacklogCard::where('id', $this->selectedCard->id)->with(['assignees.user', 'tasks.assignees.user'])->first();

        // Create a new Log
        Log::create([
            'user_id' => auth()->user()->uuid,
            'project_id' => $this->uuid,
            'backlog_id' => $this->selectedBucket->uuid,
            'card_id' => $this->selectedCard->id,
            'action' => 'create',
            'data' => json_encode($task),
            'table' => 'backlog_tasks',
            'description' => 'Created task <b>' . $task->description . '</b>',
            'environment' => config('app.env')
        ]);

        // Reset the variables
        $this->isCreatingTask = false;
        $this->createdTaskColumn = null;
        $this->taskDescription = null;
    }

    /**
     * Select a Task
     * 
     * @param int $id
     * 
     * @return void
     */
    public function selectTask($id) {
        $this->selectedTask = BacklogTask::where('id', $id)->with('assignees.user')->first();
    }

    /**
     * Add an assignee to a task
     * 
     * @param int $id
     * 
     * @return void
     */
    public function addTaskAssignee($id) {
        $projectMember = ProjectMember::where('id', $id)->first();
        $assignee = BacklogTaskAssignee::create([
            'backlog_task_id' => $this->selectedTask->id,
            'user_id' => $projectMember->user_id
        ]);

        // Update the selected card
        $card = BacklogCard::where('id', $this->selectedCard->id)->with(['assignees.user', 'tasks.assignees.user'])->first();

        // Create a new Log
        Log::create([
            'user_id' => auth()->user()->uuid,
            'project_id' => $this->uuid,
            'backlog_id' => $this->selectedBucket->uuid,
            'card_id' => $this->selectedCard->id,
            'action' => 'update',
            'data' => json_encode($card),
            'table' => 'backlog_cards',
            'description' => 'Added assignee to card <b>' . $card->name . '</b>',
            'environment' => config('app.env')
        ]);

        // Update the selected card
        $this->selectedCard = BacklogCard::where('id', $this->selectedCard->id)->with(['assignees.user', 'tasks.assignees.user'])->first();
    }

    /**
     * Remove an assignee from a task
     * 
     * @param int $id
     * 
     * @return void
     */
    public function removeTaskAssignee($id) {
        $projectMember = ProjectMember::where('id', $id)->first();
        $assignee = BacklogTaskAssignee::where('user_id', $projectMember->user_id)->where('backlog_task_id', $this->selectedTask->id)->first();
        $assignee->delete();

        // Update the selected card
        $card = BacklogCard::where('id', $this->selectedCard->id)->with(['assignees.user', 'tasks.assignees.user'])->first();

        // Create a new Log
        Log::create([
            'user_id' => auth()->user()->uuid,
            'project_id' => $this->uuid,
            'backlog_id' => $this->selectedBucket->uuid,
            'card_id' => $this->selectedCard->id,
            'action' => 'update',
            'data' => json_encode($card),
            'table' => 'backlog_cards',
            'description' => 'Removed assignee from card <b>' . $card->name . '</b>',
            'environment' => config('app.env')
        ]);

        // Update the selected card
        $this->selectedCard = BacklogCard::where('id', $this->selectedCard->id)->with(['assignees.user', 'tasks.assignees.user'])->first();
    }

    /**
     * Update the task order
     * 
     * @param int $taskId
     * @param string $newColumn
     * @param int $newIndex
     * 
     * @return void
     */
    public function updateTaskOrder($taskId, $newColumn, $newIndex) {
        // Get the task
        $task = BacklogTask::where('id', $taskId)->first();

        // Get all tasks in the new column and sort them by task_index
        $tasksInColumn = BacklogTask::where('status', $newColumn)
            ->orderBy('task_index')
            ->get();

        // Check if there is a task in the new column with the same index
        $taskWithSameIndex = $tasksInColumn->where('task_index', $newIndex)->where('status', $newColumn)->first();

        // If there is a task with the same index, increment the task_index of all tasks in the new column
        if ($taskWithSameIndex) {
            foreach ($tasksInColumn as $otherTask) {
                $otherTask->increment('task_index');
            }
        }

        // Update the moved task with its new task_index
        $task->update([
            'status' => $newColumn,
            'task_index' => $newIndex
        ]);

        // Update the selected card
        $this->selectedCard = BacklogCard::where('id', $this->selectedCard->id)->with(['assignees.user', 'tasks.assignees.user'])->first();

        // Create a new Log
        Log::create([
            'user_id' => auth()->user()->uuid,
            'project_id' => $this->uuid,
            'backlog_id' => $this->selectedBucket->uuid,
            'card_id' => $this->selectedCard->id,
            'action' => 'update',
            'data' => json_encode($task),
            'table' => 'backlog_tasks',
            'description' => 'Updated task <b>' . $task->description . '</b>',
            'environment' => config('app.env')
        ]);
    }

    /**
     * Assign the task to the current user
     * 
     * @return void
     */
    public function assignTaskToMe() {
        // Get the current user
        $user = auth()->user();

        // Check if the user is already assigned to the task
        $assignee = BacklogTaskAssignee::where('user_id', $user->uuid)->where('backlog_task_id', $this->selectedTask->id)->first();

        if ($assignee) return;

        // Create a new assignee
        $assignee = BacklogTaskAssignee::create([
            'backlog_task_id' => $this->selectedTask->id,
            'user_id' => $user->uuid
        ]);

        // Update the selected card
        $this->selectedCard = BacklogCard::where('id', $this->selectedCard->id)->with(['assignees.user', 'tasks.assignees.user'])->first();

        // Create a new Log
        Log::create([
            'user_id' => auth()->user()->uuid,
            'project_id' => $this->uuid,
            'backlog_id' => $this->selectedBucket->uuid,
            'card_id' => $this->selectedCard->id,
            'action' => 'update',
            'data' => json_encode($this->selectedCard),
            'table' => 'backlog_cards',
            'description' => 'Added assignee to card <b>' . $this->selectedCard->name . '</b>',
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
        $task = BacklogTask::where('id', $id)->first();

        // Get all tasks in the new column and sort them by task_index
        $tasksInColumn = BacklogTask::where('status', $column)
            ->orderBy('task_index')
            ->get();

        // Check if there is already a task with task index 0 in the new column
        $taskWithSameIndex = $tasksInColumn->where('task_index', 0)->where('status', $column)->first();

        // If there is a task with the same index, increment the task_index of all tasks in the new column
        if ($taskWithSameIndex) {
            foreach ($tasksInColumn as $otherTask) {
                $otherTask->increment('task_index');
            }
        }

        // Update the task with the new status and task_index
        $task->update([
            'status' => $column,
            'task_index' => 0
        ]);

        // Update the selected card
        $this->selectedCard = BacklogCard::where('id', $this->selectedCard->id)->with(['assignees.user', 'tasks.assignees.user'])->first();

        // Create a new Log
        Log::create([
            'user_id' => auth()->user()->uuid,
            'project_id' => $this->uuid,
            'backlog_id' => $this->selectedBucket->uuid,
            'card_id' => $this->selectedCard->id,
            'action' => 'update',
            'data' => json_encode($task),
            'table' => 'backlog_tasks',
            'description' => 'Moved task <b>' . $task->description . '</b>',
            'environment' => config('app.env')
        ]);
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
        $task = BacklogTask::where('id', $id)->first();

        // Check if there is a task in the same column with the same index
        $taskWithSameIndex = BacklogTask::where('task_index', $task->task_index)->where('status', $task->status)->first();

        // If there is a task with the same index, increment the task_index of all tasks in the same column
        if ($taskWithSameIndex) {
            $tasksInColumn = BacklogTask::where('status', $task->status)->get();
            foreach ($tasksInColumn as $otherTask) {
                $otherTask->increment('task_index');
            }
        }

        // Create a new task
        $newTask = BacklogTask::create([
            'description' => $task->description,
            'status' => $task->status,
            'task_index' => 0,
            'backlog_card_id' => $task->backlog_card_id,
            'backlog_id' => $task->backlog_id
        ]);

        // Update the selected card
        $this->selectedCard = BacklogCard::where('id', $this->selectedCard->id)->with(['assignees.user', 'tasks.assignees.user'])->first();

        // Create a new Log
        Log::create([
            'user_id' => auth()->user()->uuid,
            'project_id' => $this->uuid,
            'backlog_id' => $this->selectedBucket->uuid,
            'card_id' => $this->selectedCard->id,
            'action' => 'create',
            'data' => json_encode($newTask),
            'table' => 'backlog_tasks',
            'description' => 'Copied task <b>' . $newTask->description . '</b>',
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
        $task = BacklogTask::where('id', $id)->first();

        // Create a new card
        $newCard = BacklogCard::create([
            'backlog_id' => $task->backlog_id,
            'name' => $task->description,
            'description' => null,
            'approval_status' => 'None',
        ]);

        // Copy the assignees and remove them from the task
        if ($task->assignees->count() > 0) {
            foreach ($task->assignees as $assignee) {
                BacklogCardAssignee::create([
                    'backlog_card_id' => $newCard->id,
                    'user_id' => $assignee->user_id
                ]);
    
                $assignee->delete();
            }
        }

        // Remove the task
        $task->delete();
        $this->selectedTask = null;

        // Update the selected card
        $this->selectedCard = BacklogCard::where('id', $this->selectedCard->id)->with(['assignees.user', 'tasks.assignees.user'])->first();

        // Update the selected bucket
        $this->selectedBucket = BacklogModel::where('uuid', $this->selectedBucket->uuid)->with('cards')->first();

        // Update the number of cards
        $this->numOfCards = count($this->selectedBucket->cards);

        // Create a new Log
        Log::create([
            'user_id' => auth()->user()->uuid,
            'project_id' => $this->uuid,
            'backlog_id' => $this->selectedBucket->uuid,
            'card_id' => $newCard->id,
            'action' => 'create',
            'data' => json_encode($newCard),
            'table' => 'backlog_cards',
            'description' => 'Converted task to card <b>' . $newCard->name . '</b>',
            'environment' => config('app.env')
        ]);
    }

    /**
     * Open the Delete Task Modal and set the ID
     * 
     * @param int $id
     * 
     * @return void
     */
    public function deleteTask($id) {
        // Set the selected task ID
        $this->selectedId = $id;

        // Open the delete task modal
        $this->deleteTaskModal = true;
    }

    /**
     * Delete the Task
     * 
     * @return void
     */
    public function destroyTask() {
        // Get the task and delete it
        $task = BacklogTask::where('id', $this->selectedId)->first();
        $task->delete();

        $this->selectedTask = null;

        // Update the selected card
        $this->selectedCard = BacklogCard::where('id', $this->selectedCard->id)->with(['assignees.user', 'tasks.assignees.user'])->first();

        // Close the modal
        $this->deleteTaskModal = false;

        // Create a new Log
        Log::create([
            'user_id' => auth()->user()->uuid,
            'project_id' => $this->uuid,
            'backlog_id' => $this->selectedBucket->uuid,
            'card_id' => $this->selectedCard->id,
            'action' => 'delete',
            'data' => json_encode($task),
            'table' => 'backlog_tasks',
            'description' => 'Deleted task <b>' . $task->description . '</b>',
            'environment' => config('app.env')
        ]);
    }

    /**
     * Render the component
     * 
     * @return \Illuminate\View\View
     */
    public function render() {
        return view('livewire.projects.backlog.backlog');
    }
}
