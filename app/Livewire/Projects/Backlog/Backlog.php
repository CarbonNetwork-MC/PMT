<?php

namespace App\Livewire\Projects\Backlog;

use App\Models\Log;
use Livewire\Component;
use Illuminate\Support\Str;
use App\Models\BacklogCard;
use App\Models\BacklogTask;
use App\Models\ProjectMember;
use App\Models\BacklogCardAssignee;
use App\Models\BacklogTaskAssignee;
use App\Models\Backlog as BacklogModel;

class Backlog extends Component
{
    public $uuid;
    public $backlogId;

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

    public $bucket;
    public $selectedBucketId;
    public $card;
    public $selectedCardId;

    public $createBucketModal = false;
    public $editBucketModal = false;
    public $deleteBucketModal = false;

    public $createCardModal = false;

    public $selectedCardModal = false;
    public $editCardModal = false;
    public $deleteCardModal = false;

    public $isEditingCardName = false;
    public $isEditingCardDescription = false;
    public $isCreatingTask = false;
    public $createdTaskColumn;

    public $name, $description, $taskDescription;

    public function mount($uuid, $backlogId = null) {
        $this->uuid = $uuid;
        $this->backlogId = $backlogId;

        $cards = [];

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
        $this->selectedBucketId = $id;

        // Open the edit bucket modal
        $this->editBucketModal = true;

        // Get the bucket
        $this->bucket = $this->buckets->where('uuid', $id)->first();

        // Set the name and description
        $this->name = $this->bucket->name;
        $this->description = $this->bucket->description;
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
        $bucket = BacklogModel::where('uuid', $this->selectedBucketId)->first();
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
        $this->selectedBucketId = $id;

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
        $bucket = BacklogModel::where('uuid', $this->selectedBucketId)->first();
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
    public function selectCard($id) {
        // Get the selected card
        $this->selectedCard = $this->selectedBucket->cards->where('id', $id)->first();

        // Set the card variables
        $this->name = $this->selectedCard->name;
        $this->description = $this->selectedCard->description;

        // Set selectedCardApprovalStatus
        $this->selectedCardApprovalStatus = $this->selectedCard->approval_status;

        // Set the selected card color based on the approval status
        $this->selectedCardColor = $this->approvalStatusOptions[$this->selectedCard->approval_status];

        // Open the selected card modal
        $this->selectedCardModal = true;
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
     * Open the Edit Card Modal and set the ID
     * 
     * @param int $id
     * 
     * @return void
     */
    public function deleteCard($id) {
        // Set the selected card ID
        $this->selectedCardId = $id;

        // Open the delete card modal
        $this->deleteCardModal = true;
    }

    /**
     * Delete the Card
     * 
     * @return void
     */
    public function destroyCard() {
        // Get the card and delete it
        $card = BacklogCard::where('id', $this->selectedCardId)->first();
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
            // dd($taskWithSameIndex);
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
     * Render the component
     * 
     * @return \Illuminate\View\View
     */
    public function render() {
        return view('livewire.projects.backlog.backlog');
    }
}

